<?php

namespace common\modules\master\models;

use Yii;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\web\UploadedFile;

use common\components\behaviors\TokenProtectedFormBehavior;
use common\components\behaviors\LoggableBehavior;

use common\modules\master\models\Client;
use common\modules\master\models\Member;
use common\modules\master\models\StatusActive;
use common\modules\auth\models\User;

class Company extends ActiveRecord
{
    public $file;

    public static function tableName()
    {
        return 'company';
    }

    public function behaviors()
    {
        if ($this instanceof CompanySearch) {
            return [];
        }
    
        return [
            // created_at & updated_at => NOW()
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
            // created_by & updated_by => user login
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            // token protection untuk form
            'tokenProtection' => [
                'class' => TokenProtectedFormBehavior::class,
                'tokenAttribute' => 'form_token',
                'sessionKey' => 'company_token',
            ],
            // log activity otomatis
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'Company', // opsional, default pakai nama tabel
            ],
        ];
    }

    public function rules()
    {
        return [
            [['code', 'company', 'npwp'], 'required'],
            [['status_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['nama_pejabat'], 'string', 'max' => 50],
            [['sign_name'], 'string', 'max' => 150],
            [['file'], 'file','maxFiles' => 6],
            [['sign_image', 'address', 'description'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 10],
            [['company'], 'string', 'max' => 150],
            [['status_id'], 'default', 'value' => 1],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'company' => 'Company',
            'npwp' => 'NPWP',
            'address' => 'Address',
            'nama_pejabat' => 'Nama Pejabat - Formulir 1721-A1',
            'sign_name' => 'Sign Name - Formulir 1721-A1',
            'sign_image' => 'Sign - Image - Formulir 1721-A1',
            'description' => 'Description',
            'status_id' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    // Relasi ke Client
    public function getClients()
    {
        return $this->hasMany(Client::class, ['company_id' => 'id']);
    }

    // Relasi ke Member
    public function getMembers()
    {
        return $this->hasMany(Member::class, ['company_id' => 'id']);
    }

    // Relasi ke status_active
    public function getStatus()
    {
        return $this->hasOne(StatusActive::class, ['id' => 'status_id']);
    }

    // Relasi ke user created
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    // Relasi ke user updated
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public static function dropdown()
    {
        static $dropdown;
        if ($dropdown === null) {
            //$dropdown[0] = 'None';
            $models = static::find()->all();
            foreach ($models as $model) {
                $dropdown[$model->id] = $model->company;
            }
        }
        
        return $dropdown;
    }

    public function afterSave($isNew, $old) {
        /* Upload Image */
        if($isNew){
            $sign_image = $this->uploadAttachment();
            self::updateAll(['sign_image' => $sign_image], ['id' => $this->id]);
        }else{
            $sign_image = $this->uploadUpdateAttachment();
            self::updateAll(['sign_image' => $sign_image], ['id' => $this->id]);
        }
    }

    public function uploadAttachment() {
        $this->file = UploadedFile::getInstances($this, 'file');
        if (empty($this->file)) {
            return false;
        }else{
            $attachment_array = array();
            foreach ($this->file as $key => $file) {
                $filename = $this->cleanspecialchar($file->baseName) . '.' . $file->extension;
                $filename = $this->id.'_'.time().'_'.$filename;
                $file->saveAs(Yii::$app->params['uploadPathAttachment'] . $filename);
                array_push($attachment_array, $filename);
            }
            return implode('###', $attachment_array);
        }
    }

    public function uploadUpdateAttachment() {
        $this->file = UploadedFile::getInstances($this, 'file');
        if($this->sign_image){
            $attachment_array = explode('###', $this->sign_image);
        }else{
            $attachment_array = array();
        }
        foreach ($this->file as $key => $file) {
            $filename = $this->cleanspecialchar($file->baseName) . '.' . $file->extension;
            $filename = $this->id.'_'.time().'_'.$filename;
            $file->saveAs(Yii::$app->params['uploadPathAttachment'] . $filename); //Upload files to server
            array_push($attachment_array, $filename);
        }
        return implode('###', $attachment_array);
    }

    function cleanspecialchar($string) {
        $string = str_replace(' ', '-', $string); //Replaces all spaces
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }
}
