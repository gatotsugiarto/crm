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
use common\modules\master\models\StatusActive;
use common\modules\auth\models\User;

class Activity extends ActiveRecord
{
    const RELATED_ACCOUNT = 'account';
    const RELATED_LEAD = 'lead';

    /**
     * @var string|null form-only: what the activity is about, 'account' (account +
     * optional contact / opportunity) or 'lead' (a lead, stored as
     * reference_type = 'lead', reference_id = lead id)
     */
    public $relatedTo;

    /**
     * ENUM field values
     */
    const ACTIVITY_TYPE_CALL = 'Call';
    const ACTIVITY_TYPE_MEETING = 'Meeting';
    const ACTIVITY_TYPE_EMAIL = 'Email';
    const ACTIVITY_TYPE_TASK = 'Task';
    const ACTIVITY_TYPE_NOTE = 'Note';
    const PRIORITY_LOW = 'Low';
    const PRIORITY_NORMAL = 'Normal';
    const PRIORITY_HIGH = 'High';
    const PRIORITY_URGENT = 'Urgent';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'activity';
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
                'sessionKey' => 'activity_token',
            ],
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'Activity',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['account_id', 'contact_id', 'opportunity_id', 'reference_type', 'reference_id', 'assigned_to', 'subject', 'activity_date', 'due_date', 'reminder_at', 'completed_at', 'description', 'outcome', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['activity_type'], 'default', 'value' => 'Task'],
            [['priority'], 'default', 'value' => 'Normal'],
            [['is_completed'], 'default', 'value' => 0],
            [['status_id'], 'default', 'value' => 1],
            [['account_id', 'contact_id', 'opportunity_id', 'reference_id', 'assigned_to', 'is_completed', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['activity_type', 'priority', 'description'], 'string'],
            [['activity_date', 'due_date', 'reminder_at', 'completed_at', 'created_at', 'updated_at'], 'safe'],
            [['reference_type'], 'string', 'max' => 50],
            [['subject', 'outcome'], 'string', 'max' => 255],
            ['activity_type', 'in', 'range' => array_keys(self::optsActivityType())],
            ['priority', 'in', 'range' => array_keys(self::optsPriority())],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['account_id' => 'id']],
            [['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::class, 'targetAttribute' => ['contact_id' => 'id']],
            [['opportunity_id'], 'exist', 'skipOnError' => true, 'targetClass' => Opportunity::class, 'targetAttribute' => ['opportunity_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => StatusActive::class, 'targetAttribute' => ['status_id' => 'id']],

            // form rules
            [['activity_date', 'due_date', 'reminder_at', 'completed_at'], 'filter', 'filter' => [self::class, 'normalizeDateTime']],
            [['subject', 'activity_type', 'priority', 'activity_date', 'assigned_to'], 'required'],
            [['relatedTo'], 'in', 'range' => [self::RELATED_ACCOUNT, self::RELATED_LEAD]],
            [['assigned_to'], 'exist', 'skipOnError' => true, 'targetClass' => \common\modules\master\models\Team::class, 'targetAttribute' => 'id'],
            [['account_id'], 'required', 'when' => fn($m) => $m->relatedTo === self::RELATED_ACCOUNT,
                'whenClient' => "function () { return $('input[name=\"Activity[relatedTo]\"]:checked').val() === 'account'; }",
                'message' => 'Choose the account this activity is about.'],
            [['reference_id'], 'required', 'when' => fn($m) => $m->relatedTo === self::RELATED_LEAD,
                'whenClient' => "function () { return $('input[name=\"Activity[relatedTo]\"]:checked').val() === 'lead'; }",
                'message' => 'Choose the lead this activity is about.'],
            [['contact_id'], function ($attribute) {
                if ($this->account_id && !Contact::find()->where(['id' => $this->contact_id, 'account_id' => $this->account_id])->exists()) {
                    $this->addError($attribute, 'This contact belongs to another account.');
                }
            }],
            [['opportunity_id'], function ($attribute) {
                if ($this->account_id && !Opportunity::find()->where(['id' => $this->opportunity_id, 'account_id' => $this->account_id])->exists()) {
                    $this->addError($attribute, 'This opportunity belongs to another account.');
                }
            }],
            [['due_date'], 'compare', 'compareAttribute' => 'activity_date', 'operator' => '>=', 'type' => 'datetime', 'skipOnEmpty' => true,
                'when' => fn($m) => (bool) $m->activity_date, 'message' => 'Due date can\'t be before the activity date.'],
        ];
    }

    /**
     * "2026-10-09T14:30" (datetime-local input) -> "2026-10-09 14:30:00"; empty -> null.
     */
    public static function normalizeDateTime($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        $time = strtotime(str_replace('T', ' ', $value));
        return $time ? date('Y-m-d H:i:s', $time) : $value;
    }

    /** "2026-10-09 14:30:00" -> "2026-10-09T14:30" for a datetime-local input. */
    public static function toInputDateTime($value)
    {
        $time = $value ? strtotime($value) : false;
        return $time ? date('Y-m-d\TH:i', $time) : '';
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->relatedTo = $this->reference_type === self::RELATED_LEAD ? self::RELATED_LEAD : self::RELATED_ACCOUNT;
    }

    public function beforeSave($insert)
    {
        // keep one subject: a lead, or an account (with its contact / opportunity)
        if ($this->relatedTo === self::RELATED_LEAD) {
            $this->reference_type = self::RELATED_LEAD;
            $this->account_id = $this->contact_id = $this->opportunity_id = null;
        } elseif ($this->relatedTo === self::RELATED_ACCOUNT) {
            $this->reference_type = null;
            $this->reference_id = null;
        }
        // completion time follows the checkbox
        if ($this->is_completed) {
            $this->completed_at = $this->completed_at ?: date('Y-m-d H:i:s');
        } else {
            $this->completed_at = null;
        }
        return parent::beforeSave($insert);
    }

    public function getLead()
    {
        return $this->hasOne(Lead::class, ['id' => 'reference_id']);
    }

    public function getTeam()
    {
        return $this->hasOne(\common\modules\master\models\Team::class, ['id' => 'assigned_to']);
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
            'opportunity_id' => 'Opportunity',
            'reference_type' => 'Reference Type',
            'reference_id' => 'Lead',
            'relatedTo' => 'Related To',
            'assigned_to' => 'Assigned To (Team)',
            'activity_type' => 'Activity Type',
            'priority' => 'Priority',
            'subject' => 'Subject',
            'activity_date' => 'Activity Date',
            'due_date' => 'Due Date',
            'reminder_at' => 'Reminder At',
            'is_completed' => 'Completed',
            'completed_at' => 'Completed At',
            'description' => 'Description',
            'outcome' => 'Outcome',
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
     * Gets query for [[Contact]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContact()
    {
        return $this->hasOne(Contact::class, ['id' => 'contact_id']);
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
     * column activity_type ENUM value labels
     * @return string[]
     */
    public static function optsActivityType()
    {
        return [
            self::ACTIVITY_TYPE_CALL => 'Call',
            self::ACTIVITY_TYPE_MEETING => 'Meeting',
            self::ACTIVITY_TYPE_EMAIL => 'Email',
            self::ACTIVITY_TYPE_TASK => 'Task',
            self::ACTIVITY_TYPE_NOTE => 'Note',
        ];
    }

    /**
     * column priority ENUM value labels
     * @return string[]
     */
    public static function optsPriority()
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_NORMAL => 'Normal',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
        ];
    }

    /**
     * @return string
     */
    public function displayActivityType()
    {
        return self::optsActivityType()[$this->activity_type];
    }

    /**
     * @return bool
     */
    public function isActivityTypeCall()
    {
        return $this->activity_type === self::ACTIVITY_TYPE_CALL;
    }

    public function setActivityTypeToCall()
    {
        $this->activity_type = self::ACTIVITY_TYPE_CALL;
    }

    /**
     * @return bool
     */
    public function isActivityTypeMeeting()
    {
        return $this->activity_type === self::ACTIVITY_TYPE_MEETING;
    }

    public function setActivityTypeToMeeting()
    {
        $this->activity_type = self::ACTIVITY_TYPE_MEETING;
    }

    /**
     * @return bool
     */
    public function isActivityTypeEmail()
    {
        return $this->activity_type === self::ACTIVITY_TYPE_EMAIL;
    }

    public function setActivityTypeToEmail()
    {
        $this->activity_type = self::ACTIVITY_TYPE_EMAIL;
    }

    /**
     * @return bool
     */
    public function isActivityTypeTask()
    {
        return $this->activity_type === self::ACTIVITY_TYPE_TASK;
    }

    public function setActivityTypeToTask()
    {
        $this->activity_type = self::ACTIVITY_TYPE_TASK;
    }

    /**
     * @return bool
     */
    public function isActivityTypeNote()
    {
        return $this->activity_type === self::ACTIVITY_TYPE_NOTE;
    }

    public function setActivityTypeToNote()
    {
        $this->activity_type = self::ACTIVITY_TYPE_NOTE;
    }

    /**
     * @return string
     */
    public function displayPriority()
    {
        return self::optsPriority()[$this->priority];
    }

    /**
     * @return bool
     */
    public function isPriorityLow()
    {
        return $this->priority === self::PRIORITY_LOW;
    }

    public function setPriorityToLow()
    {
        $this->priority = self::PRIORITY_LOW;
    }

    /**
     * @return bool
     */
    public function isPriorityNormal()
    {
        return $this->priority === self::PRIORITY_NORMAL;
    }

    public function setPriorityToNormal()
    {
        $this->priority = self::PRIORITY_NORMAL;
    }

    /**
     * @return bool
     */
    public function isPriorityHigh()
    {
        return $this->priority === self::PRIORITY_HIGH;
    }

    public function setPriorityToHigh()
    {
        $this->priority = self::PRIORITY_HIGH;
    }

    /**
     * @return bool
     */
    public function isPriorityUrgent()
    {
        return $this->priority === self::PRIORITY_URGENT;
    }

    public function setPriorityToUrgent()
    {
        $this->priority = self::PRIORITY_URGENT;
    }
}
