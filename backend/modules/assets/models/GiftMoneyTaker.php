<?php

namespace backend\modules\assets\models;

use Yii;
use api\models\User;


/**
 * This is the model class for table "iec_gift_money_taker".
 *
 * @property int $id
 * @property int $taker_id 领红包用户ID
 * @property int $created_at 领取时间
 * @property string $amount 领取金额
 * @property int $gift_money_id 红包ID
 * @property string $reply 领取者回复
 * @property int $reply_time 回复时间
 * @property string $symbol 币种标识
 */
class GiftMoneyTaker extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_gift_money_taker';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['taker_id', 'created_at', 'taken_at', 'gift_money_id', 'reply_time'], 'integer'],
            [['amount'], 'number'],
            [['gift_money_id'], 'required'],
            [['reply'], 'string', 'max' => 255],
            [['symbol'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'taker_id' => 'Taker ID',
            'created_at' => 'Created At',
            'taken_at' => 'taken_at',
            'amount' => 'Amount',
            'gift_money_id' => 'Gift Money ID',
            'reply' => 'Reply',
            'reply_time' => 'Reply Time',
            'symbol' => 'Symbol',
        ];
    }

    /**
     * 关联用户
     *
     * @return void
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'taker_id']);
    }
}
