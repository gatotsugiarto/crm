<?php

namespace common\modules\auth\models;

use Yii;
use yii\base\Model;
use common\modules\satupayroll\models\LogActivity;

use common\modules\auth\models\User;

class ChangePasswordUser extends Model
{
    public $old_password;
    public $new_password;
    public $confirm_password;

    private $_user;

    public function __construct($user, $config = [])
    {
        $this->_user = $user;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['old_password', 'new_password', 'confirm_password'], 'required'],
            [['old_password', 'new_password', 'confirm_password'], 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
            ['old_password', 'validateOldPassword', 'skipOnError' => false],
            ['confirm_password', 'compare', 'compareAttribute' => 'new_password', 'message' => 'New password does not match.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'old_password' => 'Old Password',
            'new_password' => 'New Password',
            'confirm_password' => 'Confirm New Password',
        ];
    }

    public function validateOldPassword($attribute, $params)
    {
        if (!Yii::$app->security->validatePassword($this->$attribute, $this->_user->password_hash)) {
            $this->addError($attribute, 'Old password is incorrect.');
        }
    }

    public function changePassword()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->_user;

        // Ambil nilai sebelum perubahan
        $before = $user->getAttributes(['password_hash']);

        // Lakukan perubahan
        $user->setPassword($this->new_password);
        $after = $user->getAttributes(['password_hash']);

        $result = $user->save(false);

        if ($result) {
            $log = new LogActivity();
            $log->controller_action = 'change-password';
            $log->model_name = 'User';
            $log->record_id = $user->id;
            $log->ip_address = Yii::$app->request->userIP ?? 'console';
            $log->user_agent = Yii::$app->request->userAgent ?? 'console';
            $log->request_url = Yii::$app->request->url ?? 'console';
            $log->before_data = json_encode($before, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            $log->after_data = json_encode($after, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            $log->status = 'success';
            $log->remarks = 'Password changed';
            $log->save(false);
        }

        return $result;
    }

    public function getUser()
    {
        return $this->_user;
    }
}
