<?php
namespace api\controllers;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use common\models\Wallet;
use common\models\walletLog;
use common\models\User;
use api\models\Alcohol;

class AlcoholController extends Controller{
    /**
     * 匹配获取用户信息
     * @return [type] [description]
     */
    public function actionIndex(){
        $result = "";
        try{
            $user = new Alcohol();
            $user->setScenario('userByToken');
            $user->setAttributes(\yii::$app->request->post());
            $result = $user->userByToken();

            // var_dump($result);die;
        }catch (\ErrorException $e){
            return APIFormat::error($e->getCode(),$e->getMessage());
        }
        $data = [
            'status' => 200,
            'msg' => '请求成功',
            'data' => $result,
        ];
        return $data;
    }

    /**
     * 用户加钱
     * @return [type] [description]
     */
    public function actionAddMoney(){
        $result = "";
        try{
            $user = new Alcohol();
            $user->setScenario('AddMoney');
            $user->setAttributes(\yii::$app->request->post());
            $result = $user->AddMoney();
        }catch (\ErrorException $e){
            $data = [
                'status' => $e->getCode(),
                'msg' => $e->getMessage(),
                'data' => "",
            ];
            return $data;
        }
        $data = [
            'status' => 200,
            'msg' => '领取成功',
            'data' => "",
        ];
        return $data;
    }

    /**
     * 获取商城可操作币种
     * @return array|null|string
     */
    public function actionSymbolList(){
        $result = "";
        try{
            $alcohol = new Alcohol();
            $alcohol->setScenario('symbolList');
            $alcohol->setAttributes(\yii::$app->request->post());
            $result = $alcohol->symbolList();
            $result = [
                'status' => 200,
                'msg' => '',
                'data' => $result,
            ];
        }catch (\Exception $e){
            $result = [
                'status' => $e->getCode(),
                'msg' => $e->getMessage(),
                'data' => "",
            ];
        }
        return $result;
    }

    /**
     * 购买支付
     */
    public function actionBuyPay(){
        $result = "";
        try{
            $alcohol = new Alcohol();
            $alcohol->setScenario('buyPay');
            $alcohol->setAttributes(\yii::$app->request->post());
            $result = $alcohol->buyPay();
            $result = [
                'status' => 200,
                'msg' => '',
                'data' => $result,
            ];
        }catch (\Exception $e){
            $result = [
                'status' => $e->getCode(),
                'msg' => $e->getMessage(),
                'data' => "",
            ];
        }
        return $result;
    }

    public function actionRecipient(){
        $result = "";
        try{
            $alcohol = new Alcohol();
            $alcohol->setScenario('recipient');
            $alcohol->setAttributes(\yii::$app->request->post());
            $alcohol->recipient();
            $result = [
                'status' => 200,
                'msg' => '',
                'data' => $result,
            ];
        }catch (\Exception $e){
            $result = [
                'status' => $e->getCode(),
                'msg' => $e->getMessage(),
                'data' => "",
            ];
        }
        return $result;
    }

    public function actionActivation(){
        $result = "";
        try{
            $alcohol = new Alcohol();
            $alcohol->setScenario('activation');
            $alcohol->setAttributes(\yii::$app->request->post());
            $result = $alcohol->activation();
            $result = [
                'status' => 200,
                'msg' => '',
                'data' => $result,
            ];
        }catch (\Exception $e){
            $result = [
                'status' => $e->getCode(),
                'msg' => $e->getMessage(),
                'data' => "",
            ];
        }
        return $result;
    }

    public function actionActivationList(){
        $result = "";
        try{
            $alcohol = new Alcohol();
            $alcohol->setScenario('activationList');
            $alcohol->setAttributes(\yii::$app->request->post());
            $result = $alcohol->activationList();
            $result = [
                'status' => 200,
                'msg' => '',
                'data' => $result,
            ];
        }catch (\Exception $e){
            $result = [
                'status' => $e->getCode(),
                'msg' => $e->getMessage(),
                'data' => "",
            ];
        }
        return $result;
    }
    public function actionWineList(){
        $result = "";
        try{
            $alcohol = new Alcohol();
            $alcohol->setScenario('wineList');
            $alcohol->setAttributes(\yii::$app->request->post());
            $result = $alcohol->wineList();
            $result = [
                'status' => 200,
                'msg' => '',
                'data' => $result,
            ];
        }catch (\Exception $e){
            $result = [
                'status' => $e->getCode(),
                'msg' => $e->getMessage(),
                'data' => "",
            ];
        }
        return $result;
    }

    /**
     * 用户提现
     * @return [type] [description]
     */
    public function actionWithdraw(){
        $result = "";
        try{
            $alcohol = new Alcohol();
            $alcohol->setScenario('Withdraw');
            $alcohol->setAttributes(\yii::$app->request->post());
            $result = $alcohol->Withdraw();
            $result = [
                'status' => 200,
                'msg' => '',
                'data' => $result,
            ];
        }catch (\Exception $e){
            $result = [
                'status' => $e->getCode(),
                'msg' => $e->getMessage(),
                'data' => "",
            ];
        }
        return $result;
    }

    public function actionWtpriceList(){
        $alcohol = new Alcohol();
        $result = $alcohol->wtPrice();
        $res = [
            'status' => 200,
            'msg' => "请求成功",
            'data' => $result,
        ];
        return $res;
    }

    public function actionRefund(){
        $alcohol = new Alcohol();
        try{
            $alcohol->setScenario('Refund');
            $alcohol->setAttributes(\yii::$app->request->post());
            $alcohol->refund();
            $result = [
                'status' => 200,
                'msg' => '请求成功',
                'data' => "",
            ];
        }catch(\ErrorException $e){
            $result = [
                'status' => $e->getCode(),
                'msg' => $e->getMessage(),
                'data' => "",
            ];
        }
        return $result;
    }
}