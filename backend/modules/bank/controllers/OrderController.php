<?php

namespace backend\modules\bank\controllers;

use backend\modules\bank\models\ReissuedOrderModel;
use common\models\bank\Profit;
use common\models\WalletLog;
use Yii;
use common\models\bank\Order;
use common\models\bank\search\OrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Order();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }


    /**
     * 理财退回
     *
     * @param [type] $id
     * @return void
     */
    public function actionBack($id)
    {
        $model = $this->findModel($id);

        if($model->orderBack()){
            Yii::$app->getSession()->setFlash('success', '退回成功');
        }else{
            Yii::$app->getSession()->setFlash('error', '退回失败');
        }
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
     * excel表格下载
     * @return mixed
     */
    public function actionDownloadExcel()
    {

        $startTime = Yii::$app->request->get('start_time');
        $endTime = Yii::$app->request->get('end_time') ? Yii::$app->request->get('end_time') : date('Y-m-d');
        $status = Yii::$app->request->get('status');
        if($status == 'all'){
            $status = array_keys(Order::statusArray());
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="用户订单列表.csv"');
        header('Cache-Control: max-age=0');
        set_time_limit(0);
        ini_set("memory_limit","256M");
        $orderModel = new Order();

        $startTimeStr = strtotime($startTime.'00:00:00');
        $endTimeStr = strtotime($endTime.'23:59:59');
        if ($startTime && $endTime && ($status==0 || $status)) {
            $where = ['and',['status'=>$status],['between','created_at',$startTimeStr,$endTimeStr]];
        }else if($startTime && !$endTime && $status){
            $where = ['and',['status'=>$status],['>=','created_at',$startTimeStr]];
        }else if(!$startTime && $endTime && $status){
            $where = ['and',['status'=>$status],['<=','created_at',$endTimeStr]];
        }else{
            $where['status'] = $status;
        }
        $count = $orderModel->find()->andFilterWhere($where)->count();

        $limit = $count < 2000 ? 1 : ceil($count / 2000);

        $fp = fopen('php://output', 'a');

        $head = array('用户名', '产品名称', '利率','购买数量','周期','创建时间','订单状态','收益币种');
        foreach ($head as $i => $v) {
            $head[$i] = iconv("utf-8","gb2312//IGNORE",$v);
        }

        fputcsv($fp, $head);

        $i = 1;
        $n = 0;
        $statusArray  = Order::statusArray();
        while($n < $limit) {
            $list = $orderModel->find()->with(['user','product'])->where($where)->offset($n * 2000)->limit(2000)->asArray()->all();
            foreach ($list as $val) {
                $newData = [
                    'name'   => ' ' .iconv("utf-8","gb2312//IGNORE",$val['user']['username']),
                    'product_name' => ' '.iconv("utf-8","gb2312//IGNORE",$val['product']['name']),
                    'rate' => ' '.$val['rate'],
                    'amount' => ' '.$val['amount'],
                    'day' => ' '.$val['day'],
                    'created_at' => ' ' .date('Y-m-d H:i:s',$val['created_at']),
                    'status' =>' '.iconv("utf-8","gb2312//IGNORE",$statusArray[$val['status']]),
                    'earn_symbol' =>' '.iconv("utf-8","gb2312//IGNORE",$val['earn_symbol']),
                ];
                if (!$newData) {
                    break;
                }
                fputcsv($fp, $newData);
                $i++;
            }
            if ($i > 2000) {//读取一部分数据刷新下输出buffer
                ob_flush();
                flush();
                $i = 0;
            }
            $n++;
        }
        fclose($fp);
        exit;
    }

    public function actionReissued($id){
        $orderModel = $this->findModel($id);
        $model = new ReissuedOrderModel();
        $profitModel = Profit::find()->where([
            'order_id' => $id
        ])->orderBy('id DESC')->one();
        try{
            if($model->load(Yii::$app->request->post()) && $model->reissued($orderModel)) {
                Yii::$app->getSession()->setFlash('success', '操作成功');
                return $this->redirect(['index']);
            }
        }catch (\Exception $exception){
            Yii::$app->getSession()->setFlash('error', $exception->getMessage());
        }
        return $this->render('reissued', [
            'model' => $model,
            'orderModel' => $orderModel,
            'profitModel' => $profitModel,
        ]);
    }

}
