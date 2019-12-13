<?php

namespace api\models;

use api\controllers\APIFormat;
use backend\models\Setting;
use common\models\Currency;
use common\models\GiftMoney;
use common\models\GiftMoneyTaker;
use common\models\UserAddress;
use common\models\Verification;
use common\models\Wallet;
use common\models\WalletLog;
use yii\base\ErrorException;
use yii\base\Model;

/**
 * GiftMoneyAdd form
 */
class GiftMoneyAddForm extends Model
{
    /**
     * @var float $amount //代表实际支付的总金额
     */
    public $amount;
    public $symbol;
    public $type;
    public $unit;
    public $amount_each;
    public $description;
    /**
     * @var string $taker
     */
    public $taker;

    private $currency;
    private $wallet;
    private $user;
    private $taker_object = null;

    public function beforeValidate()
    {
        // 被迫将type 值限制放在这里，否则影响设置场景
        $scenario = $this->type2Scenario($this->type);
        if ($scenario) {
            $this->setScenario($scenario);
            $flag = true;
        } else {
            throw new ErrorException('', 5106);
        }

        return $flag && parent::beforeValidate();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount', 'symbol', 'type', 'unit', 'amount_each', 'description', 'taker',], 'trim'],

            [['unit', 'type',], 'integer'],
            [['amount', 'amount_each',], 'number'],
            [['taker',], 'string', 'length' => 16],

            [['type',], 'in', 'range' => array_keys(GiftMoney::$lib_type)],
//            ['type', function($attribute, $param){
//                $scenario = $this->type2Scenario($this->type);
//                if(!$scenario){
//                    throw new ErrorException('', 5106);
//                }
//
//                $this->setScenario($scenario);
//            }],

            [['amount', 'symbol', 'type', 'description', 'taker',], 'required', 'on' => 'single'],
            [['amount'], 'compare', 'on' => 'single', 'compareValue' => 0, 'operator' => '>', 'type' => 'number'],

            ['taker', function ($attribute, $param) {
                if(empty($this->getUser()->verification) || $this->getUser()->verification->status != Verification::STATUS_DONE){
                    throw new \ErrorException('', 7002);
                }
                if (!$this->hasErrors() && !$this->getTakerObject()) { //发送对象是否存在判断, single->user; average/random->group
                    throw new ErrorException('', 5104);
                }

                if (!$this->hasErrors() && $this->type == GiftMoney::TYPE_SINGLE && $this->taker == $this->getUser()->userid) {
                    throw new ErrorException('', 5105);
                }
            }],

            [['symbol', 'type', 'unit', 'amount_each', 'description', 'taker',], 'required', 'on' => 'average'],
            [['amount_each', 'unit',], 'compare', 'on' => 'average', 'compareValue' => 0, 'operator' => '>', 'type' => 'number'],

            [['amount', 'symbol', 'type', 'unit', 'description', 'taker',], 'required', 'on' => 'random'],
            [['amount', 'unit',], 'compare', 'on' => 'random', 'compareValue' => 0, 'operator' => '>', 'type' => 'number'],

            ['amount', function ($attribute, $params) {
                if (!$this->hasErrors()
                    && isset($this->getCurrency()->params['gift_money_max'])
                    && $this->amount > $this->getCurrency()->params['gift_money_max']
                ) {
                    throw new ErrorException('', 5054);
                }
            }],
            ['amount', function ($attribute, $params) {
                if (!$this->hasErrors()
                    && isset($this->getCurrency()->params['decimal'])
                    && pow(10, Setting::read('gift_money_decimal')) * $this->amount < $this->unit) {
                    throw new ErrorException('', 5053);
                }
            }, 'on' => 'random'],

            ['symbol', function ($attribute, $params) {
                if (!$this->hasErrors() && !$this->getCurrency()) {
                    throw new ErrorException('', 5003);
                }
            }],

            //三种发红包过滤条件
            ['type', function ($attribute, $param) {

                if (!$this->hasErrors()) {
                    switch ($this->getScenario()) {
                        case "single":
                            $this->unit = 1;
                            $this->amount_each = 0;

                            $this->amount;
                            break;
                        case "average":
                            $this->unit;
                            $this->amount_each;

                            $this->amount = $this->unit * $this->amount_each;
                            break;
                        case "random":
                            $this->amount;
                            $this->unit;

                            $this->amount_each = $this->amount / $this->unit;
                            break;
                    }

                    if (!$this->amount || $this->amount > $this->getWallet()->getAmountAvailable()) { // balance validate
                        throw new ErrorException('', 5041);
                    }
                }
            }],

            ['amount', function($attribute, $param){
                if(!$this->hasErrors()
                    && isset($this->getCurrency()->params['giftmoney_amount_daily'])
                    && $this->getCurrency()->params['giftmoney_amount_daily'] < $this->amount + GiftMoney::amountTotalToday($this->getUser()->id, $this->symbol)
                ){
                    throw new \ErrorException('', 5016);
                }
            }],

        ];
    }

    /**
     * @param $type
     * @return string
     */
    public function type2Scenario($type)
    {
        $map = GiftMoney::$lib_type_scenario;
        if (!isset($map[$type])) {
            return false;
        }

        return $map[$type];
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
     * @return bool|int
     * @throws ErrorException
     * @throws \yii\db\Exception
     */
    public function save()
    {
        if ($this->validate()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                //扣钱、金额日志
                if (false === $this->getWallet()->spendMoney($this->amount, WalletLog::TYPE_GIFTMONEY_SEND)) {
                    throw new ErrorException('', 5101);
                }

                //创建红包记录
                $mode_gift_money = GiftMoney::createFromAPP(
                    $this->getUser()->getId(),
                    $this->amount,
                    $this->symbol,
                    $this->type,
                    $this->amount_each,
                    $this->unit,
                    $this->description,
                    $this->taker
                );
                if (false === $mode_gift_money) {
                    throw new ErrorException('', 5102);
                }

                if (false === GiftMoneyTaker::generateTakers($mode_gift_money)) {
                    throw new ErrorException('', 5107);
                }

                $transaction->commit();

                return $mode_gift_money->id;
            } catch (ErrorException $e) {
                $transaction->rollBack();

                throw new ErrorException('', $e->getCode());
            }
        }

        return false;
    }

    protected function getUser()
    {
        is_null($this->user) && $this->user = \Yii::$app->user->identity;
        return $this->user;
    }

    protected function getWallet()
    {
        is_null($this->wallet) && $this->wallet = Wallet::findByUserId($this->getUser()->getId(), $this->symbol);
        return $this->wallet;
    }

    protected function getCurrency()
    {
        is_null($this->currency) && $this->currency = Currency::findCurrencyBySymbol($this->symbol);
        return $this->currency;
    }

    /**
     * @return Group|User|null
     */
    protected function getTakerObject()
    {
        is_null($this->taker_object) && $this->taker_object = $this->type == GiftMoney::TYPE_SINGLE ? User::findByUserId($this->taker) : Group::findByGroupId($this->taker);

        return $this->taker_object;
    }

    protected function getAmountTotal(){

    }
}
