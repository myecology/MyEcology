<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "iec_food_game_log".
 *
 * @property int $id
 * @property string $order_sn 订单编号
 * @property int $user_id 用户id
 * @property string $amount 金额
 * @property string $symbol 币种
 * @property string $created_at 创建时间
 * @property string $remark 备注
 * @property int $status 状态
 */
class FoodGameLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_food_game_log';
    }

    const STATUS_ADD = 1;
    const STATUS_SUBTRACT = 2;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_sn', 'user_id', 'amount', 'symbol'], 'required'],
            [['user_id', 'status'], 'integer'],
            [['amount'], 'number'],
            [['created_at'], 'safe'],
            [['order_sn'], 'string', 'max' => 50],
            [['symbol'], 'string', 'max' => 30],
            [['remark'], 'string', 'max' => 255],
            [['order_sn'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_sn' => 'Order Sn',
            'user_id' => 'User ID',
            'amount' => 'Amount',
            'symbol' => 'Symbol',
            'created_at' => 'Created At',
            'remark' => 'Remark',
            'status' => 'Status',
        ];
    }

    public function foodGameData($wallet){
        return[
            'log_id' => $this->id,
            'order_sn' => $this->order_sn,
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'symbol' => $this->symbol,
            'wallet_amount' => $wallet,
        ];
    }
}
