<?php

namespace common\models\shop;

use api\controllers\APIFormat;
use api\models\Posback;
use api\models\PosOperation;
use api\models\User;
use common\models\Message;
use common\models\Shop;
use common\models\Wallet;
use common\models\WalletLog;
use Yii;

/**
 * This is the model class for table "iec_shop_user_log".
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $type 操作类型
 * @property string $amount 操作金额
 * @property int $shop_id 商家id
 * @property int $status 状态
 * @property string $remake 备注
 * @property string $symbol 备注
 * @property string $created_at 创建时间
 * @property string $order_sn order_sn
 */
class ShopUserLog extends \yii\db\ActiveRecord
{
    public static $typeArr = [
        10 => '下级消费奖励',
        20 => '付款',
    ];

    public static $statusArr = [
        10 => 'pos机',
        20 => '售货机',
        30 => '返利',
        40 => '销售POS机'
    ];

    const TYPE_DEPOSIT = 10;
    const TYPE_WITHDRAW = 20;

    const STATUS_POS = 10;
    const STATUS_SHOP = 20;
    const STATUS_REWARD = 30;
    const STATUS_POS_SALE = 40;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_shop_user_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'amount', 'status', 'remake', 'order_sn'], 'required'],
            [['user_id', 'type', 'shop_id', 'status'], 'integer'],
            [['amount'], 'number'],
            [['created_at'], 'safe'],
            [['remake', 'order_sn'], 'string', 'max' => 255],
            [['symbol'], 'string', 'max' => 30],
            [['order_sn'], 'unique'],
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
            'type' => 'Type',
            'amount' => 'Amount',
            'shop_id' => 'Shop ID',
            'status' => 'Status',
            'remake' => 'Remake',
            'created_at' => 'Created At',
            'order_sn' => 'Order Sn',
        ];
    }


    public function getWallet(){
        return $this->hasOne(Wallet::className(),['user_id'=>$this->user_id,'symbol'=> $this->symbol]);
    }

    public static function findByOrderSn($order_sn,$user_id){
        return static::findOne([
            'order_sn' => $order_sn,
            'user_id'  =>$user_id
        ]);
    }

    /**
     * @param $type
     * @param Wallet $userWallet
     * @param $shop
     * @param PosOperation $operation
     * @param User $user
     * @param $amount
     * @param $status
     * @return bool
     * @throws \ErrorException
     * @throws \yii\base\ErrorException
     */
    public static function addLog($type,Wallet $userWallet,$shop,PosOperation $operation,User $user,$amount,$status){
        if($type == static::TYPE_WITHDRAW){
            $remake = static::$typeArr[$type].$amount.$userWallet->symbol;
            $balance_before =  WalletLog::TYPE_BUY_GOODS;
        }else{
            $remake = static::$typeArr[$type].$amount.$userWallet->symbol;
            $balance_before =  WalletLog::TYPE_BUY_REWARD;
        }
        $logData = [
            'user_id' => $userWallet->user_id,
            'type' => $type,
            'amount' => $amount,
            'shop_id' => $shop,
            'status' => $status,
            'remake' => $remake,
            'symbol' => $userWallet->symbol,
            'order_sn' => $operation->orderNo,
        ];
        $log = new static();
        $log->setAttributes($logData);
        if($log->save()){
            $operationAmount = abs($amount);
            if($amount < 0){
                $flag = $userWallet->spendMoney($operationAmount,$balance_before,(string)$log->id);
                Message::addMessage(Message::TYPE_BUY_GOODS,$user,$userWallet->symbol,$amount,$log);
            }elseif($amount > 0){
                $flag =  $userWallet->earnMoney($operationAmount,$balance_before,(string)$log->id);
                Message::addMessage(Message::TYPE_BUY_REWARD,$user,$userWallet->symbol,$amount,$log);
            }else{
                throw new \ErrorException('操作金额不能为0', 999);
            }
            if(!$flag){
                throw new \ErrorException('用户账户操作失败', 999);
            }
            return true;
        }else{
            throw new \ErrorException(APIFormat::popError($log->getErrors()), 999);
        }
    }

}
