<?php

namespace api\modules\v1\controllers;

use api\models\User;
use common\models\Verification;
use api\models\VerificationAddForm;
use api\controllers\APIFormat;
use yii;

class VerificationController extends BaseController
{
    /**
     * 认证提交
     * @return array|null
     * @throws yii\db\Exception
     */
    public function actionSubmit()
    {
        $result = null;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = new VerificationAddForm();
            $model->load(\Yii::$app->request->post(), '');

            $model_verification = $model->save();
            if (false === $model_verification) {
                throw new \ErrorException(APIFormat::popError($model->getErrors()),7000);
            }
            //更新用户审核状态
//            User::updateAll(['verification_status'=> Verification::STATUS_SUBMITTED], ['id' => $model_verification->user_id]);
            $result = $model_verification->attributesForCreated();
            $transaction->commit();
        } catch (\ErrorException $e) {
            $transaction->rollBack();
            return APIFormat::error($e->getCode(), $e->getMessage());
        }
        return APIFormat::success($result);
    }


    /**
     * 认证审核未通过的时候
     * @return array|null
     */
    public function actionView()
    {
        $result = null;
        try {
            $model_user = \Yii::$app->user->identity;

            if (false === Verification::isSubmitValid($model_user->getId())) {
                throw new \ErrorException('用户未提交审核信息', 7001);
            }

            $model_verification = $model_user->verification ? $model_user->verification : (new Verification());

            if ($model_verification->id && $model_verification->user_id != \Yii::$app->user->id) {
                throw new \ErrorException('用户认证身份未通过', 7002);
            }
            $result = $model_verification->attributesForList();
        } catch (\ErrorException $e) {
            return APIFormat::error($e->getCode(),$e->getMessage());
        }

        return APIFormat::success($result);
    }


    /**
     * 认证提交
     * @return array|null
     * @throws yii\db\Exception
     */
    public function actionUpdate()
    {
        $result = null;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = new VerificationAddForm();
            $model->load(\Yii::$app->request->post(), '');

            $model_verification = $model->update();
            if (false === $model_verification) {
                throw new \ErrorException(APIFormat::popError($model->getErrors()),7000);
            }
            //更新用户审核状态
//            User::updateAll(['verification_status'=> Verification::STATUS_SUBMITTED], ['id' => $model_verification->user_id]);
            $result = $model_verification->attributesForCreated();
            $transaction->commit();
        } catch (\ErrorException $e) {
            $transaction->rollBack();
            return APIFormat::error($e->getCode(), $e->getMessage());
        }
        return APIFormat::success($result);
    }





}
