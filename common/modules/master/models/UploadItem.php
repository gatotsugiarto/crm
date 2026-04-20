<?php

namespace common\modules\master\models;

use Yii;

/**
 * This is the model class for table "upload_item".
 *
 * @property string $upload_field
 * @property int $payroll_item_id
 * @property string|null $operator_info
 *
 * @property PayrollItem $payrollItem
 */
class UploadItem extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'upload_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['operator_info'], 'default', 'value' => null],
            [['upload_field', 'payroll_item_id'], 'required'],
            [['payroll_item_id'], 'integer'],
            [['upload_field'], 'string', 'max' => 20],
            [['operator_info'], 'string', 'max' => 255],
            [['payroll_item_id'], 'unique'],
            [['payroll_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => PayrollItem::class, 'targetAttribute' => ['payroll_item_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'upload_field' => 'Upload Field',
            'payroll_item_id' => 'Payroll Item ID',
            'operator_info' => 'Operator Info',
        ];
    }

    /**
     * Gets query for [[PayrollItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPayrollItem()
    {
        return $this->hasOne(PayrollItem::class, ['id' => 'payroll_item_id']);
    }

}
