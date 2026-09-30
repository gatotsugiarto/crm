<?php

namespace common\modules\productprice\models;

use Yii;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\components\behaviors\TokenProtectedFormBehavior;
use common\components\behaviors\LoggableBehavior;

use common\modules\master\models\StatusActive;
use common\modules\auth\models\User;

class PriceList extends ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'price_list';
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
                'sessionKey' => 'pricelist_token',
            ],
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'PriceList',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['currency'], 'default', 'value' => 'IDR'],
            [['status_id'], 'default', 'value' => 1],
            [['status_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['currency'], 'string', 'max' => 10],
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
            'name' => 'Price List',
            'currency' => 'Currency',
            'status_id' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[ProductDiscounts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductDiscounts()
    {
        return $this->hasMany(ProductDiscount::class, ['price_list_id' => 'id']);
    }

    /**
     * Gets query for [[ProductPrices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductPrices()
    {
        return $this->hasMany(ProductPrice::class, ['price_list_id' => 'id']);
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



    /**
     * Active price lists for a form's dropdown; $keepId (the record's current
     * value) is included even when that list has been deactivated.
     * @return array id => name
     */
    public static function dropdownActive($keepId = null)
    {
        $list = static::find()->select(['name', 'id'])
            ->where(['status_id' => 1])->orderBy(['name' => SORT_ASC])
            ->indexBy('id')->column();
        if ($keepId && !isset($list[$keepId]) && ($kept = static::findOne($keepId)) !== null) {
            $list[$kept->id] = $kept->name . ' (Non Active)';
        }
        return $list;
    }

    /**
     * Why this price list can't be deleted, or null. Its product prices and
     * discounts and the accounts that use it must go first; deactivating it
     * (Non Active) keeps the history instead.
     */
    public function deleteBlockers()
    {
        $counts = [
            'product price' => (int) ProductPrice::find()->where(['price_list_id' => $this->id])->count(),
            'discount'      => (int) ProductDiscount::find()->where(['price_list_id' => $this->id])->count(),
            'account'       => (int) \common\modules\sales\models\Account::find()->where(['price_list_id' => $this->id])->count(),
        ];
        $parts = [];
        foreach ($counts as $label => $n) {
            if ($n > 0) {
                $parts[] = $n . ' ' . $label . ($n === 1 ? '' : 's');
            }
        }
        if (!$parts) {
            return null;
        }
        $last = array_pop($parts);
        $list = $parts ? implode(', ', $parts) . ' and ' . $last : $last;
        return "{$this->name} still has {$list}. Set it to Non Active instead, or remove those first.";
    }
}
