<?php

namespace common\models\bank;

use common\models\Currency;
use common\models\CurrencyPrice;
use common\models\Invitation;
use Yii;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\helpers\Json;
use common\models\UserTree;
use backend\models\Setting;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeBehavior;
use common\models\SupernodeProfit;
use common\models\Wallet;
use common\models\WalletLog;
use yii\db\ActiveRecord;
use api\models\User;

/**
 * This is the model class for table "iec_bank_order".
 *
 * @property int $id
 * @property int $uid 用户ID
 * @property int $product_id 产品ID
 * @property string $rate 利率
 * @property string $amount 数量
 * @property string $symbol 币种
 * @property string $supernode_uid 超级节点收益ID
 * @property int $status 状态
 * @property int $day 周期天数
 * @property int $endtime 结束时间
 * @property int $created_at 创建时间
 * @property string $earn_symbol 收益的币种
 * @property string $currency_price 投资的币价
 * @property string $earn_currency_price 收益的币价
 */
class Order extends ActiveRecord
{
    public $password;

    const STATUS_BACK = -1;
    const STATUS_LOCK = 0;
    const STATUS_PROFIT = 10;
    const STATUS_END = 20;

    private $order_bank_symbol = '';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_bank_order';
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
            //  code
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'uid',
                ],
                'value' => function ($event) {
                    return Yii::$app->user->identity->id;
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'supernode_uid',
                ],
                'value' => function ($event) {
                    $fee = explode(',', Setting::read('supernode_top_fee'));
                    $uids = [];
                    $uid = Yii::$app->user->identity->id;
                    foreach($fee as $key=>$val){
                        $supernodeTree = User::findOne(['id' => $uid]);
                        if($supernodeTree){
                            $supernode = $supernodeTree->upid;
                        }else{
                            $supernode = null;
                        }
                        $uid = $supernode ? $supernode : 0;
                        $uids[] = $uid;
                    }
                    return implode(',', $uids);
                },
            ],
        ];
    }

    /**s
     * 场景事物支持
     * @return [type] [description]
     */
    public function transactions()
    {
        return [
            'insert' => self::OP_INSERT | self::OP_UPDATE,
            'update' => self::OP_INSERT | self::OP_UPDATE,
        ];
    }

    /**
     * 场景
     *
     * @return void
     */
    public function scenarios()
    {
        return [
            'insert' => ['product_id', 'amount', 'symbol', 'day', 'password', 'earn_symbol', 'currency_price', 'earn_currency_price'],
            'update' => ['status', 'endtime'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'amount', 'day', 'password', 'symbol', 'currency_price', 'earn_currency_price', 'earn_symbol'], 'required', 'on' => ['insert']],
            ['password', function($attribute, $params){
                if(!$this->hasErrors()){
                    $user = User::findOne(Yii::$app->user->identity->id);
                    if(!$user->validatePayment($this->password)){
                        $this->addError($attribute, '支付密码不正确');
                    }
                }
            }, 'on' => ['insert']],
            [['product_id', 'day'], 'integer', 'on' => ['insert']],
            [['amount'], 'number', 'on' => ['insert']],
            ['amount', function($attribute, $params){
                if (!$this->hasErrors()) {
                    $wallet = Wallet::find()->where(['user_id' => Yii::$app->user->identity->id, 'symbol' => $this->symbol])->one();
                    if(!$wallet || $this->amount > ($wallet['amount'] - $wallet['amount_lock'])){
                        $this->addError($attribute, $this->symbol . '数量不足');
                        return ;
                    }

                    //  是否超过购买量-是否小于最低数量
                    $product = Product::findOne($this->product_id);
                    $this->rate = $product->rate;
                    if($product->income->day == 0){
                        $day = $this->day + 2;
                        $this->endtime = strtotime(date('Y-m-d', strtotime("+".$day."day"))) - 60+43200;
                    }else{
                        $this->endtime = strtotime(date('Y-m-d', strtotime("+2day"))) - 60+43200;
                    }
                    
                    //  判断是否超过最大限制
                    if(($product->amount + $this->amount) > $product->max_amount){
                        $this->addError($attribute, '超过产品总额度');
                        return ;
                    }

                    //  判断是否个人最大， 或者 最低数量
                    $userAmount = static::find()
                        ->where(['uid' => Yii::$app->user->identity->id, 'product_id' => $product->id])
                        ->andWhere(['in','status',[static::STATUS_LOCK,static::STATUS_PROFIT]])->sum('amount');
                    $userAmount = $userAmount ?: 0;
                    $userAmount += $this->amount;
                    if($userAmount > $product->user_amount){
                        $this->addError($attribute, '超过产品最大购买额度');
                        return ;
                    }
                    if($this->amount < $product->min_amount){
                        $this->addError($attribute, '低于产品最小购买额度');
                    }
                }
            }, 'on' => ['insert']],
            ['product_id', function($attribute, $params){
                if(!$this->hasErrors()){
                    $product = Product::findOne($this->product_id);

                    $time = time();
                    if($time < $product->statime){
                        $this->addError($attribute, '产品未开始');
                        return ;
                    }
                    if($time > $product->endtime){
                        $this->addError($attribute, '产品已结束');
                    }
                }
            }, 'on' => ['insert']],

            ['status', 'in', 'range' => [static::STATUS_PROFIT,static::STATUS_END, static::STATUS_BACK], 'on' => ['update']],
            ['endtime', 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '用户ID',
            'product_id' => '产品ID',
            'rate' => '利率',
            'amount' => '数量',
            'password' => '支付密码',
            'status' => '状态',
            'day' => '周期',
            'endtime' => '结束时间',
            'created_at' => '创建时间',
        ];
    }


    public static function statusArray()
    {
        return [
            static::STATUS_BACK => '退回',
            static::STATUS_LOCK => '锁仓',
            static::STATUS_PROFIT => '收益',
            static::STATUS_END => '完成'
        ];
    }

    /**
     * 购买
     *
     * @return void
     */
    public function spendAmount()
    {
        $wallet = Wallet::find()
            ->where(['user_id' => $this->uid, 'symbol' => $this->symbol])
            ->one();
        if(!$wallet->spendMoney($this->amount, WalletLog::TYPE_BANK_PRODUCT)){
            throw new  \ErrorException('购买失败');
        }
    }

    /**
     * 收益
     *
     * @param [type] $amount
     * @param [type] $business_type
     * @return void
     */
    public function earnAmount($amount, $business_type , $symbol)
    {
        try{
            $symbol = $symbol ? $symbol : $this->symbol;
            $wallet = Wallet::find()
                ->where(['user_id' => $this->uid, 'symbol' => $symbol])
                ->one();

            if (!$wallet) {
                $model_wallet = new Wallet();
                $model_wallet->loadAttributesFromApi($symbol,$this->uid);
                if (!$model_wallet->save()) {
                    throw new \ErrorException('', 5013);
                }
            }

            if(!$wallet->earnMoney($amount, $business_type)){
                throw new  \ErrorException('收钱失败');
            }
        }catch (StaleObjectException $e){
            $this->earnAmount($amount, $business_type , $symbol);
        }
    }

    /**
     * 跑收益跟结束
     *
     * @return void
     */
    public function endtimeAmount()
    {
        if($this->endtime > time()){
            return false;
        }

        //  计算下一次返回收益的时间
        $income = $this->product->income;
        switch ($income->type) {
            case Income::TYPE_DEFAULT:
                $this->incomeDefault($income);
                break;
            case Income::TYPE_FUNDS_FEE:
                $this->incomeFundsfee($income);
                break;
            default:
                throw new  \ErrorException('不存在的类型');
                break;
        }

    }

    /**
     * 利息 按周期返回  /   最后一起返回本金
     *
     * @param [type] $income
     * @return void
     */
    public function incomeDefault(Income $income)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try{
            /*$tesst = new Income();
            $tesst->nextProfitDate();*/
            var_dump('执行开始'.microtime());
            var_dump('计算时间'.microtime());
            $nextTime = $income->nextProfitDate($this);
            Yii::info('remark:'.json_encode($nextTime), 'lockbank');
            var_dump($nextTime);
            var_dump('计算时end'.microtime());
            // 判断是否产生收益了；
            var_dump('判断是否该订单今天产生收益'.microtime());
            /*$flag = $this->profit($nextTime);
            if(!$flag){
                echo '已发送收益';
                $transaction->commit();
                return true;
            }*/
            var_dump('判断是否该订单今天产生收益end'.microtime());
            var_dump('算法产生'.microtime());
            $currency_price = CurrencyPrice::getPriceBySymbol($this->symbol);
            $earn_currency_price = CurrencyPrice::getPriceBySymbol($this->earn_symbol);
            $profit = ($this->amount * $currency_price * $this->rate / 100) / $earn_currency_price;//收益算法
            var_dump('算法产生end'.microtime());
            switch ($nextTime['type']) {
                case 20:
                    var_dump('修改时间'.microtime());
                    $this->updateEndTime($nextTime['datetime'],static::STATUS_PROFIT);
                    var_dump('修改时间end'.microtime());
                    //添加收益
                    var_dump('加钱'.microtime());
                    $this->earnAmount($profit, WalletLog::TYPE_BANK_PRODUCT_PROFIT,$this->earn_symbol);        //  收益
                    Profit::addProfit(Profit::TYPE_PROFIT, $profit, $this);
                    var_dump('加钱end'.microtime());
                    //上级节点收益
//                    $supernodeAmount = $this->amount * $this->product->super_rate / 100 / $num;
                    SupernodeProfit::addProfit($profit, $this->earn_symbol, SupernodeProfit::TYPE_BANK_PRODUCT, $this);

                    break;
                case 30:
                    //  利息周期返回最后一期
                    $this->updateEndTime($nextTime['datetime'],static::STATUS_END);


                    $this->earnAmount($profit, WalletLog::TYPE_BANK_PRODUCT_PROFIT,$this->earn_symbol);        //  收益
                    Profit::addProfit(Profit::TYPE_PROFIT, $profit, $this);

                    //  写入超级节点收益
                    SupernodeProfit::addProfit($profit, $this->earn_symbol, SupernodeProfit::TYPE_BANK_PRODUCT, $this);

                    //  最后一期（返回本金）
                    $amount = $this->amount;
                    $this->earnAmount($amount, WalletLog::TYPE_BANK_PRODUCT_ALL,$this->symbol);
                    Profit::addProfit(Profit::TYPE_PRINCIPAL, $amount, $this);

                    //  解锁明细
                    Log::bankLog(Log::TYPE_UN_LOCK_PRODUCT, $this);
                    break;
                default:
                    throw new  \ErrorException('下一次时间类型不对');
                    break;
            }

            $transaction->commit();
            return true;
        }catch(\ErrorException $e){
            $transaction->rollBack();
            throw new \ErrorException($e->getMessage());
        }catch (\Exception $exception){
            $transaction->rollBack();
            throw new \ErrorException($exception->getMessage());
        }
        return false;
    }




    public function bufa(){

        $transaction = \Yii::$app->db->beginTransaction();
        try{
            $currency_price = CurrencyPrice::getPriceBySymbol($this->symbol);
            $earn_currency_price = CurrencyPrice::getPriceBySymbol($this->earn_symbol);
            $profit = ($this->amount * $currency_price * $this->rate / 100) / $earn_currency_price;//收益算法

            //添加收益
            $this->earnAmount($profit, WalletLog::TYPE_BANK_PRODUCT_PROFIT,$this->earn_symbol);        //  收益

            Profit::addProfit(Profit::TYPE_PROFIT, $profit, $this);

            //上级节点收益
//                    $supernodeAmount = $this->amount * $this->product->super_rate / 100 / $num;
            SupernodeProfit::bufaProfit($profit, $this->earn_symbol, SupernodeProfit::TYPE_BANK_PRODUCT, $this);

            $transaction->commit();
            echo 'success'.$this->id;
            return false;
        }catch (\Exception $e){
            echo '失败'.$this->id;
            $transaction->rollBack();
             var_dump($e->getMessage());

             return $this->id;
        }

    }

    /***
     * 判断今天是否写入了收益
     * @return bool
     */
    public function profit($nextTime){
        $flag = Profit::checkProfit(Profit::TYPE_PROFIT,$this->id,$this->uid);
        //如果没有改变订单时间 并且返回下一次的状态为收益 且发放了当天收益
        if($this->endtime != $nextTime['datetime'] ){
            $this->updateEndTime($nextTime['datetime'],$nextTime['type'] == 30?static::STATUS_END:static::STATUS_PROFIT);
        }
        var_dump($flag);
        if($flag){
            return true;
        }
        return false;
    }

    /**
     * @param $endTime
     * 修改结束时间
     */
    public function updateEndTime($endTime,$status = false){
        $this->endtime = $endTime;
        var_dump('修改时间：'.$endTime);
        var_dump('修改状态：'.$status);
        if(!empty($status)){
            $this->status = $status;
        }
        $this->setScenario('update');
        if(!$this->save()){
            throw new  \ErrorException('修改订单失败');
        }
    }
    /**
     * 本金+利息    /   按周期返回
     *
     * @param [type] $income
     * @return void
     */
    public function incomeFundsfee($income)
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try{
            $nextTime = $income->nextProfitDate($this);

            switch ($nextTime['type']) {
                case 10:
                    //  一次性返回      本金+收益
                    $this->scenario = 'update';
                    $this->status = static::STATUS_END;
		
                    if(false === $this->save()){
                        throw new  \ErrorException('修改订单状态失败');
                    }

//                    $profit = $this->amount * $this->rate / 100;        //  收益对数量
                    $profit = ($this->amount * $this->currency_price * $this->rate / 100) / $this->earn_currency_price;
                    $amount = $this->amount;                            //  本金

                    $this->earnAmount($amount, WalletLog::TYPE_BANK_PRODUCT_ALL,$this->symbol);   //  加上本金
                    $this->earnAmount($profit, WalletLog::TYPE_BANK_PRODUCT_PROFIT,$this->earn_symbol);//  加上收益

                    //  写入收益
                    Profit::addProfit(Profit::TYPE_PROFIT, $profit, $this);
                    Profit::addProfit(Profit::TYPE_PRINCIPAL, $amount, $this);

                    //  写入超级节点收益
//                    $supernodeAmount = $this->amount *  $this->product->super_rate / 100;
                    SupernodeProfit::addProfit($profit, $this->earn_symbol, SupernodeProfit::TYPE_BANK_PRODUCT, $this);

                    //  解锁明细
                    Log::bankLog(Log::TYPE_UN_LOCK_PRODUCT, $this);
                    break;
                case 20:
                    //  周期返回        本金+收益   /   周期
                    $this->scenario = 'update';
                    $this->status = static::STATUS_PROFIT;
                    $this->endtime = $nextTime['datetime'];

                    if(false === $this->save()){
                        throw new  \ErrorException('修改订单状态失败');
                    }

                    //  收益
                    $num = $this->day / $income->day;
//                    $profit = $this->amount * $this->rate / 100 / $num;             //  收益
                    $profit = ($this->amount * $this->currency_price * $this->rate / 100) / $this->earn_currency_price;
                    $this->earnAmount($profit, WalletLog::TYPE_BANK_PRODUCT_PROFIT,$this->earn_symbol); //  收益
                    Profit::addProfit(Profit::TYPE_PROFIT, $profit, $this);

                    //  写入超级节点收益
//                    $supernodeAmount = $this->amount * $this->product->super_rate / 100 / $num;
                    SupernodeProfit::addProfit($profit, $this->earn_symbol, SupernodeProfit::TYPE_BANK_PRODUCT, $this);

                    //  本金
                    $amount = $this->amount / $num;                             //  本金分期
                    $this->earnAmount($amount, WalletLog::TYPE_BANK_PRODUCT_AVG,$this->symbol);       //  加上本金分期
                    Profit::addProfit(Profit::TYPE_PRINCIPAL, $amount, $this);

                    break;
                case 30:
                    //  周期返回/结束
                    $this->scenario = 'update';
                    $this->status = static::STATUS_END;
                    if(false === $this->save()){
                        throw new  \ErrorException('修改订单状态失败');
                    }

                    //  收益
                    $num = $this->day / $income->day;
//                    $profit = $this->amount * $this->rate / 100 / $num;             //  收益
                    $profit = ($this->amount * $this->currency_price * $this->rate / 100) / $this->earn_currency_price;
                    $this->earnAmount($profit, WalletLog::TYPE_BANK_PRODUCT_PROFIT,$this->earn_symbol);        //  收益
                    Profit::addProfit(Profit::TYPE_PROFIT, $profit, $this);

                    //  写入上级收益
//                    $supernodeAmount = $this->amount *  $this->product->super_rate / 100 / $num;
                    SupernodeProfit::addProfit($profit, $this->earn_symbol, SupernodeProfit::TYPE_BANK_PRODUCT, $this);

                    //  本金
                    $amount = $this->amount / $num;                             //  本金分期
                    $this->earnAmount($amount, WalletLog::TYPE_BANK_PRODUCT_AVG,$this->product->symbol);       //  加上本金分期
                    Profit::addProfit(Profit::TYPE_PRINCIPAL, $amount, $this);

                    //  解锁明细
                    Log::bankLog(Log::TYPE_UN_LOCK_PRODUCT, $this);
                    break;
                default:
                    throw new  \ErrorException('下一次时间类型不对');
                    break;
            }

            $transaction->commit();
            return true;
        }catch( \ErrorException $e){
            $transaction->rollBack();
            throw new  \ErrorException($e->getMessage());
        }
        return false;
    }


    /**
     * 
     *
     * @return void
     */
    public function orderBack()
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $this->scenario = 'update';
            $this->status = static::STATUS_BACK;

            if(false === $this->save()){
                throw new  \ErrorException('状态更新');
            }

            //  退还数量
            $this->earnAmount($this->amount, WalletLog::TYPE_BANK_PRODUCT_BACK,$this->product->symbol);

            //  补充交易记录
            Log::bankLog(Log::TYPE_BACK_PRODUCT, $this);

            $transaction->commit();
            return true;
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw new  \ErrorException($th->getMessage());
        }
        return false;
    }

    /**
     * 脚本退回没有退回本金的理财
     */
    public function jiaobenBack(){
        $transaction = Yii::$app->db->beginTransaction();
        try{
            if($this->status != 20){
                throw new Exception('状态不对');
            }
            $this->earnAmount($this->amount, WalletLog::TYPE_BANK_PRODUCT_BACK,$this->product->symbol);
            Profit::addProfit(Profit::TYPE_PRINCIPAL, $this->amount, $this);
            //  补充交易记录
            Log::bankLog(Log::TYPE_UN_LOCK_PRODUCT, $this);
            $transaction->commit();
            echo 'success';
        }catch (\Exception $exception){
            $transaction->rollBack();
            echo 'error'.$this->id;
            var_dump($exception->getMessage());
        }
    }


    /**
     * 关联用户
     *
     * @return void
     */
    public function getUser()
    {
        return $this->hasOne(\api\models\User::className(), ['id' => 'uid']);
    }

    /**
     * 关联产品
     *
     * @return void
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * 关联收益
     *
     * @return void
     */
    public function getProfit()
    {
        return $this->hasMany(Profit::className(), ['order_id' => 'id']);
    }

    /**
     * 根据user_id 但会正在收益的订单的统计币种总价格
     * @param $user_id
     * return int amount usdt(btc)
     */
    public  function amountByUserId($user_id){
        $orderData = Order::find()->where([
            'and',
            'uid' => $user_id,
            ['between', 'status', Order::STATUS_LOCK, Order::STATUS_PROFIT]
        ])->all();
        $amount = 0;
        foreach($orderData as $order){
            $orderAmount = $this->orderSymbolPrice($order);
            $amount += $orderAmount;
        }
    }

    /**
     * 根据订单币种转换成 统计的币种
     * @param Order $order
     * @return float
     */
    private  function orderSymbolPrice(Order $order){
        $orderPrice = CurrencyPrice::getPriceBySymbol($order->symbol);
        if($this->order_bank_symbol){
            $this->order_bank_symbol = Setting::read('bank_order_symbol','bank');
        }

        $symbol = $this->order_bank_symbol ? $this->order_bank_symbol : 'USDT(BTC)';
        $symbolPrice = CurrencyPrice::getPriceBySymbol($symbol);
        $amount = $orderPrice * $order->amount / $symbolPrice;
        return $amount;
    }
}
