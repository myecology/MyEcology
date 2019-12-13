<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;


/**
 * This is the model class for table "iec_currency_params".
 *
 * @property int $id
 * @property int $currency_id 币种ID
 * @property string $symbol 币种标识
 * @property string $key 配置名
 * @property string $value 配置值
 * @property int $updated_at 更改时间
 */
class CurrencyParam extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_currency_param';
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['currency_id', 'key', 'value'], 'required'],
            ['currency_id', 'filter', 'filter' => function($value){
                $currency = Currency::findOne($this->currency_id);
                $this->symbol = $currency->symbol;
                return $this->currency_id;
            }],
            [['currency_id'], 'integer'],
            [['symbol'], 'string', 'max' => 32],
            [['key'], 'string', 'max' => 128],
            [['value'], 'string'],
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
            'key' => '键名',
            'value' => '值',
            'updated_at' => '更新时间',
        ];
    }

    public static function paramMap()
    {
        return [
            'ETH' => [
                'address',
                'password',

                'bytecode',
                'abi',
                'gas',
            ],
            'BTC' => [
                'address',
                'password',
            ],
            'EOS' => [
                'address',
                'password',
            ],
        ];
    }

    public static function loadParams(Currency $currency)
    {
//        $paramMap = isset(static::paramMap()[$currency->model]) ? static::paramMap()[$currency->model] : null;
//        if (is_null($paramMap)) {
//            return false;
//        }

//        $params = static::find()
//            ->where([
//                'currency_id' => $currency->id,
//            ])
//            ->indexBy('key')
//            ->all();
//
//        $result = [];
//        foreach ($paramMap as $attribute) {
//            $result[$attribute] = isset($params[$attribute]) ? $params[$attribute]->value : null;
//        }

        $params = static::find()
            ->where([
                'currency_id' => $currency->id,
            ])
            ->indexBy('key')
            ->asArray()
            ->all();

        $result = [];
        foreach ($params as $i => $attribute) {
            $result[$i] = $attribute['value'];
        }

        return $result;
    }

    public function getCurrency()
    {
        return $this->hasOne(Currency::className(), ['id' => 'currency_id',]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function queryForEthContract()
    {
        return static::find()->where([
            '' => '',
        ])->andWhere(['key' => 'contract_address'])
            ->orderBy(['id' => SORT_ASC]);
    }

    /**
     * @param $contract_address
     * @return CurrencyParam|null
     */
    public static function findByContractAddress($contract_address)
    {
        return static::findOne([
            'value' => $contract_address,
        ]);
    }

    /**
     * @param string $symbol
     * @return array
     */
    public static function loadBySymbol($symbol)
    {
        $params = static::find()
            ->select(['symbol', 'value', 'key'])
            ->where([
                'symbol' => $symbol,
            ])->orderBy(['id' => SORT_ASC])
            ->asArray()
            ->indexBy('key')
            ->all();

        foreach($params as $i => $param){
            $params[$i] = $param['value'];
        }

        return $params;
    }

}
