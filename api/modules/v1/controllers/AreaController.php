<?php
namespace api\modules\v1\controllers;

use api\controllers\APIFormat;
use common\models\Area;
use Yii;

/**
 * 用户操作
 */
class AreaController extends BaseController
{
    /**
     * 省级
     *
     * @return void
     */
    public function actionProvince()
    {
        $data = Area::find()->select("id,name,lat,lng")->where(['level' => 1])->asArray()->all();

        return APIFormat::success($data);
    }

    /**
     * 市级
     *
     * @param [type] $pid
     * @return void
     */
    public function actionCity($pid)
    {
        $data = Area::find()->select("id,pid,name,lat,lng")->where(['pid' => $pid, 'level' => 2])->asArray()->all();

        return APIFormat::success($data);
    }

    /**
     * 县级
     *
     * @param [type] $pid
     * @return void
     */
    public function actionCounty($pid)
    {
        $data = Area::find()->select("id,node,name,lat,lng")->where(['pid' => $pid, 'level' => 3])->asArray()->all();

        return APIFormat::success($data);
    }

}