<?php

namespace api\modules\v1\controllers;

use api\controllers\APIFormat;
use api\models\AddressAddForm;
use api\models\AddressUpdateForm;
use common\models\Currency;
use common\models\UserAddress;
use yii\base\ErrorException;

class AddressController extends \api\modules\v1\controllers\BaseController
{
    public function actionAdd()
    {
        try {
            /**
            * @var string $address
            * @var string $alias
            */

            $model = new AddressAddForm();
            $model->load(\Yii::$app->request->post(), '');

            if (!$model->validate()) {
                throw new \ErrorException(APIFormat::popError($model->getErrors()), 5001);
            }

            if (false === $model->save()) {
                throw new \ErrorException('', 5002);
            }

            $result = APIFormat::success(true);
        } catch (\ErrorException $e) {
            $result = APIFormat::error($e->getCode(), $e->getMessage());
        }

        return $result;
    }

    public function actionUpdate()
    {
        try {
            /**
             * @var integer $id
             * @var string $address
             * @var string $alias
             */

            $model = new AddressUpdateForm();
            $model->load(\Yii::$app->request->post(), '');

            if (false === $model->update()) {
                throw new ErrorException('', 5002);
            }

            $result = APIFormat::success(true);
        } catch (ErrorException $e) {
            $result = APIFormat::error($e->getCode(), $e->getMessage());
        }

        return $result;
    }

    public function actionDelete()
    {
        try {
            /**
             * @var integer $id
             */
            $id = \Yii::$app->request->post('id');

            $model = UserAddress::findOne(['user_id' => \Yii::$app->user->getId(), 'id' => $id]);
            if (!$model) {
                throw new ErrorException(APIFormat::popError($model->getErrors()), 5009);
            }

            if (false === $model->delete()) {
                throw new ErrorException('', 5011);
            }

            $result = APIFormat::success(true);
        } catch (ErrorException $e) {
            $result = APIFormat::error($e->getCode(), $e->getMessage());
        }

        return $result;
    }

    public function actionList()
    {
        try {
            $symbol = \Yii::$app->request->post('symbol');

            $currency = Currency::findCurrencyBySymbol($symbol);
            if (!$currency) {
                throw new ErrorException('', 5003);
            }

            $user_id = \Yii::$app->user->getId();
            $addresses = UserAddress::loadByUserId($user_id, $currency->model);
            $result = APIFormat::success($addresses);
        } catch (ErrorException $e) {
            $result = APIFormat::error($e->getCode());
        }

        return $result;
    }
}
