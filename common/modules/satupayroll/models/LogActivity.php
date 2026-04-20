<?php

namespace common\modules\satupayroll\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

use common\modules\auth\models\User;
use common\modules\master\models\Employee;

class LogActivity extends ActiveRecord
{
    public static function tableName()
    {
        return 'log_activity';
    }

    public function behaviors()
    {
        return [
            // created_at otomatis NOW()
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
            // created_by otomatis user login
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'action_by',
                'updatedByAttribute' => false, // log tidak di-update
            ],
        ];
    }

    public function rules()
    {
        return [
            [['controller_action', 'model_name', 'record_id'], 'required'],
            [['employee_id', 'record_id', 'action_by'], 'integer'],
            [['created_at'], 'safe'],
            [['controller_action', 'model_name', 'status'], 'string', 'max' => 50],
            [['ip_address'], 'string', 'max' => 45],
            [['user_agent'], 'string', 'max' => 255],
            [['request_url'], 'string', 'max' => 255],
            [['before_data', 'after_data'], 'string'],
            [['remarks'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'controller_action' => 'Action',
            'model_name' => 'Model',
            'record_id' => 'Record ID',
            'action_by' => 'Action By',
            'created_at' => 'Action Date',
            'ip_address' => 'IP Address',
            'user_agent' => 'User Agent',
            'request_url' => 'Request URL',
            'before_data' => 'Before Data',
            'after_data' => 'After Data',
            'status' => 'Status',
            'employee_id' => 'Employee',
            'remarks' => 'Remarks',
        ];
    }

    // Relasi ke user
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'action_by']);
    }

    // Relasi ke employee
    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['id' => 'employee_id']);
    }
}
