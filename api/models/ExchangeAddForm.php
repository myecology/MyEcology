<?php

namespace api\models;

use common\models\CurrencyPrice;
use common\models\Exchange;
use common\models\Wallet;
use common\models\WalletLog;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use common\models\Currency;
use api\models\User;
use backend\models\Setting;
use common\models\ExchangeWt;

/**
 * Exchange form
 */
class ExchangeAddForm extends Model
{
    public $user_id;
    public $symbol;
    public $amount;
    public $e_symbol;
    public $currency;
    public $e_currency;

    public function beforeValidate()
    {
        $this->user_id = \Yii::$app->user->getId();
        return parent::beforeValidate();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'symbol', 'amount', 'e_symbol'], 'required'],
            ['amount', function ($attribute, $params) {
                if (!$this->hasErrors()) {
                    $wallet = Wallet::findByUserId($this->user_id,$this->symbol);
                    if ($wallet->amount - $wallet->amount_lock < $this->amount) {
                        $this->addError($attribute,'用户余额不足');
                    }
                }
            }],
            ['symbol', function($attribute, $params) {
                if (!$this->hasErrors()) {
                    $this->currency =  Currency::findCurrencyBySymbol($this->symbol);

                    if (!$this->currency || !$this->exchange($this->symbol)) {
                        $this->addError($attribute,'兑换币种不存在');
                    }
                    $params = $this->currency->params;
                    if(isset($params['exchange_status'])&& (int)$params['exchange_status'] == 1){
                        throw new \ErrorException('该币种不支持兑换', 5051);
                    }
                }
            }],
            ['e_symbol', function($attribute, $params) {
                if (!$this->hasErrors()) {
                    $this->e_currency =  Currency::findCurrencyBySymbol($this->e_symbol);
                    if (!$this->e_currency || !$this->exchange($this->e_symbol)) {
                        $this->addError($attribute,'兑换币种不存在');
                    }
                    if($this->e_symbol == 'WT1918'){
                        $this->addError($attribute,'不支持兑换该币种');
                    }
                }
            }],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'symbol' => '币种',
            'currency_id' => 'Currency ID',
            'amount' => '金额',
            'e_symbol' => '兑换币种',
            'e_currency_id' => 'E Currency ID',
            'price' => 'Price',
            'created_at' => 'Created At',
        ];
    }


    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        $model = new Exchange();
        try {
            $transaction = \Yii::$app->db->beginTransaction();
            $model->loadForCreateForm($this->user_id,$this->symbol,$this->currency,$this->e_symbol,$this->e_currency,$this->amount);
            if (false === $model->save()) {
                throw new \ErrorException('兑换失败', 8001);
            }
            $poundage = $this->currency->currencyPrice->poundage;//手续费
            $spendWallet = Wallet::findByUserId($this->user_id, $this->symbol);
            if (!$spendWallet) {
                throw  new \Exception('获取用户钱包失败',8004);
            }
            $spendRes = $spendWallet->spendMoney($this->amount,WalletLog::TYPE_EXCHANGE_SPEND,$model->id.'-'.$poundage);
            if ($spendRes === false) {
                throw  new \Exception('一键兑换扣款失败',8002);
            }
            //目前兑换币种汇率
            $earnWallet = Wallet::findByUserId($this->user_id,$this->e_symbol);
            if (!$earnWallet) {
                throw  new \Exception('获取用户钱包失败',8004);
            }
            $earnAmount = (($this->amount - $poundage) * $this->currency->currencyPrice->price) / $this->e_currency->currencyPrice->price;
            $earnRes = $earnWallet->earnMoney($earnAmount, WalletLog::TYPE_EXCHANGE_EARN);
            if ($earnRes === false) {
                throw  new \Exception('一键兑换扣款进账失败',8003);
            }
            if($this->symbol === 'WT1918'){
                $this->wtprice($earnAmount);
            }
            $transaction->commit();
            $result = $model;
        } catch (\ErrorException $e) {
            $transaction->rollBack();
            throw new \ErrorException($e->getMessage(), $e->getCode());
        }

        return $result;
    }

    /**
     * WT1918兑换其他币种
     * @return [type] [description]
     */
    public function wtprice($earnAmount){
        //查找指定用户
        $wt_user = Setting::read('wallet_wt1918','wallet');
        if(empty($wt_user)){
            throw  new \Exception('没有设置指定用户',8404);
        }
        $user = User::findOne(['username'=>$wt_user]);
        if(empty($user)){
            throw  new \Exception('指定用户不存在',8404);
        }
        $poundage = $this->currency->currencyPrice->poundage;//手续费
        //记录wt1918兑换记录
        $exchangewt = new ExchangeWt();
        $exchangewt->user_id = $this->user_id;
        $exchangewt->e_symbol = $this->e_symbol;
        $exchangewt->amount = $earnAmount;
        $exchangewt->wt_number = $this->amount;
        $exchangewt->fee = $poundage;
        $exchangewt->create_time = date("Y-m-d H:i:s");
        $exchangewt->symbol_price = CurrencyPrice::getPriceBySymbol($this->e_symbol);
        // var_dump($exchangewt->save()->getRawSql());die;
        if(!$exchangewt->save()){
            throw  new \Exception('兑换币种记录添加失败',8500);
        }

        //查询用户兑换的币种余额
        $wallet = Wallet::findOneByWallet($this->e_symbol,$user->id);
        // var_dump($earnAmount);die;
        if($earnAmount > $wallet->amount){
            throw  new \Exception('商户余额不足，稍后兑换！',8500);
        }
        //扣除指定用户币种余额
        $spendRes = $wallet->spendMoney($earnAmount,WalletLog::TYPE_EXCHANGE_SPEND);
        if ($spendRes === false) {
            throw  new \Exception('扣除指定用户金额失败',8002);
        }
        //增加指定用户wt1918
        $wallet_wt = Wallet::findOneByWallet($this->symbol,$user->id);
        $earnRes = $wallet_wt->earnMoney($this->amount, WalletLog::TYPE_EXCHANGE_EARN);
        if ($earnRes === false) {
            throw  new \Exception('指定用户进账失败',8003);
        }
    }


    public function exchange($symbol){
        $model = new CurrencyPrice();
        $result = $model->find()->select('symbol')->where(['is_exchange' => 1,'symbol'=>$symbol])->asArray()->one();
        if(empty($result)){
            return false;
        }else{
            return true;
        }

    }




}