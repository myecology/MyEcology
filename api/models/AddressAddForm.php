<?php

namespace api\models;

use api\controllers\APIFormat;
use common\models\Currency;
use common\models\UserAddress;
use common\models\Withdraw;
use common\modules\ethereum\models\EthereumService;
use Web3\Utils;
use yii\base\ErrorException;
use yii\base\Model;
use yii\helpers\Url;

/**
 * AddressAdd form
 */
class AddressAddForm extends Model
{
    public $address;
    public $alias;
    public $symbol;

    /**
     * @var string $model 根据币种配置的公链模型，直接传值
     */
    private $model = null;
    private $currency;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['address', 'alias', 'symbol'], 'trim'],
            [['symbol'], 'required', 'message' => '无法获取币种信息，请更新版本'],
            [['address',], 'required'],

            ['address', 'unique', 'targetClass' => '\common\models\UserAddress', 'message' => '该钱包地址已存在',
                'filter' => [
                    'user_id' => \Yii::$app->user->getId()
                ],],

            //['username', 'match', 'pattern' => '/^[1][345678][0-9]{9}$/'],

            ['alias', 'string', 'max' => 20],
            ['address', function($attribute, $params){
                if(!Withdraw::addressValid($this->address,$this->getCurrency()->model)){
//                    $this->addError($attribute, APIFormat::$code[5048]);
                    throw new \ErrorException(APIFormat::$code[5048], 5048);
                }
            }],
            ['symbol', function ($attribute, $param) {
                if (!$this->hasErrors() && $this->symbol && $this->getCurrency()) {
                    $this->model = $this->getCurrency()->model;

                    if ($this->model == 'ETH' && !Utils::isAddress($this->address)) {
//                        $this->addError($attribute, APIFormat::$code[5048]);
                        throw new \ErrorException(APIFormat::$code[5048], 5048);
                    }
                }

                return true;
            }],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'address' => '钱包地址',
            'alias' => '备注名',
        ];
    }

    /**
     * @return bool|UserAddress
     */
    public function save()
    {
        if ($this->validate()) {
            $model = new UserAddress();
            $model->setAttributes([
                'user_id' => \Yii::$app->user->getId(),
                'address' => $this->address,
                'name' => $this->alias ?: $this->generateName(),
                'created_at' => time(),
                'model' => $this->model ?: UserAddress::detectModelByAddress($this->address),
            ]);

            return $model->save() ? $model : false;
        }
        return false;
    }

    protected function getCurrency()
    {
        is_null($this->currency) && $this->currency = Currency::findCurrencyBySymbol($this->symbol);
        return $this->currency;
    }

    protected function generateName()
    {
        return date('钱包:Y-m-d H:i:s');
    }
}
