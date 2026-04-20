<?php

namespace common\modules\sales\models;

use Yii;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\components\behaviors\TokenProtectedFormBehavior;
use common\components\behaviors\LoggableBehavior;

use common\modules\master\models\ActiveStatus;
use common\modules\master\models\Country;
use common\modules\master\models\Province;
use common\modules\master\models\City;
use common\modules\master\models\PostalCode;
use common\modules\master\models\Team;

use common\modules\auth\models\User;

use common\modules\productprice\models\PriceList;

class Account extends ActiveRecord
{

    /**
     * ENUM field values
     */
    const ACCOUNT_TYPE_PROSPECT = 'Prospect';
    const ACCOUNT_TYPE_CUSTOMER = 'Customer';
    const ACCOUNT_TYPE_PARTNER = 'Partner';
    const ACCOUNT_TYPE_RESELLER = 'Reseller';
    const ACCOUNT_TYPE_VENDOR = 'Vendor';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'account';
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
                'sessionKey' => 'account_token',
            ],
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'Account',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_account_id', 'code', 'industry', 'tax_number', 'phone', 'email', 'website', 'address', 'price_list_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'owner_user_id'], 'default', 'value' => null],
            [['account_type'], 'default', 'value' => 'Prospect'],
            [['status_id'], 'default', 'value' => 1],
            [['parent_account_id', 'price_list_id', 'city_id', 'province_id', 'country_id', 'postal_code_id', 'status_id', 'created_by', 'updated_by', 'owner_user_id'], 'integer'],
            [['name'], 'required'],
            [['account_type', 'address'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['code', 'phone'], 'string', 'max' => 50],
            [['name', 'description'], 'string', 'max' => 255],
            [['industry', 'tax_number', 'email'], 'string', 'max' => 100],
            [['website'], 'string', 'max' => 150],
            ['account_type', 'in', 'range' => array_keys(self::optsAccountType())],
            [['code'], 'unique'],
            [['owner_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['owner_user_id' => 'id']],
            [['parent_account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['parent_account_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => ActiveStatus::class, 'targetAttribute' => ['status_id' => 'id']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::class, 'targetAttribute' => ['country_id' => 'id']],
            [['province_id'], 'exist', 'skipOnError' => true, 'targetClass' => Province::class, 'targetAttribute' => ['province_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id']],
            [['postal_code_id'], 'exist', 'skipOnError' => true, 'targetClass' => PostalCode::class, 'targetAttribute' => ['postal_code_id' => 'id']],
            [['price_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => PriceList::class, 'targetAttribute' => ['price_list_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_account_id' => 'Group Account',
            'code' => 'Code',
            'name' => 'Name',
            'account_type' => 'Type',
            'industry' => 'Industry',
            'tax_number' => 'Tax Number',
            'phone' => 'Phone',
            'email' => 'Email',
            'website' => 'Website',
            'address' => 'Address',
            'city_id' => 'City',
            'province_id' => 'Province',
            'country_id' => 'Country',
            'postal_code_id' => 'Postal Code',
            'price_list_id' => 'Price List',
            'description' => 'Description',
            'status_id' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'owner_user_id' => 'Owner User',
        ];
    }

    public function getAccounts()
    {
        return $this->hasMany(Account::class, ['parent_account_id' => 'id']);
    }

    public function getCountry()
    {
        return $this->hasOne(Country::class, ['id' => 'country_id']);
    }

    public function getProvince()
    {
        return $this->hasOne(Province::class, ['id' => 'province_id']);
    }

    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    public function getPostalCode()
    {
        return $this->hasOne(PostalCode::class, ['id' => 'postal_code_id']);
    }

    public function getActivities()
    {
        return $this->hasMany(Activity::class, ['account_id' => 'id']);
    }

    public function getContacts()
    {
        return $this->hasMany(Contact::class, ['account_id' => 'id']);
    }

    public function getInvoices()
    {
        return $this->hasMany(Invoice::class, ['account_id' => 'id']);
    }

    public function getOpportunities()
    {
        return $this->hasMany(Opportunity::class, ['account_id' => 'id']);
    }

    public function getTeam()
    {
        return $this->hasOne(Team::class, ['id' => 'owner_user_id']);
    }

    public function getParentAccount()
    {
        return $this->hasOne(Account::class, ['id' => 'parent_account_id']);
    }

    public function getPriceList()
    {
        return $this->hasOne(PriceList::class, ['id' => 'price_list_id']);
    }

    public function getQuotations()
    {
        return $this->hasMany(Quotation::class, ['account_id' => 'id']);
    }

    public function getSalesOrders()
    {
        return $this->hasMany(SalesOrder::class, ['account_id' => 'id']);
    }

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
     * column account_type ENUM value labels
     * @return string[]
     */
    public static function optsAccountType()
    {
        return [
            self::ACCOUNT_TYPE_PROSPECT => 'Prospect',
            self::ACCOUNT_TYPE_CUSTOMER => 'Customer',
            self::ACCOUNT_TYPE_PARTNER => 'Partner',
            self::ACCOUNT_TYPE_RESELLER => 'Reseller',
            self::ACCOUNT_TYPE_VENDOR => 'Vendor',
        ];
    }

    /**
     * @return string
     */
    public function displayAccountType()
    {
        return self::optsAccountType()[$this->account_type];
    }

    /**
     * @return bool
     */
    public function isAccountTypeProspect()
    {
        return $this->account_type === self::ACCOUNT_TYPE_PROSPECT;
    }

    public function setAccountTypeToProspect()
    {
        $this->account_type = self::ACCOUNT_TYPE_PROSPECT;
    }

    /**
     * @return bool
     */
    public function isAccountTypeCustomer()
    {
        return $this->account_type === self::ACCOUNT_TYPE_CUSTOMER;
    }

    public function setAccountTypeToCustomer()
    {
        $this->account_type = self::ACCOUNT_TYPE_CUSTOMER;
    }

    /**
     * @return bool
     */
    public function isAccountTypePartner()
    {
        return $this->account_type === self::ACCOUNT_TYPE_PARTNER;
    }

    public function setAccountTypeToPartner()
    {
        $this->account_type = self::ACCOUNT_TYPE_PARTNER;
    }

    /**
     * @return bool
     */
    public function isAccountTypeReseller()
    {
        return $this->account_type === self::ACCOUNT_TYPE_RESELLER;
    }

    public function setAccountTypeToReseller()
    {
        $this->account_type = self::ACCOUNT_TYPE_RESELLER;
    }

    /**
     * @return bool
     */
    public function isAccountTypeVendor()
    {
        return $this->account_type === self::ACCOUNT_TYPE_VENDOR;
    }

    public function setAccountTypeToVendor()
    {
        $this->account_type = self::ACCOUNT_TYPE_VENDOR;
    }

    public static function dropdown()
    {
        static $dropdown;
        if ($dropdown === null) {
            
            $models = static::find()->all();
            foreach ($models as $model) {
                $dropdown[$model->id] = $model->name;
            }
        }
            
        return $dropdown;
    }
}
