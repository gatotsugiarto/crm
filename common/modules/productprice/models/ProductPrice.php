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

class ProductPrice extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_price';
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
                'sessionKey' => 'productprice_token',
            ],
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'ProductPrice',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'price_list_id', 'price', 'valid_from', 'valid_to', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['status_id'], 'default', 'value' => 1],
            [['product_id', 'price_list_id', 'min_qty', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['price'], 'number'],
            [['valid_from', 'valid_to', 'created_at', 'updated_at'], 'safe'],
            [['product_id', 'price_list_id', 'min_qty', 'valid_from'], 'unique', 'targetAttribute' => ['product_id', 'price_list_id', 'min_qty', 'valid_from']],
            [['price_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => PriceList::class, 'targetAttribute' => ['price_list_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
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
            'product_id' => 'Product',
            'price_list_id' => 'Price List',
            'price' => 'Price',
            'valid_from' => 'Valid From',
            'valid_to' => 'Valid To',
            'min_qty' => 'Min Qty',
            'status_id' => 'Status ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[PriceList]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPriceList()
    {
        return $this->hasOne(PriceList::class, ['id' => 'price_list_id']);
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
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


}
