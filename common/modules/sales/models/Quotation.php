<?php

namespace common\modules\sales\models;

use Yii;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\components\behaviors\TokenProtectedFormBehavior;
use common\components\behaviors\LoggableBehavior;

use common\modules\master\models\StatusActive;
use common\modules\auth\models\User;

use common\modules\sales\models\Opportunity;

class Quotation extends ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_DRAFT = 'Draft';
    const STATUS_SENT = 'Sent';
    const STATUS_APPROVED = 'Approved';
    const STATUS_REJECTED = 'Rejected';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quotation';
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
                'sessionKey' => 'quotation_token',
            ],
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'Quotation',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quotation_number', 'account_id', 'opportunity_id', 'quotation_date', 'valid_until', 'total_amount', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'Draft'],
            [['status_id'], 'default', 'value' => 1],
            [['account_id', 'opportunity_id', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['quotation_date', 'valid_until', 'created_at', 'updated_at'], 'safe'],
            [['total_amount'], 'number'],
            [['status'], 'string'],
            [['quotation_number'], 'string', 'max' => 50],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['account_id' => 'id']],
            [['opportunity_id'], 'exist', 'skipOnError' => true, 'targetClass' => Opportunity::class, 'targetAttribute' => ['opportunity_id' => 'id']],
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
            'quotation_number' => 'Quotation Number',
            'account_id' => 'Account',
            'opportunity_id' => 'Opportunity',
            'quotation_date' => 'Quotation Date',
            'valid_until' => 'Valid Until',
            'total_amount' => 'Total Amount',
            'status' => 'Status',
            'status_id' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert && empty($this->quotation_number)) {
            $this->quotation_number = Yii::$app->db->createCommand("
                SELECT CONCAT(
                    'QTN/',
                    DATE_FORMAT(NOW(), '%Y%m%d'),
                    '/',
                    LPAD(
                        IFNULL(MAX(CAST(SUBSTRING_INDEX(quotation_number, '/', -1) AS UNSIGNED)), 0) + 1,
                        4,
                        '0'
                    )
                )
                FROM quotation
                WHERE DATE(created_at) = CURDATE()
            ")->queryScalar();
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (isset($changedAttributes['status'])) {

            // APPROVED
            if ($this->status === 'Approved' && $changedAttributes['status'] !== 'Approved') {

                $this->handleApproved();
            }

            // REJECTED
            if ($this->status === 'Rejected' && $changedAttributes['status'] !== 'Rejected') {

                $this->handleRejected();
            }
        }

        // saat pertama kali create quotation
        if ($insert && $this->opportunity_id) {
            $this->setOpportunityProposal();
        }
    }

    protected function setOpportunityProposal()
    {
        $opportunity = Opportunity::findOne($this->opportunity_id);

        if ($opportunity && $opportunity->stage !== 'Closed Won') {
            $opportunity->stage = 'Proposal';
            $opportunity->save(false);
        }
    }

    protected function handleApproved()
    {
        // 1. Update Opportunity
        $opportunity = Opportunity::findOne($this->opportunity_id);

        if ($opportunity) {
            $opportunity->stage = 'Closed Won';
            $opportunity->probability = 100;
            $opportunity->amount = $this->total_amount;
            $opportunity->save(false);
        }
    }

    protected function copyItemsToSalesOrder($salesOrderId)
    {
        $items = QuotationItem::find()
            ->where(['quotation_id' => $this->id])
            ->all();

        foreach ($items as $item) {

            $soItem = new SalesOrderItem();
            $soItem->sales_order_id = $salesOrderId;
            $soItem->product_id = $item->product_id;
            $soItem->qty = $item->qty;
            $soItem->price = $item->price;
            $soItem->discount = $item->discount;
            $soItem->total = $item->total;
            $soItem->status_id = 1;

            $soItem->save(false);
        }
    }

    protected function handleRejected()
    {
        $opportunity = Opportunity::findOne($this->opportunity_id);

        if ($opportunity) {
            $opportunity->stage = 'Closed Lost';
            $opportunity->probability = 0;
            $opportunity->save(false);
        }
    }

    protected function generateSoNumber()
    {
        return Yii::$app->db->createCommand("
            SELECT CONCAT(
                'SO/',
                DATE_FORMAT(NOW(), '%Y%m%d'),
                '/',
                LPAD(
                    IFNULL(MAX(CAST(SUBSTRING_INDEX(order_number, '/', -1) AS UNSIGNED)), 0) + 1,
                    4,
                    '0'
                )
            )
            FROM sales_order
            WHERE DATE(created_at) = CURDATE()
        ")->queryScalar();
    }

    /**
     * Gets query for [[Account]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::class, ['id' => 'account_id']);
    }

    /**
     * Gets query for [[Opportunity]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOpportunity()
    {
        return $this->hasOne(Opportunity::class, ['id' => 'opportunity_id']);
    }

    /**
     * Gets query for [[QuotationItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotationItems()
    {
        return $this->hasMany(QuotationItem::class, ['quotation_id' => 'id']);
    }

    /**
     * Gets query for [[SalesOrders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSalesOrder()
    {
        return $this->hasOne(SalesOrder::class, ['quotation_id' => 'id']);
    }

    /**
     * Gets query for [[Status0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0()
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
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SENT => 'Sent',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    /**
     * @return string
     */
    public function displayStatus()
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function setStatusToDraft()
    {
        $this->status = self::STATUS_DRAFT;
    }

    /**
     * @return bool
     */
    public function isStatusSent()
    {
        return $this->status === self::STATUS_SENT;
    }

    public function setStatusToSent()
    {
        $this->status = self::STATUS_SENT;
    }

    /**
     * @return bool
     */
    public function isStatusApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function setStatusToApproved()
    {
        $this->status = self::STATUS_APPROVED;
    }

    /**
     * @return bool
     */
    public function isStatusRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function setStatusToRejected()
    {
        $this->status = self::STATUS_REJECTED;
    }

    public function generateQuotationNumberPreview()
    {
        return 'QTN/' . date('Ymd') . '/AUTO';
    }

    public static function dropdown()
    {
        static $dropdown;
        if ($dropdown === null) {
            
            $models = static::find()->all();
            foreach ($models as $model) {
                $dropdown[$model->id] = $model->quotation_number;
            }
        }
            
        return $dropdown;
    }
}
