<?php

namespace api\modules\v1\controllers;

use api\controllers\APIFormat;
use api\models\WalletAddForm;
use common\models\Currency;
use api\models\WalletAddress;
use common\models\CurrencyPrice;
use common\models\Wallet;

class CurrencyController extends BaseController
{
    public function actionAdd2user()
    {
        try {
            $symbol = \Yii::$app->request->post('symbol');

            $model = new WalletAddForm();
            $model->load(\Yii::$app->request->post(), '');

            if (!Currency::isExists($symbol)) {
                throw new \ErrorException(null, 5003);
            }

            if (!$model->validate()) {
                throw new \ErrorException(APIFormat::popError($model->getErrors()), 5006);
            }

            if (false === $model->save()) {
                throw new \ErrorException('', 5007);
            }

            $result = APIFormat::success(true);
        } catch (\ErrorException $e) {
            $result = APIFormat::error($e->getCode(), $e->getMessage() ?: null);
        }

        return $result;
    }

    public function actionIndex()
    {
        try {
            $is_filted = \Yii::$app->request->post('is_filted');

            $currencies = Currency::loadAvailable();

            $wallets_user = [];
            if ($is_filted) {
                $user_id = \Yii::$app->user->getId();
                $wallets_user = Wallet::loadByUserId($user_id);
            }

            if ($wallets_user) {
                foreach ($currencies as $i => $currency) {
                    $currency['is_enabled'] = strval($wallets_user && isset($wallets_user[$currency['symbol']]) && $wallets_user[$currency['symbol']]->getIsDisplayed() ? 1 : 0);
                    $currencies[$i] = $currency;
                }
            }

            $result = APIFormat::success($currencies);
        } catch (\ErrorException $e) {
            $result = APIFormat::error($e->getCode());
        }

        return $result;
    }

    public function actionValue()
    {
        try {
            $symbol = \Yii::$app->request->post('symbol');
            $amount = \Yii::$app->request->post('amount');
            $amount = doubleval($amount);

            if (!Currency::isExists($symbol)) {
                throw new \ErrorException('', 5003);
            }
            if (!$amount || $amount < 0) {
                throw new \ErrorException('', 5004);
            }

            $amount_value = CurrencyPrice::convert($amount, $symbol);
            if (false === $amount_value) {
                throw new \ErrorException('', 5005);
            }

            $result = APIFormat::success(APIFormat::asMoney(strval($amount_value)));
        } catch (\ErrorException $e) {
            $result = APIFormat::error($e->getCode());
        }

        return $result;
    }

    public function actionAddress()
    {
        try {
            $symbol = \Yii::$app->request->post('symbol');

            $currency = Currency::findCurrencyBySymbol($symbol);
            if (!$currency) {
                throw new \ErrorException('', 5003);
            }

            $walletAddress = WalletAddress::getWalletAddress($symbol, \Yii::$app->user->getId(), $currency);

            $result = APIFormat::success($walletAddress->address);
//            $result = APIFormat::success('系统维护暂时不支持充值,如果想充值请联系客服');
        } catch (\ErrorException $e) {
            $result = APIFormat::error($e->getCode(), $e->getMessage() ?: null);
        }

        return $result;
    }

}
