<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/7/17
 * Time: 2:12 PM
 */

namespace api\models;


use api\controllers\APIFormat;
use common\models\SendService;
use common\models\Shop;
use common\models\shop\ShopUserLog;
use common\models\shop\ShopWalletLog;
use common\models\shop\UserVip;
use common\models\Wallet;
use yii\base\ErrorException;
use yii\base\Model;

class Posback extends Model
{
    public static $operationArr = [
        10 => '下级消费奖励',
        20 => '付款',
        30 => '商家收款',
    ];

    /*private static $checkUrl = [
        10 => '下级消费奖励',
        20 => '付款',
        30 => '商家收款',
    ];*/

    public static $statusArr = [
        10 => 'pos机',
        20 => '自动售货机'
    ];


    public  $data = '[{
 "orderNo":"订单号",
 "orderType":"订类型号",//POS.BOX.TOTAL
 "ordercontents":[
  {
   "order"："执行顺序",
   "phoneNo"："商家手机号",
   "symbol"："币种",
   "amount"："交易数量",
   "role":"角色类型"//_CLIENT_ORDER 用户 //_SHOP_ORDER 商家 //  _DAILI_ORDER 代理
  },
  {
   "order"："执行顺序",
   "phoneNo"："商家手机号",
   "symbol"："币种",
   "amount"："交易数量",
   "role":"角色类型"
  }
 ]
}]';

    public $contents;
    public $user_id;  //用户_id
    public $sign;     // 签名
    public $telephone; // 电话号码
    public $token; //tokens
    public $symbol;
    public $page;
    public $page_size;

    private $secretKey = 'pos_ant';

    private $userModel;
    private $userWalletModel;
    private $shopModel;
    private $transaction;
    private $checkUrl;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['sign', function($attribute, $param){
                $paramData = $this->signData();
                $sign = $this->_getSortParams($paramData);
                if($sign != $this->sign){
                    throw new \ErrorException('签名错误', 999);
                }
            },'on' => ['userList','userByToken','userOperation','shopEarn']],


            ['token',function(){
                $this->userModel = User::findOne([
                    'access_token' => $this->token
                ]);
                if(empty($this->userModel)){
                    throw new \ErrorException('用户未找到', 999);
                }
            },'on' => ['userByToken']],
            [['user_id','telephone'] , function(){
                $this->userModel = User::find()->orFilterWhere([
                        'id' => $this->user_id
                    ])->orFilterWhere([
                        'username' => $this->telephone
                    ])->one();
                if(empty($this->userModel)){
                    throw new \ErrorException('用户未找到', 999);
                }
            },'on' => ['userByToken']],
            ['symbol' ,function(){
                if(!empty($this->symbol)){
                    $this->userWalletModel = Wallet::findOne([
                        'user_id' => $this->userModel->id,
                        'symbol' => $this->symbol
                    ]);
                }
            },'on' => ['userByToken']],
            ['order_sn',function(){
                $ShopUserLog = ShopWalletLog::findByOrderSn($this->order_sn);
                if(empty($ShopUserLog)){
                    $this->check();
                }else{
                    throw new \ErrorException('该订单已操作', 999);
                }
            },'on' => 'shopEarn'],
            ['contents',function(){
                $this->transaction = json_decode($this->contents,true);
                if(empty($this->transaction)){
                    throw new \ErrorException('contents 转数组失败', 999);
                }
                //$this->check();
            },'on' => 'userOperation']
        ];


    }

    public function scenarios()
    {
        return [
            'userByToken' => ['token','user_id','telephone','sign','symbol'],
            'userOperation' => ['sign','contents'],
            'userList' => ['sign','page','page_size'],
        ];
    }

    /**
     * 用户列表
     * @return [type] [description]
     */
    public function userList(){
        if(!$this->validate()){
            throw new ErrorException(Callback::getModelError($this),'999');
        }
        $user_list = User::posSearch(\Yii::$app->request->post());


        $attributes = null;

        /**
         * @var User $exchangeLog
         */
        foreach ($user_list->getModels() as $i => $exchangeLog) {
            $data = $this->userData($exchangeLog);
            !empty($data)? $attributes[] = $data : false;
        }

        $data = empty($attributes) ? [] : $attributes;

        $result = [
            'list' => $data,
            'summary' => [
                'pages_total' => (string)$user_list->getPagination()->pageCount,
                'count_total' => (string)$user_list->getTotalCount(),
            ],
        ];
        return $result;
    }

    public function userByToken(){
        if($this->validate()){
            $wallet = [
                'symbol' => '',
                'amount' => ''
            ];
            if(!empty($this->userWalletModel)){
                $wallet = [
                  'symbol' => $this->userWalletModel->symbol,
                  'amount' => $this->userWalletModel->getAmountAvailable(),
                ];
            }
            $user = $this->userData($this->userModel);
            return array_merge($user,$wallet);
        }
        throw new \ErrorException(APIFormat::popError($this->getErrors()),999);
    }

    public function userOperation()
    {
        if($this->validate()){
            $transaction = \Yii::$app->db->beginTransaction();
            try{
                $type = $this->operation == 20 ? ShopUserLog::TYPE_WITHDRAW :ShopUserLog::TYPE_DEPOSIT;
                $result = ShopUserLog::addLog($type,$this);
                if($result){
                    $transaction->commit();
                    return true;
                }
                $transaction->rollBack();
                return false;
            }catch (\ErrorException $e){
                $transaction->rollBack();
                throw new \ErrorException($e->getMessage(),$e->getCode());
            }catch (\Exception $exception){
                $transaction->rollBack();
                throw new \ErrorException($exception->getMessage(),$exception->getCode());
            }
        }
        throw new \ErrorException(APIFormat::popError($this->getErrors()),999);
    }

    public function shopEarn(){
        if($this->validate()){
            $transaction = \Yii::$app->db->beginTransaction();
            try{
                $result = ShopWalletLog::addLog(ShopWalletLog::TYPE_DEPOSIT,$this->amount,$this->symbol,$this->userModel->id,$this->shopModel->id,$this->order_sn,$this->status);
                if($result){
                    $transaction->commit();
                    return true;
                }
                $transaction->rollBack();
                return false;
            }catch (\ErrorException $e){
                $transaction->rollBack();
                throw new \ErrorException($e->getMessage(),$e->getCode());
            }catch (\Exception $exception){
                $transaction->rollBack();
                throw new \ErrorException($exception->getMessage(),$exception->getCode());
            }
        }
        throw new \ErrorException(APIFormat::popError($this->getErrors()),999);
    }
    /**
     * @param User $model
     * @return array
     */
    private function userData(User $model){
        return [
            "id" => $model->id,//用户id
            "upid" => $model->upid,//父级id
            "telephone" => $model->username,//电话号码
            "nickname" => $model->nickname,//用户名
            "userid" => $model->userid,//用户随机
            "headimgurl" => $model->headimgurl,//头像
            'shop' => Shop::userIsShop($model->userid),
            'vip' => UserVip::vip($model->id),
            'antToken' => Wallet::antTokenPos($model->id)
        ];
    }

    public function signData(){
        return [
            'user_id' => $this->user_id,
            'sign' => $this->sign,
            'telephone' => $this->telephone,
            'token' => $this->token,
            'contents' => $this->contents,
            'page' => $this->page,
            'page_size' => $this->page_size
        ];
    }

    private function _getSortParams($param = [])
    {
        unset($param['sign']);
        ksort($param);
        $signstr = '';
        if (is_array($param)) {
            foreach ($param as $key => $value) {
                if (!$value) {
                    continue;
                }
                $signstr .= $key . '=' . $value . '&';
            }
            $signstr = rtrim($signstr, '&');
        }
        \Yii::info('signstr:'.json_encode($signstr),'call');
        $sign = base64_encode(hash_hmac('sha1', $signstr, $this->secretKey, true));
        \Yii::info('sign:'.$sign,'call');
        return $sign;
    }

    private function check(){
        $checkParam = [
            'contents' => $this->contents,
            'sign' => $this->_getSortParams(['contents' => $this->contents])
        ];
        $url = static::$checkUrl[$this->operation];
        $result = SendService::curlPost($url,$checkParam);
        if(empty($result)){
            throw new \ErrorException('订单编号不存在', 999);
        }else{
            if($result['code'] == '200'){
                return true;
            }else{
                throw new \ErrorException($result['message'], 999);
            }
        }
    }

    public function operation(){
        if($this->validate()){
            if(is_array($this->transaction)){
                $result = [];
                foreach ($this->transaction as $key => $transaction){
                    try{
                        $operation = new PosOperation();
                        $operation->setAttributes($transaction);
                        $result[] = $operation->operation();
                    }catch (\ErrorException $exception){
                        $transaction['status'] = $exception->getCode();
                        $transaction['msg'] = $exception->getMessage();
                        $transaction['data'] = 'fall';
                        $result[] = $transaction;
                        continue;
                    }catch (\Exception $exception){
                        $transaction['status'] = $exception->getCode();
                        $transaction['msg'] = $exception->getMessage();
                        $transaction['data'] = 'fall';
                        $result[] = $transaction;
                        continue;
                    }
                }
                return $result;
            }
        }
        throw new \ErrorException(APIFormat::popError($this->getErrors()),999);
    }

    /**
     * 获取用户钱包
     * @return mixed
     */
    public function getUserWallet(){
        return $this->userWalletModel;
    }

    /***
     * 获取用户
     * @return mixed
     */
    public function getUser(){
        return $this->userModel;
    }

    /***
     * 获取商家信息
     * @return mixed
     */
    public function getShop(){
        return $this->shopModel;
    }


}