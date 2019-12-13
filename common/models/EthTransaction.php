<?php

namespace common\models;

use api\models\WalletAddress;
use common\modules\ethereum\models\EthereumService;
use Web3\Utils;
use Yii;

/**
 * This is the model class for table "iec_eth_transaction".
 *
 * @property int $id
 * @property int $block_number 区块编号
 * @property string $from_address 转出地址
 * @property string $to_address 接收地址
 * @property int $created_at 交易时间
 * @property string $transaction_hash 交易时间
 * @property string $nonce 同交易生效序列
 * @property string $block_hash 块哈希
 * @property int $transaction_index 交易序列
 * @property string $value 金额
 * @property string $gas 提交GAS
 * @property string $gas_price GAS单价
 * @property int $is_error 是否错误
 * @property int $txreceipt_status 交易接收状态
 * @property string $input 交易附加信息
 * @property string $contract_address 合约地址
 * @property string $cumulative_gas_used 累计GAS开销
 * @property string $gas_used GAS开销
 * @property int $confirmations 确认次数
 * @property int $status 状态
 * @property int $type 类型
 * @property int $contract_to 合约收款地址
 * @property int $contract_value 合约金额
 */
class EthTransaction extends \yii\db\ActiveRecord
{
    public $lib_status = [
        0 => '新导入',
        10 => '已处理',
        20 => '已丢弃',
    ];
    const STATUS_NEW = 0;
    const STATUS_DONE = 10;
    const STATUS_CLOSED = 20;

    public static $lib_type = [
        10 => '以太坊',
        20 => '智能合约',
    ];
    const TYPE_ETHER = 10;
    const TYPE_TOKEN = 20;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_eth_transaction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['block_number', 'created_at', 'transaction_index', 'is_error', 'txreceipt_status', 'confirmations', 'status', 'type'], 'integer'],
            [['from_address', 'to_address', 'contract_to', 'block_hash', 'contract_address', 'transaction_hash'], 'string', 'max' => 255],
            [['nonce', 'value', 'gas', 'gas_price', 'cumulative_gas_used', 'gas_used', 'contract_value',], 'string', 'max' => 64],
            [['input'], 'string',],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'block_number' => 'Block Number',
            'from_address' => 'From Address',
            'to_address' => 'To Address',
            'created_at' => 'Created At',
            'transaction_hash' => 'Transaction Hash',
            'nonce' => 'Nonce',
            'block_hash' => 'Block Hash',
            'transaction_index' => 'Transaction Index',
            'value' => 'Value',
            'gas' => 'Gas',
            'gas_price' => 'Gas Price',
            'is_error' => 'Is Error',
            'txreceipt_status' => 'Txreceipt Status',
            'input' => 'Input',
            'contract_address' => 'Contract Address',
            'cumulative_gas_used' => 'Cumulative Gas Used',
            'gas_used' => 'Gas Used',
            'confirmations' => 'Confirmations',

            'status' => 'Status',
            'contract_to' => 'Contract To',
            'contract_value' => 'Contract Value',
            'type' => 'Type',
        ];
    }

    /**
     * @param $transaction
     * "blockNumber": "6409076",
     * "timeStamp": "1538051860",
     * "hash": "0xcbe26b1e33466db2b9621a31dc68f9e973fe66e9cceb3d550a215cfe5a7067b8",
     * "nonce": "13",
     * "blockHash": "0x971471eeb17166f3e23ba6baa9cf13ab9cadafcbbfb526bb47e2b92fc0880abe",
     * "transactionIndex": "111",
     * "from": "0xf7788b1ac7639b1e6d0b7383eff5cfca986eaf8f",
     * "to": "0xfecc2d3f310bd35f937840b23500ec5fe5e9f39c",
     * "value": "0",
     * "gas": "60000",
     * "gasPrice": "10695312500",
     * "isError": "0",
     * "txreceipt_status": "1",
     * "input": "0xa9059cbb00000000000000000000000040a9853f6407dede4931913168a102dbdebf3103000000000000000000000000000000000000000000000000000000023c346000",
     * "contractAddress": "",
     * "cumulativeGasUsed": "4737968",
     * "gasUsed": "59639",
     * "confirmations": "622"
     * @return bool|EthTransaction|null
     */
    public static function safeSaveEther($transaction)
    {
        $model = static::findOne([
            'transaction_hash' => $transaction['hash'],
        ]);

        if (!$model) {
            $model = new EthTransaction();

            $type = EthereumService::isToken($transaction['input']) ? static::TYPE_TOKEN : static::TYPE_ETHER;
            $status = $type == static::TYPE_ETHER ? static::STATUS_NEW : static::STATUS_CLOSED;
            if (!$transaction['to'] || !$transaction['from'] || $transaction['isError']) { //空转入地址代表可能是创建智能合约记录，关闭状态
                $status = static::STATUS_CLOSED;
            }

            $model->setAttributes([
                'block_number' => $transaction['blockNumber'],
                'from_address' => $transaction['from'],
                'to_address' => $transaction['to'],
                'created_at' => $transaction['timeStamp'],
                'transaction_hash' => $transaction['hash'],
                'nonce' => $transaction['nonce'],
                'block_hash' => $transaction['blockHash'],
                'transaction_index' => $transaction['transactionIndex'],
                'value' => $transaction['value'],
                'gas' => $transaction['gas'],
                'gas_price' => $transaction['gasPrice'],
                'is_error' => $transaction['isError'],
                'txreceipt_status' => $transaction['txreceipt_status'],
                'input' => $transaction['input'],
                'contract_address' => $transaction['contractAddress'],
                'cumulative_gas_used' => $transaction['cumulativeGasUsed'],
                'gas_used' => $transaction['gasUsed'],
                'confirmations' => $transaction['confirmations'],

                'status' => $status,
                'type' => $type,

//                'contract_to' => 'Contract To',
//                'contract_value' => 'Contract Value',
            ]);

            return $model->save();
        } elseif ($model->getIsImportAble() && ($model->nonce != $transaction['nonce']
                || $model->is_error != $transaction['isError']
                || $model->txreceipt_status != $transaction['txreceipt_status']
                || $model->cumulative_gas_used != $transaction['cumulativeGasUsed']
            )
        ) {
            $model->setAttributes([
                'nonce' => $transaction['nonce'],
                'cumulative_gas_used' => $transaction['cumulativeGasUsed'],
                'confirmations' => $transaction['confirmations'],
            ]);

            $model->save();
        }

        return $model;
    }

    /**
     * @param $transaction
     * "blockNumber": "6263629",
     * "timeStamp": "1535967090",
     * "hash": "0xe413fad1e055d84d3d739eb418105a29b87f41b335fb672b75ef1eba3d691365",
     * "nonce": "7",
     * "blockHash": "0x57dfe1484990c6f6e5b93a8d9ec436078a6fc8210fffb2fa7996a359778e1cb0",
     * "from": "0x08792c8c0530edc83ebfb0bcb1b7cc3722785798",
     * "contractAddress": "0x1a508c28b9a84dff3f71c53d2053eb138974468b",
     * "to": "0x482053530d98093c14d07311094e6325b5aa2149",
     * "value": "10000000000000000000000",
     * "tokenName": "",
     * "tokenSymbol": "",
     * "tokenDecimal": "",
     * "transactionIndex": "85",
     * "gas": "57045",
     * "gasPrice": "3000000000",
     * "gasUsed": "38030",
     * "cumulativeGasUsed": "7056703",
     * "input": "0xa9059cbb000000000000000000000000482053530d98093c14d07311094e6325b5aa214900000000000000000000000000000000000000000000021e19e0c9bab2400000",
     * "confirmations": "146449"
     * @return bool|EthTransaction|null
     */
    public static function safeSaveToken($transaction)
    {
        $model = static::findOne([
            'transaction_hash' => $transaction['hash'],
        ]);

        if (!$model) {
            $model = new EthTransaction();
            $model->setAttributes([
                'block_number' => $transaction['blockNumber'],
                'from_address' => $transaction['from'],
                'to_address' => $transaction['to'],
                'created_at' => $transaction['timeStamp'],
                'transaction_hash' => $transaction['hash'],
                'nonce' => $transaction['nonce'],
                'block_hash' => $transaction['blockHash'],
                'transaction_index' => $transaction['transactionIndex'],
                'value' => $transaction['value'],
                'gas' => $transaction['gas'],
                'gas_price' => $transaction['gasPrice'],
//                'is_error' => $transaction['isError'],
//                'txreceipt_status' => $transaction['txreceipt_status'],
                'input' => $transaction['input'],
                'contract_address' => $transaction['contractAddress'],
                'cumulative_gas_used' => $transaction['cumulativeGasUsed'],
                'gas_used' => $transaction['gasUsed'],
                'confirmations' => $transaction['confirmations'],

                'status' => static::STATUS_NEW,
                'type' => static::TYPE_TOKEN,
            ]);

            return $model->save();
        } elseif ($model->getIsImportAble() && ($model->nonce != $transaction['nonce']
                || $model->confirmations != $transaction['confirmations']
                || $model->cumulative_gas_used != $transaction['cumulativeGasUsed']
            )
        ) {
            $model->setAttributes([
                'nonce' => $transaction['nonce'],
                'cumulative_gas_used' => $transaction['cumulativeGasUsed'],
                'confirmations' => $transaction['confirmations'],
            ]);

            $model->save();
        }

        return $model;
    }

    public function getIsImportAble()
    {
        return $this->status == static::STATUS_NEW;
    }

    public function getIsToken()
    {
        return $this->type == static::TYPE_TOKEN;
    }

    public function setImported()
    {
        $this->status = static::STATUS_DONE;
    }

    public function setClosed()
    {
        $this->status = static::STATUS_CLOSED;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function queryImportable()
    {
        return static::find()
            ->where([
                'status' => static::STATUS_NEW,
            ])->orderBy(['id' => SORT_ASC]);
    }

    public function importDeposit()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($this->getIsToken() && $this->contract_address) { //token
                $model_currency_param = CurrencyParam::findByContractAddress($this->contract_address);
                if (!$model_currency_param) {
                    throw new \ErrorException("currency_params.contract_address({$this->contract_address}) not found, stop", 4000);
                }

                $model_currency = $model_currency_param->currency;
                if (!$model_currency) {
                    throw new \ErrorException('currency related to contract_address not found. hardly happened.', 1002);
                }
            } elseif (!$this->getIsToken()) { // ether
                $model_currency = Currency::findCurrencyBySymbol('ETH');
            } else {
                throw new \ErrorException('currency related to this tx not found, closed it.', 4001);
            }

            $decimal = isset($model_currency->params['decimal']) ? $model_currency->params['decimal'] : null;

            $model_wallet_address = WalletAddress::findBySymbolAndAddress($model_currency->symbol, $this->to_address, $model_currency);
            if (false === $model_wallet_address ||
                (isset($model_currency->params['address']) && strtolower($model_currency->params['address']) == strtolower($this->from_address))
            ) { // false 时代表交易记录非充值类交易，关闭状态
                throw new \ErrorException('finding wallet address/generating wallet address/deposit from hot wallet, close this transaction', 4002);
            }

            $model_wallet = $model_wallet_address->wallet;
            if (!$model_wallet) {
                throw new \ErrorException('tx unexpected, closed it.', 1003);
            }

            //deposit
            $model_deposit = Deposit::depositByTransaction(EthereumService::fromWei($this->value, $decimal), $model_wallet, $model_currency, $this, $source = '外部转入');

            $this->setImported();
            if (!$this->save()) {
                throw new \ErrorException('tx.setImported failed', 1010);
            }

            $transaction->commit();
            $result = $model_deposit;
        } catch (\ErrorException $e) {
            $transaction->rollBack();

            if ($e->getCode() >= 4000) {
                $this->setClosed();
                $this->save();
            }

            $this->addError('transaction_hash', $e->getCode());
            $result = false;
        }

        return $result;
    }
}
