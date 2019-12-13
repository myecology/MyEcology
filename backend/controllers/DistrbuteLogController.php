<?php

namespace backend\controllers;

use Yii;
use common\models\DistrbuteLog;
use backend\models\search\DistrbuteLogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\search\FillSearch;
use backend\models\search\ReleaseDistrbuteLogSearch;
use common\models\Fill;

/**
 * DistrbuteLogController implements the CRUD actions for DistrbuteLog model.
 */
class DistrbuteLogController extends Controller
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
     * Lists all DistrbuteLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DistrbuteLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DistrbuteLog model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {   
        $modelinfo = DistrbuteLog::findOne($id);
        if($modelinfo->status == 1){
            $searchModel = new FillSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$modelinfo->id);

            return $this->render('fill', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }else{
           $searchModel = new ReleaseDistrbuteLogSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$modelinfo->id);

            return $this->render('view', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]); 
        }
        /*return $this->render('view', [
            'model' => $this->findModel($id),
        ]);*/
    }

    /**
     * Creates a new DistrbuteLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    /*public function actionCreate()
    {
        $model = new DistrbuteLog();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }*/

    /**
     * Updates an existing DistrbuteLog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    /*public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }*/

    /**
     * Deletes an existing DistrbuteLog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = Fill::findOne($id);
        $distrbute_id = $model->distrbute_id;
        $count = Fill::find()
        ->where(['distrbute_id'=>$distrbute_id])
        ->andWhere(['is_del'=>1])
        ->count();
        $model->is_del = 2;
        $model->save();
        if($count == 1){
            DistrbuteLog::findOne($distrbute_id)->delete();
            return $this->redirect(['index']);
        }
        return $this->redirect(['view','id'=>$distrbute_id]);
    }

    /**
     * Finds the DistrbuteLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DistrbuteLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DistrbuteLog::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
