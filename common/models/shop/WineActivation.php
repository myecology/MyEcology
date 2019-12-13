<?php

namespace common\models\shop;

use api\controllers\APIFormat;
use api\models\User;
use backend\models\Setting;
use common\models\CurrencyPrice;
use common\models\Wallet;
use common\models\WalletLog;
use Yii;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;

/**
 * This is the model class for table "iec_wine_activation".
 *
 * @property int $id
 * @property string $activation_code 激活码
 * @property int $user_id 用户id
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 * @property int $status 状态
 * @property int $type 类型
 * @property string $end_time 结束时间
 * @property int $count 次数
 * @property int $grant_count 发放次数
 * @property string $price 价格
 * @property string $every_amount 每次发放数量
 * @property string $every_price 每次发放价格
 * @property string $symbol 币种
 * * @property string $symbol_price 币种
 */
class WineActivation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_wine_activation';
    }

    public static $statusArr = [
        1 => '初始化',
        2 => '发放中',
        3 => '结束'
    ];

    const STATUS_START = 1;
    const STATUS_GRANT = 2;
    const STATUS_END = 3;

    public static $typeArr = [
        1 => '酒链激活码'
    ];

    const TYPE_WINE = 1;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['activation_code', 'user_id', 'end_time', 'count'], 'required'],
            [['user_id', 'status', 'type', 'count', 'grant_count'], 'integer'],
            [['updated_at', 'created_at', 'end_time'], 'safe'],
            [['price', 'every_amount', 'every_price','symbol_price'], 'number'],
            [['activation_code', 'symbol'], 'string', 'max' => 30],
            [['activation_code', 'user_id'], 'unique', 'targetAttribute' => ['activation_code', 'user_id']],
            [['activation_code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'activation_code' => '激活码',
            'user_id' => '用户id',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
            'status' => '状态',
            'type' => '类型',
            'end_time' => '结束时间',
            'count' => '剩余次数',
            'grant_count' => '发放次数',
            'price' => '价格',
            'every_amount' => '每日挖矿数量',
            'every_price' => '每次发放价格',
            'symbol' => '币种',
            'symbol_price' => 'wt价格',
        ];
    }


    /**
     * 激活活动
     * @param $activation_code
     * @param $user_id
     * @param int $type
     * @param int $price
     * @param string $symbol
     * @return array
     * @throws ErrorException
     */
    public static function addActivation($activation_code,$user_id,$type = WineActivation::TYPE_WINE,$price = 5198,$symbol='WT1918'){
        $data = [
            'activation_code' => $activation_code,
            'user_id' => $user_id,
            'type' => $type,
            'price' => $price,
            'symbol' => $symbol,
        ];
        $wine_data = array_merge($data,static::addParameter($price,$type,$symbol));
        $wineModel = new static();
        $wineModel->setAttributes($wine_data);
        if($wineModel->save()){
            return $wineModel->resultData();
        }
        throw new ErrorException(json_encode($wineModel->getErrors()),777);
    }
    /*
     * 配置参数
     */
    public static $endTimeParameter = [
        WineActivation::TYPE_WINE => [
            'time' => '+9 month', //时长
            'amount' => 'every_amount', //配置是否是实时价格 every_amount ,every_price
            'price' => true,//是否实时价格
            'wallet_log_type' => WalletLog::TYPE_WINE_ACTIVITY_GRANT,
        ]
    ];

    /***
     * 获取参数
     * @param $price
     * @param $type
     * @param $symbol
     * @return array
     */
    public static function addParameter($price,$type,$symbol){
        $time = static::$endTimeParameter[$type]['time'];
//        $typeAmount = static::$endTimeParameter[$type]['amount'];
        $end_time = date("Y-m-d 11:59:59",strtotime($time,strtotime(date('Y-m-d 11:59:59'))));
        $count = static::diffBetweenTwoDays($end_time,date('Y-m-d 11:59:59'));
        $symbol_price = CurrencyPrice::getPriceBySymbol($symbol);
        if(empty($symbol_price)){
            throw new ErrorException('symbol_price null');
        }
        if(empty($count)){
            throw new ErrorException('count null');
        }
        $every_amount = $price/$symbol_price/$count;
        $every_price = $price/$count;
        $data = [
            'end_time' => $end_time,
            'count' => $count,
            'every_amount' => $every_amount,
            'every_price' => $every_price,
            'symbol_price' => $symbol_price
        ];
        return $data;
    }

    /**
     * 求两个日期之间相差的天数
     * (针对1970年1月1日之后，求之前可以采用泰勒公式)
     * @param string $day1
     * @param string $day2
     * @return number
     */
    public static function diffBetweenTwoDays ($day1, $day2)
    {
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);

        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        return intval(($second1 - $second2) / 86400);
    }

    public function resultData(){
        $price = Setting::read('wine_price','wine');
//        $symbol = Setting::read('wine_symbol','wine');
        $symbol_price = CurrencyPrice::getPriceBySymbol($this->symbol);
        return [
            'activation_code' => $this->activation_code,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'status' => isset(WineActivation::$statusArr[$this->status])?WineActivation::$statusArr[$this->status]:$this->status,
            'end_time' => $this->end_time,
            'count' => $this->count,
            'grant_count' => $this->grant_count,
            'count_amount'=> $price.'.00RMB 等值',
            'amount' => round($this->every_price/$symbol_price,8),
            'price' => '￥ '.$this->every_price,
            'symbol' => $this->symbol
        ];
    }


    public static function runActivation(){
        // $activationArr = static::findAll([
        //     'and',
        //     ['in','status',[WineActivation::STATUS_START,WineActivation::STATUS_GRANT]],
        //     ['>','count','0']
        // ]);
        $activationArr = static::find()->where([
            'and',
            ['in','status',[WineActivation::STATUS_START,WineActivation::STATUS_GRANT]],
            ['>','count','0']
        ])->all();
        /**
         * @var WineActivation $activation
         */
        foreach ($activationArr as $activation){
            $activation->runGrant();
        }
    }

    public function runGrant(){
        $transaction = \Yii::$app->db->beginTransaction();//开启事务
        try{
            $flag = WineActivationLog::find()->where([
                'and',
                ['user_id' => $this->user_id],
                ['activation_id' => $this->id],
                ['BETWEEN','created_at',date('Y-m-d 0:0:0',time()),date('Y-m-d 23:59:59',time())]
            ])->one();
            if(!empty($flag)){
                $transaction->rollBack();
                return true;
            }
            if(static::$endTimeParameter[$this->type]['price']){
                $symbolPrice = CurrencyPrice::getPriceBySymbol($this->symbol);
                $amount = round($this->every_price/$symbolPrice,8);
            }else{
                $amountFeild = static::$endTimeParameter[$this->type]['amount'];
                $amount = $this->$amountFeild;
            }
            $data = [
                'activation_id' => $this->id,
                'user_id' => $this->user_id,
                'amount' => $amount,
                'symbol' => $this->symbol,
            ];
            $activationLog = new WineActivationLog();
            $activationLog->setAttributes($data);
            if(!$activationLog->save()){
                throw new ErrorException('添加活动记录失败');
            }
            $this->updateGrant();
            $type = static::$endTimeParameter[$this->type]['wallet_log_type'];
            /***
             * @var Wallet $userWallet
             */
            $userWallet = Wallet::findOneByWallet($this->symbol,$this->user_id);
            if(!$userWallet->earnMoney($amount,$type,$activationLog->id)){
                throw new ErrorException('加钱失败');
            }
            $transaction->commit();
        }catch (\ErrorException $errorException){
            $transaction->rollBack();
            Yii::error($errorException->getMessage(),'wine');
        }catch (StaleObjectException $objectException){
            $transaction->rollBack();
            $this->runGrant();
        }catch (\Exception $exception){
            $transaction->rollBack();
            Yii::error($exception->getMessage(),'wine');
        }
    }



    public function bufa(){
        $transaction = \Yii::$app->db->beginTransaction();//开启事务
        try{
            if(static::$endTimeParameter[$this->type]['price']){
                $symbolPrice = CurrencyPrice::getPriceBySymbol($this->symbol);
                $amount = round($this->every_price/$symbolPrice,8);
            }else{
                $amountFeild = static::$endTimeParameter[$this->type]['amount'];
                $amount = $this->$amountFeild;
            }
            $data = [
                'activation_id' => $this->id,
                'user_id' => $this->user_id,
                'amount' => $amount,
                'symbol' => $this->symbol,
            ];
            $activationLog = new WineActivationLog();
            $activationLog->setAttributes($data);
            if(!$activationLog->save()){
                throw new ErrorException('添加活动记录失败');
            }
            $this->updateGrant();
            $type = static::$endTimeParameter[$this->type]['wallet_log_type'];
            /***
             * @var Wallet $userWallet
             */
            $userWallet = Wallet::findOneByWallet($this->symbol,$this->user_id);
            if(!$userWallet->earnMoney($amount,$type,$activationLog->id)){
                throw new ErrorException('加钱失败');
            }
            $transaction->commit();
        }catch (\ErrorException $errorException){
            $transaction->rollBack();
            var_dump($errorException->getMessage());
        }catch (StaleObjectException $objectException){
            var_dump('StaleObjectException');
            var_dump($objectException->getMessage());
            $transaction->rollBack();
            $this->bufa();
        }catch (\Exception $exception){
            $transaction->rollBack();
            var_dump($exception->getMessage());
        }
    }
    public function updateGrant(){
        if($this->grant_count == 0){
            $this->status = static::STATUS_GRANT;
        }
        $this->grant_count += 1;
        $this->count -= 1;
        if($this->count < 0){
            throw new ErrorException('数据错误');
        }
        if($this->count == 0){
            $this->status = static::STATUS_END;
        }

        if($this->save()){
            return true;
        }
        throw new ErrorException('修改状态失败');
    }


    public function getUser(){
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function shopSearch($user_id,$page)
    {
        $query = static::find()
            ->andWhere(['user_id' => $user_id])
            ->orderBy(['created_at' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'page' => $page - 1,
            ]
        ]);
        return $dataProvider;
    }
}
