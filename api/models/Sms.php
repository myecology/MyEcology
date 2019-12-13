<?php

namespace api\models;

use Yii;
use yii\base\ErrorException;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Json;

/**
 * This is the model class for table "iec_sms".
 *
 * @property int $id
 * @property int $type 短信类型
 * @property string $phone 手机号码
 * @property string $code 验证码
 * @property int $status 返回状态吗
 * @property string $response 返回信息
 * @property int $createtime 创建时间
 */
class Sms extends \yii\db\ActiveRecord
{

    public $verifyCode;

    const TODAY_NUM = 10; //  号码发送次数
    const EXPIRE_TIME = 300;   //过期时间

    const TYPE_SIGNUP = 1;                  //  注册验证
    const TYPE_FORGET_PASSWORD = 2;         //  忘记密码
    const TYPE_FORGET_PAYMENT = 3;          //  忘记支付

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_sms';
    }

    /**
     * 模型行为
     * @return array    数组
     */
    public function behaviors()
    {
        return [
            //添加时间戳
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['createtime'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '类型',
            'phone' => '手机号码',
            'code' => 'Code',
            'status' => 'Status',
            'response' => 'Response',
            'createtime' => 'Createtime',
        ];
    }

    /**
     * 发送验证码
     *
     * @return void
     */
    public function sendCode()
    {
        $this->generateCode();
        switch ($this->type) {
            case 1:
                $sign = '蚂蚁窝';
                $code = 'SMS_158395182';
                $param = [
                    'code' => (string) $this->code,
                ];
                break;
            case 2:
                $sign = '蚂蚁窝';
                $code = 'SMS_158395181';
                $param = [
                    'code' => (string) $this->code,
                ];
                break;
            case 3:
                $sign = '蚂蚁窝';
                $code = 'SMS_158395181';
                $param = [
                    'code' => (string) $this->code,
                ];
                break;
        }
       $this->sendjuSms();
        // 阿里大鱼发送短信
//        $this->sendSms($sign, $code, $param);

        if(false === $this->save()){
            return false;
        }else{
            return $this;
        }
    }

    /**
     * 发送验证码
     *
     * @param [type] $sign
     * @param [type] $code
     * @param array $param
     * @return void
     */
    protected function sendSms($sign, $code, array $param)
    {
        $response = Yii::$app->aliyun->sendSms(
            $sign,
            $code,
            $this->phone,
            $param
        );
        $response = Json::decode($response);

        $this->status = $response['code'];
        $this->response = $response['message'];

        if ($response['code'] != 200) {
            $this->addError('status', '短信服务器错误');
        }
    }

    public function  sendjuSms(){
        $sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
        $smsConf = array(
            'key'   => '099fdec26797abf1518e87fd4f422654', //您申请的APPKEY
            'mobile'    => $this->phone, //接受短信的用户手机号码
            'tpl_id'    => 182753, //您申请的短信模板ID，根据实际情况修改
            'tpl_value' =>'#code#='.$this->code.'&#company#=MyEcology' //您设置的模板变量，根据实际情况修改
        );
        $content = $this->juhecurl($sendUrl,$smsConf,1); //请求发送短信

        if($content){
            $result = json_decode($content,true);
            $error_code = $result['error_code'];
            if($error_code == 0){
                $this->status = 200;
                $this->response = $result['reason'];
                return true;
            }else{
                $this->status = $error_code;
                $this->response = $result['reason'];
                throw new ErrorException($result['reason'],1999);
            }
        }else{
            throw new ErrorException('短信发送失败3',1999);
        }
    }

    /**
     * 请求接口返回内容
     * @param  string $url [请求的URL地址]
     * @param  string $params [请求的参数]
     * @param  int $ipost [是否采用POST形式]
     * @return  string
     */
    function juhecurl($url,$params=false,$ispost=0){
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        if( $ispost )
        {
            curl_setopt( $ch , CURLOPT_POST , true );
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
        }
        else
        {
            if($params){
                curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
            }else{
                curl_setopt( $ch , CURLOPT_URL , $url);
            }
        }
        $response = curl_exec( $ch );
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
        $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
        curl_close( $ch );
        return $response;
    }
    /**
     * 设置短信验证码
     * @return [type] [description]
     */
    protected function generateCode()
    {
        $this->code = rand(100000, 999999);
    }

}
