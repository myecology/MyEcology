<?php

namespace common\models;

use phpDocumentor\Reflection\DocBlock\Tags\Param;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Url;

/**
 * This is the model class for table "iec_currency".
 *
 * @property int $id
 * @property string $symbol 英文缩写
 * @property string $description 中文描述
 * @property int $created_at 创建时间
 * @property int $updated_at 最后修改时间
 * @property int $status 状态
 * @property int $weight 排序权重
 * @property string $model 币种模型
 * @property string $icon 小图标
 * @property string $fee_symbol 手续费币种
 * @property string $fee_withdraw_amount 提现手续费金额
 *
 */
class Currency extends \yii\db\ActiveRecord
{
    public static $lib_status = [
        0 => '禁用',
        10 => '启用',
    ];
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 10;

    public static $lib_model = [
        0 => 'ETH',
        1 => 'BTC',
    ];
    const MODEL_ETH = 'ETH';
    const MODEL_BTC = 'BTC';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_currency';
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
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => ['updated_at'],
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
            [['icon', 'symbol', 'weight', 'model', 'fee_symbol', 'fee_withdraw_amount', 'status'], 'required'],
            [['status', 'weight'], 'integer'],
            [['symbol', 'model', 'fee_symbol'], 'string', 'max' => 32],
            [['description'], 'string', 'max' => 128],
            [['icon'], 'string', 'max' => 255],
            ['status', 'default', 'value' => 0],
            [['fee_withdraw_amount'], 'number',],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'symbol' => '币种',
            'description' => '说明',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'status' => '状态',
            'weight' => '权重',
            'model' => '模型',
            'icon' => '图标',
            'fee_symbol' => '手续费币种',
            'fee_withdraw_amount' => '手续费',
        ];
    }

    /**
     * 写入后事件
     *
     * @param [type] $insert
     * @param [type] $changedAttributes
     * @return void
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        //  删除缓存
        Yii::$app->cache->delete('currency-available');
    }

    /**
     * 加载所有可用币种列表
     * @return null|array
     */
    public static function loadAvailable()
    {
        return Yii::$app->cache->getOrSet('currency-available-all', function () {
            $result = static::find()
                ->where(['status' => static::STATUS_ENABLED])
                ->orderBy(['weight' => SORT_DESC])
                ->limit(100)
                ->asArray()
                ->all();

            foreach ($result as $i => $currency) {
                $result[$i] = [
                    'id' => $currency['id'],
                    'weight' => (string)$currency['weight'],
                    'symbol' => (string)$currency['symbol'],
                    'description' => (string)$currency['description'],
                    'icon' => (string)Currency::iconAbs($currency['icon']),
                ];
            }

            return $result;
        }, 60);
    }

    /**
     * @return array
     */
    public static function loadList()
    {
        $result = [];

        $currencies = static::loadAvailable();
        foreach($currencies as $i => $currency){
            $result[$currency['id']] = $result['symbol'];
        }

        return $result;
    }

    /**
     * 标识是否存在
     * @param $symbol
     * @return bool
     */
    public static function isExists($symbol)
    {
        return static::find()->where([
            'symbol' => $symbol,
            'status' => static::STATUS_ENABLED
        ])->exists();
    }

    /**
     * 币种标识查询币种配置信息
     * @param $symbol
     * @return bool|Currency
     */
    public static function findCurrencyBySymbol($symbol)
    {
        return Yii::$app->cache->getOrSet("currency-{$symbol}", function () use ($symbol) {
            return static::find()
                ->where(['symbol' => $symbol, 'status' => static::STATUS_ENABLED])
                ->one();
        }, 60);
    }

    public function getParams()
    {
        return CurrencyParam::loadParams($this);
    }

    public function getIconAbs()
    {
        return static::iconAbs($this->icon);
    }

    public static function iconAbs($url)
    {
        return Url::to($url, true);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeeCurrency()
    {
        return $this->hasOne(Currency::className(), ['symbol' => 'fee_symbol']);
    }

    /**
     * @param int $amount
     * @return int
     */
    public function getFeeWithdrawAmount($amount = 0)
    {
        $fee_amount = $this->fee_symbol ? $this->fee_withdraw_amount : 0;
        if ($this->symbol == $this->fee_symbol
            && isset($this->params['withdraw_ratio'])
            && $this->params['withdraw_ratio']
        ) {
            $fee_amount = max($fee_amount, $amount * $this->params['withdraw_ratio']);
        }

        return $fee_amount;
    }

    /**
     * @return null
     */
    public function getFeeSymbol()
    {
        return $this->feeCurrency ? $this->feeCurrency->fee_symbol : null;
    }

    public function getIsSameFeeAndCurrency()
    {
        return $this->symbol == $this->fee_symbol;
    }

    /**
     * @param $currency_id
     * @return mixed
     */
    public static function findCurrencyById($currency_id)
    {
        return Yii::$app->cache->getOrSet("currency-#{$currency_id}", function () use ($currency_id) {
            return static::find()
                ->where(['id' => $currency_id, 'status' => static::STATUS_ENABLED])
                ->one();
        }, 5);
    }

    /**
     * 反权重排序，用于"用户钱包列表"支持后创建的钱包优先显示
     * @param $symbols
     * @return array|Currency[]
     */
    public static function findAllBySymbols($symbols)
    {
        $_symbols = explode(',', $symbols);

        return static::find()
            ->where(['in', 'symbol', $_symbols])
            ->andWhere(['status' => static::STATUS_ENABLED])
            ->orderBy(['weight' => SORT_DESC])
            ->indexBy('symbol')
            ->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function queryAvailable()
    {
        return static::find()
            ->where([
                'status' => static::STATUS_ENABLED,
            ])
            ->orderBy(['weight' => SORT_DESC])
            ->indexBy('symbol');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrencyPrice()
    {
        return $this->hasOne(CurrencyPrice::className(), ['currency_id' => 'id'])->orderBy('id DESC');
    }
}
