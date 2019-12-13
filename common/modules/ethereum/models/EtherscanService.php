<?php
/**
 * Created by PhpStorm.
 * User: cake
 * Date: 2018/9/27
 * Time: 上午9:54
 */

namespace common\modules\ethereum\models;


use yii\base\Model;
use yii\httpclient\Client;

class EtherscanService extends Model
{
    public $url = 'https://api.etherscan.io/api';
    public $apikey = 'PH1MI7ARFPUYZMKIB8AZW2XEW7YQE6H4VZ';

    public $header = null;
    public $response = null;
    public $errors = [];
    protected $client = null;

    public function getClient()
    {
        if (is_null($this->client)) {
            $this->client = new Client();
        }

        return $this->client;
    }

    public function getHeader()
    {
        if (is_null($this->header)) {
            $this->header = [
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Safari/605.1.15',
                'Host' => 'api.etherscan.io',
            ];
        }

        return $this->header;
    }

    /**
     * @return bool
     */
    public function getIsError()
    {
        return !empty($this->errors);
    }

    public function reset()
    {
    }

    /**
     * @param $data
     * @param bool $status_check
     * @return bool|null
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    protected function request($data, $status_check = false)
    {
        isset($data['apikey']) || $data['apikey'] = $this->apikey;

        $result = null;
        try {
            $client = $this->getClient();
            $this->reset();

            $this->response = $client->createRequest()
                ->setMethod('POST')
                ->setUrl($this->url)
                ->setData($data)
                ->setHeaders($this->header)
                ->send();

            if (!$this->response->getIsOk()) {
                throw new \ErrorException('request failed', 1000);
            }

            if ($status_check && 1 != $this->response->data['status']) {
                throw new \ErrorException(var_export($this->response->data, 1), 1001);
            }

            if (!isset($this->response->data['result'])) {
                throw new \ErrorException('expecting result in response', 1002);
            }

            $result = $this->response->data['result'];
        } catch (\ErrorException $e) {
            $this->errors[] = "#{$e->getCode()}: {$e->getMessage()}";
            $result = false;
        }

        return $result;
    }

    public function getTransactions($address, $block_number_start = 0, $offset = 200)
    {
        $result = $this->request([
            'module' => 'account',
            'action' => 'txlist',
            'address' => $address,
            'startblock' => $block_number_start,
            'endblock' => 'latest',
            'sort' => 'desc',
            'page' => '1',
            'offset' => $offset,
        ]);

        return $result ?: $this->errors;
    }

    public function getTransactionsContract($address, $block_number_start = 0, $offset = 200)
    {
        return $this->request([
            'module' => 'account',
            'action' => 'tokentx',
            'address' => $address,
            'startblock' => $block_number_start,
            'endblock' => 'latest',
            'sort' => 'desc',
            'page' => '1',
            'offset' => $offset,
        ]);
    }

    public function getTransaction($tx_hash)
    {
        return $this->request([
            'module' => 'proxy',
            'action' => 'eth_getTransactionByHash',
            'txhash' => $tx_hash,
        ]);
    }

    public function getTransactionReceipt($tx_hash)
    {
        return $this->request([
            'module' => 'transaction',
            'action' => 'gettxreceiptstatus',
            'txhash' => $tx_hash,
        ]);
    }


    /**
     * @sample https://api.etherscan.io/api?module=contract&action=getabi&address=0xBB9bc244D798123fDe783fCc1C72d3Bb8C189413&apikey=YourApiKeyToken
     *
     * @param $contract_address
     * @return bool|null
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function getAbi($contract_address)
    {
        return $this->request([
            'module'=> 'contract',
            'action'=> 'getabi',
            'address'=> $contract_address,
        ]);
    }

    /**
     * @sample https://api.etherscan.io/api?module=contract&action=getsourcecode&address=0xBB9bc244D798123fDe783fCc1C72d3Bb8C189413&apikey=YourApiKeyToken
     *
     * @param $contract_address
     * @return bool|null
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function getSourceCode($contract_address)
    {
        $response = $this->request([
            'module'=> 'contract',
            'action'=> 'getsourcecode',
            'address'=> $contract_address,
        ]);

        return is_array($response) && count($response) && isset($response[0]) ? $response[0] : $response;
    }

    /**
     * @param $contract_address
     * @return bool|null
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function getContractParam($contract_address){
        return $this->getSourceCode($contract_address);
    }

}