<?php
namespace frontend\controllers;

use api\controllers\APIFormat;
use api\models\SignupForm;
use api\models\SmsForm;
use api\models\User;
use common\models\Currency;
use common\models\Invitation;
use common\models\InvitePool;
use common\models\Wallet;
use Yii;
use yii\base\ErrorException;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\IdentityInterface;
use backend\models\Setting;
use backend\models\Version;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $inviteCode = Yii::$app->request->get('code');
        $user = User::find()->where(['code' => $inviteCode])->one();
//        if($user && $user->pool_id > 0){
//            $invitePool = InvitePool::find()->where(['uid' => $user->pool_id])->orderBy('id desc')->one();
//        }else{
        $invitePool = InvitePool::find()->where(['status' => 10, 'type' => 1])->orderBy('id desc')->one();
//        }

        //  注册
        $model = new SignupForm();
        $sms = new SmsForm();

        return $this->render('_index', [
            'inviteCode' => $inviteCode,
            'invitePool' => $invitePool,
            'model' => $model,
            'sms' => $sms
        ]);
    }

    /**
     * 自动登陆
     *
     * @return void
     */
    public function actionLogin()
    {
        $userid = substr(Yii::$app->user->returnUrl, strpos(Yii::$app->user->returnUrl, '=') + 1);
        $model = User::find()->where(['userid' => $userid])->one();

        if($model){
            Yii::$app->user->login($model, 0);
            return $this->redirect(substr(Yii::$app->user->returnUrl, 0, strpos(Yii::$app->user->returnUrl, '?')));
        }
    }


    /**
     * 发送验证码
     *
     * @return void
     */
    public function actionSms()
    {
        try{
            $model = new SmsForm();
            $model->setScenario('signup');

            $model->load(Yii::$app->request->post());
            $msg = null;
            if ($model->sendAlidayu()) {
                return Json::encode(APIFormat::success(true));
//            if ($sms->status == 200) {
//
//            } else {
//                $msg = $sms->response;
//            }
            } else {
              throw new  \ErrorException('短信发送失败',1999);
            }
        }catch (\Exception $exception){
            return Json::encode(APIFormat::error(3001, $exception->getMessage()));
        }



    }

    
    public function actionSignup()
    {
        $model = new SignupForm();
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $model->load(Yii::$app->request->post());
            if ($user = $model->signup()) {

                //邀请记录
                //if(!Invitation::createInvitation($user, $user->upid)){
                   // return Json::encode(APIFormat::error(4016));
                //}
                if ($user instanceof IdentityInterface) {
                    $curr = Currency::find()->where(['status'=>Currency::STATUS_ENABLED])->asArray()->all();
                    if($curr){
                        foreach ($curr as $v){
                            Wallet::generateWallet($v['symbol'],$user->id);
                        }
                    }
                    $transaction->commit();
                    return Json::encode(APIFormat::success([
                        'username' => $user->username,
                        'nickname' => $user->nickname,
                        'friend' => $user->friend,
                        'iecid' => $user->iecid,
                        'token' => $user->access_token,
                        'userid' => $user->userid,
                        'headimgurl' => $user->headimgurl,
                        'payment_hash' => is_null($user->payment_hash) ? false : true,
                        'description' => $user->description,
                        'code' => $user->code,
                    ]));
                }
            }
            $msg = $model->errors;
        }catch(\ErrorException $e){
            $transaction->rollBack();
            $msg = $e->getMessage();
        }
        return Json::encode(APIFormat::error(4001, $msg));
    }

    public function actionDownload()
    {
        $ios = Version::find()->where(['type' => 2])->orderBy('num desc')->one();
        $android = Version::find()->where(['type' => 1])->orderBy('num desc')->one();
        $type = Version::typeArray();

        return $this->renderPartial('download', [
            'ios' => $ios,
            'android' => $android,
            'type' => $type
        ]);
    }

    /**
     * 协议
     *
     * @param [type] $key
     * @return void
     */
    public function actionProtocol($key)
    {
        $data = Setting::find()->where(['key' => $key])->one();
        return $this->renderPartial('protocol', [
            'data' => $data,
        ]);
    }

    public function actionError(){
        return '';
    }
}
