<?php

namespace backend\controllers;

use Yii;
use backend\models\StartPage;
use backend\models\search\StartPageSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StartPageController implements the CRUD actions for StartPage model.
 */
class StartPageController extends Controller
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
     * Lists all StartPage models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StartPageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StartPage model.
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
     * Creates a new StartPage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new StartPage();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing StartPage model.
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
     * Deletes an existing StartPage model.
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
     * Finds the StartPage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StartPage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StartPage::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionUpload()
    {
        if (Yii::$app->request->isPost) {

            $res = [];

            $initialPreview = [];

            $initialPreviewConfig = [];

            $images = UploadedFile::getInstancesByName("UploadImage[img]");
            var_dump($images);
            die;
            if (count($images) > 0) {

                foreach ($images as $key => $image) {

                    if ($image->size > 2048 * 1024) {

                        $res = ['error' => '图片最大不可超过2M'];

                        return json_encode($res);

                    }

                    if (!in_array(strtolower($image->extension), array('gif', 'jpg', 'jpeg', 'png'))) {

                        $res = ['error' => '请上传标准图片文件, 支持gif,jpg,png和jpeg.'];

                        return json_encode($res);

                    }

                    $dir = '/uploads/temp/';

                    //生成唯一uuid用来保存到服务器上图片名称

                    $pickey = ToolExtend::genuuid();

                    $filename = $pickey . '.' . $image->getExtension();

                    //如果文件夹不存在，则新建文件夹

                    if (!file_exists(Yii::getAlias('@backend') . '/web' . $dir)) {

                        FileHelper::createDirectory(Yii::getAlias('@backend') . '/web' . $dir, 777);

                    }

                    $filepath = realpath(Yii::getAlias('@backend') . '/web' . $dir) . '/';

                    $file = $filepath . $filename;

                    if ($image->saveAs($file)) {

                        $imgpath = $dir . $filename;

                        /*Image::thumbnail($file, 100, 100)

                          ->save($file . '_100x100.jpg', ['quality' => 80]);

             */

                        //  array_push($initialPreview, "<img src='" . $imgpath . "' class='file-preview-image' alt='" . $filename . "' title='" . $filename . "'>");

                        $config = [

                            'caption' => $filename,

                            'width' => '120px',

                            'url' => '../upload/delete', // server delete action

                            'key' => $pickey,

                            'extra' => ['filename' => $filename]

                        ];

                        array_push($initialPreviewConfig, $config);

                        $res = [

                            "initialPreview" => $initialPreview,

                            "initialPreviewConfig" => $initialPreviewConfig,

                            "imgfile" => "<input name='image[]' id='" . $pickey . "' type='hidden' value='" . $imgpath . "'/>",

                            'filename' => $filename,

                            'imagePath' => $imgpath,

                        ];

                    }

                }

            }

            return json_encode($res);
        }
    }
}
