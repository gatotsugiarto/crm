<?php

namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Member login form
 */
class MemberLoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_member;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $member = $this->getMember();
            if (
                !$member ||
                !$member->validatePassword($this->password) ||
                $member->status != 10 // ✅ hanya login jika status aktif
            ) {
                $this->addError($attribute, 'Incorrect username or password, or account is not active.');
            }
        }
    }

    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getMember(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }

        return false;
    }

    protected function getMember()
    {
        if ($this->_member === null) {
            $this->_member = Member::findByUsername($this->username);
        }

        return $this->_member;
    }
}
