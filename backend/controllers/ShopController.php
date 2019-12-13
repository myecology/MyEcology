<?php

namespace backend\controllers;

use common\models\Area;
use Yii;
use backend\models\Shop;
use backend\models\search\ShopSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;

/**
 * ShopController implements the CRUD actions for Shop model.
 */
class ShopController extends Controller
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
     * Lists all Shop models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ShopSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Shop model.
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
     * Creates a new Shop model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new \backend\models\Shop();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $area = Area::getLatAndLng($model->address);
            if ($area) {
                $lat = $area['lat'];
                $lng = $area['lng'];
                \backend\models\Shop::updateAll(['lat'=>$lat,'lng'=>$lng],['id'=>$model->id]);
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Shop model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (yii::$app->request->isPost) {
            $area = Area::getLatAndLng($model->address);
            if ($area) {
                $model->lat = $area['lat'];
                $model->lng = $area['lng'];
            }
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Shop model.
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
     * Finds the Shop model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Shop the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Shop::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
     * 通过审核
     */
    public function actionSuccess()
    {
        $id = yii::$app->request->get('id');

        $model = $this->findModel($id);

        if ($model->updateStatus(\common\models\Shop::STATUS_PASS)) {

            Yii::$app->getSession()->setFlash('success', '操作成功');

        } else {
            Yii::$app->getSession()->setFlash('error', '操作失败');
        }

        return $this->redirect(['index']);
    }

    /**
     * 拒绝审核
     */
    public function actionRefuse()
    {
        $id = yii::$app->request->get('id');
        $refuse_reason = yii::$app->request->post('refuse_reason');

        $model = $this->findModel($id);

        if ($model->status != \common\models\Shop::STATUS_SUBMITTED) {

            Yii::$app->getSession()->setFlash('error', '当前状态无法拒绝');
        }

        if ($model->updateStatus(\common\models\Shop::STATUS_REFUSE,$refuse_reason)) {

            Yii::$app->getSession()->setFlash('success', '操作成功');

        } else {
            Yii::$app->getSession()->setFlash('error', '操作失败');
        }

        return $this->redirect(['index']);
    }


    /**
     * Function output the site that you selected.
     * @param int $pid
     * @param int $typeid
     */
    public function actionSite($pid, $typeid = 0)
    {
        $model = new Area();
        $model = $model::getCityList($pid);

        if($typeid == 1){$aa="--请选择市--";}else if($typeid == 2 && $model){$aa="--请选择区--";}

        echo Html::tag('option',$aa, ['value'=>'empty']) ;

        foreach($model as $value=>$name)
        {
            echo Html::tag('option',Html::encode($name),array('value'=>$value));
        }
    }
}
