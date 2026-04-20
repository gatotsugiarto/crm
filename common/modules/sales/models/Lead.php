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
use common\modules\master\models\Team;

use common\modules\auth\models\User;

use common\modules\sales\models\Account;
use common\modules\sales\models\Contact;

class Lead extends ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lead';
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
                'sessionKey' => 'lead_token',
            ],
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'Lead',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_name', 'contact_name', 'email', 'phone', 'lead_source', 'industry', 'converted_account_id', 'converted_contact_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['is_converted'], 'default', 'value' => 0],
            [['status_id'], 'default', 'value' => 1],
            [['is_converted', 'converted_account_id', 'converted_contact_id', 'city_id', 'province_id', 'country_id', 'postal_code_id', 'owner_user_id', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['company_name', 'contact_name', 'email', 'phone', 'lead_source', 'industry', 'owner_user_id', 'address', 'city_id', 'province_id', 'country_id', 'postal_code_id'], 'required'],
            [['address', 'created_at', 'updated_at'], 'safe'],
            [['company_name', 'description'], 'string', 'max' => 255],
            [['contact_name'], 'string', 'max' => 200],
            [['email'], 'string', 'max' => 150],
            [['phone'], 'string', 'max' => 50],
            [['lead_source', 'industry'], 'string', 'max' => 100],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::class, 'targetAttribute' => ['country_id' => 'id']],
            [['province_id'], 'exist', 'skipOnError' => true, 'targetClass' => Province::class, 'targetAttribute' => ['province_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id']],
            [['postal_code_id'], 'exist', 'skipOnError' => true, 'targetClass' => PostalCode::class, 'targetAttribute' => ['postal_code_id' => 'id']],
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
            'company_name' => 'Company Name',
            'contact_name' => 'Contact Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'lead_source' => 'Lead Source',
            'industry' => 'Industry',
            'city_id' => 'City',
            'province_id' => 'Province',
            'country_id' => 'Country',
            'postal_code_id' => 'Postal Code',
            'is_converted' => 'Is Converted?',
            'converted_account_id' => 'Converted Account',
            'converted_contact_id' => 'Converted Contact',
            'owner_user_id' => 'Owner User',
            'status_id' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
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

    public function getTeam()
    {
        return $this->hasOne(Team::class, ['id' => 'owner_user_id']);
    }

    public function getAccount()
    {
        return $this->hasOne(Account::class, ['id' => 'converted_account_id']);
    }

    public function getContact()
    {
        return $this->hasOne(Contact::class, ['id' => 'converted_contact_id']);
    }

    public static function dropdown()
    {
        static $dropdown;
        if ($dropdown === null) {
            
            $models = static::find()->all();
            foreach ($models as $model) {
                $dropdown[$model->id] = $model->company_name;
            }
        }
            
        return $dropdown;
    }


}
