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

class Invoice extends ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_DRAFT = 'Draft';
    const STATUS_SENT = 'Sent';
    const STATUS_PAID = 'Paid';
    const STATUS_OVERDUE = 'Overdue';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice';
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
                'sessionKey' => 'invoice_token',
            ],
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'Invoice',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['invoice_number', 'account_id', 'sales_order_id', 'invoice_date', 'due_date', 'total_amount', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'Draft'],
            [['status_id'], 'default', 'value' => 1],
            [['account_id', 'sales_order_id', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['invoice_date', 'due_date', 'created_at', 'updated_at'], 'safe'],
            [['total_amount'], 'number'],
            [['status'], 'string'],
            [['invoice_number'], 'string', 'max' => 50],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['sales_order_id'], 'unique'],
            [['sales_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalesOrder::class, 'targetAttribute' => ['sales_order_id' => 'id']],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['account_id' => 'id']],
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
            'invoice_number' => 'Invoice Number',
            'account_id' => 'Account ID',
            'sales_order_id' => 'Sales Order ID',
            'invoice_date' => 'Invoice Date',
            'due_date' => 'Due Date',
            'total_amount' => 'Total Amount',
            'status' => 'Status',
            'status_id' => 'Status ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
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
     * Gets query for [[InvoiceItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, ['invoice_id' => 'id']);
    }

    /**
     * Gets query for [[SalesOrder]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSalesOrder()
    {
        return $this->hasOne(SalesOrder::class, ['id' => 'sales_order_id']);
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
            self::STATUS_PAID => 'Paid',
            self::STATUS_OVERDUE => 'Overdue',
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
    public function isStatusPaid()
    {
        return $this->status === self::STATUS_PAID;
    }

    public function setStatusToPaid()
    {
        $this->status = self::STATUS_PAID;
    }

    /**
     * @return bool
     */
    public function isStatusOverdue()
    {
        return $this->status === self::STATUS_OVERDUE;
    }

    public function setStatusToOverdue()
    {
        $this->status = self::STATUS_OVERDUE;
    }

    public static function dropdown()
    {
        static $dropdown;
        if ($dropdown === null) {
            
            $models = static::find()->all();
            foreach ($models as $model) {
                $dropdown[$model->id] = $model->invoice_number;
            }
        }
            
        return $dropdown;
    }
}
