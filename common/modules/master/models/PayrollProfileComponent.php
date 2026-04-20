<?php

namespace common\modules\master\models;

use Yii;

/**
 * This is the model class for table "payroll_profile_component".
 *
 * @property int $id
 * @property int $profile_id
 * @property int $item_id
 * @property int $status_id
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property PayrollItem $item
 * @property PayrollProfile $profile
 */
class PayrollProfileComponent extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payroll_profile_component';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['status_id'], 'default', 'value' => 1],
            [['profile_id', 'item_id'], 'required'],
            [['profile_id', 'item_id', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => PayrollProfile::class, 'targetAttribute' => ['profile_id' => 'id']],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => PayrollItem::class, 'targetAttribute' => ['item_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'profile_id' => 'Profile ID',
            'item_id' => 'Item ID',
            'status_id' => 'Status ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[Item]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(PayrollItem::class, ['id' => 'item_id']);
    }

    /**
     * Gets query for [[Profile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(PayrollProfile::class, ['id' => 'profile_id']);
    }

    public static function dropdown()
    {
        static $dropdown;
        if ($dropdown === null) {
            
            $models = static::find()->all();
            foreach ($models as $model) {
                $dropdown[$model->id] = $model->name.' ['.$model->code.']';
            }
        }
            
        return $dropdown;
    }

}
