<?php
namespace api\controllers;

use api\models\user;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use api\models\FriendMoment;

use common\models\UserTree;
use backend\models\Setting;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public function actionIndex()
    {
        echo 'ok';
    }

    /**
     * 错误信息
     *
     * @return void
     */
    public function actionError()
    {
        return Json::encode(APIFormat::error(404));
    }
}
