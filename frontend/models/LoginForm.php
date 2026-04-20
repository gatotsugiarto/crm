namespace frontend\models;

use yii\base\Model;

class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_member = false;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute)
    {
        if (!$this->hasErrors()) {
            $member = $this->getMember();
            if (!$member || !$member->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    public function login()
    {
        if ($this->validate()) {
            return \Yii::$app->user->login($this->getMember(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    public function getMember()
    {
        if ($this->_member === false) {
            $this->_member = Member::findByUsername($this->username);
        }
        return $this->_member;
    }
}
