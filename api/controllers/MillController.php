<?php

namespace api\controllers;

use api\models\Mill;

use yii\web\Controller;

class MillController extends Controller
{

    public function actionIndex()
    {
        return 'mill';
    }

    public function actionPhoneByUser(){
        $result = null;
        try {
            $MillModel = new Mill();
            $MillModel->setScenario('phoneGetUser');
            $MillModel->setAttributes(\Yii::$app->request->post());
            $data = $MillModel->getUserYytByPhone();
            if(empty($data)){
                throw new \ErrorException('用户不存在',999);
            }
            return APIFormat::success($data->code);
        } catch (\ErrorException $e) {
            return APIFormat::error($e->getCode(),$e->getMessage());
        }
    }

    public function actionPayment(){
        $result = null;
        try {
            $millModel = new Mill();
            $millModel->setScenario('payment');
            \Yii::info('post:'.json_encode(\Yii::$app->request->post()),'mill');
            $millModel->setAttributes(\Yii::$app->request->post());
            if(!$result = $millModel->save()){
                throw new \ErrorException('失败',1099);
            }
            return APIFormat::success('支付成功');
        } catch (\ErrorException $e) {
            return APIFormat::error($e->getCode(),$e->getMessage());
        }
    }
    public function actionMill(){
        $result = null;
        try {
            $millModel = new Mill();
            $millModel->setScenario('mill');
            $millModel->setAttributes(\Yii::$app->request->post());
            if(!$result = $millModel->save()){
                throw new \ErrorException('失败',1099);
            }
            return APIFormat::success('提现成功');
        } catch (\ErrorException $e) {
            return APIFormat::error($e->getCode(),$e->getMessage());
        }
    }


    public function actionUserParent(){
        $result = null;
        try {
            $MillModel = new Mill();
            $MillModel->setScenario('phoneGetUser');
            $MillModel->setAttributes(\Yii::$app->request->post());
            $data = $MillModel->getUserParentByPhone();
            if(empty($data)){
                throw new \ErrorException('用户不存在',999);
            }
            return APIFormat::success($data);
        } catch (\ErrorException $e) {
            return APIFormat::error($e->getCode(),$e->getMessage());
        }
    }
    /**
     * 根据user_phone获取用户信息
     */
    public function actionPhoneSymbolAmount(){
        try{
            $posBack = new Mill();
            $posBack->setScenario('phoneGetUserBySymbol');
            $posBack->setAttributes(\Yii::$app->request->post());
            $result = $posBack->phoneGetUserBySymbol();
            return APIFormat::success($result);
        }catch (\ErrorException $e){
            return APIFormat::error($e->getCode(),$e->getMessage());
        }catch (\Exception $exception){
            return APIFormat::error($exception->getCode(),$exception->getMessage());
        }
    }
    

}