<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/8/19
 * Time: 10:04 AM
 */

namespace api\controllers;


use api\models\FoodGame;
use api\models\User;
use yii\web\Controller;

class FoodgameController extends Controller
{
    /**
     * 获取用户信息
     */
    public function actionGetUser(){
        $result = null;
        try {
            $foodGameModel = new FoodGame();
            $foodGameModel->setScenario('getUser');
            $params = \Yii::$app->request->post();
            \Yii::info('postData:'.json_encode($params),'foodgame');
            $foodGameModel->setAttributes($params);
            $data = $foodGameModel->getUserData();
            if(!empty($data)){
                $result = [
                    'status' => 200,
                    'data' => $data,
                    'msg' => 'success'
                ];
            }else{
                $result = [
                    'status' => 400,
                    'data' => false,
                    'msg' => '未知错误'
                ];
            }
        } catch (\ErrorException $e) {
            $result = [
                'status' => $e->getCode(),
                'data' => false,
                'msg' => $e->getMessage()
            ];
        }
        return $result;
    }
    /***
     * 用户操作
     */
    public function actionOperation(){
        $result = null;
        try {
            $foodGameModel = new FoodGame();
            $foodGameModel->setScenario('operation');
            $params = \Yii::$app->request->post();
            \Yii::info('postData:'.json_encode($params),'foodgame');
            $foodGameModel->setAttributes($params);
            $data = $foodGameModel->operation();
            if(!empty($data)){
                $result = [
                    'status' => 200,
                    'data' => $data,
                    'msg' => 'success'
                ];
            }else{
                $result = [
                    'status' => 400,
                    'data' => false,
                    'msg' => '未知错误'
                ];
            }
        } catch (\ErrorException $e) {
            $result = [
                'status' => $e->getCode(),
                'data' => false,
                'msg' => $e->getMessage()
            ];
        }
        return $result;
    }
    /***
     * 获取好友列表
     */
    public function actionGetFriendList(){
        $result = null;
        try {
            $foodGameModel = new FoodGame();
            $foodGameModel->setScenario('getFriendList');
            $params = \Yii::$app->request->post();
            \Yii::info('postData:'.json_encode($params),'foodgame');
            $foodGameModel->setAttributes($params);
            $data = $foodGameModel->getUserList();
            if(!empty($data)){
                $result = [
                    'status' => 200,
                    'data' => $data,
                    'msg' => 'success'
                ];
            }else{
                $result = [
                    'status' => 400,
                    'data' => false,
                    'msg' => '未知错误'
                ];
            }
        } catch (\ErrorException $e) {
            $result = [
                'status' => $e->getCode(),
                'data' => false,
                'msg' => $e->getMessage()
            ];
        }
        return $result;
    }
    /***
     * 消息通知
     */
    public function actionMessage(){
        $result = null;
        try {
            $foodGameModel = new FoodGame();
            $foodGameModel->setScenario('message');
            $params = \Yii::$app->request->post();
            \Yii::info('postData:'.json_encode($params),'foodgame');
            $foodGameModel->setAttributes($params);
            $foodGameModel->message();
            $result = [
                'status' => 200,
                'data' => '',
                'msg' => 'success'
            ];
        } catch (\ErrorException $e) {
            $result = [
                'status' => $e->getCode(),
                'data' => false,
                'msg' => $e->getMessage()
            ];
        }
        return $result;
    }


    public function actionToken(){
        $id = \Yii::$app->request->post('id');
        $id = empty($id)?9306:$id;
        return [
            'status' => 200,
            'data' => User::findOne($id)->access_token,
            'msg' => 'success'
        ];
    }
}