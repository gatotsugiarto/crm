<?php

namespace common\modules\auth\models;

use Yii;
use common\components\behaviors\TokenProtectedFormBehavior;
use common\modules\master\models\Company;
use common\modules\satupayroll\models\Client;

class Member extends \yii\db\ActiveRecord
{

    public $password;
    
    public static function tableName()
    {
        return 'member';
    }

    public function rules()
    {
        return [
            
            [['password_reset_token', 'verification_token'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 10],
            [['username', 'fullname', 'email', 'client_id', 'company_id'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'verification_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            ['email', 'email'],
            [['password_reset_token'], 'unique'],
            [['password'], 'required', 'on' => 'create'],

            [['form_token'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'fullname' => 'Fullname',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'verification_token' => 'Verification Token',
            'client_id' => 'Client',
            'company_id' => 'Company',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function resetPassword()
    {
        
        $passwordDefault = Yii::$app->params['member.passwordDefault'];
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

    public function behaviors()
    {
        return [
            'tokenProtection' => [
                'class' => TokenProtectedFormBehavior::class,
                'tokenAttribute' => 'form_token',
                'sessionKey' => 'member_create_token',
            ],
        ];
    }

    public function getCompany()
    {
        return $this->hasOne(Company::class, ['id' => 'company_id']);
    }

    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }


}
