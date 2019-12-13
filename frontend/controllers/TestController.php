<?php
namespace frontend\controllers;

use api\models\User;
use Yii;
use api\controllers\APIFormat;
use yii\helpers\Json;
use common\models\LoginForm;
use api\models\Sms;
use yii\web\Controller;

/**
 * Site controller
 */
class TestController extends Controller
{

    public function actionIndex()
    {
        echo md5('3c3b0f318183c10bebff7c999453816bcq');
    }

    public function actionAccessToken()
    {
        $userName = yii::$app->request->get('username');
        $model = User::find()->where(['username' => $userName])->one();
        $model->generateAccessToken();
        if (false !== $model->save()) {
            echo 'success';exit;
        }
        echo 'error';exit;
    }


}