<?php

namespace common\controllers;

use yii\web\Controller;
use yii\web\Response;

/**
 * Default controller for the `bobing` module
 */
class JsonBaseController extends Controller
{
    public $enableCsrfValidation = false;

    private $response_code = 200;
    private $response_message = null;

    public function init()
    {
        parent::init();

        \Yii::$app->response->format = Response::FORMAT_JSON;
    }

    protected function setCode($code){
        $this->response_code = $code;
    }

    protected function getCode(){
        return $this->response_code;
    }

    protected function setMessage($message){
        $this->response_message = $message;
    }

    protected function getIsSuccess(){
        return $this->response_code == 200;
    }

    public function afterAction($action, $result)
    {
        $content = parent::afterAction($action, $result);

        return [
            'code' => $this->response_code,
            'message' => $this->response_message,
            'data' => $content,
        ];
    }

}
