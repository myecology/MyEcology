<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/7/2
 * Time: 9:40 AM
 */

namespace api\controllers;


use api\models\Wechat;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class WechatController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['call'],
                'rules' => [
                    [
                        'actions' => ['call'],
                        'allow' => true,
                        'roles' => ['?'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'call' => ['post'],
                ],
            ],
        ];
    }


    public function actionPhoneCheck(){
        $result = null;
        try {
            $transaction = \Yii::$app->db->beginTransaction();
            $wechatModel = new Wechat();
            $wechatModel->setScenario('phoneCheck');
            $params = \Yii::$app->request->post();
            \Yii::info('callConData:'.json_encode($params),'wechat');
            $wechatModel->setAttributes($params);

            if($wechatModel->phoneByUser()){
                $transaction->commit();
                $result = [
                    'status' => 200,
                    'data' => 'success',
                    'msg' => ''
                ];
            }else{
                $transaction->rollBack();
                $result = [
                    'status' => 400,
                    'data' => false,
                    'msg' => '未知错误'
                ];
            }
        } catch (\ErrorException $e) {
            $transaction->rollBack();
            $result = [
                'status' => $e->getCode(),
                'data' => false,
                'msg' => $e->getMessage()
            ];
        }
        return $result;
    }

    public function actionPhoneMfcc(){
        $result = null;
        try {
            $transaction = \Yii::$app->db->beginTransaction();
            $wechatModel = new Wechat();
            $wechatModel->setScenario('phoneMfcc');
            $params = \Yii::$app->request->post();
            \Yii::info('callConData:'.json_encode($params),'wechat');
            $wechatModel->setAttributes($params);

            if($wechatModel->phoneByMfcc()){
                $transaction->commit();
                $result = [
                    'status' => 200,
                    'data' => 'success',
                    'msg' => ''
                ];
            }else{
                $transaction->rollBack();
                $result = [
                    'status' => 400,
                    'data' => false,
                    'msg' => '未知错误'
                ];
            }
        } catch (\ErrorException $e) {
            $transaction->rollBack();
            $result = [
                'status' => $e->getCode(),
                'data' => false,
                'msg' => $e->getMessage()
            ];
        }
        return $result;
    }
}