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

class ProductDiscount extends ActiveRecord
{

    /**
     * ENUM field values
     */
    const DISCOUNT_TYPE_PERCENT = 'percent';
    const DISCOUNT_TYPE_AMOUNT = 'amount';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_discount';
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
                'sessionKey' => 'productdiscount_token',
            ],
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'ProductDiscount',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'price_list_id', 'discount_type', 'discount_value', 'valid_from', 'valid_to', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['status_id'], 'default', 'value' => 1],
            [['is_stackable'], 'default', 'value' => 0],
            [['product_id', 'price_list_id', 'priority', 'min_qty', 'is_stackable', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['discount_type'], 'string'],
            [['discount_value'], 'number'],
            [['valid_from', 'valid_to', 'created_at', 'updated_at'], 'safe'],
            ['discount_type', 'in', 'range' => array_keys(self::optsDiscountType())],
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
            'discount_type' => 'Discount Type',
            'discount_value' => 'Discount Value',
            'priority' => 'Priority',
            'min_qty' => 'Min Qty',
            'valid_from' => 'Valid From',
            'valid_to' => 'Valid To',
            'is_stackable' => 'Is Stackable',
            'status_id' => 'Status',
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



    /**
     * column discount_type ENUM value labels
     * @return string[]
     */
    public static function optsDiscountType()
    {
        return [
            self::DISCOUNT_TYPE_PERCENT => 'percent',
            self::DISCOUNT_TYPE_AMOUNT => 'amount',
        ];
    }

    /**
     * @return string
     */
    public function displayDiscountType()
    {
        return self::optsDiscountType()[$this->discount_type];
    }

    /**
     * @return bool
     */
    public function isDiscountTypePercent()
    {
        return $this->discount_type === self::DISCOUNT_TYPE_PERCENT;
    }

    public function setDiscountTypeToPercent()
    {
        $this->discount_type = self::DISCOUNT_TYPE_PERCENT;
    }

    /**
     * @return bool
     */
    public function isDiscountTypeAmount()
    {
        return $this->discount_type === self::DISCOUNT_TYPE_AMOUNT;
    }

    public function setDiscountTypeToAmount()
    {
        $this->discount_type = self::DISCOUNT_TYPE_AMOUNT;
    }
}
