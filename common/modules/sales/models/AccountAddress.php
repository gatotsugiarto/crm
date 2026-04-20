<?php

namespace common\modules\sales\models;

use Yii;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\components\behaviors\TokenProtectedFormBehavior;
use common\components\behaviors\LoggableBehavior;

use common\modules\master\models\StatusActive;
use common\modules\master\models\Country;
use common\modules\master\models\Province;
use common\modules\master\models\City;
use common\modules\master\models\PostalCode;

use common\modules\auth\models\User;

class AccountAddress extends ActiveRecord
{

    /**
     * ENUM field values
     */
    const ADDRESS_TYPE_INVOICE = 'Invoice';
    const ADDRESS_TYPE_BRANCH = 'Branch';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'account_address';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        if ($this instanceof UserSearch) {
            return [];
        }

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
            'tokenProtection' => [
                'class' => TokenProtectedFormBehavior::class,
                'tokenAttribute' => 'form_token',
                'sessionKey' => 'accountaddress_token',
            ],
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'AccountAddress',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['account_id', 'address', 'city_id', 'province_id', 'country_id', 'postal_code_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['address_type'], 'default', 'value' => 'Invoice'],
            [['status_id'], 'default', 'value' => 1],
            [['account_id', 'city_id', 'province_id', 'country_id', 'postal_code_id', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['address_type', 'address'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            ['address_type', 'in', 'range' => array_keys(self::optsAddressType())],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::class, 'targetAttribute' => ['country_id' => 'id']],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['account_id' => 'id']],
            [['province_id'], 'exist', 'skipOnError' => true, 'targetClass' => Province::class, 'targetAttribute' => ['province_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => StatusActive::class, 'targetAttribute' => ['status_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_id' => 'Account',
            'address_type' => 'Address Type',
            'address' => 'Address',
            'city_id' => 'City',
            'province_id' => 'Province',
            'country_id' => 'Country',
            'postal_code_id' => 'Postal Code',
            'status_id' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[Account]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::class, ['id' => 'account_id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * Gets query for [[Country]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::class, ['id' => 'country_id']);
    }

    /**
     * Gets query for [[Province]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProvince()
    {
        return $this->hasOne(Province::class, ['id' => 'province_id']);
    }

    public function getPostalCode()
    {
        return $this->hasOne(PostalCode::class, ['id' => 'postal_code_id']);
    }

    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery
     */
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



    /**
     * column address_type ENUM value labels
     * @return string[]
     */
    public static function optsAddressType()
    {
        return [
            self::ADDRESS_TYPE_INVOICE => 'Invoice',
            self::ADDRESS_TYPE_BRANCH => 'Branch',
        ];
    }

    /**
     * @return string
     */
    public function displayAddressType()
    {
        return self::optsAddressType()[$this->address_type];
    }

    /**
     * @return bool
     */
    public function isAddressTypeInvoice()
    {
        return $this->address_type === self::ADDRESS_TYPE_INVOICE;
    }

    public function setAddressTypeToInvoice()
    {
        $this->address_type = self::ADDRESS_TYPE_INVOICE;
    }

    /**
     * @return bool
     */
    public function isAddressTypeBranch()
    {
        return $this->address_type === self::ADDRESS_TYPE_BRANCH;
    }

    public function setAddressTypeToBranch()
    {
        $this->address_type = self::ADDRESS_TYPE_BRANCH;
    }
}
