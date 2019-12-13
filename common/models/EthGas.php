<?php

namespace common\models;

use common\modules\ethereum\models\EthereumService;
use Web3\Utils;
use Yii;

/**
 * This is the model class for table "iec_eth_gas".
 *
 * @property int $id
 * @property string $tx_hash 交易哈希
 * @property int $created_at 创建时间
 * @property int $type 类别
 * @property string $business_sn 业务单号
 * @property string $amount 金额
 * @property string $gas_used 实际使用燃油数
 * @property string $gas_price GAS单价
 * @property string $desc 描述
 */
class EthGas extends \yii\db\ActiveRecord
{
    public static $lib_type = [
        10 => '推送归集GAS',
        20 => '归集GAS',
        30 => '提现GAS',
    ];
    const TYPE_COLLECT_PUSH = 10;
    const TYPE_COLLECT = 20;
    const TYPE_WITHDRAW = 30;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_eth_gas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'type'], 'integer'],
            [['amount',], 'number'],
            [['tx_hash', 'desc'], 'string', 'max' => 255],
            [['business_sn', 'gas_used', 'gas_price',], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tx_hash' => 'Tx Hash',
            'created_at' => 'Created At',
            'type' => 'Type',
            'business_sn' => 'Business Sn',
            'amount' => 'Amount',
            'gas_used' => 'Gas Used',
            'gas_price' => 'Gas Price',
            'desc' => 'Desc',
        ];
    }

    /**
     * @param $gas_price
     * @param $gas_used
     * @param $tx_hash
     * @param $type
     * @param $business_sn
     * @param string $desc
     * @return EthGas
     */
    public static function ethGasLog($gas_price, $gas_used, $tx_hash, $type, $business_sn, $desc = '')
    {
        $_gas_price = strval(Utils::toBn($gas_price));
        $_gas_used = strval(Utils::toBn($gas_used));
        $amount = EthereumService::fromWei($_gas_price * $_gas_used, 18);

        $model = new EthGas();
        $model->setAttributes([
            'tx_hash' => $tx_hash,
            'created_at' => time(),
            'type' => $type,
            'business_sn' => strval($business_sn),
            'amount' => $amount,
            'gas_used' => $gas_used,
            'gas_price' => $gas_price,
            'desc' => $desc ?: static::$lib_type[$type] . ":{$amount} ETH",
        ]);

        $model->save();
        return $model;
    }
}
