<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/8/19
 * Time: 10:17 AM
 */

namespace api\models;


use api\controllers\APIFormat;
use backend\models\Setting;
use common\models\FoodGameLog;
use common\models\SendService;
use common\models\Wallet;
use common\models\WalletLog;
use yii\base\ErrorException;
use yii\base\Model;
use yii\db\Exception;

class FoodGame extends Model
{
    public $token; //token
    public $sign; // 签名
    public $user_id; // user_id
    public $symbol;  // 币种信息
    public $amount;  // 数量
    public $status;  // 操作状态
    public $order_sn; //操作订单编号
    public $message; //回掉信息
    public $remark; // 备注
    public $page;
    public $page_size;


    private $operationAmount;
    private $operationStatus;

    private $checkUrl = 'http://demo.laih5.cn/h5/farm/index/outside/VerifyOrders';

    private static $statusArr = [
        'add' => '增加',
        'subtract' => '减少'
    ];

    const STATUS_ADD = 'add';
    const STATUS_SUBTRACT = 'subtract';
    /**
     * @var User $userModel
     */
    private $userModel;

    /***
     * @var Wallet $walletModel;
     */
    private $walletModel;


    private $secretKey = 'game_mfcc';

    public function rules(){
        return[
            ['sign',function(){
                $paramData = $this->signData();
                $sign = $this->_getSortParams($paramData);
                if($sign != $this->sign){
                    throw new \ErrorException('签名错误', 999);
                }
            },'on' =>['operation','getUser','getFriendList','message']],
            [['token','sign'],'required','on'=>['getUser']],
            [['user_id','sign'],'required','on'=>['getFriendList']],
            [['user_id','sign','message'],'required','on'=>['message']],
            ['token',function(){
                $this->userModel = User::findOne([
                    'access_token' => $this->token
                ]);
                if(empty($this->userModel)){
                    throw new ErrorException('认证失败请重新登录',777);
                }
            },'on'=>'getUser'],

            [['user_id','sign','amount','status','order_sn'],'required','on'=>['operation']],
            ['user_id',function(){
                $this->userModel = User::findOne($this->user_id);
                if(empty($this->userModel)){
                    throw new ErrorException('认证失败请重新登录',777);
                }
            },'on' => ['getFriendList','operation','message']],
            ['symbol',function(){
                if(empty($this->symbol)){
                    $this->symbol = 'MFCC';
                }
                $foodSymbol = Setting::read('FoodGameSymbol','foodgame');
                $foodSymbols = explode(',',$foodSymbol);
                if(!in_array($foodSymbol,$foodSymbols)){
                    throw new ErrorException('暂未开通此币种操作',555);
                }

                $this->walletModel = Wallet::createWallet($this->userModel->id,$this->symbol);
                if(empty($this->walletModel)){
                    throw new ErrorException('获取钱包错误',555);
                }
            },'on' => ['operation','getUser']],
            ['amount',function(){
                if(abs($this->amount) == 0){
                    throw new ErrorException('操作金额不能为0',999);
                }
            },'on' => 'operation'],
            ['status',function(){
                if(!isset(static::$statusArr[$this->status])){
                    throw new ErrorException('操作选项不正确',999);
                }
                switch($this->status){
                    case static::STATUS_ADD:
                        $this->operationAmount = abs($this->amount);
                        $this->operationStatus = FoodGameLog::STATUS_ADD;
                        break;
                    case static::STATUS_SUBTRACT:
                        $this->operationAmount = -abs($this->amount);
                        $this->operationStatus = FoodGameLog::STATUS_SUBTRACT;
                        break;
                    default:
                        throw new ErrorException('操作选项不正确',999);
                        break;
                }
            }],
            ['order_sn',function(){
                $orderLog = FoodGameLog::findOne([
                    'order_sn' => 'order_sn'
                ]);
                if(!empty($orderLog)){
                    throw new ErrorException('改订单已操作',999);
                }
                $this->checkOrderSn();
            },'on' => 'operation'],

            [['page','page_size'],function(){
                if(empty($this->page_size) || $this->page_size <= 0){
                    $this->page_size = 200;
                }
                if(empty($this->page) || $this->page <= 0){
                    $this->page_size = 1;
                }
            },'on' => ['getFriendList']],
            ['message','required','on' => 'message']
        ];
    }


    public function scenarios()
    {
        return [
            'getUser' => ['token','sign','symbol'],
            'message' => ['user_id','message','sign'],
            'getFriendList' => ['user_id','sign','page','page_size'],
            'operation' => ['user_id','sign', 'amount', 'order_sn','status','symbol','remark'],

        ];
    }
    public function getUserData(){
        if($this->validate()){
            $walletArr = [];
            if(!empty($this->walletModel)){
                $walletArr = [
                    'symbol' => $this->walletModel->symbol,
                    'amount' => $this->walletModel->amount,
                ];
            }
            return array_merge([
                'id' => $this->userModel->id,
                'nickname' => $this->userModel->nickname,
                'headimgurl' => $this->userModel->headimgurl,
                'telephone' => $this->userModel->username,
            ],$walletArr);
        }
        throw new ErrorException(APIFormat::popError($this->getErrors()),999);
    }


    public function operation(){
        if($this->validate()){
            $transaction = \Yii::$app->db->beginTransaction();
            try{
                $foodGamemodel = new FoodGameLog();
                $data = $this->signData();
                $data['amount'] = $this->operationAmount;
                $data['status'] = $this->operationStatus;
                $foodGamemodel->setAttributes($data);
                if(!$foodGamemodel->save()){
                    throw new ErrorException(APIFormat::popError($foodGamemodel->getErrors()),999);
                }
                $amount = abs($this->amount);
                switch($this->status){
                    case static::STATUS_ADD:
                        $walletFlag = $this->walletModel->earnMoney($amount,WalletLog::TYPE_FOOD_GAME_ADD,(string)$foodGamemodel->id);
                        if(!$walletFlag){
                            throw new ErrorException('加钱操作失败',999);
                        }
                        break;
                    case static::STATUS_SUBTRACT:
                        $walletFlag = $this->walletModel->spendMoney($amount,WalletLog::TYPE_FOOD_GAME_SUBTRACT,(string)$foodGamemodel->id);
                        if(!$walletFlag){
                            throw new ErrorException('用户加钱失败',999);
                        }
                        break;
                    default:
                        throw new ErrorException('操作选项不正确',999);
                        break;
                }
                //给融云发送消息

                //推送到消息中心


                $transaction->commit();
                return $foodGamemodel->foodGameData($this->walletModel->amount);

            }catch (ErrorException $exception){
                $transaction->rollBack();
                throw new ErrorException($exception->getMessage(),$exception->getCode());

            }catch (StaleObjectException $staleObjectException){
                $transaction->rollBack();
                $this->walletModel = Wallet::createWallet($this->userModel->id,$this->symbol);
                $this->operation();
            }catch (Exception $exception){
                throw new ErrorException($exception->getMessage(),$exception->getCode());
            }
        }
        throw new ErrorException(APIFormat::popError($this->getErrors()),999);
    }

    public function getUserList(){
        if($this->validate()){
//            $users = UserFriend::userList($this->userModel->userid,$this->signData());
//
            $result = UserFriend::find()->where([
                'iec_user_friend.in_userid' => $this->userModel->userid
            ])->leftJoin('iec_user','iec_user.userid = iec_user_friend.to_userid')
                ->select('iec_user.id')->column();
                //->select('iec_user.id')->asArray()->all();

            return $result;
        }
        throw new ErrorException(APIFormat::popError($this->getErrors()),999);
    }

    private function userFriendData(User $user){
        if(empty($user)){
            return [];
        }
        return [
            'id' => $user->id,
            'nickname' => $user->nickname,
            'headimgurl' => $user->headimgurl,
        ];
    }


    public function message(){
        if($this->validate()){
            //给融云发送消息
            return true;
        }
        throw new ErrorException(APIFormat::popError($this->getErrors()),999);
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
        \Yii::info('signstr:'.json_encode($signstr),'foodgame');
        $sign = base64_encode(hash_hmac('sha1', $signstr, $this->secretKey, true));
        \Yii::info('sign:'.$sign,'foodgame');
        return $sign;
    }

    public function signData(){
        return [
            'amount' => $this->amount,
            'sign' => $this->sign,
            'token' => $this->token,
            'user_id' => $this->user_id,
            'symbol' => $this->symbol,
            'status' => $this->status,
            'order_sn' => $this->order_sn,
            'message' => $this->message,
            'remark' => $this->remark,
            'page' => $this->page,
            'page_size' => $this->page_size,
        ];
    }
    public function checkOrderSn(){
        $checkData = [
            'order_sn' => $this->order_sn,
            'time_stamp' => time(),
        ];
        $sign = $this->_getSortParams($checkData);
        $checkData['sign'] = $sign;
        \Yii::info('checkdata:'.json_encode($checkData),'foodgame');
        //验证操作;
        $result = SendService::curlPost($this->checkUrl,$checkData);
        if(empty($result)){
            throw new ErrorException('订单存在异常',999);
        }
        if($result['code'] != 200){
            throw new ErrorException($result['msg'],$result['code']);
        }

    }
}