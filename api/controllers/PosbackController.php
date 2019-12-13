<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/7/17
 * Time: 2:11 PM
 */

namespace api\controllers;


use api\models\Posback;
use yii\base\ErrorException;
use yii\web\Controller;
use yii\web\Response;

class PosbackController extends Controller
{

    private $response_code = 200;
    private $response_message = 'success';

    public function init()
    {
        parent::init();
        \Yii::$app->response->format = Response::FORMAT_JSON;
    }

    /**
     * 获取用户列表
     * @return array|string
     */
    public function actionUserList(){
        $result = '';
        try{
            $posBack = new Posback();
            $posBack->setScenario('userList');
            $posBack->setAttributes(\Yii::$app->request->post());
            $result = $posBack->userList();
        }catch (\ErrorException $e){
           $this->response_code = $e->getCode();
           $this->response_message = $e->getMessage();
        }catch (\Exception $exception){
            $this->response_code = $exception->getCode();
            $this->response_message = $exception->getMessage();
        }
        return $result;
    }

    /**
     * 根据user_token获取用户信息
     */
    public function actionUserInfo(){
        $result = '';
        try{
            $posBack = new Posback();
            $posBack->setScenario('userByToken');
            $posBack->setAttributes(\Yii::$app->request->post());
            $result = $posBack->userByToken();
        }catch (\ErrorException $e){
            $this->response_code = $e->getCode();
            $this->response_message = $e->getMessage();
        }catch (\Exception $exception){
            $this->response_code = $exception->getCode();
            $this->response_message = $exception->getMessage();
        }
        return $result;
    }

    public function actionOperation(){
        $result = '';
        try{
            $posBack = new Posback();
            $posBack->setScenario('userOperation');
            $posBack->setAttributes(\Yii::$app->request->post());
            $result = $posBack->operation();
        }catch (\ErrorException $e){
            $this->response_code = $e->getCode();
            $this->response_message = $e->getMessage();
        }
        /*catch (\Exception $exception){
            $this->response_code = $exception->getCode();
            $this->response_message = $exception->getMessage();
        }*/
        return $result;
    }

    public function actionEarn(){
        $result = '';
        try{
            $posBack = new Posback();
            $posBack->setScenario('shopEarn');
            $posBack->setAttributes(\Yii::$app->request->post());
            $result = $posBack->shopEarn();
        }catch (\ErrorException $e){
            $this->response_code = $e->getCode();
            $this->response_message = $e->getMessage();
        }catch (\Exception $exception){
            $this->response_code = $exception->getCode();
            $this->response_message = $exception->getMessage();
        }
        return $result;
    }

    /**
     * 根据ApiHelper::errors() 自动补充错误信息
     * @param \yii\base\Action $action
     * @param mixed $result
     * @return array|mixed
     */
    public function afterAction($action, $result)
    {
        $content = parent::afterAction($action, $result);

        return [
            'status' => $this->response_code,
            'msg' => $this->response_message,
            'data' => $content,
        ];
    }
    
}