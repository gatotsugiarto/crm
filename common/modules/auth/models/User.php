<?php

namespace common\modules\auth\models;

use Yii;
use common\components\behaviors\TokenProtectedFormBehavior;
use common\components\behaviors\LoggableBehavior;
use common\modules\master\models\ApplicationSetting;

class User extends \yii\db\ActiveRecord
{

    public $form_token;
    public $password;
    public $role;
    
    public static function tableName()
    {
        return 'user';
    }

    public function behaviors()
    {
        if ($this instanceof UserSearch) {
            return [];
        }

        return [
            'tokenProtection' => [
                'class' => TokenProtectedFormBehavior::class,
                'tokenAttribute' => 'form_token',
                'sessionKey' => 'user_create_token',
            ],
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'User', // opsional, default pakai nama tabel
            ],
        ];
    }

    public function rules()
    {
        return [
            [['password_reset_token', 'verification_token'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 10],
            [['username', 'fullname', 'email', 'role'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'verification_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique', 'on' => 'create'],
            [['email'], 'trim'],
            [['email'], 'required'],
            [['email'], 'email'],
            [['email'], 'unique',
                'targetClass' => self::class,
                'targetAttribute' => 'email',
                'filter' => function ($query) {
                    $query->andWhere(['not', ['id' => $this->id]]);
                },
                'message' => 'This email address has already been taken.'
            ],
            [['password_reset_token'], 'unique'],
            [['password'], 'required', 'on' => 'create'],

            [
            ['password'],
                'match',
                'pattern' => '/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
                'message' => 'Password must be at least 8 characters long and contain at least one uppercase letter, one number, and one special character.'
            ],

            [['team_id'], 'default', 'value' => null],
            [['team_id'], 'integer'],
            [['team_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\modules\master\models\Team::class, 'targetAttribute' => ['team_id' => 'id']],
            [['team_id'], 'validateManagerTeam', 'skipOnEmpty' => false],

            [['job_title'], 'default', 'value' => null],
            [['job_title'], 'string', 'max' => 100],
            [['form_token'], 'safe'],
        ];
    }

    /**
     * A team's manager (team.user_id) must stay a member of that team, otherwise the
     * team would list a manager who isn't in it. Change the team's manager first.
     */
    public function validateManagerTeam($attribute)
    {
        // Only when the team is being changed, so older data where a Team Leader
        // isn't a member of their team doesn't block editing other fields.
        if ($this->isNewRecord || !$this->isAttributeChanged('team_id', false)) {
            return;
        }
        $managed = \common\modules\master\models\Team::find()
            ->where(['user_id' => $this->id, 'status_id' => 1])
            ->one();
        if ($managed !== null && (int) $this->team_id !== (int) $managed->id) {
            $this->addError($attribute, "This user is the Team Leader of {$managed->name}, so they must stay in that team. Assign another Team Leader to {$managed->name} first.");
        }
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'fullname' => 'Fullname',
            'job_title' => 'Job Title',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'role' => 'Basic Role',
            'team_id' => 'Sales Team',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'verification_token' => 'Verification Token',
        ];
    }

    public function resetPassword()
    {
        
        $passwordDefaultApp = Yii::$app->params['user.passwordDefault'];
        $model = ApplicationSetting::findOne(1);
        $passwordDefault = $model ? $model->default_password : $passwordDefaultApp;
        $this->password_hash = Yii::$app->security->generatePasswordHash($passwordDefault);
        
        return true;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = Yii::$app->security->generateRandomString();
                $this->created_at = time();
            }
            $this->updated_at = time();

            if (!empty($this->password)) {
                $this->password_hash = Yii::$app->security->generatePasswordHash($this->password);
            } elseif ($this->isNewRecord) {
                $passwordDefault = Yii::$app->params['user.passwordDefault'];
                $this->password_hash = Yii::$app->security->generatePasswordHash($passwordDefault);
            }

            return true;
        }
        return false;
    }

    public function getTeam()
    {
        return $this->hasOne(\common\modules\master\models\Team::class, ['id' => 'team_id']);
    }

    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['user_id' => 'id']);
    }

    public function getAuthItems()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'item_name'])
            ->via('authAssignments');
    }

    public function getRole()
    {
        return \common\modules\auth\models\AuthAssignment::find()
            ->alias('aa')
            ->select('aa.item_name')
            ->innerJoin('auth_item ai', 'aa.item_name = ai.name AND ai.type = 1')
            ->where(['aa.user_id' => $this->id])
            ->scalar();
    }

    public function saveRole($roleName)
    {
        $auth = \Yii::$app->authManager;

        // 1️⃣ Hapus semua role yang dimiliki user
        $auth->revokeAll($this->id);

        // 2️⃣ Jika ada role baru, assign
        if (!empty($roleName)) {
            $role = $auth->getRole($roleName);

            if ($role) {
                $auth->assign($role, $this->id);
            } else {
                throw new \Exception("Role '{$roleName}' tidak ditemukan.");
            }
        }

        return true;
    }

    public static function dropdown()
    {
        static $dropdown;
        if ($dropdown === null) {
            
            $models = static::find()->where(['>', 'id', 1])->all();
            foreach ($models as $model) {
                $dropdown[$model->id] = $model->fullname;
            }
        }
            
        return $dropdown;
    }


}
