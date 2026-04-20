<?php

namespace common\modules\master\models;

use Yii;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\components\behaviors\TokenProtectedFormBehavior;
use common\components\behaviors\LoggableBehavior;

use common\modules\master\models\Status;
use common\modules\auth\models\User;

class City extends ActiveRecord
{

    /**
     * ENUM field values
     */
    const TYPE_CITY = 'CITY';
    const TYPE_REGENCY = 'REGENCY';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'city';
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
                'sessionKey' => 'city_token',
            ],
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'City',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['status_id'], 'default', 'value' => 1],
            [['province_id', 'name', 'type'], 'required'],
            [['province_id', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['type'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['code'], 'string', 'max' => 10],
            [['name'], 'string', 'max' => 100],
            ['type', 'in', 'range' => array_keys(self::optsType())],
            [['province_id', 'name'], 'unique', 'targetAttribute' => ['province_id', 'name']],
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
            'province_id' => 'Province',
            'code' => 'Code',
            'name' => 'Name',
            'type' => 'Type',
            'status_id' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[PostalCodes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPostalCodes()
    {
        return $this->hasMany(PostalCode::class, ['city_id' => 'id']);
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
     * column type ENUM value labels
     * @return string[]
     */
    public static function optsType()
    {
        return [
            self::TYPE_CITY => 'CITY',
            self::TYPE_REGENCY => 'REGENCY',
        ];
    }

    /**
     * @return string
     */
    public function displayType()
    {
        return self::optsType()[$this->type];
    }

    /**
     * @return bool
     */
    public function isTypeCity()
    {
        return $this->type === self::TYPE_CITY;
    }

    public function setTypeToCity()
    {
        $this->type = self::TYPE_CITY;
    }

    /**
     * @return bool
     */
    public function isTypeRegency()
    {
        return $this->type === self::TYPE_REGENCY;
    }

    public function setTypeToRegency()
    {
        $this->type = self::TYPE_REGENCY;
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
