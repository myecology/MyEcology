<?php

namespace backend\controllers;

use Yii;
use common\models\Crowdfunding;
use backend\models\CrowdfundingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\CrowdfundingFrom;
use yii\base\ErrorException;

/**
 * CrowdfundingController implements the CRUD actions for Crowdfunding model.
 */
class CrowdfundingController extends Controller
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
     * Lists all Crowdfunding models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CrowdfundingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Crowdfunding model.
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
     * Creates a new Crowdfunding model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Crowdfunding();
        try{
            if ($model->load(Yii::$app->request->post()) && $model->add()) {
                return $this->redirect(['index']);
            }
        }catch(\ErrorException $e){
            Yii::$app->getSession()->setFlash('error', $e->getMessage());
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Crowdfunding model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {   
        // $crfrom = new CrowdfundingFrom();
        $model = $this->findModel($id);
        // var_dump(Yii::$app->request->post());die;
        try{
            if ($model->load(Yii::$app->request->post()) && $model->updatedata($id)) {
                return $this->redirect(['index']);
            }
        }catch(\ErrorException $e){
            Yii::$app->getSession()->setFlash('error', $e->getMessage());
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Crowdfunding model.
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
     * Finds the Crowdfunding model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Crowdfunding the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Crowdfunding::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
