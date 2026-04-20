<?php

namespace common\modules\master\models;

use Yii;

/**
 * This is the model class for table "employee_status_transaction".
 *
 * @property int $id
 * @property int $employee_id
 * @property int $other_id
 * @property int $before_status_id
 * @property int $after_status_id
 * @property int $module_id
 * @property string|null $module
 * @property int $status_id
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class EmployeeStatusTransaction extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employee_status_transaction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['module', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['status_id'], 'default', 'value' => 1],
            [['employee_id', 'other_id'], 'required'],
            [['employee_id', 'other_id', 'before_status_id', 'after_status_id', 'module_id', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['module'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'employee_id' => 'Employee ID',
            'other_id' => 'Other ID',
            'before_status_id' => 'Before Status ID',
            'after_status_id' => 'After Status ID',
            'module_id' => 'Module ID',
            'module' => 'Module',
            'status_id' => 'Status ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

}
