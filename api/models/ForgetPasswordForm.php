<?php
namespace api\models;

use Yii;
use api\models\User;
use yii\base\Model;
use api\models\Sms;

/**
 * Password reset form
 */
class ForgetPasswordForm extends Model
{
    public $phone;
    public $password;
    public $code;

    private $_user;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['password', 'phone', 'code'], 'required'],
            ['password', 'string', 'min' => 6, 'max' => 20],
            ['password', function ($attribute, $params) {
                if (!$this->hasErrors()) {
                    $a = preg_match('/[0-9]/', $this->password);
                    $b = preg_match('/[a-z]/', $this->password);
//                    $c = preg_match('/[A-Z]/', $this->password);

                    if (!$a || !$b ) { //|| !$c
                        $this->addError($attribute, '密码必须6-20位字母和数字');
                    }
                }
            }],
            ['phone', 'match', 'pattern' => '/^[1][3456789][0-9]{9}$/', 'message' => '手机号码格式不对'],
            ['phone', function($attribute, $params){
                if(!$this->hasErrors() && !$this->getUser()){
                    $this->addError($attribute, '不存在此用户！');
                }
            }],
            ['code', function ($attribute, $params) {
                if (!$this->hasErrors()) {
                    $expireTime = time() - Sms::EXPIRE_TIME;
                    $where = [
                        'AND',
                        ['=', 'type', Sms::TYPE_FORGET_PASSWORD],
                        ['=', 'phone', $this->phone],
                        ['=', 'code', $this->code],
                        ['=', 'status', 200],
                        ['>=', 'createtime', $expireTime],
                    ];
                    $sms = Sms::find()->where($where)->orderBy('id desc')->one();
                    if (is_null($sms) || $this->code != $sms->code) {
                        $this->addError($attribute, '验证码过期或错误');
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
            'code' => '验证码',
            'phone' => '手机号码',
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
            $this->_user = User::findByUsername($this->phone);
        }

        return $this->_user;
    }
}
