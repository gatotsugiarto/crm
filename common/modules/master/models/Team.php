<?php

namespace common\modules\master\models;

use Yii;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\components\behaviors\TokenProtectedFormBehavior;
use common\components\behaviors\LoggableBehavior;

use common\modules\master\models\Status;
use common\modules\auth\models\User;

class Team extends ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'team';
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
                'sessionKey' => 'team_token',
            ],
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'Team',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'user_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['status_id'], 'default', 'value' => 1],
            [['name'], 'required'],
            [['user_id', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 255],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => StatusActive::class, 'targetAttribute' => ['status_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            // A user belongs to one team (user.team_id), and the DB trigger moves the
            // Team Leader into the team they lead, so leading two teams would silently
            // pull them out of the first one.
            [['user_id'], function ($attribute) {
                if (!$this->isNewRecord && !$this->isAttributeChanged('user_id', false)) {
                    return;
                }
                $other = static::find()
                    ->where(['user_id' => $this->user_id, 'status_id' => 1])
                    ->andFilterWhere(['<>', 'id', $this->id])
                    ->one();
                if ($other !== null) {
                    $this->addError($attribute, "This user is already the Team Leader of {$other->name}. A user can lead only one team.");
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
            'name' => 'Sales Team',
            'description' => 'Description',
            'user_id' => 'Team Leader',
            'status_id' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
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
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['team_id' => 'id']);
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
     * Active members of a team, for the Assigned Sales dropdown (empty until a
     * team is chosen). Also used by master/lookup/team-members.
     * @return array user id => fullname
     */
    public static function membersDropdown($teamId)
    {
        if ($teamId === null || $teamId === '') {
            return [];
        }
        return User::find()->select(['fullname', 'id'])
            ->where(['team_id' => $teamId, 'status' => 10])
            ->orderBy(['fullname' => SORT_ASC])
            ->indexBy('id')->column();
    }

    /**
     * Active teams for a form's dropdown; $keepId (the record's current team) is
     * included even when that team has been deactivated.
     * @return array id => name
     */
    public static function dropdownActive($keepId = null)
    {
        $list = static::find()->select(['name', 'id'])
            ->where(['status_id' => 1])->orderBy(['name' => SORT_ASC])
            ->indexBy('id')->column();
        if ($keepId && !isset($list[$keepId]) && ($kept = static::findOne($keepId)) !== null) {
            $list[$kept->id] = $kept->name . ' (Non Active)';
        }
        return $list;
    }

    /**
     * Why this team can't be deleted, or null. Leads, accounts, opportunities and
     * activities it owns and its members point to it (FK RESTRICT); deactivating
     * the team (Non Active) keeps that history instead.
     */
    public function deleteBlockers()
    {
        $db = static::getDb();
        $count = fn($sql) => (int) $db->createCommand($sql, [':id' => $this->id])->queryScalar();
        $counts = [
            'lead'        => $count('SELECT COUNT(*) FROM `lead` WHERE owner_user_id = :id'),
            'account'     => $count('SELECT COUNT(*) FROM account WHERE owner_user_id = :id'),
            'opportunity' => $count('SELECT COUNT(*) FROM opportunity WHERE owner_user_id = :id'),
            'activity'    => $count('SELECT COUNT(*) FROM activity WHERE assigned_to = :id'),
            'member'      => $count('SELECT COUNT(*) FROM `user` WHERE team_id = :id'),
        ];
        $parts = [];
        foreach ($counts as $label => $n) {
            if ($n > 0) {
                $plural = $label === 'opportunity' ? 'opportunities' : ($label === 'activity' ? 'activities' : $label . 's');
                $parts[] = $n . ' ' . ($n === 1 ? $label : $plural);
            }
        }
        if (!$parts) {
            return null;
        }
        $last = array_pop($parts);
        $list = $parts ? implode(', ', $parts) . ' and ' . $last : $last;
        return "{$this->name} still has {$list}. Set it to Non Active instead, or move those to another team first.";
    }
}
