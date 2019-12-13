<?php

namespace common\models\bank;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "iec_bank_income".
 *
 * @property int $id
 * @property string $name 收益名称
 * @property int $day 收益周期
 * @property int $num 次数
 * @property int $created_at 创建时间
 */
class Income extends \yii\db\ActiveRecord
{

    const TYPE_DEFAULT = 0;                 //  默认只有本金预算利息
    const TYPE_FUNDS_FEE = 10;              //  本金+收益 按周期返回

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_bank_income';
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
            [['name', 'day', 'type'], 'required'],
            [['day', 'num'], 'integer'],
            ['num', 'default', 'value' => 0],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'day' => '间隔天数',
            'num' => '周期',
            'type' => '收益类型',
            'created_at' => '创建时间',
        ];
    }

    //  计算下一次收益的时间
    public function nextProfitDate(Order $order)
    {
        //  type=10     一次性返回
        //  type=20     周期返回
        //  type=30     周期结束
        $data = ['datetime' => false, 'type' => 10, 'key' => 0];
        if($this->day > 0){
            $startime = $order->created_at; //订单开始时间
            //计算出每个发放收益的时间
            $profitArray = $this->generateProfitArray($startime, $order->day);
//            var_dump($profitArray);
            Yii::info($startime.':'.json_encode($profitArray), 'lockbank');
            $key = $this->dateKey($startime,$this->day);
//            var_dump($key);
            Yii::info('key:'.$key, 'lockbank');
            
            if($key){
                if(isset($profitArray[$key])){
                    $data = ['datetime' => $profitArray[$key], 'type' => 20, 'key' => $key];
                }else{
                    $data = ['datetime' => time(), 'type' => 30, 'key' => $key];
                }
            }else{
                $data = ['datetime' => $profitArray[0], 'type' => 20, 'key' => 0];
            }
        }

        return $data;
    }

    /**
     * 返回当前应执行到第几个收益
     * @param $statday
     * @param $day
     * @return float
     */
    public function dateKey($statday,$day){

        $today = time();
        $statday = strtotime(date('Y-m-d',$statday));
        $key = ceil(ceil(($today - $statday -2*86400) / 86400)/$day);
        return $key;
    }

    /**
     * 生成收益期数
     *
     * @param [type] $time
     * @return void
     */
    public function generateProfitArray($time, $num)
    {
        $baseTime = strtotime(date('Y-m-d', $time));
        Yii::info($time.':'.$baseTime, 'lockbank');
        $time = $baseTime + 2 * 86400;
        Yii::info('time:'.$time, 'lockbank');
        $dateTime = [];
        $key = 0;
        $num = $num - $this->day;
        for($i = 0; $i < $num; $i+=$this->day){
            $time = $time + $this->day * 86400;
            $dateTime[$key] = $time - 43201;
            $key++;
        }
        return $dateTime;
    }


    /**
     * 模式
     *
     * @return void
     */
    public static function typeArray()
    {
        return [
            static::TYPE_DEFAULT => '利息周期返回',
            static::TYPE_FUNDS_FEE => '本金+利息周期返回'
        ];
    }
}
