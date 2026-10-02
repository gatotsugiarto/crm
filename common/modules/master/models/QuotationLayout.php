<?php

namespace common\modules\master\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use common\components\behaviors\LoggableBehavior;

/**
 * The single SPH (quotation letter) template, edited in Master Data -> Layout
 * Quotation. New quotations copy its texts (see Quotation::beforeSave) so an
 * issued SPH doesn't change when this template does.
 *
 * @property int $id
 * @property string $company_name
 * @property string|null $company_address
 * @property string|null $company_phone
 * @property string|null $company_website
 * @property string|null $logo_file
 * @property string $number_code middle part of the SPH number, e.g. SPH/SLS-{LINE}/EXT
 *           ({LINE} = the quotation's business line: NHS, NXG, IPTV, ...)
 * @property string $city
 * @property string|null $recipient_title
 * @property string|null $opening_text
 * @property string|null $terms_text one clause per line (Recurring deals)
 * @property string|null $terms_text_otc one clause per line (OTC deals: no contract clauses)
 * @property string|null $installation_notes one note per line
 * @property string|null $closing_text
 * @property string $sign_left_label
 * @property string $sign_right_label
 * @property string|null $signer_name default "Diajukan Oleh" name
 * @property string|null $signer_title default "Diajukan Oleh" job title
 * @property int|null $default_contract_months
 */
class QuotationLayout extends ActiveRecord
{
    const LOGO_EXTENSIONS = ['png', 'jpg', 'jpeg'];

    /** @var UploadedFile|null */
    public $logoUpload;

    public static function tableName()
    {
        return 'quotation_layout';
    }

    /**
     * The template (row 1).
     */
    public static function current()
    {
        $layout = static::findOne(1);
        if ($layout === null) {
            throw new \RuntimeException('Quotation layout is missing; run the migrations.');
        }
        return $layout;
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
                'modelName' => 'QuotationLayout',
            ],
        ];
    }

    public function rules()
    {
        return [
            [['company_name', 'number_code', 'city', 'sign_left_label', 'sign_right_label'], 'required'],
            [['company_name'], 'string', 'max' => 150],
            [['company_address'], 'string', 'max' => 255],
            [['company_phone', 'company_website', 'recipient_title', 'signer_name', 'signer_title'], 'string', 'max' => 100],
            [['number_code', 'city', 'sign_left_label', 'sign_right_label'], 'string', 'max' => 50],
            [['number_code'], 'match', 'pattern' => '/^([A-Za-z0-9\-\/]|\{LINE\})+$/', 'message' => 'Use letters, digits, "-", "/" and {LINE} only.'],
            [['opening_text', 'terms_text', 'terms_text_otc', 'installation_notes', 'closing_text'], 'string'],
            [['default_contract_months'], 'integer', 'min' => 1, 'max' => 240],
            [['logoUpload'], 'file', 'skipOnEmpty' => true, 'extensions' => self::LOGO_EXTENSIONS, 'maxSize' => 2 * 1024 * 1024],
        ];
    }

    public function attributeLabels()
    {
        return [
            'company_name' => 'Company Name',
            'company_address' => 'Address (footer)',
            'company_phone' => 'Phone / Fax (footer)',
            'company_website' => 'Website (footer)',
            'logoUpload' => 'Logo (PNG/JPG, max 2 MB)',
            'number_code' => 'SPH Number Code',
            'city' => 'City',
            'recipient_title' => 'Recipient Title',
            'opening_text' => 'Opening Text',
            'terms_text' => 'Terms & Conditions - Recurring (one per line)',
            'terms_text_otc' => 'Terms & Conditions - OTC (one per line)',
            'installation_notes' => 'Installation Notes (one per line)',
            'closing_text' => 'Closing Text',
            'sign_left_label' => 'Left Signature Label',
            'sign_right_label' => 'Right Signature Label',
            'signer_name' => 'Fallback Signer Name',
            'signer_title' => 'Fallback Signer Job Title',
            'default_contract_months' => 'Default Contract (months)',
        ];
    }

    public static function storageDir()
    {
        return Yii::getAlias('@backend/runtime/quotation-layout');
    }

    public function getLogoPath()
    {
        return $this->logo_file ? self::storageDir() . DIRECTORY_SEPARATOR . $this->logo_file : null;
    }

    public function hasLogo()
    {
        $path = $this->getLogoPath();
        return $path !== null && is_file($path);
    }

    /**
     * Saves the uploaded logo (if any) under a new name, then the row; the old
     * logo file is removed once the row points to the new one.
     */
    public function saveWithLogo()
    {
        $this->logoUpload = UploadedFile::getInstance($this, 'logoUpload');
        if (!$this->validate()) {
            return false;
        }

        $old = $this->getLogoPath();
        if ($this->logoUpload !== null) {
            FileHelper::createDirectory(self::storageDir(), 0775, true);
            $name = 'logo-' . time() . '.' . strtolower($this->logoUpload->extension);
            if (!$this->logoUpload->saveAs(self::storageDir() . DIRECTORY_SEPARATOR . $name)) {
                $this->addError('logoUpload', 'The logo could not be saved on the server.');
                return false;
            }
            $this->logo_file = $name;
        }

        if (!$this->save(false)) {
            return false;
        }
        if ($this->logoUpload !== null && $old !== null && $old !== $this->getLogoPath()) {
            @unlink($old);
        }
        return true;
    }

    /**
     * HTML for one line of template text: escaped, with **text** turned into bold
     * (the only markup the SPH texts support).
     */
    public static function inline($text)
    {
        return preg_replace('/\*\*(.+?)\*\*/s', '<b>$1</b>', \yii\helpers\Html::encode((string) $text));
    }

    /** Splits a one-item-per-line text into trimmed, non-empty lines. */
    public static function lines($text)
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $text)), 'strlen'));
    }
}
