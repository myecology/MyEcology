<?php

namespace common\models\activation;

use backend\modules\member\models\User;
use Yii;

/**
 * This is the model class for table "iec_activation_reward_log".
 *
 * @property int $id
 * @property int $mall_goods_log_id
 * @property int $user_id
 * @property int $activation_id 活动id
 * @property string $amount 金额
 * @property string $symbol 币种
 * @property string $created_at
 */
class ActivationRewardLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_activation_reward_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mall_goods_log_id', 'user_id', 'activation_id', 'amount'], 'required'],
            [['mall_goods_log_id', 'user_id', 'activation_id'], 'integer'],
            [['amount'], 'number'],
            [['created_at'], 'safe'],
            [['symbol'], 'string', 'max' => 20],
        ];
    }
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getActivation()
    {
        return $this->hasOne(Activation::className(), ['id' => 'activation_id']);
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mall_goods_log_id' => '商城购买记录id',
            'user_id' => '用户id',
            'activation_id' => '活动id',
            'amount' => '金额',
            'symbol' => '币种',
            'created_at' => '创建时间',
        ];
    }

    public static function addLog($user_id,$activation_id,$mall_goods_log_id,$amount,$symbol){
        $model = static::findOne([
            'mall_goods_log_id' => $mall_goods_log_id,
            'activation_id' => $activation_id,
            'user_id' => $user_id,
        ]);
        if(!empty($model)){
            throw new \ErrorException('该条记录已操作');
        }else{
            $model = new static();
            $model->mall_goods_log_id = $mall_goods_log_id;
            $model->user_id = $user_id;
            $model->activation_id = $activation_id;
            $model->amount = $amount;
            $model->symbol = $symbol;
            if($model->save()){
                return $model;
            }
            throw new \ErrorException($model->getFirstError());
        }
    }
}
