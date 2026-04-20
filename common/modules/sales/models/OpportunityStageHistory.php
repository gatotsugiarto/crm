<?php

namespace common\modules\sales\models;

use Yii;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\components\behaviors\TokenProtectedFormBehavior;
use common\components\behaviors\LoggableBehavior;

use common\modules\master\models\Status;
use common\modules\auth\models\User;

class OpportunityStageHistory extends ActiveRecord
{

    /**
     * ENUM field values
     */
    const OLD_STAGE_PROSPECTING = 'Prospecting';
    const OLD_STAGE_QUALIFICATION = 'Qualification';
    const OLD_STAGE_PROPOSAL = 'Proposal';
    const OLD_STAGE_NEGOTIATION = 'Negotiation';
    const OLD_STAGE_CLOSED_WON = 'Closed Won';
    const OLD_STAGE_CLOSED_LOST = 'Closed Lost';
    const NEW_STAGE_PROSPECTING = 'Prospecting';
    const NEW_STAGE_QUALIFICATION = 'Qualification';
    const NEW_STAGE_PROPOSAL = 'Proposal';
    const NEW_STAGE_NEGOTIATION = 'Negotiation';
    const NEW_STAGE_CLOSED_WON = 'Closed Won';
    const NEW_STAGE_CLOSED_LOST = 'Closed Lost';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'opportunity_stage_history';
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
                'sessionKey' => 'opportunitystagehistory_token',
            ],
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'OpportunityStageHistory',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['old_stage', 'changed_by', 'description', 'days_in_previous_stage'], 'default', 'value' => null],
            [['opportunity_id', 'new_stage'], 'required'],
            [['opportunity_id', 'changed_by', 'days_in_previous_stage'], 'integer'],
            [['old_stage', 'new_stage'], 'string'],
            [['changed_at'], 'safe'],
            [['description'], 'string', 'max' => 255],
            ['old_stage', 'in', 'range' => array_keys(self::optsOldStage())],
            ['new_stage', 'in', 'range' => array_keys(self::optsNewStage())],
            [['changed_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['changed_by' => 'id']],
            [['opportunity_id'], 'exist', 'skipOnError' => true, 'targetClass' => Opportunity::class, 'targetAttribute' => ['opportunity_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'opportunity_id' => 'Opportunity',
            'old_stage' => 'Old Stage',
            'new_stage' => 'New Stage',
            'changed_at' => 'Changed At',
            'changed_by' => 'Changed By',
            'description' => 'Description',
            'days_in_previous_stage' => 'Days In Previous Stage',
        ];
    }

    /**
     * Gets query for [[ChangedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChangedBy()
    {
        return $this->hasOne(User::class, ['id' => 'changed_by']);
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
     * column old_stage ENUM value labels
     * @return string[]
     */
    public static function optsOldStage()
    {
        return [
            self::OLD_STAGE_PROSPECTING => 'Prospecting',
            self::OLD_STAGE_QUALIFICATION => 'Qualification',
            self::OLD_STAGE_PROPOSAL => 'Proposal',
            self::OLD_STAGE_NEGOTIATION => 'Negotiation',
            self::OLD_STAGE_CLOSED_WON => 'Closed Won',
            self::OLD_STAGE_CLOSED_LOST => 'Closed Lost',
        ];
    }

    /**
     * column new_stage ENUM value labels
     * @return string[]
     */
    public static function optsNewStage()
    {
        return [
            self::NEW_STAGE_PROSPECTING => 'Prospecting',
            self::NEW_STAGE_QUALIFICATION => 'Qualification',
            self::NEW_STAGE_PROPOSAL => 'Proposal',
            self::NEW_STAGE_NEGOTIATION => 'Negotiation',
            self::NEW_STAGE_CLOSED_WON => 'Closed Won',
            self::NEW_STAGE_CLOSED_LOST => 'Closed Lost',
        ];
    }

    /**
     * @return string
     */
    public function displayOldStage()
    {
        return self::optsOldStage()[$this->old_stage];
    }

    /**
     * @return bool
     */
    public function isOldStageProspecting()
    {
        return $this->old_stage === self::OLD_STAGE_PROSPECTING;
    }

    public function setOldStageToProspecting()
    {
        $this->old_stage = self::OLD_STAGE_PROSPECTING;
    }

    /**
     * @return bool
     */
    public function isOldStageQualification()
    {
        return $this->old_stage === self::OLD_STAGE_QUALIFICATION;
    }

    public function setOldStageToQualification()
    {
        $this->old_stage = self::OLD_STAGE_QUALIFICATION;
    }

    /**
     * @return bool
     */
    public function isOldStageProposal()
    {
        return $this->old_stage === self::OLD_STAGE_PROPOSAL;
    }

    public function setOldStageToProposal()
    {
        $this->old_stage = self::OLD_STAGE_PROPOSAL;
    }

    /**
     * @return bool
     */
    public function isOldStageNegotiation()
    {
        return $this->old_stage === self::OLD_STAGE_NEGOTIATION;
    }

    public function setOldStageToNegotiation()
    {
        $this->old_stage = self::OLD_STAGE_NEGOTIATION;
    }

    /**
     * @return bool
     */
    public function isOldStageClosedWon()
    {
        return $this->old_stage === self::OLD_STAGE_CLOSED_WON;
    }

    public function setOldStageToClosedWon()
    {
        $this->old_stage = self::OLD_STAGE_CLOSED_WON;
    }

    /**
     * @return bool
     */
    public function isOldStageClosedLost()
    {
        return $this->old_stage === self::OLD_STAGE_CLOSED_LOST;
    }

    public function setOldStageToClosedLost()
    {
        $this->old_stage = self::OLD_STAGE_CLOSED_LOST;
    }

    /**
     * @return string
     */
    public function displayNewStage()
    {
        return self::optsNewStage()[$this->new_stage];
    }

    /**
     * @return bool
     */
    public function isNewStageProspecting()
    {
        return $this->new_stage === self::NEW_STAGE_PROSPECTING;
    }

    public function setNewStageToProspecting()
    {
        $this->new_stage = self::NEW_STAGE_PROSPECTING;
    }

    /**
     * @return bool
     */
    public function isNewStageQualification()
    {
        return $this->new_stage === self::NEW_STAGE_QUALIFICATION;
    }

    public function setNewStageToQualification()
    {
        $this->new_stage = self::NEW_STAGE_QUALIFICATION;
    }

    /**
     * @return bool
     */
    public function isNewStageProposal()
    {
        return $this->new_stage === self::NEW_STAGE_PROPOSAL;
    }

    public function setNewStageToProposal()
    {
        $this->new_stage = self::NEW_STAGE_PROPOSAL;
    }

    /**
     * @return bool
     */
    public function isNewStageNegotiation()
    {
        return $this->new_stage === self::NEW_STAGE_NEGOTIATION;
    }

    public function setNewStageToNegotiation()
    {
        $this->new_stage = self::NEW_STAGE_NEGOTIATION;
    }

    /**
     * @return bool
     */
    public function isNewStageClosedWon()
    {
        return $this->new_stage === self::NEW_STAGE_CLOSED_WON;
    }

    public function setNewStageToClosedWon()
    {
        $this->new_stage = self::NEW_STAGE_CLOSED_WON;
    }

    /**
     * @return bool
     */
    public function isNewStageClosedLost()
    {
        return $this->new_stage === self::NEW_STAGE_CLOSED_LOST;
    }

    public function setNewStageToClosedLost()
    {
        $this->new_stage = self::NEW_STAGE_CLOSED_LOST;
    }
}
