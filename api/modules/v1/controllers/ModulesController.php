<?php
namespace api\modules\v1\controllers;

use Yii;
use backend\models\Setting;
use api\controllers\APIFormat;

/**
 * 模块
 */
class ModulesController extends BaseController
{

    /**
     * 模型数据列表
     *
     * @return void
     */
    public function actionIndex()
    {
        return APIFormat::success(Setting::read(null, 'modules'));
    }

}