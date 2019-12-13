<?php

namespace api\models;

use Yii;
use api\models\Sms;
use api\models\User;
use yii\base\Model;
use yii\helpers\Url;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $area;
    public $username;
    public $code;
    public $inviteCode;
    public $password;
    private $upid = 0;
    private $pool_id = 0;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\api\models\User', 'message' => '该手机号码已被注册'],
            ['username', 'match', 'pattern' => '/^[1][3456789][0-9]{9}$/', 'message' => '手机号码格式不对'],

            ['area', 'default', 'value' => 86],
            ['area', 'integer'],

            ['code', 'required'],
            ['code', function ($attribute, $params) {
                if (!$this->hasErrors()) {
                    if (YII_DEBUG) {
                        return true;
                    }
                    $expireTime = time() - Sms::EXPIRE_TIME;
                    $where = [
                        'AND',
                        ['=', 'type', Sms::TYPE_SIGNUP],
                        ['=', 'phone', $this->username],
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

            ['inviteCode', 'required'],
            ['inviteCode', 'filter', 'filter' => function ($value) {
                if ($this->inviteCode) {
                    $topUser = User::find()->select('id,username,pool_id')->where(['code' => $this->inviteCode])->one();
                    if ($topUser) {
                        $this->upid = $topUser->id;
                        $this->pool_id = $topUser->pool_id;
                    } else {
                        $this->addError('inviteCode', '未知的邀请码');
                    }
                }
                return $this->inviteCode;
            }],

            ['password', 'required'],
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
            ['password', 'string', 'min' => 6, 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'username' => '手机号码',
            'code' => '验证码',
            'inviteCode' => '邀请码',
            'password' => '密码',
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->setPassword($this->password);
            $user->nickname = date('Y') . $this->generateNickname();
            $user->headimgurl = Yii::$app->params['imagesUrl'] . '/images/default/default_headimgurl.png';
            $user->crontab_status = \backend\models\User::CRONTAB_STATUS_SIGNUP;
            $user->upid = $this->upid;
            $user->pool_id = $this->pool_id;
            $user->generateAuthKey();
            $user->generateUserid();

            if(!$user->generateAccessToken() || false === $user->save()){
                return false;
            }

            //  redis 队列
            Yii::$app->redis->lpush('console//controller//signup', $user->id);

            return $user;
        }
        return false;
    }

    /**
     * 生成昵称
     *
     * @return void
     */
    public function generateNickname()
    {
        $length = 6;
        $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        $str = '';
        $arr_len = count($arr);
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $arr_len - 1);
            $str .= $arr[$rand];
        }
        return $str;
    }
}
