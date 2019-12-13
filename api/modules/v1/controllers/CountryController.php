<?php


namespace api\modules\v1\controllers;

use api\controllers\APIFormat;
use common\models\Country;
class CountryController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['optional'] = ['index'];
        return $behaviors;
    }
    public function actionIndex(){
        header('Access-Control-Allow-Origin:*');
        header("Access-Control-Allow-Credentials : true");
        $list = Country::find()->select('id,name,telephone_code')->all();
        return APIFormat::success($list);
    }
}