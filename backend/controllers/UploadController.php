<?php

namespace backend\controllers;

use backend\models\UploadForm;
use common\models\Alioss;
use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;

/**
 *  Common/Upload
 */
class UploadController extends Controller
{
    /**
     * 上传文件
     */
    public function actionIndex()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try{
            $model = new Alioss();
            $model->image = UploadedFile::getInstanceByName('image');
            $imagePath = $model->upload();
            return $data = [
                //相对地址
                'image' => $imagePath,
                //显示图片地址
                'initialPreview' => $imagePath,
                //点击删除图片
                'initialPreviewConfig' => [
                    [
                        'key' => 0,
                        // 'url' => 'delete'
                    ],
                ],
            ];
        }catch (\Exception $exception){
            return [
                'error' => $exception->getMessage(),
            ];
        }
//        $model = new UploadForm();
//        if (Yii::$app->request->isPost) {
//            //设置模型场景
//            $model->setScenario(Yii::$app->request->post('uploadType'));
//            $model->inputFile = UploadedFile::getInstanceByName('image');
//
//            if ($model->upload()) {
//                return $data = [
//                    //相对地址
//                    'image' => Yii::$app->params['imagesUrl'] .'/' . $model->filePath,
//                    //显示图片地址
//                    'initialPreview' => Yii::$app->params['imagesUrl'] .'/' . $model->filePath,
//                    //点击删除图片
//                    'initialPreviewConfig' => [
//                        [
//                            'key' => 0,
//                            // 'url' => 'delete'
//                        ],
//                    ],
//                ];
//            } else {
//                return [
//                    'error' => $model->errors['inputFile'],
//                ];
//            }
//        }
    }

    /**
     *  删除文件
     */
    public function actionDelete()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [];
    }

}
