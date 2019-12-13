<?php

namespace backend\models;

use api\controllers\APIFormat;
use api\models\Sms;
use Yii;
use yii\base\ErrorException;

/**
 * This is the model class for table "iec_admin_sms".
 *
 * @property int $id
 * @property string $username
 * @property string $code
 * @property int $updated_at
 * @property int $count
 */
class AdminSms extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_admin_sms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'code'], 'required'],
            [['updated_at', 'count'], 'integer'],
            [['username'], 'string', 'max' => 20],
            [['code'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'code' => 'Code',
            'updated_at' => 'Updated At',
            'count' => 'Count',
        ];
    }

    public static function add($username,$code){
        $model = new static();
        $model->username = $username;
        $model->code = (string)$code;
        $model->count = 0;
        $model->updated_at = time();
        if($model->save()) {
            return true;
        }
        throw new ErrorException(APIFormat::popError($model->errors),1000);
    }

    public static function sendSms($username,$password){
        if(empty($username)){
            throw new \ErrorException('请输入用户名',999);
        }
        if(empty($password)){
            throw new \ErrorException('请输入密码',999);
        }
        $userModel =  \common\models\User::findOne(['username'=>$username]);
        if(empty($userModel) || !$userModel->validatePassword($password)){
            throw new \ErrorException('用户名或者密码不正确',999);
        }
        $code = static::generateCode();
        $result = static::sendjuSms($username,$code);
        if($result){
            $result = static::add($username,$code);
            return $result;
        }
    }

    public static function  sendjuSms($phone,$code){
        $sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
        $smsConf = array(
            'key'   => '099fdec26797abf1518e87fd4f422654', //您申请的APPKEY
            'mobile'    => $phone, //接受短信的用户手机号码
            'tpl_id'    => 182753, //您申请的短信模板ID，根据实际情况修改
            'tpl_value' =>'#code#='.$code.'&#company#=MyEcology' //您设置的模板变量，根据实际情况修改
        );
        $model = new Sms();
        $content = $model->juhecurl($sendUrl,$smsConf,1); //请求发送短信

        if($content){
            $result = json_decode($content,true);
            $error_code = $result['error_code'];
            if($error_code == 0){
                return true;
            }else{
                throw new ErrorException($result['reason'],1999);
            }
        }else{
            throw new ErrorException('短信发送失败3',1999);
        }
    }

    /**
     * 设置短信验证码
     * @return [type] [description]
     */
    protected static function generateCode()
    {
        return rand(100000, 999999);
    }

    public static function check($username,$code){
        $sms = static::find()->where([
            'username' => $username,
        ])->orderBy('id desc')->one();
        if(empty($sms)){
            throw new ErrorException('请先发送验证码',1999);
        }
        if($sms->count > 3){
            throw new ErrorException('已错误三次请重新发送验证码',1999);
        }
        if($sms->code != $code){
            $sms->count = $sms->count+1;
            $sms->save();
            throw new ErrorException('验证码错误',1999);
        }
        return true;
    }
}
