<?php

namespace api\models;

use Yii;
use yii\base\Model;

/**
 * Signup form
 */
class DposSignupForm extends Model
{
    public $area;
    public $username;
    public $code;
    public $inviteCode;
    public $password;
    private $upid = 0;
    private $pool_id = 0;
    public $type;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
//            ['username', 'unique', 'targetClass' => '\api\models\User', 'message' => '该手机号码已被注册'],
            ['username', 'match', 'pattern' => '/^[1][3456789][0-9]{9}$/', 'message' => '手机号码格式不对'],
            ['password', 'required'],
            ['password', 'string', 'min' => 6, 'max' => 20],
            ['type', 'trim'],
            ['type', 'required'],
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
            'type'  => '类型',
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
            //如果用户已经存在，保存用户类别
            if (User::find()->where(['username' => $this->username, 'type' => \api\models\User::TYPE_DEFAULT])->one()) {
                $user->type = $this->type;
                $user->save();
            } else {
                $user->username = $this->username;
                $user->setPassword($this->password);
                $user->nickname = date('Y') . $this->generateNickname();
                $user->headimgurl = Yii::$app->params['imagesUrl'] . '/images/default/default_headimgurl.png';
                $user->crontab_status = \backend\models\User::CRONTAB_STATUS_DEFAULT;
                $user->upid = 0;
                $user->pool_id = 1;
                $user->generateAuthKey();
                $user->generateUserid();

                if(!$user->generateAccessToken() || false === $user->save()){
                    return false;
                }
            }

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
