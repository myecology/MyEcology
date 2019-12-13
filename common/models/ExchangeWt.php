<?php

namespace common\models;

use Yii;
use api\models\User;

/**
 * This is the model class for table "iec_exchange_wt1918".
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $e_symbol 兑换币种
 * @property string $amount 兑换数量
 * @property string $wt_number wt数量
 * @property string $create_time 兑换时间
 * @property string $symbol_price 币种价格
 * @property string $fee 手续费
 */
class ExchangeWt extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_exchange_wt1918';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'amount', 'wt_number', 'symbol_price', 'fee'], 'required'],
            [['user_id'], 'integer'],
            [['wt_number', 'symbol_price', 'fee', 'amount'], 'number'],
            [['create_time','amount'], 'safe'],
            [['e_symbol'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户id',
            'e_symbol' => '兑换币种',
            'amount' => '实际金额',
            'wt_number' => 'wt数量',
            'create_time' => '兑换时间',
            'symbol_price' => '币种价格',
            'fee' => '手续费',
        ];
    }

    /**
     * 获取用户信息
     * @return [type] [description]
     */
    public function getUser(){
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
