<?php

namespace api\models;

use api\controllers\APIFormat;
use Yii;
use api\models\Sms;
use yii\base\Model;
use common\helpers\GeetestLib;

/**
 * Signup form
 */
class SmsForm extends Model
{
    public $phone;
    public $type;
    public $verifyCode;

    /**
     * 场景
     *
     * @return void
     */
    public function scenarios()
    {
        return [
            'signup' => ['phone', 'type', 'verifyCode'],
            'forget' => ['type','verifyCode'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone', 'type'], 'required', 'on' => ['signup']],
            ['phone', 'match', 'pattern' => '/^[1][3456789][0-9]{9}$/', 'on' => ['signup']],
            ['phone', function($attribute, $params){
                if($this->type == Sms::TYPE_SIGNUP){
                    $user = \api\models\User::find()->where(['username' => $this->phone])->one();
                    if($user){
                        $this->addError('phone', '号码已注册');
                    }
                }
            }, 'on' => 'signup'],
            ['type', 'in', 'range' => [Sms::TYPE_SIGNUP, Sms::TYPE_FORGET_PASSWORD], 'on' => ['signup']],
            ['type', 'in', 'range' => [Sms::TYPE_FORGET_PAYMENT], 'on' => ['forget']],

            ['phone', function ($attribute, $params) {
                if (!$this->hasErrors()) {
                    //  验证次数
                    $statime = strtotime(date('Y-m-d'));
                    $endtime = time();

                    $count = Sms::find()->where([
                        'AND',
                        ['=', 'phone', $this->phone],
                        ['between', 'createtime', $statime, $endtime],
                    ])->count();

                    if ($count > Sms::TODAY_NUM) {
                        $this->addError('phone', '该号码今日获取验证码次数过多');
                        return false;
                    }
                }
            }, 'on' => ['signup']],
            ['verifyCode', 'required'],
            ['verifyCode',function($attribute, $params){
//                return true;
                $post = Yii::$app->request->post();
                $GtSdk = new GeetestLib(Yii::$app->params['geetest']['appMobileId'], Yii::$app->params['geetest']['appMobileKey']);
                $data = array(
                        "user_id" => 'iec_qwer!!!!1234', # 网站用户id
                        "client_type" => "h5", #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
                        "ip_address" => Yii::$app->request->userIP # 请在此处传输用户请求验证时所携带的IP
                    );
                // $session = Yii::$app->session;
                // $session->open();
                // if ($session->get('gtserver') == 1) {   //服务器正常
                //     $result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
                //     if (!$result) {
                //         $this->addError($attribute, '正常图形验证码错误');
                //     }
                // }else{  //服务器宕机,走failback模式
                //     if (!$GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
                //         $this->addError($attribute, \yii\helpers\Json::encode([
                //             'md5' => md5($_POST['geetest_challenge']),
                //             'validate' => $_POST['geetest_validate']
                //         ]));
                //     }
                // }

                    $result = $GtSdk->success_validate($post['geetest_challenge'], $post['geetest_validate'], $post['geetest_seccode'], $data);
                    if (!$result) {
                        $this->addError($attribute, '正常图形验证码错误');
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
            'type' => '类型',
            'phone' => '手机号码',
            'verifyCode' => '图形验证码'
        ];
    }





    //  发送阿利大于
    public function sendAlidayu()
    {
        if (!$this->validate()) {
            throw  new \ErrorException(APIFormat::popError($this->getErrors()),'1999');
//            return false;
        }

        $model = new Sms();
        $model->phone = $this->phone;
        $model->type = $this->type;
        if($model->sendCode()){
            return $model;
        }else{
            return false;
        }
    }
}