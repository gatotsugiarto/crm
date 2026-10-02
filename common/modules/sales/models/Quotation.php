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
use common\modules\master\models\QuotationLayout;
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
            [['contract_months', 'payment_method', 'opening_text', 'terms_text', 'installation_notes', 'closing_text', 'signer_name', 'signer_title'], 'default', 'value' => null],
            [['signer_name', 'signer_title'], 'string', 'max' => 100],
            [['contract_months'], 'integer', 'min' => 1, 'max' => 240],
            // one business line per quotation; it is part of the SPH number, so it is
            // chosen when the quotation is created and can't change afterwards
            [['business_line'], 'filter', 'filter' => fn($v) => $v === null || $v === '' ? null : strtoupper(trim($v))],
            [['business_line'], 'string', 'max' => 20],
            [['business_line'], 'required', 'when' => fn($m) => $m->isNewRecord,
                'whenClient' => 'function () { return $("#quotation-business_line").length && !$("#quotation-business_line").prop("disabled"); }',
                'message' => 'Choose the business line of the products in this quotation.'],
            [['business_line'], function ($attribute) {
                if (!$this->isNewRecord && $this->isAttributeChanged('business_line', false)) {
                    $this->addError($attribute, 'The business line is part of the quotation number and can\'t be changed.');
                }
            }],
            [['payment_method'], 'string', 'max' => 50],
            [['opening_text', 'terms_text', 'installation_notes', 'closing_text'], 'string'],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['account_id' => 'id']],
            [['opportunity_id'], 'exist', 'skipOnError' => true, 'targetClass' => Opportunity::class, 'targetAttribute' => ['opportunity_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => StatusActive::class, 'targetAttribute' => ['status_id' => 'id']],
            // Approving creates the Sales Order (DB trigger), so moving into or out of
            // Approved through the edit form needs the same right as the Approve button.
            [['status'], function ($attribute) {
                $old = $this->getOldAttribute('status');
                $touchesApproved = $this->status !== $old && ($this->status === self::STATUS_APPROVED || $old === self::STATUS_APPROVED);
                if ($touchesApproved && !\common\components\rbac\SalesAccess::canApproveQuotation()) {
                    $this->addError($attribute, 'Only a Sales Manager can approve a quotation or change an approved one.');
                }
            }],
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
            'business_line' => 'Business Line',
            'contract_months' => 'Contract Duration (months)',
            'payment_method' => 'Payment Method',
            'opening_text' => 'Opening Text',
            'terms_text' => 'Terms & Conditions (one per line)',
            'installation_notes' => 'Installation Notes (one per line)',
            'closing_text' => 'Closing Text',
            'signer_name' => 'Signer Name (Diajukan Oleh)',
            'signer_title' => 'Signer Job Title',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            if (empty($this->quotation_number)) {
                $this->quotation_number = self::nextSphNumber($this->quotation_date ?: date('Y-m-d'), $this->business_line);
            }
            // Snapshot the SPH template so an issued quotation keeps its wording.
            $layout = QuotationLayout::findOne(1);
            if ($layout !== null) {
                // signer is not copied: it defaults to the account's Assigned Sales at print time
                $otc = $this->opportunity && $this->opportunity->isOtc();
                foreach (['opening_text', 'terms_text', 'installation_notes', 'closing_text'] as $attr) {
                    if ($this->$attr === null || $this->$attr === '') {
                        // OTC deals get the terms without contract / subscription clauses
                        $this->$attr = ($attr === 'terms_text' && $otc && $layout->terms_text_otc) ? $layout->terms_text_otc : $layout->$attr;
                    }
                }
                // a one time charge has no contract duration
                if (!$this->contract_months && !$otc) {
                    $this->contract_months = $layout->default_contract_months;
                }
            }
        }

        return parent::beforeSave($insert);
    }

    /**
     * Next SPH number for a business line and the year of $date, e.g.
     * "001/SPH/SLS-NHS/EXT/IX/2026". The running number (3 digits, more once past
     * 999) restarts every year and is counted per line: the line and the year are
     * both in the number, so numbers never collide. Only SPH-format numbers are
     * counted (older QTN/QUO numbers are ignored). The rows read are locked
     * (FOR UPDATE) so concurrent inserts take turns; ux_quotation_number rejects a
     * duplicate regardless.
     */
    public static function nextSphNumber($date, $businessLine)
    {
        $time = strtotime($date) ?: time();
        $year = date('Y', $time);
        $roman = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'][(int) date('n', $time) - 1];
        $layout = QuotationLayout::findOne(1);
        $pattern = $layout ? trim($layout->number_code, '/') : 'SPH/SLS-{LINE}/EXT';
        $code = str_replace('{LINE}', strtoupper((string) $businessLine), $pattern);

        $last = (int) Yii::$app->db->createCommand(
            "SELECT MAX(CAST(SUBSTRING_INDEX(quotation_number, '/', 1) AS UNSIGNED))
               FROM quotation
              WHERE quotation_number REGEXP '^[0-9]{3,}/'
                AND quotation_number LIKE :like
                FOR UPDATE",
            [':like' => '%/' . $code . '/%/' . $year]
        )->queryScalar();

        return sprintf('%03d/%s/%s/%s', $last + 1, $code, $roman, $year);
    }

    /**
     * Payment method to print when none was entered: from the recurring items'
     * product revenue model (monthly billing -> "Bulanan", yearly upfront ->
     * "Tahunan (di depan)").
     */
    public function defaultPaymentMethod()
    {
        foreach ($this->quotationItems as $item) {
            $model = (string) ($item->product->revenue_model ?? '');
            if (stripos($model, 'Bulanan') !== false) {
                return 'Bulanan';
            }
            if (stripos($model, 'Tahun') !== false) {
                return 'Tahunan (di depan)';
            }
        }
        // only one-time items (beli putus, installation)
        return $this->quotationItems ? 'Sekali Bayar' : null;
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
            // match the Proposal stage (the DB trigger does the same on Sent)
            if ((int) $opportunity->probability < 50) {
                $opportunity->probability = 50;
            }
            $opportunity->save(false);
        }
    }

    protected function handleApproved()
    {
        Opportunity::findOne($this->opportunity_id)?->syncStageFromQuotations();
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
        Opportunity::findOne($this->opportunity_id)?->syncStageFromQuotations();
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
     * Approved and Rejected are final: the items can't change any more (an approved
     * quotation already has its Sales Order; a revision is a new quotation).
     */
    public function isLocked()
    {
        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_REJECTED], true);
    }

    /** Coloured badge for a quotation status (lists and detail pages). */
    public static function statusBadge($status)
    {
        $class = [
            self::STATUS_DRAFT => 'badge-secondary',
            self::STATUS_SENT => 'badge-info',
            self::STATUS_APPROVED => 'badge-success',
            self::STATUS_REJECTED => 'badge-danger',
        ][$status] ?? 'badge-light';
        return \yii\helpers\Html::tag('span', \yii\helpers\Html::encode($status ?: '-'), ['class' => "badge $class"]);
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
        return 'Auto, e.g. 001/SPH/SLS-<line>/EXT/' . ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'][(int) date('n') - 1] . '/' . date('Y');
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
