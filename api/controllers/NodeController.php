<?php
namespace api\controllers;
use yii\web\Controller;
use api\models\Node;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\base\Exception;

class NodeController extends Controller{
	/**
	 * 支付备选节点接口
	 * @return [type] [description]
	 */
	public function actionPayAlte(){
		try{
			$model = new Node();
			$model->setScenario('Payalte');
			$model->setAttributes(\Yii::$app->request->post());
			$result = $model->payalte();
			$data = ['msg'=>'支付成功','status'=>200,'data'=>''];
		}catch(\Exception $e){
			$data = ['msg'=>$e->getMessage(),'status'=>$e->getCode(),'data'=>''];
		}
		return $data;
	}

	/**
	 * 超级节点支付
	 * @return [type] [description]
	 */
	public function actionPaySuper(){
		try{
			$model = new Node();
			$model->setScenario('Superpay');
			$model->setAttributes(\Yii::$app->request->post());
			$model->superpay();
			$data = ['msg'=>'支付成功','status'=>200,'data'=>''];
		}catch(\Exception $e){
			$data = ['msg'=>$e->getMessage(),'status'=>$e->getCode(),'data'=>''];
		}
		return $data;
	}

	/**
	 * 获取下级用户注册
	 * @return [type] [description]
	 */
	public function actionNextLeve(){
		$result = "";
		try{
			$model = new Node();
			$model->setScenario('Nextleve');
			$model->setAttributes(\Yii::$app->request->post());
			$result = $model->nextleve();
			$data = ['msg'=>'成功','status'=>200,'data'=>$result];
		}catch(\Exception $e){
			$data = ['msg'=>$e->getMessage(),'status'=>$e->getCode(),'data'=>''];
		}
		return $data;
	}

	/**
	 * 币种验证
	 * @return [type] [description]
	 */
	public function actionSymbol(){
		try{
			$model = new Node();
			$model->setScenario("Symbol");
			$model->setAttributes(\Yii::$app->request->post());
			$model->symbol();
			$data = ["msg"=>'请求成功','status'=>200,'data'=>""];
		}catch(\Exception $e){
			$data = [
				'msg' => $e->getMessage(),
				'status' => $e->getCode(),
				'data' => "",
			];
		}
		return $data;
	}

	/**
	 * 超级节点详情
	 * @return [type] [description]
	 */
	public function actionDetails(){
		$result = "";;
		try{
			$model = new Node();
			$model->setScenario("Details");
			$model->setAttributes(\Yii::$app->request->post());
			$result = $model->details();
			$data = ['msg'=>'请求成功','status'=>200,'data'=>$result];
		}catch(\Exception $e){
			$data = [
				'msg' => $e->getMessage(),
				'status' => $e->getCode(),
				'data' => '',
			];
		}
		return $data;
	}

	/**
	 * [actionReward description]
	 * @return [type] [description]
	 */
	public function actionReward(){
		$result = "";
		try{
			$model = new Node();
			$model->setScenario('Reward');
			$model->setAttributes(\Yii::$app->request->post());
			$result = $model->reward();
			$data = ['status'=>200,'msg'=>'分成成功','data'=>""];
		}catch(\Exception $e){
			$data = [
				'status'=>$e->getCode(),
				'msg'=>$e->getMessage(),
				'data'=>'',
			];
		}
		return $data;
	}

	/**
	 * 超级节点记录
	 * @return [type] [description]
	 */
	public function actionRecord(){
		$result = "";
		try{
			$model = new Node();
			$model->setScenario('Record');
			$model->setAttributes(\Yii::$app->request->post());
			$result = $model->record();
			$data = ['msg'=>'请求成功','status'=>200,'data'=>$result];
		}catch(\Exception $e){
			$data = [
				'msg' => $e->getMessage(),
				'status' => $e->getCode(),
				'data' => '',
			];
		}
		return $data;
	}

	/**
	 * [获取用户所有下级]
	 * @return [type] [description]
	 */
	public function actionGetLeve(){
		try{
			$model = new Node();
			$model->setScenario("Getleve");
			$model->setAttributes(\yii::$app->request->post());
			$result = $model->getleve();
			$data = ['msg'=>"请求成功",'status'=>200,'data'=>$result];
		}catch(\ErrorException $e){
			$data = [
				'msg' => $e->getMessage(),
				'status' => $e->getCode(),
				'data' => '',
			];	
		}
		return $data;
	}
}
