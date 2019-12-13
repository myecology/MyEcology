<?php

namespace common\models\bank;

use common\models\Currency;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "iec_bank_product".
 *
 * @property int $id
 * @property string $name 名称
 * @property string $symbol 币种标识
 * @property string $amount 数量
 * @property string $rate 年利率
 * @property string $min_amount 最小数量
 * @property string $user_amount 个人最大数量
 * @property string $max_amount 最大数量
 * @property int $income_id 收益类型
 * @property string $income_description 收益描述
 * @property int $type 产品类型
 * @property string $fee 费用
 * @property string $fee_explain 费用说明
 * @property int $day 周期天数
 * @property string $description 描述
 * @property int $statime 开始时间
 * @property int $status 状态
 * @property int $endtime 结束时间
 * @property int $created_at 创建时间
 * @property string $earn_symbol 收益币种
 * @property int $earn_currency_id 收益币种标识id
 * @property int $earn_currency_price 收益币种价格
 * @property int $currency_price 收益币种价格
 */
class Product extends \yii\db\ActiveRecord
{

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_bank_product';
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
            [['name', 'symbol','earn_currency_id','income_id', 'day', 'max_amount', 'min_amount', 'user_amount', 'rate', 'description','currency_price','earn_currency_price'], 'required'],
//            ['symbol', 'default', 'value' => 'IEC'],
            ['status', 'in', 'range' => [static::STATUS_DELETED, static::STATUS_ACTIVE]],
            ['status', 'default', 'value' => static::STATUS_ACTIVE],
            ['income_id', 'filter', 'filter' => function($value){
                $income = Income::findOne($this->income_id);
                $this->income_description = $income->name;
                return $this->income_id;
            }],
            [['type', 'fee', 'amount'], 'default', 'value' => 0],
            [['fee_explain'], 'default', 'value' => ''],
            ['earn_symbol', 'filter', 'filter' => function($value){
                $currency = Currency::findCurrencyById($this->earn_currency_id);
                $this->earn_symbol = $currency->symbol;
                return $this->earn_symbol;
            }],
            ['statime', 'filter', 'filter' => function($value){
                if($this->statime){
                    $time = strtotime($this->statime);
                }else{
                    $time = time();
                }
                return $time;
            }],
            ['endtime', 'filter', 'filter' => function($value){
                if($this->endtime){
                    $time = strtotime($this->endtime);
                }else{
                    $time = time() + $this->day * 86400;
                }
                return $time;
            }],
            [['amount', 'rate', 'super_rate', 'min_amount', 'max_amount', 'fee'], 'number'],
            [['income_id', 'type', 'day'], 'integer'],
            [['name', 'symbol', 'income_description', 'fee_explain', 'description'], 'string', 'max' => 255],
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
            'symbol' => '投资币种',
            'amount' => '数量',
            'rate' => '个人日收益率',
            'super_rate' => '超级节点收益',
            'min_amount' => '最小数量',
            'max_amount' => '上限数量',
            'user_amount' => '个人最大',
            'income_id' => '收益',
            'income_description' => '收益描述',
            'type' => '类型',
            'fee' => '费用',
            'fee_explain' => '费用说明',
            'day' => '周期',
            'description' => '描述',
            'status' => '状态',
            'statime' => '开始时间',
            'endtime' => '结束时间',
            'created_at' => '创建时间',
            'earn_currency_id' => '收益币种',
            'currency_price' => '投资币价',
            'earn_currency_price' => '收益币价',

        ];
    }


    /**
     * 更新数量
     *
     * @param [type] $id
     * @return void
     */
    public static function updateAmount($id)
    {
        $model = static::findOne($id);
        $model->amount = Order::find()->where(['product_id' => $model->id])->sum('amount');
        if(false === $model->save(false)){
            throw new \yii\base\ErrorException('更新数量失败');
        }
        return $model;
    }

    public function getIncome()
    {
        return $this->hasOne(Income::className(), ['id' => 'income_id']);
    }

    /**
     * 状态
     *
     * @return void
     */
    public static function statusArray()
    {
        return [
            static::STATUS_ACTIVE => '开启',
            static::STATUS_DELETED => '关闭',
        ];
    }

    /**
     * 获取收益币种
     * @param $id
     * @return string
     */
    public static function getEarnSymbol($id)
    {
        $model = static::findOne($id);
        return $model->earn_symbol;
    }






}
