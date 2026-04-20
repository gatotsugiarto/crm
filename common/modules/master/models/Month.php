<?php

namespace common\modules\master\models;

use Yii;

/**
 * This is the model class for table "month".
 *
 * @property int $id
 * @property string $short
 * @property string $month
 * @property string $month_en
 */
class Month extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'month';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['short', 'month', 'month_en'], 'required'],
            [['short', 'month', 'month_en'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'short' => 'Short',
            'month' => 'Month',
            'month_en' => 'Month En',
        ];
    }

    public static function dropdown()
    {
        static $dropdown;
        if ($dropdown === null) {
            
            $models = static::find()->all();
            foreach ($models as $model) {
                $dropdown[$model->id] = $model->month_en;
            }
        }
            
        return $dropdown;
    }

}
