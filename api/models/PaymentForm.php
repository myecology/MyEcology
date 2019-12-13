<?php
namespace api\models;

use api\models\Sms;
use api\models\User;
use Yii;
use yii\base\Model;

/**
 * Password reset form
 */
class PaymentForm extends Model
{
    public $phone;
    public $old_password;
    public $password;
    public $code;

    private $_user;

    /**
     * 场景
     */
    public function scenarios()
    {
        return [
            'set' => ['password'],
            'reset' => ['old_password', 'password'],
            'forget' => ['password', 'phone', 'code'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['old_password', 'required', 'on' => ['reset']],
            [['code', 'phone'], 'required', 'on' => ['forget']],
            ['phone', function($attribute, $params){
                if(!$this->hasErrors()){
                    if(Yii::$app->user->identity->username != $this->phone){
                        $this->addError($attribute, '手机号码不正确');
                    }
                }
            }, 'on' => ['forget']],
            ['old_password', function ($attribute, $params) {
                if (!$this->hasErrors()) {
                    $user = $this->getUser();
                    if (!$user->validatePayment($this->old_password)) {
                        $this->addError($attribute, '旧密码不正确');
                    }
                }
            }, 'on' => ['reset']],
            ['password', function ($attribute, $params) {
                if (!$this->hasErrors()) {
                    $user = $this->getUser();
                    if (!is_null($user->payment_hash)) {
                       $this->addError($attribute, '已经设置了支付密码');
                    }
                }
            }, 'on' => ['set']],
            ['phone', 'match', 'pattern' => '/^[1][345678][0-9]{9}$/', 'message' => '手机号码格式不对', 'on' => ['forget']],
            ['code', function ($attribute, $params) {
                if (!$this->hasErrors()) {
                    $expireTime = time() - Sms::EXPIRE_TIME;
                    $where = [
                        'AND',
                        ['=', 'type', Sms::TYPE_FORGET_PAYMENT],
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
            }, 'on' => ['forget']],

            ['password', 'string', 'min' => 6, 'max' => 6],
            ['password', function ($attribute, $params) {
                if (!$this->hasErrors()) {
                    $a = preg_match('/^\d*$/', $this->password);
                    // $b = preg_match('/[a-z]/', $this->password);
                    // $c = preg_match('/[A-Z]/', $this->password);

                    if (!$a) {
                        $this->addError($attribute, '支付密码必须6位纯数字');
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
            'phone' => '手机号码',
            'password' => '支付密码',
            'old_password' => '旧密码',
            'code' => '验证码',
        ];
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function resetPassword()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->getUser();
        $user->setPayment($this->password);

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

            $this->_user = User::findByUsername($this->phone ?: Yii::$app->user->identity->username);
        }

        return $this->_user;
    }
}
