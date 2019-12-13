<?php

namespace api\modules\v1\controllers;

use api\controllers\APIFormat;
use api\models\GiftMoneyAddForm;
use api\models\GiftMoneyTakeForm;
use common\models\GiftMoney;
use common\models\GiftMoneyTaker;

class GiftMoneyController extends \api\modules\v1\controllers\BaseController
{
    public function actionAdd()
    {
        try {
            /**
             * @var float	$amount	金额
             * @var string	$symbol	币种
             * @var int	    $type	红包类型，10:个人红包，20:等额群红包，30:随机群红包
             * @var int	    $unit	个数，随机群红包时必选
             * @var float	$amount_each	单个红包金额，等额群红包时必选
             * @var string	$description	红包描述
             */

            $model = new GiftMoneyAddForm();
            $model->load(\Yii::$app->request->post(), '');

            $model_result = $model->save();
            if (false === $model_result) {
                throw new \ErrorException(APIFormat::popError($model->getErrors()), 5200);
            }

            $result = APIFormat::success((string)$model_result);
        } catch (\ErrorException $e) {
            $result = APIFormat::error($e->getCode(), $e->getMessage()?:null);
        }

        return $result;
    }

    public function actionCheck()
    {
        try {
            /**
             * @var float	$amount	金额
             * @var string	$symbol	币种
             * @var int	    $type	红包类型，10:个人红包，20:等额群红包，30:随机群红包
             * @var int	    $unit	个数，随机群红包时必选
             * @var float	$amount_each	单个红包金额，等额群红包时必选
             * @var string	$description	红包描述
             */

            $model = new GiftMoneyAddForm();
            $model->load(\Yii::$app->request->post(), '');

            if (!$model->validate()) {
                throw new \ErrorException(APIFormat::popError($model->getErrors()), 5100);
            }

            $result = APIFormat::success(true);
        } catch (\ErrorException $e) {
            $result = APIFormat::error($e->getCode(), $e->getMessage()?:null);
        }

        return $result;
    }

    public function actionTake()
    {
        try {
            /**
             * @var int	$id	红包ID
             */

            $model = new GiftMoneyTakeForm();
            $model->load(\Yii::$app->request->post(), '');

            if (!$model->save()) {
                throw new \ErrorException(APIFormat::popError($model->getErrors()), 5300);
            }

            $result = APIFormat::success("1");
        } catch (\ErrorException $e) {
            $result = APIFormat::error($e->getCode(), $e->getMessage()?:null);
        }catch (\Exception $exception){
            $result = APIFormat::error($exception->getCode(), $exception->getMessage()?:null);
        }
        return $result;
    }

    public function actionView()
    {
        try {
            /**
             * @var int	$id	红包ID
             */
            $id = \Yii::$app->request->post('id');

            $model = GiftMoney::findById($id);
            if (!$model) {
                throw new \ErrorException('', 5400);
            }

            $viewer = $model->attributesForView(\Yii::$app->user->getId());
            if(false === $viewer){
                throw new \ErrorException('', 5900);
            }

            $searchModel = new GiftMoneyTaker();
            $dataProvider = $searchModel->search(\Yii::$app->request->post(), $model->id);

            $summary = [];
            $summary['page_total'] = (string)$dataProvider->getPagination()->pageCount;
            $summary['count_total'] = (string)$dataProvider->getTotalCount();

            $takers = $model->takersAttributesForView(\Yii::$app->user->getId(), $dataProvider->getModels());

            $result = APIFormat::success([
                'viewer' => $viewer,
                'summary' => $summary,
                'takers' => (Array)$takers,
            ]);
        } catch (\ErrorException $e) {
            $result = APIFormat::error($e->getCode(), $e->getMessage()?:null);
        }

        return $result;
    }

    public function actionOperation(){
        try {
            /**
             * @var int	$id	红包ID
             */
            $model = new GiftMoneyTakeForm();
            $model->load(\Yii::$app->request->post(), '');

            $taker = $model->attributesTaker();
            if(false === $taker){
                throw new \ErrorException('', 5900);
            }

            $operation = $model->operationCode();
            $result = APIFormat::success([
                'operation' => (string)$operation,
                'taker' => (Array)$taker,
            ]);
        } catch (\ErrorException $e) {
            $result = APIFormat::error($e->getCode(), $e->getMessage()?:null);
        }

        return $result;
    }

}
