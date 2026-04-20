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

class Product extends ActiveRecord
{

    /**
     * ENUM field values
     */
    const TYPE_GOODS = 'Goods';
    const TYPE_SERVICE = 'Service';
    const TYPE_SUBSCRIPTION = 'Subscription';
    const TYPE_BUNDLE = 'Bundle';
    const TYPE_SOFTWARE = 'Software';

    const BUNDLE_PRICE_TYPE_FIXED = 'fixed';
    const BUNDLE_PRICE_TYPE_SUM = 'sum';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
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
                'sessionKey' => 'product_token',
            ],
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'Product',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'category_id', 'uom_id', 'description', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['type'], 'default', 'value' => 'Service'],
            [['bundle_price_type'], 'default', 'value' => 'fixed'],
            [['base_price'], 'default', 'value' => 0.00],
            [['status_id'], 'default', 'value' => 1],
            [['name'], 'required'],
            [['category_id', 'uom_id', 'is_bundle_expand', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['type', 'bundle_price_type', 'description'], 'string'],
            [['base_price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['code'], 'string', 'max' => 50],
            [['name'], 'string', 'max' => 255],
            ['type', 'in', 'range' => array_keys(self::optsType())],
            ['bundle_price_type', 'in', 'range' => array_keys(self::optsBundlePriceType())],
            [['code'], 'unique'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductCategory::class, 'targetAttribute' => ['category_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => StatusActive::class, 'targetAttribute' => ['status_id' => 'id']],
            [['uom_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductUom::class, 'targetAttribute' => ['uom_id' => 'id']],
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
            'name' => 'Product',
            'category_id' => 'Category',
            'uom_id' => 'UOM',
            'type' => 'Type',
            'bundle_price_type' => 'Bundle Price Type',
            'description' => 'Description',
            'base_price' => 'Base Price (COGS)',
            'is_bundle_expand' => 'Is Bundle Expand',
            'status_id' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(ProductCategory::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[OpportunityProducts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOpportunityProducts()
    {
        return $this->hasMany(OpportunityProduct::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for [[ProductBundleItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductBundleItems()
    {
        return $this->hasMany(ProductBundleItem::class, ['bundle_product_id' => 'id']);
    }

    /**
     * Gets query for [[ProductBundleItems0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductBundleItems0()
    {
        return $this->hasMany(ProductBundleItem::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for [[ProductDiscounts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductDiscounts()
    {
        return $this->hasMany(ProductDiscount::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for [[ProductPrices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductPrices()
    {
        return $this->hasMany(ProductPrice::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for [[QuotationItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotationItems()
    {
        return $this->hasMany(QuotationItem::class, ['product_id' => 'id']);
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

    /**
     * Gets query for [[Uom]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUom()
    {
        return $this->hasOne(ProductUom::class, ['id' => 'uom_id']);
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
     * column type ENUM value labels
     * @return string[]
     */
    public static function optsType()
    {
        return [
            self::TYPE_GOODS => 'Goods',
            self::TYPE_SERVICE => 'Service',
            self::TYPE_SUBSCRIPTION => 'Subscription',
            self::TYPE_BUNDLE => 'Bundle',
            self::TYPE_SOFTWARE => 'Software',
        ];
    }

    /**
     * column bundle_price_type ENUM value labels
     * @return string[]
     */
    public static function optsBundlePriceType()
    {
        return [
            self::BUNDLE_PRICE_TYPE_FIXED => 'fixed',
            self::BUNDLE_PRICE_TYPE_SUM => 'sum',
        ];
    }

    /**
     * @return string
     */
    public function displayType()
    {
        return self::optsType()[$this->type];
    }

    /**
     * @return bool
     */
    public function isTypeGoods()
    {
        return $this->type === self::TYPE_GOODS;
    }

    public function setTypeToGoods()
    {
        $this->type = self::TYPE_GOODS;
    }

    /**
     * @return bool
     */
    public function isTypeService()
    {
        return $this->type === self::TYPE_SERVICE;
    }

    public function setTypeToService()
    {
        $this->type = self::TYPE_SERVICE;
    }

    /**
     * @return bool
     */
    public function isTypeSubscription()
    {
        return $this->type === self::TYPE_SUBSCRIPTION;
    }

    public function setTypeToSubscription()
    {
        $this->type = self::TYPE_SUBSCRIPTION;
    }

    /**
     * @return bool
     */
    public function isTypeBundle()
    {
        return $this->type === self::TYPE_BUNDLE;
    }

    public function setTypeToBundle()
    {
        $this->type = self::TYPE_BUNDLE;
    }

    /**
     * @return bool
     */
    public function isTypeSoftware()
    {
        return $this->type === self::TYPE_SOFTWARE;
    }

    public function setTypeToSoftware()
    {
        $this->type = self::TYPE_SOFTWARE;
    }

    /**
     * @return string
     */
    public function displayBundlePriceType()
    {
        return self::optsBundlePriceType()[$this->bundle_price_type];
    }

    /**
     * @return bool
     */
    public function isBundlePriceTypeFixed()
    {
        return $this->bundle_price_type === self::BUNDLE_PRICE_TYPE_FIXED;
    }

    public function setBundlePriceTypeToFixed()
    {
        $this->bundle_price_type = self::BUNDLE_PRICE_TYPE_FIXED;
    }

    /**
     * @return bool
     */
    public function isBundlePriceTypeSum()
    {
        return $this->bundle_price_type === self::BUNDLE_PRICE_TYPE_SUM;
    }

    public function setBundlePriceTypeToSum()
    {
        $this->bundle_price_type = self::BUNDLE_PRICE_TYPE_SUM;
    }
}
