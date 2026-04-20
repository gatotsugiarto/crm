<?php

namespace common\modules\master\models;

use Yii;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\components\behaviors\TokenProtectedFormBehavior;
use common\components\behaviors\LoggableBehavior;

use common\modules\auth\models\User;

class EmployeePending extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const PENDING_TYPE_PAYROLL = 'PAYROLL';
    const PENDING_TYPE_PTKP = 'PTKP';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employee_pending';
    }

    public function behaviors()
    {
        if ($this instanceof UserSearch) {
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
                'sessionKey' => 'employee_pending_token',
            ],
            
            // log activity otomatis
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'EmployeePending', // opsional, default pakai nama tabel
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['e_number', 'marital_status', 'family_status', 'ptkp', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['status_id'], 'default', 'value' => 1],
            [['pending_type'], 'default', 'value' => 'PTKP'],
            [['employee_id', 'pending_date'], 'required'],
            [['employee_id', 'marital_status_id', 'family_status_id', 'ptkp_id', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['pending_type'], 'string'],
            [['pending_date', 'created_at', 'updated_at'], 'safe'],
            [['e_number'], 'string', 'max' => 50],
            [['marital_status', 'family_status', 'ptkp'], 'string', 'max' => 255],
            ['pending_type', 'in', 'range' => array_keys(self::optsPendingType())],
            [['e_number'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'employee_id' => 'Employee',
            'e_number' => 'E Number',
            'marital_status_id' => 'Marital Status ID',
            'marital_status' => 'Marital Status',
            'family_status_id' => 'Family Status ID',
            'family_status' => 'Family Status',
            'ptkp_id' => 'Ptkp ID',
            'ptkp' => 'Ptkp',
            'pending_date' => 'Pending Date',
            'pending_type' => 'Pending Status',
            'status_id' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['id' => 'employee_id']);
    }


    /**
     * column pending_type ENUM value labels
     * @return string[]
     */
    public static function optsPendingType()
    {
        return [
            self::PENDING_TYPE_PAYROLL => 'PAYROLL',
            self::PENDING_TYPE_PTKP => 'PTKP',
        ];
    }

    /**
     * @return string
     */
    public function displayPendingType()
    {
        return self::optsPendingType()[$this->pending_type];
    }

    /**
     * @return bool
     */
    public function isPendingTypePayroll()
    {
        return $this->pending_type === self::PENDING_TYPE_PAYROLL;
    }

    public function setPendingTypeToPayroll()
    {
        $this->pending_type = self::PENDING_TYPE_PAYROLL;
    }

    /**
     * @return bool
     */
    public function isPendingTypePtkp()
    {
        return $this->pending_type === self::PENDING_TYPE_PTKP;
    }

    public function setPendingTypeToPtkp()
    {
        $this->pending_type = self::PENDING_TYPE_PTKP;
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
}
