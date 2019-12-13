<?php

namespace common\models;

use api\models\WalletAddress;
use Yii;

/**
 * This is the model class for table "iec_eth_collect".
 *
 * @property int $id
 * @property string $symbol 币种标识
 * @property int $user_id 用户ID
 * @property string $from_address 钱包地址
 * @property string $created_at 创建时间
 * @property int $status 任务状态
 * @property string $gas_cost 归集GAS
 * @property string $gas_amount 归集金额
 * @property int $gas_time 归集提交时间
 * @property string $gas_tx_hash 归集交易哈希
 * @property int $gas_tx_status 归集交易状态
 * @property string $collect_amount 归集金额
 * @property int $collect_time 归集提交时间
 * @property string $collect_tx_hash 归集交易哈希
 * @property int $collect_tx_status 归集交易状态
 */
class EthCollect extends \yii\db\ActiveRecord
{
    public static $lib_status = [
        0 => '待处理',
        1 => '转入GAS',
        2 => '归集代币',
        3 => '完成',
        4 => '失败',
    ];
    const STATUS_NEW = 0;
    const STATUS_GAS = 1;
    const STATUS_COLLECTING = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_FAILED = 4;

    public static $lib_gas_tx_status = [
        0 => '待处理',
        1 => '处理中',
        2 => '完成',
        3 => '失败',
    ];
    const GAS_TX_STATUS_NEW = 0;
    const GAS_TX_STATUS_PROCESSING = 1;
    const GAS_TX_STATUS_COMPLETED = 2;
    const GAS_TX_STATUS_FAILED = 3;

    public static $lib_collect_tx_status = [
        0 => '待处理',
        1 => '处理中',
        2 => '完成',
        3 => '失败',
    ];
    const COLLECT_TX_STATUS_NEW = 0;
    const COLLECT_TX_STATUS_PROCESSING = 1;
    const COLLECT_TX_STATUS_COMPLETED = 2;
    const COLLECT_TX_STATUS_FAILED = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_eth_collect';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['symbol', 'from_address',], 'required'],
            [['user_id', 'status', 'gas_time', 'gas_tx_status', 'collect_time', 'collect_tx_status', 'created_at'], 'integer'],
            [['gas_cost', 'gas_amount', 'collect_amount'], 'number'],
            [['symbol'], 'string', 'max' => 32],
            [['from_address', 'gas_tx_hash', 'collect_tx_hash'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'symbol' => 'Symbol',
            'user_id' => 'User ID',
            'from_address' => 'From Address',
            'created_at' => 'Created At',
            'status' => 'Status',
            'gas_cost' => 'Gas Cost',
            'gas_amount' => 'Gas Amount',
            'gas_time' => 'Gas Time',
            'gas_tx_hash' => 'Gas Tx Hash',
            'gas_tx_status' => 'Gas Tx Status',
            'collect_amount' => 'Collect Amount',
            'collect_time' => 'Collect Time',
            'collect_tx_hash' => 'Collect Tx Hash',
            'collect_tx_status' => 'Collect Tx Status',
        ];
    }

    /**
     * @param $user_id
     * @param $symbol
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function findForTokenCollect($user_id, $symbol)
    {
        return static::find()->where([
            'user_id' => $user_id,
            'symbol' => $symbol,
        ])->andWhere(['in', 'status', [static::STATUS_NEW, static::STATUS_GAS, static::STATUS_COLLECTING],])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function queryForCollect()
    {
        return static::find()
            ->andWhere(['in', 'status', [static::STATUS_NEW, static::STATUS_GAS, static::STATUS_COLLECTING],])
            ->orderBy(['created_at' => SORT_DESC]);
    }

    public function attributesForCollect($gas_amount, WalletAddress $wallet_address)
    {
        $this->setAttributes([
            'symbol' => $wallet_address->symbol,
            'user_id' => $wallet_address->user_id,
            'from_address' => $wallet_address->address,
            'created_at' => time(),
            'status' => static::STATUS_NEW,
//            'gas_cost' => 0,
            'gas_amount' => $gas_amount, //转入用户钱包的gas金额
//            'gas_time' => 'Gas Time',
//            'gas_tx_hash' => 'Gas Tx Hash',
//            'gas_tx_status' => static::GAS_TX_STATUS_NEW,
//            'collect_amount' => 'Collect Amount',
//            'collect_time' => 'Collect Time',
//            'collect_tx_hash' => 'Collect Tx Hash',
//            'collect_tx_status' => static::COLLECT_TX_STATUS_NEW,
        ]);
    }

    public function setStatusNew()
    {
        $this->status = static::STATUS_NEW;
    }

    public function setStatusGas()
    {
        $this->status = static::STATUS_GAS;
    }

    public function attributesForGas($tx_hash)
    {
        $this->setAttributes([
//            'gas_cost' => 0,
            'gas_time' => time(),
            'gas_tx_hash' => $tx_hash,
            'gas_tx_status' => static::GAS_TX_STATUS_PROCESSING,
        ]);
    }

    /**
     * @param $tx_hash
     * @param $amount
     */
    public function attributesForCollecting($tx_hash, $amount)
    {
        $this->setAttributes([
            'collect_amount' => $amount,
            'collect_time' => time(),
            'collect_tx_hash' => $tx_hash,
            'collect_tx_status' => static::COLLECT_TX_STATUS_PROCESSING,
        ]);
    }

    public function setStatusCollecting()
    {
        $this->status = static::STATUS_COLLECTING;
    }

    public function setStatusCompleted()
    {
        $this->status = static::STATUS_COMPLETED;
    }

    public function setStatusFailed()
    {
        $this->status = static::STATUS_FAILED;
    }

    /**
     * timeout for gas(ETH) push
     * @return bool
     */
    public function getIsGasTimeout()
    {
        return time() - $this->gas_time >= 600;
    }

    /**
     * timeout for token push
     * @return bool
     */
    public function getIsCollectTimeout()
    {
        return time() - $this->gas_time >= 1800;
    }

    public function addGasCost($gas_cost)
    {
        $this->gas_cost += $gas_cost;
    }

    public function clearGasInfo()
    {
        $this->setAttributes([
            'gas_tx_hash' => null,
            'gas_tx_status' => static::GAS_TX_STATUS_FAILED,
        ]);

    }

    public function clearCollectInfo()
    {
        $this->setAttributes([
            'collect_tx_hash' => null,
            'collect_tx_status' => static::COLLECT_TX_STATUS_FAILED,
        ]);

    }

    public function getWalletAddress()
    {
        return $this->hasOne(WalletAddress::className(), ['symbol' => 'symbol', 'user_id' => 'user_id']);
    }
}
