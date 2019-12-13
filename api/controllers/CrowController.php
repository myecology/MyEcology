<?php
namespace api\controllers;
use yii\base\Controller;
use api\models\CrowFrom;
use yii\base\ErrorException;
class CrowController extends Controller{
	/**
	 * 众筹支付
	 * @return [type] [description]
	 */
	public function actionPayCrow(){
		$result = "";
		try{
			$model = new CrowFrom();
			$model->setScenario('PayCrow');
			$model->setAttributes(\yii::$app->request->post());
            $result = $model->crowpay();
            $data = [
                'status' => 200,
                'msg' => "支付成功",
                'data' => '',
            ];
		}catch(\ErrorException $e){
			$data = [
                'status' => $e->getCode(),
                'msg' => $e->getMessage(),
                'data' => '',
            ];
		}
		return $data;
	}

	/**
	 * 查询币种余额
	 * @return [type] [description]
	 */
	public function actionBalance(){
		$result = "";
		try{
			$model = new CrowFrom();
			$model->setScenario("Balance");
			$model->setAttributes(\yii::$app->request->post());
			$result = $model->balance();
			$data = [
                'status' => 200,
                'msg' => "请求成功",
                'data' => $result,
            ];
		}catch(\ErrorException $e){
			$data = [
                'status' => $e->getCode(),
                'msg' => $e->getMessage(),
                'data' => '',
            ];
		}
		return $data;
	}

	public function actionListCount(){
		$result = "";
		try{
			$model = new CrowFrom();
			$model->setScenario('ReleaseCount');
			$model->setAttributes(\yii::$app->request->post());
			$result = $model->ReleaseCount();
			$data = ['msg'=>'请求成功',"status"=>200,'data'=>$result];
		}catch(\ErrorException $e){
			$data = [
				'status' => $e->getCode(),
				'msg' => $e->getMessage(),
				'data' => $result,
			];
		}
		return $data;
	}
}