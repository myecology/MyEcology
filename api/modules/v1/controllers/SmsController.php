<?php
namespace api\modules\v1\controllers;

use Yii;
use yii\web\Response;
use api\controllers\APIFormat;
use api\models\Sms;
use api\models\SmsForm;

/**
 * 用户控制器
 */
class SmsController extends BaseController
{
    /**
	 * 行为
	 * @return [type] [description]
	 */
	public function behaviors()
	{
		$behaviors = parent::behaviors();
	    $behaviors['authenticator']['optional'] = ['index'];
	    return $behaviors;
    }

    /**
     * 获取验证码
     *
     * @return void
     */
    public function actionIndex()
    {
        try{
            $model = new SmsForm();
            $model->setScenario('signup');

            $model->setAttributes(Yii::$app->request->post());
            $msg = null;
            if ($model->sendAlidayu()) {
                return APIFormat::success(true);
//            if ($sms->status == 200) {
//
//            } else {
//                $msg = $sms->response;
//            }
            } else {
                throw new  \ErrorException('短信发送失败1',1999);
            }
        }catch (\Exception $exception){
            return APIFormat::error(3001, $exception->getMessage());
        }

//        $model = new SmsForm;
//
//        $model->setScenario('signup');
//        $model->setAttributes(Yii::$app->request->post());
//
//        $msg = null;
//        if ($sms = $model->sendAlidayu()) {
//            if ($sms->status == 200) {
//                return APIFormat::success(true);
//            } else {
//                $msg = $sms->response;
//            }
//        } else {
//            $msg = $model->errors;
//        }
//
//        return APIFormat::error(3001, $msg);
    } 

    /**
     * 忘记支付密码验证码
     *
     * @return void
     */
    public function actionForget()
    {
        try{
            $model = new SmsForm();
            $model->phone = Yii::$app->user->identity->username;
            $model->setScenario('forget');
            $model->setAttributes(Yii::$app->request->post());
//            $msg = null;
            if ($model->sendAlidayu()) {
                return APIFormat::success(true);
//            if ($sms->status == 200) {
//
//            } else {
//                $msg = $sms->response;
//            }
            } else {
                throw new  \ErrorException('短信发送失败',1999);
            }
        }catch (\Exception $exception){
            return APIFormat::error(3001, $exception->getMessage());
        }
//        $model = new SmsForm;
//        $model->phone = Yii::$app->user->identity->username;
//        $model->setScenario('forget');
//        $model->setAttributes(Yii::$app->request->post());
//
//        $msg = null;
//        if ($sms = $model->sendAlidayu()) {
//            if ($sms->status == 200) {
//                return APIFormat::success(true);
//            } else {
//                $msg = $sms->response;
//            }
//        } else {
//            $msg = $model->errors;
//        }
//
//        return APIFormat::error(3001, $msg);
    }


    /**
     * 验证码验证
     *
     * @return void
     */
    public function actionVerification()
    {
        $type = Yii::$app->request->post('type');
        $code = Yii::$app->request->post('code');

        if($type && $code){
            $expireTime = time() - Sms::EXPIRE_TIME;
            $where = [
                'AND',
                ['=', 'type', $type],
                ['=', 'phone', Yii::$app->user->identity->username],
                ['=', 'code', $code],
                ['=', 'status', 200],
                ['>=', 'createtime', $expireTime],
            ];
            $sms = Sms::find()->where($where)->orderBy('id desc')->one();

            if($sms){
                return APIFormat::success(true);
            }
        }
        return APIFormat::error(3002);
    }
}
