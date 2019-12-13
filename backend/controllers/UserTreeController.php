<?php

namespace backend\controllers;

use Yii;
use common\models\UserTree;
use common\models\search\UserTreeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserTreeController implements the CRUD actions for UserTree model.
 */
class UserTreeController extends Controller
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
     * Lists all UserTree models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserTreeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 重置超级节点
     *
     * @return void
     */
    public function actionRest()
    {
        $roots = UserTree::find()->roots()->select('root')->all();
        foreach($roots as $val){
            $root = UserTree::findOne(['id' => $val->root]);

            UserTree::updateAll(['node_lvl' => 0],[
                'AND',
                ['>', 'lft', $root->lft],
                ['<', 'rgt', $root->rgt],
                ['=', 'root', $root->root],
            ]);
            
            $top = $root->children()->andWhere(['node' => 10])->orderBy('id asc')->one();
            if($top){
                $top->node_lvl = 1;
                $top->save();
                UserTree::updateAll(['node_lvl' => $top->node_lvl],[
                    'AND',
                    ['>', 'lft', $top->lft],
                    ['<', 'rgt', $top->rgt],
                    ['=', 'root', $top->root],
                ]);

                $all = UserTree::findOne(['id' => $top->id])->children()->andWhere(['node' => 10])->orderBy('id asc')->all();
                foreach($all as $key => $node){
                    $node->node_lvl = $key + 1;
                    if(false === $node->save()){
                        exit;
                    }
                    UserTree::updateAll(['node_lvl' =>$node->node_lvl],[
                        'AND',
                        ['>', 'lft', $node->lft],
                        ['<', 'rgt', $node->rgt],
                        ['=', 'root', $node->root],
                    ]);
                }
            }
        }
    }


    /**
     * Displays a single UserTree model.
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
     * Creates a new UserTree model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserTree();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserTree model.
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
     * Deletes an existing UserTree model.
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
     * Finds the UserTree model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserTree the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserTree::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
