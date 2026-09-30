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
use common\modules\master\models\Team;

use common\modules\auth\models\User;

class Opportunity extends ActiveRecord
{
    /** @var Quotation[] Draft/Sent quotations the last createQuotations() set to Rejected */
    public $replacedQuotations = [];


    /**
     * ENUM field values
     */
    const STAGE_PROSPECTING = 'Prospecting';
    const STAGE_QUALIFICATION = 'Qualification';
    const STAGE_PROPOSAL = 'Proposal';
    const STAGE_NEGOTIATION = 'Negotiation';
    const STAGE_CLOSED_WON = 'Closed Won';
    const STAGE_CLOSED_LOST = 'Closed Lost';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'opportunity';
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
                'sessionKey' => 'opportunity_token',
            ],
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'Opportunity',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['account_id', 'contact_id', 'close_date', 'description', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['stage'], 'default', 'value' => 'Prospecting'],
            [['amount'], 'default', 'value' => 0.00],
            [['probability'], 'default', 'value' => 0],
            [['status_id'], 'default', 'value' => 1],
            [['account_id', 'contact_id', 'owner_user_id', 'probability', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['name'], 'required'],
            [['stage'], 'string'],
            [['amount'], 'number'],
            [['close_date', 'created_at', 'updated_at'], 'safe'],
            [['name', 'description'], 'string', 'max' => 255],
            ['stage', 'in', 'range' => array_keys(self::optsStage())],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['account_id' => 'id']],
            [['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::class, 'targetAttribute' => ['contact_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => StatusActive::class, 'targetAttribute' => ['status_id' => 'id']],
            [['amount'], 'validateAmountFromProducts'],
            [['owner_user_id'], function ($attribute) {
                \common\components\rbac\SalesAccess::checkAssignment($this, $attribute);
            }, 'skipOnEmpty' => false],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_id' => 'Account',
            'contact_id' => 'Contact',
            'owner_user_id' => 'Owner User',
            'name' => 'Name',
            'stage' => 'Stage',
            'amount' => 'Amount',
            'close_date' => 'Close Date',
            'probability' => 'Probability (%)',
            'description' => 'Description',
            'status_id' => 'Status',
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
     * Gets query for [[Activities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActivities()
    {
        return $this->hasMany(Activity::class, ['opportunity_id' => 'id']);
    }

    /**
     * Gets query for [[Contact]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContact()
    {
        return $this->hasOne(Contact::class, ['id' => 'contact_id']);
    }

    /**
     * Gets query for [[OpportunityProducts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOpportunityProducts()
    {
        return $this->hasMany(OpportunityProduct::class, ['opportunity_id' => 'id']);
    }

    /**
     * Gets query for [[OpportunityStageHistories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOpportunityStageHistories()
    {
        return $this->hasMany(OpportunityStageHistory::class, ['opportunity_id' => 'id']);
    }

    /**
     * Gets query for [[OwnerUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(Team::class, ['id' => 'owner_user_id']);
    }

    /**
     * Gets query for [[Quotations]].
     *
     * @return \yii\db\ActiveQuery
     */
    /**
     * Inline-validator body for amount: once the opportunity has products, the
     * amount is their total (kept by the opportunity_product triggers), so it
     * can't be typed in.
     */
    public function validateAmountFromProducts($attribute)
    {
        if (!$this->isNewRecord && $this->isAttributeChanged('amount', false)
            && $this->getOpportunityProducts()->exists()) {
            $this->addError($attribute, 'Amount is calculated from the products; change the products instead.');
        }
    }

    public function getQuotations()
    {
        return $this->hasMany(Quotation::class, ['opportunity_id' => 'id']);
    }

    /** Active products of this opportunity (the ones copied into quotations). */
    private function activeProducts()
    {
        return $this->getOpportunityProducts()
            ->andWhere(['or', ['status_id' => 1], ['status_id' => null]])
            ->with('product')->orderBy(['id' => SORT_ASC])->all();
    }

    /**
     * Business lines that still need a quotation: lines of the active products
     * that don't have an approved quotation yet.
     * @return array line => OpportunityProduct[]
     */
    private function linesToQuote()
    {
        $approved = $this->getQuotations()->select('business_line')
            ->where(['status' => Quotation::STATUS_APPROVED])->column();
        $lines = [];
        foreach ($this->activeProducts() as $row) {
            $line = $row->product->business_line ?? null;
            if ($line !== null && !in_array($line, $approved, true)) {
                $lines[$line][] = $row;
            }
        }
        return $lines;
    }

    /**
     * Draft/Sent quotations that Create Quotation will replace: the open ones in the
     * business lines it is about to quote again.
     * @return Quotation[]
     */
    public function quotationsToReplace()
    {
        $lines = array_keys($this->linesToQuote());
        if (!$lines) {
            return [];
        }
        return $this->getQuotations()
            ->where(['status' => [Quotation::STATUS_DRAFT, Quotation::STATUS_SENT], 'business_line' => $lines])
            ->orderBy(['id' => SORT_ASC])->all();
    }

    /**
     * Why quotations can't be created from this opportunity yet, or null.
     */
    public function quotationBlocker()
    {
        $products = $this->activeProducts();
        if (!$products) {
            return 'Add products to this opportunity first; they are copied into the quotation.';
        }
        $missing = [];
        foreach ($products as $row) {
            if (empty($row->product->business_line)) {
                $missing[] = $row->product->name ?? ('#' . $row->product_id);
            }
        }
        if ($missing) {
            return 'Set the Business Line (Product & Pricing -> Products) for: ' . implode(', ', array_unique($missing)) . '.';
        }
        if (!$this->linesToQuote()) {
            return 'Every business line of this opportunity already has an approved quotation.';
        }
        return null;
    }

    /**
     * Creates one Draft quotation per business line of this opportunity's products
     * (lines that already have an approved quotation are skipped), each for the
     * opportunity's account, dated today, valid 30 days, numbered in its line, with
     * one item per product of that line (qty, price, discount; totals by the
     * quotation_item triggers). Creating a quotation moves the opportunity to
     * Proposal (Quotation::afterSave).
     *
     * It is also the revise action: Draft/Sent quotations of those lines
     * (quotationsToReplace()) are set to Rejected after their replacement exists,
     * so the opportunity never looks "all rejected" (Closed Lost) in between. The
     * rejected ones are left in $replacedQuotations.
     *
     * @return Quotation[]
     * @throws \RuntimeException with quotationBlocker()'s reason
     */
    public function createQuotations()
    {
        $blocker = $this->quotationBlocker();
        if ($blocker !== null) {
            throw new \RuntimeException($blocker);
        }

        $created = [];
        $this->replacedQuotations = $this->quotationsToReplace();
        $transaction = static::getDb()->beginTransaction();
        try {
            foreach ($this->linesToQuote() as $line => $rows) {
                $quotation = new Quotation([
                    'account_id'     => $this->account_id,
                    'opportunity_id' => $this->id,
                    'business_line'  => $line,
                    'quotation_date' => date('Y-m-d'),
                    'valid_until'    => date('Y-m-d', strtotime('+30 days')),
                    'status'         => Quotation::STATUS_DRAFT,
                    'status_id'      => 1,
                ]);
                $quotation->detachBehavior('tokenProtection');
                if (!$quotation->save()) {
                    throw new \RuntimeException('Quotation could not be created: ' . implode(' ', $quotation->getFirstErrors()));
                }

                foreach ($rows as $row) {
                    $item = new QuotationItem([
                        'quotation_id' => $quotation->id,
                        'product_id'   => $row->product_id,
                        'qty'          => $row->qty,
                        'price'        => $row->price,
                        'discount'     => $row->discount,
                        'status_id'    => 1,
                    ]);
                    $item->detachBehavior('tokenProtection');
                    if (!$item->save()) {
                        throw new \RuntimeException('Quotation item could not be created: ' . implode(' ', $item->getFirstErrors()));
                    }
                }

                $paymentMethod = $quotation->defaultPaymentMethod();
                if ($paymentMethod !== null) {
                    $quotation->updateAttributes(['payment_method' => $paymentMethod]);
                }
                $created[] = $quotation;
            }

            foreach ($this->replacedQuotations as $old) {
                $old->detachBehavior('tokenProtection');
                $old->status = Quotation::STATUS_REJECTED;
                if (!$old->save(false, ['status', 'updated_at', 'updated_by'])) {
                    throw new \RuntimeException("Quotation {$old->quotation_number} could not be set to Rejected.");
                }
            }
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        foreach ($created as $quotation) {
            $quotation->refresh();
        }
        return $created;
    }

    /**
     * Closes the opportunity once none of its quotations is still Draft/Sent:
     * Closed Won with the approved quotations' total if any was approved, else
     * Closed Lost. Same rule as the trg_quotation_to_sales_order trigger; called
     * when a quotation is approved or rejected through the model.
     */
    public function syncStageFromQuotations()
    {
        $statuses = $this->getQuotations()->select('status')->column();
        if (!$statuses || array_intersect($statuses, [Quotation::STATUS_DRAFT, Quotation::STATUS_SENT])) {
            return;
        }
        if (in_array(Quotation::STATUS_APPROVED, $statuses, true)) {
            $this->stage = 'Closed Won';
            $this->probability = 100;
            $this->amount = (float) $this->getQuotations()->where(['status' => Quotation::STATUS_APPROVED])->sum('total_amount');
        } else {
            $this->stage = 'Closed Lost';
            $this->probability = 0;
        }
        $this->save(false);
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
     * column stage ENUM value labels
     * @return string[]
     */
    public static function optsStage()
    {
        return [
            self::STAGE_PROSPECTING => 'Prospecting',
            self::STAGE_QUALIFICATION => 'Qualification',
            self::STAGE_PROPOSAL => 'Proposal',
            self::STAGE_NEGOTIATION => 'Negotiation',
            self::STAGE_CLOSED_WON => 'Closed Won',
            self::STAGE_CLOSED_LOST => 'Closed Lost',
        ];
    }

    /**
     * @return string
     */
    public function displayStage()
    {
        return self::optsStage()[$this->stage];
    }

    /**
     * @return bool
     */
    public function isStageProspecting()
    {
        return $this->stage === self::STAGE_PROSPECTING;
    }

    public function setStageToProspecting()
    {
        $this->stage = self::STAGE_PROSPECTING;
    }

    /**
     * @return bool
     */
    public function isStageQualification()
    {
        return $this->stage === self::STAGE_QUALIFICATION;
    }

    public function setStageToQualification()
    {
        $this->stage = self::STAGE_QUALIFICATION;
    }

    /**
     * @return bool
     */
    public function isStageProposal()
    {
        return $this->stage === self::STAGE_PROPOSAL;
    }

    public function setStageToProposal()
    {
        $this->stage = self::STAGE_PROPOSAL;
    }

    /**
     * @return bool
     */
    public function isStageNegotiation()
    {
        return $this->stage === self::STAGE_NEGOTIATION;
    }

    public function setStageToNegotiation()
    {
        $this->stage = self::STAGE_NEGOTIATION;
    }

    /**
     * @return bool
     */
    public function isStageClosedWon()
    {
        return $this->stage === self::STAGE_CLOSED_WON;
    }

    public function setStageToClosedWon()
    {
        $this->stage = self::STAGE_CLOSED_WON;
    }

    /**
     * @return bool
     */
    public function isStageClosedLost()
    {
        return $this->stage === self::STAGE_CLOSED_LOST;
    }

    public function setStageToClosedLost()
    {
        $this->stage = self::STAGE_CLOSED_LOST;
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
}
