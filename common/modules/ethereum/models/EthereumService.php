<?php
/**
 * Created by PhpStorm.
 * User: cake
 * Date: 2018/9/21
 * Time: 上午10:37
 */

namespace common\modules\ethereum\models;

use BI\BigInteger;
use kornrunner\Keccak;
use Sop\CryptoEncoding\PEM;
use Sop\CryptoTypes\Asymmetric\EC\ECPrivateKey;
use Web3\Contract;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Utils;
use Web3\Web3;

use Web3p\EthereumTx\Transaction;
use yii\base\Model;
use yii\httpclient\Client;

class EthereumService extends Model
{
    private $web3;
    private $etherscan_service;

    private $contract;

    const DEFAULT_DECIMAL = 18;

    public $gas_limit = "0xEA60"; //60000

    /**
     * @return Web3
     * @throws \ErrorException
     */
    public function getWeb3()
    {
        if (is_null($this->web3)) {
            $rpc_addr = \Yii::$app->params['eth_rpc'];
            if (!$rpc_addr) {
                throw new \ErrorException('eth_rpc param not exists', 28000);
            }

            $this->web3 = new Web3(new HttpProvider(new HttpRequestManager($rpc_addr, 10)));
        }

        return $this->web3;
    }

    /**
     * @return EtherscanService
     */
    public function getEtherscan()
    {
        if (is_null($this->etherscan_service)) {
            $this->etherscan_service = new EtherscanService();
        }

        return $this->etherscan_service;
    }

    /**
     * @param $password
     * @return array, 0=>address, 1=>private key
     * @throws \ErrorException
     */
    public function newAccount($password)
    {
        if (!$password) {
            throw new \ErrorException('password is expected', 28001);
        }

        $config = [
            'private_key_type' => OPENSSL_KEYTYPE_EC,
            'curve_name' => 'secp256k1'
        ];
        $res = openssl_pkey_new($config);
        if (!$res) {
            throw new \ErrorException('ERROR: Fail to generate private key. -> ' . openssl_error_string(), 28002);
        }
        // 生成私钥
        openssl_pkey_export($res, $priv_key);
        // 获取公钥
        $key_detail = openssl_pkey_get_details($res);
        $pub_key = $key_detail["key"];
        $priv_pem = PEM::fromString($priv_key);
        // 转换为椭圆曲线私钥格式
        $ec_priv_key = ECPrivateKey::fromPEM($priv_pem);
        // 然后将其转换为ASN1结构
        $ec_priv_seq = $ec_priv_key->toASN1();
        // HEX中的私钥和公钥
        $priv_key_hex = bin2hex($ec_priv_seq->at(1)->asOctetString()->string());
        // - $priv_key_len = strlen($priv_key_hex) / 2;
        $pub_key_hex = bin2hex($ec_priv_seq->at(3)->asTagged()->asExplicit()->asBitString()->string());
        // - $pub_key_len = strlen($pub_key_hex) / 2;
        // 从公钥导出以太坊地址
        // 每个EC公钥始终以0x04开头，
        // 我们需要删除前导0x04才能正确hash它
        $pub_key_hex_2 = substr($pub_key_hex, 2);
        $pub_key_len_2 = strlen($pub_key_hex_2) / 2;
        // Hash
        $hash = Keccak::hash(hex2bin($pub_key_hex_2), 256);
        // 以太坊地址长度为20个字节。 （40个十六进制字符长）
        // 我们只需要最后20个字节作为以太坊地址
        $wallet_address = '0x' . substr($hash, -40);
        $wallet_private_key = $priv_key_hex;

        return [$wallet_address, $wallet_private_key];
    }

    public static function generatePasswd($length = 6)
    {
        $result = null;

        $seed = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $i = 0;
        while ($i++ < $length) {
            $result .= $seed[rand(0, strlen($seed) - 1)];
        }

        return $result;
    }

    /**
     * 0x开始，返回以太坊余额
     * @param $address
     * @return float
     * @throws \ErrorException
     */
    public function getBalance($address, $nature_value = false)
    {
        $balanceInWei = 0;

        $eth = $this->getWeb3()->getEth();
        $eth->getBalance($address, function ($err, $response) use ($eth, &$balanceInWei, $nature_value) {
            if ($err !== null) {
                throw new \ErrorException('web3.eth.getBalance failed', 28003);
            }

            $balanceInWei = (string)$response;
            if ($nature_value) {
//                list($bnq, $bnr) = Utils::fromWei($balanceInWei, 'ether');
//                $balanceInWei = $bnr->value;

                $balanceInWei = bcdiv($balanceInWei, Utils::UNITS['ether'], 18);
            }
        });

        return $balanceInWei;
    }

    public function getNonce($address)
    {
        $result = '';

        $eth = $this->getWeb3()->getEth();
        $eth->getTransactionCount($address, function ($err, $response) use ($address, &$result) {
            if ($err !== null) {
                throw new \ErrorException('web3.eth.getTransactionCount failed', 28008);
            }

            $result = (float)$response->value;
        });

        return $result;
    }

    public function getGasPrice()
    {
        $result = null;
        $this->getWeb3()->getEth()->gasPrice(function ($err, $response) use (&$result) {
            if ($err !== null) {
                throw new \ErrorException('web3.eth.getTransactionCount failed', 28009);
            }

            $result = Utils::fromWei((string)$response, 'gwei');
            $result = (string)(is_array($result) ? $result[0] : $result);
        });

        return $result;
    }

    public function getGasPrices()
    {
        $client = new Client();
        $response = $client->post('https://ethgasstation.info/json/ethgasAPI.json')->send();

        $result = false;
        if ($response->getIsOk()) {
            $result = [
//                $response->data['safeLow'] / 10,
//                $response->data['average'] / 10,
//                $response->data['fast'] / 10,

                'low' => $response->data['safeLow'] / 10,
                'medium' => $response->data['average'] / 10,
                'high' => $response->data['fast'] / 10,
            ];
        }

        return $result;
    }

    /**
     * @return int
     * @throws \ErrorException
     */
    public function getBlockNumber()
    {
        $blockNumber = 0;

        $eth = $this->getWeb3()->getEth();
        $eth->blockNumber(function ($err, $response) use ($eth, &$blockNumber) {
            if ($err !== null) {
                throw new \ErrorException('web3.eth.blockNumber failed', 28004);
            }

            $blockNumber = $response;
        });

        return $blockNumber;
    }

    /**
     * @param $address
     * @param int $block_start
     * @param int $limit
     * @return array
     */
    public function getTransactiosByAddress($address, $block_start = 0, $limit = 200)
    {
        $result = $this->getEtherscan()->getTransactions($address, $block_start, $limit);
        return $result;
    }

    /**
     * @param $tx_hash
     * @return bool|null
     */
    public function getTxDetailByHash($tx_hash)
    {
        $result = $this->getEtherscan()->getTransactionReceipt($tx_hash);
        if ($result && isset($result['status']) && !$result['status']) {
            $result['status'] = 0;
        }

//        $result = $this->getEtherscan()->getTransaction($tx_hash);
        return $result;
    }

    /**
     * @param $address
     * @param int $block_start
     * @param int $limit
     * @return bool|null
     */
    public function getTokenTransactiosByAddress($address, $block_start = 0, $limit = 200)
    {
        $result = $this->getEtherscan()->getTransactionsContract($address, $block_start, $limit);
        return $result;
    }

    /**
     * @param $tx_hash
     * @return |null
     * @throws \ErrorException
     */
    public function getReceipt($tx_hash)
    {
        if (!$tx_hash) {
            return null;
        }

        $this->getWeb3()->getEth()->getTransactionReceipt($tx_hash, function ($err, $r) use (&$result) {
            if ($err !== null) {
                throw new \ErrorException(strval($err), 28016);
            }

            $result = $r;
        });

        return $result;
    }

    /**
     * @param $tx_hash
     * @return |null
     * @throws \ErrorException
     */
    public function getTx($tx_hash)
    {
        if (!$tx_hash) {
            return null;
        }

        $this->getWeb3()->getEth()->getTransactionByHash($tx_hash, function ($err, $r) use (&$result) {
            if ($err !== null) {
                throw new \ErrorException(strval($err), 28016);
            }

            $result = $r;
        });

        return $result;
    }

    public static $lib_tx_status = [
        0 => 'empty',
        1 => 'pending',
        2 => 'failure',
        -1 => 'unknown',
        9 => 'success',
    ];
    const TX_EMPTY = 0;
    const TX_PENDING = 1;
    const TX_FAILURE = 2;
    const TX_UNKNOWN = -1;
    const TX_SUCCESS = 9;

    /**
     * @param $tx_hash
     * @param bool $return_desc
     * @return int|null
     * @throws \ErrorException
     */
    public function queryTxStatus($tx_hash, $return_desc = false)
    {
        $result = null;

        $block_data = $this->getTx($tx_hash);
//        var_dump($block_data, $this->getReceipt($tx_hash), $this->getEtherscan()->getTransaction($tx_hash), $this->getEtherscan()->getTransactionReceipt($tx_hash), str_repeat('#', 20));
        if (is_null($block_data)) {
            $result = $this->getEtherscan()->getTransaction($tx_hash) ? static::TX_PENDING: static::TX_EMPTY; // empty submit, need re-submit
        } elseif ($block_data && is_null($block_data->blockNumber)) {
            $result = static::TX_PENDING; // pending, need wait
        } else {
            $receipt_data = $this->getReceipt($tx_hash);
            if (is_null($receipt_data)) {
                $result = static::TX_PENDING;
            } elseif ($receipt_data && 0 == base_convert($receipt_data->status, 16, 10)) {
                $result = static::TX_FAILURE; // tx failure, need re-submit
            } elseif ($receipt_data && 1 == base_convert($receipt_data->status, 16, 10)) {
                $result = static::TX_SUCCESS; // tx success, need re-submit
            } else {
                $result = static::TX_UNKNOWN;
            }
        }

        $return_desc && $result = static::$lib_tx_status[$result];
        return $result;
    }

    /**
     * @param $input_string
     * @return bool
     */
    public static function isToken($input_string)
    {
        $input_decoded = @base_convert($input_string, 16, 10);
        return "0" !== $input_decoded;
    }

    /**
     * @param $amount
     * @param null $decimal
     * @return string|null
     */
    public static function fromWei($amount, $decimal = null)
    {
        if (!$decimal) {
            $decimal = static::DEFAULT_DECIMAL;
        }

        return bcdiv($amount, pow(10, $decimal), $decimal);
    }

    /**
     * @param $amount
     * @param null $decimal
     * @return string
     */
    public static function toWei($amount, $decimal = null)
    {
        if (!$decimal) {
            $decimal = static::DEFAULT_DECIMAL;
        }

        return bcmul($amount , pow(10, $decimal));
    }

    /**
     * @param string $abi
     * @return Contract
     * @throws \ErrorException
     */
    public function initContract($abi)
    {
        $contract = new Contract($this->getWeb3()->getProvider(), $abi);
        $this->contract = $contract;

        return $this->contract;
    }

    /**
     * @param string $address
     * @param string $contract_address
     * @param string $abi
     * @param int $wei
     * @return string
     * @throws \ErrorException
     */
    public function getBalanceToken($address, $contract_address, $abi, $wei = null)
    {
        $result = null;

        $contract = $this->initContract($abi);
        if (!$contract) {
            throw new \ErrorException('contract instance getting failed', 28005);
        }

        $self = &$this;
        $contract->at($contract_address)->call('balanceOf', $address, [
            'from' => $address
        ], function ($err, $result_inside) use ($contract, &$result, $wei, $self) {
            if ($err !== null) {
                throw new \ErrorException(strval($err), 28006);
            }

            if (!$result_inside || !is_array($result_inside)) {
                throw new \ErrorException(strval($err), 28007);
            }

            $result = is_array($result_inside) ? array_values($result_inside)[0]: $result_inside;
            $result = is_object($result) ? strval($result) : 0;
            if (!is_null($wei)) {
                $result = $self->fromWei($result, $wei);
            }
        });

        return (string)$result;
    }

    public function sendEther($from, $from_key, $to, $amount, $gasPrice = null)
    {
        $nonce = $this->getNonce($from);
        $nonce = '0x' . ($nonce ? Utils::toHex($nonce) : 0);

        if (is_null($gasPrice)) {
            $gasPrice = $this->getGasPrices();
            if (is_array($gasPrice)) {
                $gasPrice = $gasPrice['medium'];
            } else {
                $gasPrice = $this->getGasPrice();
            }
        }

        $tx = [
            "to" => $to,
            "value" => '0x' . Utils::toWei(strval($amount), 'ether')->toHex(),
            "gas" => $this->gas_limit,
            "gasPrice" => '0x' . Utils::toWei(strval($gasPrice), 'gwei')->toHex(), // converts the gwei price to wei
            "nonce" => $nonce,
            "chainId" => 1,
        ];

        return $this->_sendRawTransaction($tx, $from_key);
    }

    /**
     * @param $from
     * @param $from_key
     * @param $to
     * @param $amount
     * @param $contract_address
     * @param $token_decimal
     * @param null $gasPrice
     * @return null
     */
    public function sendToken($from, $from_key,
                              $to,
                              $amount,
                              $contract_address, $token_decimal,
                              $gasPrice = null
    )
    {
        $func_map = [
            'set' => '60fe47b1',
            'transfer' => 'a9059cbb',
        ];

        $amount_in_wei = static::toWei($amount, $token_decimal);
        $amount_hexed = Utils::toBn($amount_in_wei)->toHex();

        $nonce = $this->getNonce($from);
        $nonce = '0x' . ($nonce ? Utils::toHex($nonce) : 0);

        $data = '0x' . $func_map['transfer'];
        $data .= sprintf('%064s', Utils::toBn($to)->toHex());
        $data .= sprintf('%064s', $amount_hexed);

        $tx = [
            "to" => $contract_address,
            "value" => 0x0,
            "gasPrice" => '0x' . Utils::toWei(strval($gasPrice ?: $this->getGasPrice()), 'gwei')->toHex(), // converts the gwei price to wei
            "nonce" => $nonce,
            "chainId" => 1,
            'gasLimit' => $this->gas_limit,
            'data' => $data
        ];

        return $this->_sendRawTransaction($tx, $from_key);
    }

    protected function _sendRawTransaction(array $tx, $key)
    {
        $transaction = new Transaction($tx);
        $transaction->sign($key);
        $serializedTransaction = $transaction->serialize();

        $transactionId = null;
        $this->getWeb3()->getEth()->sendRawTransaction('0x' . $serializedTransaction->toString('hex'), function ($err, $response) use (&$transactionId) {
            if (!is_null($err)) {
                throw new \ErrorException(strval($err), 28008);
            }

            $transactionId = $response;
        });

        return $transactionId;
    }

    /**
     * @param $tx_hash
     * @return bool
     */
    public static function txHashValidEth($tx_hash)
    {
        substr($tx_hash, 0, 2) != '0x' && $tx_hash = '0x' . $tx_hash;
        return (bool)preg_match('#^0x[0-9a-zA-Z]{64}$#i', $tx_hash);
    }

    /**
     * @param $contract_address
     * @return |null
     * @throws \ErrorException
     */
    public function getDecimal($contract_address)
    {
        $result = null;
        if (!$this->contract) {
            throw new \ErrorException('contract instance getting failed', 28005);
        }

        $contract = & $this->contract;
        $self = &$this;
        $contract->at($contract_address)->call('decimals', function ($err, $result_inside) use ($contract, &$result, $self) {
            if ($err !== null) {
                throw new \ErrorException(strval($err), 28006);
            }

            if (!$result_inside || !is_array($result_inside)) {
                throw new \ErrorException(strval($err), 28007);
            }

            $result = is_array($result_inside) && isset($result_inside[0]) ? strval($result_inside[0]): null;
        });

        return $result;
    }

    /**
     * @param $contract_address
     * @return |null
     * @throws \ErrorException
     */
    public function getName($contract_address)
    {
        $result = null;
        if (!$this->contract) {
            throw new \ErrorException('contract instance getting failed', 28005);
        }

        $contract = & $this->contract;
        $self = &$this;
        $contract->at($contract_address)->call('name', function ($err, $result_inside) use ($contract, &$result, $self) {
            if ($err !== null) {
                throw new \ErrorException(strval($err), 28006);
            }

            if (!$result_inside || !is_array($result_inside)) {
                throw new \ErrorException(strval($err), 28007);
            }

            $result = is_array($result_inside) && isset($result_inside[0]) ? strval($result_inside[0]): null;
        });

        return $result;
    }

    /**
     * @param $contract_address
     * @return |null
     * @throws \ErrorException
     */
    public function getSymbol($contract_address)
    {
        $result = null;
        if (!$this->contract) {
            throw new \ErrorException('contract instance getting failed', 28005);
        }

        $contract = & $this->contract;
        $self = &$this;
        $contract->at($contract_address)->call('symbol', function ($err, $result_inside) use ($contract, &$result, $self) {
            if ($err !== null) {
                throw new \ErrorException(strval($err), 28006);
            }

            if (!$result_inside || !is_array($result_inside)) {
                throw new \ErrorException(strval($err), 28007);
            }

            $result = is_array($result_inside) && isset($result_inside[0]) ? strval($result_inside[0]): $result_inside;
        });

        return $result;
    }

    /**
     * @param $contract_address
     * @param $abi
     * @return array|bool
     * @throws \ErrorException
     */
    public function getERC20Info($contract_address, $abi)
    {
        if(is_null(json_decode($abi, 1))){
            throw new \ErrorException('invalid ABI input', 28010);
        }

        $this->initContract($abi);
        try{
            $result = [
                'name' => $this->getName($contract_address),
                'decimal' => $this->getDecimal($contract_address),
                'symbol' => $this->getSymbol($contract_address),
            ];
        }catch (\ErrorException $e){
            $result = false;
        }

        return $result;
    }

}