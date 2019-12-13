<?php
namespace api\modules\v1\controllers;

use api\controllers\APIFormat;
use backend\models\StartPage;
use Yii;
use yii\helpers\Url;
use backend\models\MomentBanner;

/**
 * 用户操作
 */
class AdController extends BaseController
{
    /**
     * 行为
     * @return [type] [description]
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['optional'] = ['start-page'];
        return $behaviors;
    }


    public function actionIndex()
    {
        $data = MomentBanner::find()->where(['status' => 10])->orderBy('sort asc')->asArray()->all();
        return APIFormat::success($data);
    }

    /**s
     * 启动页数据
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionStartPage(){
        $data = StartPage::find()->select(["img","time","status","redirecturl"])->where(['status' => 10])->asArray()->all();

        return APIFormat::success($data);
    }



}
