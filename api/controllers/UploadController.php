<?php

namespace api\controllers;

use api\models\UploadForm;
use api\modules\v1\controllers\BaseController;
use common\models\Alioss;
use Yii;
use yii\web\UploadedFile;

/**
 *  Common/Upload
 */
class UploadController extends BaseController
{
    /**
     * 上传文件
     */
    public function actionIndex()
    {

        $model = new UploadForm();

        if (Yii::$app->request->isPost) {
            //设置模型场景
            $model->setScenario('image');
            $model->inputFile = UploadedFile::getInstanceByName('image');

            if ($model->upload()) {
                $path = Yii::$app->params['imagesUrl'] . '/' . $model->filePath;
                return APIFormat::success($path);
            } else {
                return APIFormat::error(1000, $model->errors);
            }
        }
    }

    /**
     * 上传文件
     */
    public function actionImage()
    {
        try{
            $model = new Alioss();
            $model->image = UploadedFile::getInstanceByName('image');
            $path =  $model->upload();
            return APIFormat::success($path);
        }catch (\Exception $exception){
            return APIFormat::error($exception->getCode(), $exception->getMessage());
        }
    }
}
