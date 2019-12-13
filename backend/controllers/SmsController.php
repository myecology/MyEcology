<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/11/4
 * Time: 7:09 PM
 */

namespace backend\controllers;


use backend\models\AdminSms;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class SmsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => 'sms',
                        'allow' => true,
                        'roles' => '?',
                    ]
                ]
            ]
        ];
    }
    public function actionSms(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try{
            $username = \Yii::$app->request->post('username');
            $password = \Yii::$app->request->post('password');
            AdminSms::sendSms($username,$password);
            return [
                'code' => 200,
                'msg' => '短信发送成功',
                'data' => [],
            ];
        }catch (\Exception $exception){
            return [
                'code' => $exception->getCode(),
                'msg' => $exception->getMessage(),
                'data' => [],
            ];
        }
    }
}