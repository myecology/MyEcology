<?php
namespace api\models;

use Yii;
use api\models\User;
use yii\base\Model;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{
    public $old_password;
    public $password;

    private $_user;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['password','old_password'], 'required'],
            ['old_password', function($attribute, $params){
                if(!$this->hasErrors()){
                    $user = $this->getUser();
                    if(!$user->validatePassword($this->old_password)){
                        $this->addError($attribute, '旧密码不正确');
                    }
                }
            }],
            ['password', 'string', 'min' => 6, 'max' => 20],
            ['password', function ($attribute, $params) {
                if (!$this->hasErrors()) {
                    $a = preg_match('/[0-9]/', $this->password);
                    $b = preg_match('/[a-z]/', $this->password);
                    // $c = preg_match('/[A-Z]/', $this->password);

                    if (!$a || !$b) {
                        $this->addError($attribute, '密码必须6-20位（包含小写字母和数字）');
                    }
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
            'password' => '密码',
        ];
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function resetPassword()
    {
        if(!$this->validate()){
            return false;
        }

        $user = $this->getUser();
        $user->setPassword($this->password);

        return $user->save(false);
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername(Yii::$app->user->identity->username);
        }

        return $this->_user;
    }
}
