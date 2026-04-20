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

class SalesOrder extends ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_DRAFT = 'Draft';
    const STATUS_CONFIRMED = 'Confirmed';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_CANCELLED = 'Cancelled';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sales_order';
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
                'sessionKey' => 'salesorder_token',
            ],
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'SalesOrder',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_number', 'account_id', 'quotation_id', 'order_date', 'total_amount', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'Draft'],
            [['status_id'], 'default', 'value' => 1],
            [['account_id', 'quotation_id', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['order_date', 'created_at', 'updated_at'], 'safe'],
            [['total_amount'], 'number'],
            [['status'], 'string'],
            [['order_number'], 'string', 'max' => 50],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['account_id' => 'id']],
            [['quotation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quotation::class, 'targetAttribute' => ['quotation_id' => 'id']],
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
            'order_number' => 'Order Number',
            'account_id' => 'Account ID',
            'quotation_id' => 'Quotation ID',
            'order_date' => 'Order Date',
            'total_amount' => 'Total Amount',
            'status' => 'Status',
            'status_id' => 'Status ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (isset($changedAttributes['status'])) {

            if ($this->status === 'Confirmed' && $changedAttributes['status'] !== 'Confirmed') {
                $this->generateInvoice();
            }
        }
    }

    protected function generateInvoice()
    {
        // ❌ prevent duplicate
        if (Invoice::find()->where(['sales_order_id' => $this->id])->exists()) {
            return;
        }

        // ❗ validasi item
        if (!SalesOrderItem::find()->where(['sales_order_id' => $this->id])->exists()) {
            throw new \Exception('Sales Order item kosong');
        }

        $invoice = new Invoice();
        $invoice->sales_order_id = $this->id;
        $invoice->account_id = $this->account_id;
        $invoice->invoice_date = date('Y-m-d');

        // 🔥 default due date (misal 14 hari)
        $invoice->due_date = date('Y-m-d', strtotime('+14 days'));

        $invoice->total_amount = $this->total_amount;
        $invoice->status = 'Draft';
        $invoice->status_id = 1;

        $invoice->invoice_number = $this->generateInvoiceNumber();

        $invoice->created_at = date('Y-m-d H:i:s');
        $invoice->created_by = Yii::$app->user->id ?? null;

        if ($invoice->save(false)) {
            $this->copyItemsToInvoice($invoice->id);
        }
    }

    protected function copyItemsToInvoice($invoiceId)
    {
        $items = SalesOrderItem::find()
            ->where(['sales_order_id' => $this->id])
            ->all();

        foreach ($items as $item) {

            $invItem = new InvoiceItem();
            $invItem->invoice_id = $invoiceId;
            $invItem->product_id = $item->product_id;
            $invItem->qty = $item->qty;
            $invItem->price = $item->price;
            $invItem->discount = $item->discount;
            $invItem->total = $item->total;
            $invItem->status_id = 1;
            $invItem->created_at = date('Y-m-d H:i:s');
            $invItem->created_by = Yii::$app->user->id ?? null;

            $invItem->save(false);
        }
    }

    protected function generateInvoiceNumber()
    {
        return Yii::$app->db->createCommand("
            SELECT CONCAT(
                'INV/',
                DATE_FORMAT(NOW(), '%Y%m%d'),
                '/',
                LPAD(
                    IFNULL(MAX(CAST(SUBSTRING_INDEX(invoice_number, '/', -1) AS UNSIGNED)), 0) + 1,
                    4,
                    '0'
                )
            )
            FROM invoice
            WHERE DATE(created_at) = CURDATE()
        ")->queryScalar();
    }

    public function getSalesOrderItems()
    {
        return $this->hasMany(SalesOrderItem::class, ['sales_order_id' => 'id']);
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
     * Gets query for [[Invoices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoice::class, ['sales_order_id' => 'id']);
    }

    /**
     * Gets query for [[Quotation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotation()
    {
        return $this->hasOne(Quotation::class, ['id' => 'quotation_id']);
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
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
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
    public function isStatusConfirmed()
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function setStatusToConfirmed()
    {
        $this->status = self::STATUS_CONFIRMED;
    }

    /**
     * @return bool
     */
    public function isStatusCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function setStatusToCompleted()
    {
        $this->status = self::STATUS_COMPLETED;
    }

    /**
     * @return bool
     */
    public function isStatusCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function setStatusToCancelled()
    {
        $this->status = self::STATUS_CANCELLED;
    }

    public static function dropdown()
    {
        static $dropdown;
        if ($dropdown === null) {
            
            $models = static::find()->all();
            foreach ($models as $model) {
                $dropdown[$model->id] = $model->order_number;
            }
        }
            
        return $dropdown;
    }
}
