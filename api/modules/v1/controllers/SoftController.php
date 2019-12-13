<?php
namespace api\modules\v1\controllers;

use api\controllers\APIFormat;
use yii\helpers\Url;
use backend\models\Version;
use Yii;

/**
 * 用户操作
 */
class SoftController extends BaseController
{
    /**
     * 行为
     * @return [type] [description]
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['optional'] = ['index'];
        return $behaviors;
    }


    public function actionIndex($type)
    {
        $data = Version::find()->where(['type' => $type])->orderBy('created_at desc')->asArray()->one();
        return APIFormat::success($data);
    }
}