<?php

namespace api\modules\v1\controllers;

use api\controllers\APIFormat;
use common\models\Deposit;
use yii\base\ErrorException;

class DepositController extends \api\modules\v1\controllers\BaseController
{
    public function actionList()
    {
        try {
            $deposits = Deposit::findByUserId(\Yii::$app->user->getId());

            $result = APIFormat::success($deposits);
        } catch (ErrorException $e) {
            $result = APIFormat::error($e->getCode(), $e->getMessage());
        }

        return $result;
    }

    public function actionView()
    {
        try {
            $id = \Yii::$app->request->post('id');

            $deposit = Deposit::findForDepositApi($id);
            if(false === $deposit){
                throw new ErrorException('', 5020);
            }

            $result = APIFormat::success($deposit);
        } catch (ErrorException $e) {
            $result = APIFormat::error($e->getCode(), $e->getMessage() ?: null);
        }

        return $result;
    }

}
