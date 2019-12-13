<?php

namespace backend\models;

use Yii;
use api\models\User;

/**
 * This is the model class for table "iec_transfer".
 *
 * @property int $id
 * @property int $sender_id 转出人ID
 * @property int $receiver_id 转入人ID
 * @property string $symbol 币种标识
 * @property int $currency_id 币种ID
 * @property string $amount 金额
 * @property int $created_at 创建时间
 * @property int $taken_at 接收时间
 * @property int $status 状态
 * @property string $description 描述
 */
class Transfer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_transfer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sender_id', 'receiver_id', 'currency_id', 'created_at', 'taken_at', 'status'], 'integer'],
            [['symbol', 'currency_id', 'description'], 'required'],
            [['amount'], 'number'],
            [['symbol'], 'string', 'max' => 32],
            [['description'], 'string', 'max' => 255],
        ];
    }

    public static $lib_status = [
        10 => '已提交',
        20 => '已接收',
        30 => '已超时',
    ];

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sender_id' => '转出id',
            'receiver_id' => '接收id',
            'symbol' => '币种标识',
            'currency_id' => '币种ID',
            'amount' => '金额',
            'created_at' => '创建时间',
            'taken_at' => '接收时间',
            'status' => '状态',
            'description' => '描述',
        ];
    }

    /**
     * 获取用户信息
     * @return [type] [description]
     */
    public function getUser(){
        return $this->hasOne(User::className(), ['id' => 'sender_id']);
    }

    /**
     * 获取用户信息
     * @return [type] [description]
     */
    public function getReceiver(){
        return $this->hasOne(User::className(), ['id' => 'receiver_id']);
    }
}
