<?php

namespace common\modules\master\models;

use Yii;

/**
 * This is the model class for table "status_payroll".
 *
 * @property int $id
 * @property string $status_payroll
 */
class StatusPayroll extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'status_payroll';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status_payroll'], 'required'],
            [['status_payroll'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status_payroll' => 'Status Payroll',
        ];
    }

}
