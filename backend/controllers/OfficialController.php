<?php

namespace backend\controllers;

use api\controllers\RongCloudExpansion;
use Yii;
use common\models\Official;
use common\models\search\OfficialSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OfficialController implements the CRUD actions for Official model.
 */
class OfficialController extends Controller
{
    /**
     * {@inheritdoc}
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
     * Lists all Official models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OfficialSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Official model.
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
     * Creates a new Official model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Official();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if($model->display == 1){
                $config = [
                    'appKey' => Yii::$app->params['RongCloud']['appKey'],//'vnroth0kvb43o',
                    'appSecret' => Yii::$app->params['RongCloud']['appSecret'],//'TVeiKg32gBsYw'
                ];
                $content = [
                    'content' => $model->title,
                    'extra' => $model->created_at,
                ];
                $server = new RongCloudExpansion($config['appKey'], $config['appSecret']);
                $result = $server->Message()->broadcast(1,'RC:Official',json_encode($content));
                $result = json_decode($result,true);
                if(isset($result) && $result['code'] == 200){
                    return $this->redirect(['index']);
                }else{
                    $model->display = 0;
                    if($model->save()){
                        return $this->redirect(['index']);
                    }
                }
            }else{
                return $this->redirect(['index']);
            }

        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Official model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Official model.
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
     * Finds the Official model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Official the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Official::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
