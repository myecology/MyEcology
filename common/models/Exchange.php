<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "iec_exchange".
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $symbol 兑换币种
 * @property int $currency_id 兑换的币种id
 * @property string $amount 兑换金额
 * @property string $e_symbol 要兑换的币种
 * @property int $e_currency_id 要兑换的币种id
 * @property string $price 要兑换币种当前金额
 * @property int $created_at 创建时间
 */
class Exchange extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_exchange';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'symbol', 'currency_id', 'e_symbol', 'e_currency_id'], 'required'],
            [['user_id', 'currency_id', 'e_currency_id', 'created_at'], 'integer'],
            [['amount', 'price'], 'number'],
            [['symbol', 'e_symbol'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'symbol' => 'Symbol',
            'currency_id' => 'Currency ID',
            'amount' => '金额',
            'e_symbol' => 'E Symbol',
            'e_currency_id' => 'E Currency ID',
            'price' => 'Price',
            'created_at' => 'Created At',
        ];
    }


    public function loadForCreateForm($user_id,$symbol,$currency,$e_symbol,$e_currency,$amount)
    {
        $this->setAttributes([
            'user_id' => $user_id,
            'symbol'  => $symbol,
            'currency_id' => $currency->id,
            'amount' => $amount,
            'e_symbol' => $e_symbol,
            'e_currency_id' => $e_currency->id,
            'price' => $e_currency->currencyPrice->price,
            'created_at' => time(),
        ]);
    }
}
