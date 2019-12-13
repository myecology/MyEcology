<?php
namespace api\controllers;

use api\controllers\APIFormat;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use common\helpers\GeetestLib;

/**
 * Site controller
 */
class GeetestController extends Controller
{
    public function actionIndex()
    {
        $GtSdk = new GeetestLib(Yii::$app->params['geetest']['appMobileId'], Yii::$app->params['geetest']['appMobileKey']);
        $data = array(
            "user_id" => 'iec_qwer!!!!1234', # 网站用户id
            "client_type" => "native", #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
            "ip_address" => Yii::$app->request->userIP, # 请在此处传输用户请求验证时所携带的IP
        );
        $status = $GtSdk->pre_process($data, 1);
        
        $session = Yii::$app->session;
        $session->open();
        $session->set('gtserver', $status);
        $session->set('user_id', $data['user_id']);
        echo $GtSdk->get_response_str();
    }

}
