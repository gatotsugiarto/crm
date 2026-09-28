<?php

namespace common\modules\sales\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use common\components\behaviors\LoggableBehavior;
use common\modules\auth\models\User;

/**
 * A document attached to an Account (contract, NPWP, company profile, ...).
 *
 * The file lives in params['accountAttachmentPath']/<account_id>/<stored_name>,
 * outside the web root; AccountController::actionDownloadattachment serves it.
 *
 * @property int $id
 * @property int $account_id
 * @property string $file_name original file name, shown to users
 * @property string $stored_name random name on disk
 * @property string|null $mime_type
 * @property int|null $file_size bytes
 * @property string|null $description
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class AccountAttachment extends ActiveRecord
{
    const EXTENSIONS = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'csv', 'txt', 'jpg', 'jpeg', 'png', 'zip', 'rar'];

    /** @var UploadedFile|null */
    public $file;

    public static function tableName()
    {
        return 'account_attachment';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'AccountAttachment',
            ],
        ];
    }

    public function rules()
    {
        return [
            [['account_id'], 'required'],
            [['account_id', 'file_size', 'created_by', 'updated_by'], 'integer'],
            [['description'], 'string', 'max' => 255],
            [['description'], 'trim'],
            [['file'], 'file',
                'skipOnEmpty' => false,
                'extensions' => self::EXTENSIONS,
                'checkExtensionByMimeType' => false,
                'maxSize' => Yii::$app->params['accountAttachmentMaxSize'] ?? 10 * 1024 * 1024,
                'on' => 'upload',
            ],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['account_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_id' => 'Account',
            'file' => 'File',
            'file_name' => 'File Name',
            'mime_type' => 'Type',
            'file_size' => 'Size',
            'description' => 'Description',
            'created_at' => 'Uploaded At',
            'created_by' => 'Uploaded By',
        ];
    }

    public function getAccount()
    {
        return $this->hasOne(Account::class, ['id' => 'account_id']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public static function storageDir($accountId)
    {
        return Yii::getAlias(Yii::$app->params['accountAttachmentPath']) . DIRECTORY_SEPARATOR . (int) $accountId;
    }

    public function getFilePath()
    {
        return self::storageDir($this->account_id) . DIRECTORY_SEPARATOR . $this->stored_name;
    }

    /**
     * Validates $this->file, writes it to disk and inserts the row. The file is
     * removed again if the insert fails, so disk and table stay in step.
     */
    public function upload()
    {
        $this->scenario = 'upload';
        if (!$this->validate()) {
            return false;
        }

        $dir = self::storageDir($this->account_id);
        FileHelper::createDirectory($dir, 0775, true);

        $this->file_name = mb_substr($this->file->baseName, 0, 240) . '.' . $this->file->extension;
        $this->stored_name = Yii::$app->security->generateRandomString(24) . '.' . strtolower($this->file->extension);
        $this->mime_type = mb_substr((string) $this->file->type, 0, 100);
        $this->file_size = (int) $this->file->size;

        if (!$this->file->saveAs($this->getFilePath())) {
            $this->addError('file', 'The file could not be saved on the server.');
            return false;
        }

        if (!$this->save(false)) {
            @unlink($this->getFilePath());
            return false;
        }

        return true;
    }

    public function afterDelete()
    {
        parent::afterDelete();
        @unlink($this->getFilePath());
    }
}
