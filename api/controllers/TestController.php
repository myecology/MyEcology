<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/8/13
 * Time: 3:24 PM
 */

namespace api\controllers;

use backend\queue\TeQueueController;
use backend\queue\TestQueueController;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class TestController extends Controller
{
    public $enableMsgFromCode = true;
    /**
     * {@inheritdoc}
     */
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
    public function actionCall(){
        $id = \Yii::$app->request->post('id');
        \Yii::$app->queue->delay('5')->push(new TeQueueController([
            'id' => $id,
        ])) ;
        echo $id;
        exit();
    }
}