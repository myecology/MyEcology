<?php

namespace common\models;

use backend\models\Setting;
use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeBehavior;



/**
 * This is the model class for table "iec_currency_price".
 *
 * @property int $id
 * @property int $currency_id 币种ID
 * @property string $symbol 币种标识
 * @property string $price 中文描述
 * @property int $updated_at 最后修改时间
 * @property int $updated_date 最后修改日期
 * @property string $source 来源标识
 * @property int $is_exchange 来源标识
 */
class CurrencyPrice extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_currency_price';
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
                    self::EVENT_BEFORE_INSERT => ['updated_at'],
                    self::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
            //  code
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'updated_date',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_date',
                ],
                'value' => function ($event) {
                    return date('Y-m-d');
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['currency_id', 'price'], 'required'],
            ['currency_id', 'filter', 'filter' => function($value){
                $currency = Currency::findOne($this->currency_id);
                $this->symbol = $currency->symbol;
                return $this->currency_id;
            }],
            [['currency_id','is_exchange'], 'integer'],
            [['price','poundage'], 'number'],
            [['symbol'], 'string', 'max' => 32],
            [['source'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'currency_id' => '币种',
            'symbol' => '标识',
            'price' => '价格',
            'updated_at' => '更新时间',
            'updated_date' => '更新日期',
            'source' => '来源',
            'is_exchange' => '兑换',
            'poundage' => '兑币手续费'
        ];
    }

    /**
     * 金额、标识转换成人民币价格
     * 每日市价采集
     * @param $amount
     * @param $symbol
     * @return bool|float|int
     */
    public static function convert($amount, $symbol)
    {
//        $date = date('Ymd');
//        $price = Yii::$app->cache->getOrSet("symbol-{$symbol}-{$date}",
//            function () use ($symbol, $date) {
//                $model = static::find()->where(['symbol' => $symbol])
////                    ->andWhere(['updated_date' => $date]) //TODO 采集上线后删除注释
//                    ->orderBy(['updated_date' => SORT_DESC])
//                    ->limit(1)
//                    ->one();
//                return $model ? $model->price : 0;
//            }, 1800);

        $model = static::find()->where(['symbol' => $symbol])
//                    ->andWhere(['updated_date' => $date]) //TODO 采集上线后删除注释
            ->orderBy(['updated_date' => SORT_DESC])
            ->limit(1)
            ->one();

        $price = $model ? $model->price : 0;
        return $amount * $price;
    }

    /**
     * @param $price
     * @param string $source
     * @return bool
     */
    public function refreshPrice($price, $source = '')
    {
        $this->setAttributes([
            'price' => $price,
            'updated_at' => time(),
            'updated_date' => date('Ymd'),
            'source' => $source,
        ]);

        return $this->save();
    }

    /**
     * 根据币种获取价格
     * @param $symbol
     * @return int|mixed
     */
    public static function getPriceBySymbol($symbol)
    {
        $model_currency = CurrencyPrice::find()->where(['symbol' => $symbol])->one();
        return $model_currency ? $model_currency->price : 0;
    }

    public static function getSymbol($symbol)
    {
        $model_currency = CurrencyPrice::find()->where(['symbol' => $symbol])->one();
        return $model_currency ? $model_currency : false;
    }

    public function updatePrice(){
        $currency_price_symbols = explode(',',Setting::read('currency_price_symbols', 'currency'));
        $url = Setting::read('currency_price_symbols_url', 'currency');
        if(!empty($url)){
            foreach ($currency_price_symbols as $symbol){
                $urlPrice = $url.strtolower($symbol);
                $symbol_price = static::curlGet($urlPrice);
                if(empty($symbol_price)){
                    continue;
                }
                var_dump($symbol_price);
                $price = $symbol_price[strtolower($symbol)]['cny'];
                var_dump($price);
                $price = !empty($price) ? $price : false;
                if($price){
                    if($symbol == 'USDT'){
                        $model_usdt_btc = static::getSymbol("USDT(BTC)");
                        if(!empty($model_usdt_btc)){
                            $model_usdt_btc->updated_at = time();
                            $model_usdt_btc->price = $price;
                            $model_usdt_btc->save();
                        }
                        $model_usdt_eth = static::getSymbol("USDT(ETH)");
                        if(!empty($model_usdt_eth)){
                            $model_usdt_eth->updated_at = time();
                            $model_usdt_eth->price = $price;
                            $model_usdt_eth->save();
                        }
                    }else{
                        $model = static::getSymbol($symbol);
                        if(!empty($model)){
                            $model->updated_at = time();
                            $model->price = $price;
                            $model->save();
                        }
                    }
                }
            }
        }

    }



    private static function curlGet($url)
    {
        var_dump($url);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        $headers = [];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($code == 200) {
            $price = false;
            $data = json_decode($data,true);
            var_dump($data);
            if($data['status'] == 200){
                $price =  empty($data['data'])?false:$data['data'];
            }
            return $price;
        }
        return false;
    }

    public function getIcon(){
        return $this->hasOne(Currency::className(),['symbol'=>'symbol']);
    }

    public static function exchangeList(){
        $result = [];
        $icon =  self::find()->joinWith('icon')->select('iec_currency_price.symbol')->where(['is_exchange' => 1])->all();
        foreach ($icon as $model){
            $result[] = $model->iconList();
        }
        return $result;
    }

    public function iconList(){
        return [
            'symbol'=>$this->symbol,
            'icon'=>$this->icon->icon
        ];
    }

    public static function updateAnt(){
        $url = 'http://39.100.130.137:9093/api/v1/ticker?symbol=45';
        $data = static::curlAntget($url);
        $usdtprice = $data['last'];
        $model = static::getSymbol('ANT');
        $price = static::getPriceBySymbol('USDT(BTC)');
        $model->price = $usdtprice * $price;
        $model->updated_at = time();
        $model->save();
        $model = static::getSymbol('ANT');
        var_dump($model);
    }

    private static function curlAntget($url)
    {
        var_dump($url);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        $headers = [];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($code == 200) {
            $price = false;
            $data = json_decode($data,true);
            var_dump($data);
            if($data['code'] == 200){
                $price =  empty($data['data'])?false:$data['data'];
            }
            return $price;
        }
        return false;
    }
}
