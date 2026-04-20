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
    public function getQuotations()
    {
        return $this->hasMany(Quotation::class, ['opportunity_id' => 'id']);
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
