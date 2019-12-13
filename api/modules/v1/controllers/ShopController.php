<?php

namespace api\modules\v1\controllers;

use api\models\ShopAddForm;
use common\models\Shop;
use yii;
use common\models\Area;
use common\models\ShopType;
use api\controllers\APIFormat;
use api\models\UploadForm;
use yii\web\UploadedFile;


/**
 * 商家api接口
 * Class ShopController
 * @package api\modules\v1\controllers
 */
class ShopController extends BaseController
{
    /**
     * 首页
     */
    public function actionIndex()
    {
        $shopType = ShopType::find()->orderBy(['weight'=>SORT_DESC,'id'=>SORT_ASC])->limit(8)->all();
        /**
         * @var ShopType $model
         */
        foreach ($shopType as $key => $model) {
            $shopTypeList[] = $model->attributesForList();
        }

        //获取猜你喜欢列表
        $shopList = Shop::getLikeShopList();

        $userid = yii::$app->user->identity->userid;

        $isShopExist = Shop::isExist($userid);

        $result = [
            'isShopExist' => $isShopExist,
            'shopList' => $shopList,
            'shopTypeList' => $shopTypeList,
        ];

        return APIFormat::success($result);
    }


    /**
     * 行业类别
     */
    public function actionType()
    {
        $result = null;
        $result = ShopType::getList();
        return APIFormat::success($result);
    }


    /**
     * 城市列表
     */
    public function actionCityList()
    {
        $result = null;
        $list  = Area::find()->where(['level'=>2])->orderBy(['first_letter'=>SORT_ASC])->all();
        foreach ($list as $key => $val) {
            $data[$val->first_letter][] = $val->attributesList();
        }

        //热门城市
        $hotList = Area::getHotCity();

        $result = [
            'list' => $data,
            'hot_list' => $hotList,
        ];
        return APIFormat::success($result);
    }


    /**
     * 上传图片
     */
    public function actionUploadImage()
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
     * 商家列表
     */
    public function actionList()
    {
        $result = [];

        $_post = yii::$app->request->post();

        $searchModel = new Shop();

        $dataProvider = $searchModel->search($_post);

        $data = [];
        /**
         * @var Shop $model_shop
         */
        foreach ($dataProvider->getModels() as $i => $model_shop) {
            $attributes = $model_shop->attributesForList();
            $data[] = $attributes;
        }

        $shopTypeList = ShopType::getList();

        $result = [
            'shopTypeList' => $shopTypeList,
            'list' => $data,
            'summary' => [
                'pages_total' => (string)$dataProvider->getPagination()->pageCount,
                'count_total' => (string)$dataProvider->getTotalCount(),
            ],
        ];

        return APIFormat::success($result);
    }

    /**
     * 商家入驻
     */
    public function actionCreate()
    {
        try {
            $userid = yii::$app->user->identity->userid;

            $exists = Shop::find()->where(['userid'=>$userid])->exists();
            if ($exists) {
                throw new \ErrorException('已申请过资料', 1007);
            }

            $model = new ShopAddForm();
            $model->load(yii::$app->request->post(), '');

            if (false === $model->save()) {
                throw new \ErrorException(ShopAddForm::getModelError($model), 1000);
            }

        } catch (\ErrorException $e) {
            return APIFormat::error($e->getCode(),$e->getMessage());
        }
        return APIFormat::success('提交成功');
    }


    /**
     * 商家详情
     * @return array|void
     */
    public function actionDetail()
    {
        $result = null;

        try {
            $id = yii::$app->request->post('id');

            if (empty($id)) {
                throw  new \ErrorException('id必须传递',1001);
            }
            $model = new Shop();
            $result = $model->getDetailById($id);

            return APIFormat::success($result);

        } catch (\ErrorException $e) {
            return APIFormat::error($e->getCode(),$e->getMessage());

        }
        return APIFormat::success('提交成功');
    }


    /**
     * 商家修改信息页面
     * @return array|void
     */
    public function actionView()
    {
        $result = null;

        try {
            $userid = yii::$app->user->identity->userid;
            $model = new Shop();
            $data = $model->getDetailByUserid($userid);
            if ($data['userid'] != $userid) {
                throw  new \ErrorException('商家不属于您所有，您无法修改信息',1001);
            }
            $result = $data;
            return APIFormat::success($result);
        } catch (\ErrorException $e) {
            return APIFormat::error($e->getCode(),$e->getMessage());
        }
    }


    /**
     * 商家资料更新
     */
    public function actionUpdate()
    {
        try {
            $userid = yii::$app->user->identity->userid;
            $model = new ShopAddForm();
            $_post = yii::$app->request->post();
            if (!Shop::find()->where(['userid'=>$userid])->one()) {
                throw new \ErrorException('找不到数据', 1003);
            }
            $model->load($_post, '');
            if (false === $model->update($userid)) {
                throw new \ErrorException(ShopAddForm::getModelError($model), 1000);
            }
        } catch (\ErrorException $e) {
            return APIFormat::error($e->getCode(),$e->getMessage());
        }
        return APIFormat::success('提交成功');
    }


    /**
     * 定位城市
     */
    public function actionLocation()
    {
        $result = null;

        $lat = yii::$app->request->get('lat');
        $lng = yii::$app->request->get('lng');

        if (empty($lat) || empty($lng)) {
            return APIFormat::success(['id'=>110100,'name'=>'北京市']);
        }
        $city = false;
        if($lat && $lng) {
            $arr = Area::changeToBaidu($lat,$lng);
            $url = 'http://api.map.baidu.com/geocoder/v2/?callback=&location='.$arr['y'].','.$arr['x'].'.&output=json&pois=1&ak=7w0ElbkFWRhABwXKx4NsvDBVLBsdGnQG';
            $content = file_get_contents($url);
            Yii::info('baidumap:'.$content, 'shop');
            $place = json_decode($content,true);
//            var_dump($place['result']['addressComponent']['city']);
            $city = $place['result']['addressComponent']['city'];
        }

        if (!$city) {
            return APIFormat::error('城市定位失败','1007');
        }

        $area = Area::find()->where(['name'=>$city])->one();

        $result = [
            'id' => $area->id,
            'name' => $area->name,
        ];

        return APIFormat::success($result);
    }
}
