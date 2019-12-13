<?php

namespace common\models;

use common\models\bank\Order;
use common\models\Wallet;
use Yii;
use backend\models\Setting;
use common\models\WalletLog;
use yii\behaviors\TimestampBehavior;
use yii\db\StaleObjectException;


/**
 * This is the model class for table "iec_supernode_profit".
 *
 * @property int $id
 * @property int $uid 用户ID
 * @property string $title 标题
 * @property int $node 普通节点
 * @property int $type 收益类型
 * @property int $hasid 关联ID
 * @property int $in_uid userID
 * @property string $amount 数量
 * @property string $symbol 数量
 * @property int $status 状态
 * @property int $created_at 创建时间
 */
class SupernodeProfit extends \yii\db\ActiveRecord
{
    const TYPE_BANK_PRODUCT = 10;       //  
    const TYPE_BANK_EXPECT = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_supernode_profit';
    }

    /**
     * 模型行为
     * @return [type] [description]
     */
    public function behaviors()
    {
        return [
            //创建时间
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'in_uid', 'title', 'hasid', 'amount'], 'required'],
            [['status', 'node'], 'default', 'value' => 0],
            [['uid', 'node', 'type', 'hasid', 'status'], 'integer'],
            [['amount'], 'number'],
            ['symbol', 'default', 'value' => 'ANT'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'in_uid' => 'in_uid',
            'title' => 'Title',
            'node' => 'Node',
            'symbol' => 'Symbol',
            'type' => 'Type',
            'hasid' => 'Hasid',
            'amount' => 'Amount',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    /**
     * 添加收益
     *
     * @param [type] $type
     * @param [type] $model
     * @return void
     */
    public static function addProfit($amount, $symbol, $type, Order $model)
    {
        var_dump('获取上级以及奖励提成'.microtime());
        $fee = explode(',', Setting::read('supernode_top_fee'));
        $minimum_revenue_assets = floatval(Setting::read('minimum_revenue_assets')); // 上级用户最低资产
        $supernodeUid = explode(',', $model->supernode_uid);
        var_dump('获取上级以及奖励提成end'.microtime());
        switch ($type) {
            case static::TYPE_BANK_PRODUCT:
                $feeAmount = 0;
                foreach ($fee as $key => $val) {
                    $user_id = $supernodeUid[$key];
                    if ($user_id == 0) {
                        break;
                    }
                    var_dump('检查是否发送奖励'.microtime());
                    /*$flag = static::check($model->id,$user_id);
                    if(!$flag){
                        continue;
                    }*/
                    var_dump('检查是否发送奖励end'.microtime());
                    //获取上级收益人钱包总值
                    var_dump('获取用户钱包订单总价值'.microtime());
                    $value_total = static::userAmount($user_id);
                    $feeAmount = $feeAmount ? ($feeAmount * $val / 100) : ($amount * $val / 100);
                    if($value_total < $minimum_revenue_assets){
                        continue;
                    }
                    var_dump('获取用户钱包订单总价值end'.microtime());
                    var_dump('用户id：'.$user_id.'-'.$value_total);
                    var_dump('添加记录'.microtime());
                    $supernodeProfitModel = new static();
                    $supernodeProfitModel->title = '令牌理财';
                    $supernodeProfitModel->type = static::TYPE_BANK_PRODUCT;
                    $supernodeProfitModel->uid = $user_id;
                    $supernodeProfitModel->in_uid = $model->uid;
                    $supernodeProfitModel->symbol = $symbol;
                    $supernodeProfitModel->hasid = $model->id;
                    $supernodeProfitModel->amount = $feeAmount;

                    if (false === $supernodeProfitModel->save()) {
                        throw new \yii\base\ErrorException('上级收益添加失败');
                    }
                    var_dump('添加记录end'.microtime());
                    var_dump('超级节点加钱开始'.microtime());
                    //  写入金额
                    $supernodeProfitModel->earnAmount($feeAmount, WalletLog::TYPE_PROFIT_SUPERNODE_BANK_PRODUCT);
                    var_dump('超级节点加钱开始end'.microtime());
                    //  发送通知 -  超级节点收益
                    $user = \api\models\User::findOne($user_id);
                    if (!empty($user)) {
                        \common\models\Message::addMessage(\common\models\Message::TYPE_SUPERNODE_PROFIT, $user, $supernodeProfitModel->symbol, $supernodeProfitModel->amount, $supernodeProfitModel);
                    }
                }

                break;

            default:
                throw new \yii\base\ErrorException('节点类型不正确');
                break;
        }
    }

    /**
     * 判断今天是否发送奖励
     * @param $order_id
     * @param $user_id
     * @return bool
     */
    public static function check($order_id,$user_id){
        $flag = static::find()->andWhere([
            'hasid' => $order_id,
            'uid' => $user_id,
        ])->select('id')->andWhere(['between','created_at',strtotime(date('Y-m-d')),strtotime(date('Y-m-d 23:59:59'))])->one();
        if($flag){
            return false;
        }
        return true;
    }

    public static function userAmount($user_id)
    {
        $date = date('Ymd');
        $price = Yii::$app->cache->getOrSet("user-{$user_id}-{$date}",
            function () use ($user_id, $date) {
                $wallets = \common\models\Wallet::find()->where([
                    'user_id' => $user_id
                ])->select('amount,symbol')->all();
                $value_total = 0;
                foreach($wallets as $wallet){
                    $price = static::symbolPrice($wallet->amount,$wallet->symbol);
                    $value_total += empty($price)? 0 : floatval($price);
                }
                $orders = Order::find()->where([
                    'AND',
                    ['uid'=>$user_id],
                    ['between', 'status', Order::STATUS_LOCK, Order::STATUS_PROFIT]
                ])->select('amount,symbol')->all();
                if(!empty($orders)){
                    foreach ($orders as $order){
                        $price = static::symbolPrice($order->amount,$order->symbol);
                        $value_total += empty($price)? 0 : floatval($price);
                    }
                }
                return $value_total;
            }, 9000);
        return $price;
    }

    public static function symbolPrice($amount , $symbol){
        $date = date('YmdH');
        $price = \Yii::$app->cache->getOrSet("symbol-{$date}-price",function () use($symbol){
            $model = CurrencyPrice::findOne(['symbol' => $symbol]);
                return $model ? $model->price : 0;
        },'3600');
        return $amount * $price;
    }
    /**
     * 补发收益
     *
     * @param [type] $type
     * @param [type] $model
     * @return void
     */
    public static function bufaProfit($amount, $symbol, $type, Order $model)
    {
        $fee = explode(',', Setting::read('supernode_top_fee'));
//        $minimum_revenue_assets = floatval(Setting::read('minimum_revenue_assets')); // 上级用户最低资产
        $supernodeUid = explode(',', $model->supernode_uid);
        switch ($type) {
            case static::TYPE_BANK_PRODUCT:
                $feeAmount = 0;
                foreach ($fee as $key => $val) {
                    $user_id = $supernodeUid[$key];
                    if ($user_id == 0) {
                        break;
                    }
//                    $flag = static::bufaCheck($model->id,$user_id);
//                    if(!$flag){
//                        continue;
//                    }
                    //获取上级收益人钱包总值
//                    $wallets = Wallet::loadForWalletUser($user_id,0);
//                    $value_total = 0;
//                    foreach($wallets as $i => $wallet){
//                        $value_total += isset($wallet['value']) ? floatval($wallet['value']):0;
//                    }
//                    $orders = Order::find()->where([
//                        'AND',
//                        ['uid'=>$supernodeUid[$key]],
//                        ['between', 'status', Order::STATUS_LOCK, Order::STATUS_PROFIT]
//                    ])->all();
//                    if(!empty($orders)){
//                        foreach ($orders as $order){
//                            $price = CurrencyPrice::convert($order->amount,$order->symbol);
//                            $value_total += empty($price)? 0 : floatval($price);
//                        }
//                    }
                    $feeAmount = $feeAmount ? ($feeAmount * $val / 100) : ($amount * $val / 100);
//                    if($value_total < $minimum_revenue_assets){
//                        continue;
//                    }
                    var_dump('用户id：'.$user_id.'-');
                    $supernodeProfitModel = new static();
                    $supernodeProfitModel->title = '令牌理财';
                    $supernodeProfitModel->type = static::TYPE_BANK_PRODUCT;
                    $supernodeProfitModel->uid = $user_id;
                    $supernodeProfitModel->in_uid = $model->uid;
                    $supernodeProfitModel->symbol = $symbol;
                    $supernodeProfitModel->hasid = $model->id;
                    $supernodeProfitModel->amount = $feeAmount;

                    if (false === $supernodeProfitModel->save()) {
                        throw new \yii\base\ErrorException('上级收益添加失败');
                    }

                    //  写入金额
                    $supernodeProfitModel->earnAmount($feeAmount, WalletLog::TYPE_PROFIT_SUPERNODE_BANK_PRODUCT);

                    //  发送通知 -  超级节点收益
                    $user = \api\models\User::findOne($user_id);
                    if (!empty($user)) {
                        \common\models\Message::addMessage(\common\models\Message::TYPE_SUPERNODE_PROFIT, $user, $supernodeProfitModel->symbol, $supernodeProfitModel->amount, $supernodeProfitModel);
                    }
                }

                break;

            default:
                throw new \yii\base\ErrorException('节点类型不正确');
                break;
        }
    }

    public static function bufaCheck($order_id,$user_id){
        $flag = static::find()->andWhere([
            'hasid' => $order_id,
            'uid' => $user_id,
        ])->andWhere(['between','created_at',strtotime(date('Y-m-d 12:00:00')),strtotime(date('Y-m-d 23:59:59'))])->one();
        if($flag){
            return false;
        }
        echo '添加上级收益';
        return true;
    }
    /**
     * 收益
     *
     * @param [type] $amount
     * @param [type] $business_type
     * @return void
     */
    public function earnAmount($amount, $business_type)
    {
        if ($this->uid == 0) {
            return true;                //      平台收益不加钱
        }
        try{
            $wallet = Wallet::find()
                ->where(['user_id' => $this->uid, 'symbol' => $this->symbol])
                ->one();
            if (!$wallet) {
                $model_wallet = new Wallet();
                $model_wallet->loadAttributesFromApi($this->symbol, $this->uid);
                if (!$model_wallet->save()) {
                    throw new \ErrorException('', 5013);
                }
//                //添加钱包地址
//                $model_wallet_address = new \api\models\WalletAddress();
//                $model_wallet_address->loadAttributesWithoutAddress($this->symbol, $this->uid);
//                if (!$model_wallet_address->save()) {
//                    throw new \ErrorException('', 5014);
//                }
                $wallet = $model_wallet;
            }
            if (!$wallet->earnMoney($amount, $business_type)) {
                throw new \yii\base\ErrorException('上级收益加款失败');
            }
        }catch (StaleObjectException $e){
            $this->earnAmount($amount, $business_type);
        }
    }

    /**
     * 关联用户
     *
     * @return void
     */
    public function getUser()
    {
        return $this->hasOne(\backend\models\User::className(), ['id' => 'in_uid'])->select("id,username,nickname,userid");
    }

    /**
     * 获取消息通知的备注
     *
     * @return void
     */
    public function messageDescription()
    {
        switch ($this->type) {
            case static::TYPE_BANK_PRODUCT:
                $user = \api\models\User::findOne($this->in_uid);
                $fromUser = \api\models\User::findOne($this->uid);
                $userFriend = \api\models\UserFriend::find()->where(['in_userid' => $fromUser->userid, 'to_userid' => $user->userid])->one();
                if ($userFriend && $userFriend->remark) {
                    $nickname = $userFriend->remark;
                } else {
                    $nickname = $user->nickname;
                }
                $data = $nickname . ' 节点收益';
                break;
        }
        return $data;
    }
}
