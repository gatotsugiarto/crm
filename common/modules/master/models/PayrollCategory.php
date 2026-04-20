<?php

namespace common\modules\master\models;

use Yii;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\components\behaviors\TokenProtectedFormBehavior;
use common\components\behaviors\LoggableBehavior;

/**
 * This is the model class for table "payroll_category".
 *
 * @property int $id
 * @property string|null $code
 * @property string|null $name
 * @property int|null $display_order
 *
 * @property PayrollItem[] $payrollItems
 */
class PayrollCategory extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payroll_category';
    }

    public function behaviors()
    {
        if ($this instanceof UserSearch) {
            return [];
        }

        return [
            // created_at & updated_at => NOW()
            // [
            //     'class' => TimestampBehavior::class,
            //     'attributes' => [
            //         ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
            //         ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
            //     ],
            //     'value' => new Expression('NOW()'),
            // ],

            // // created_by & updated_by => user login
            // [
            //     'class' => BlameableBehavior::class,
            //     'createdByAttribute' => 'created_by',
            //     'updatedByAttribute' => 'updated_by',
            // ],
            
            // token protection untuk form
            'tokenProtection' => [
                'class' => TokenProtectedFormBehavior::class,
                'tokenAttribute' => 'form_token',
                'sessionKey' => 'payrollcategory_token',
            ],
            
            // log activity otomatis
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'PayrollCategory', // opsional, default pakai nama tabel
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'name', 'display_order'], 'default', 'value' => null],
            [['display_order'], 'integer'],
            [['code'], 'string', 'max' => 40],
            [['name'], 'string', 'max' => 100],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'display_order' => 'Display Order',
        ];
    }

    /**
     * Gets query for [[PayrollItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPayrollItems()
    {
        return $this->hasMany(PayrollItem::class, ['category_id' => 'id']);
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
