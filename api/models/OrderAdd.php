<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/6/22
 * Time: 10:58 AM
 */

namespace api\models;
use api\controllers\APIFormat;
use common\models\bank\Log;
use common\models\bank\Product;
use common\models\bank\Order;
use common\models\Message;
use common\models\Wallet;
use yii\base\Model;

class OrderAdd extends Model
{

    public $product_id;
    public $amount;
    public $password;

    private $product_model;
    private $user_model;

    public function rules()
    {
        return [
            [['product_id', 'amount', 'password'],'required'],
            ['password', 'string', 'min' => 6],
            ['password', 'validatePassword'],
            ['amount','validateAmount']
        ];
    }

    public function validatePassword($attribute, $params)
    {
        $this->user_model = \Yii::$app->user->identity;
        if (!$this->user_model || !$this->user_model->validatePassword($this->password)) {
            throw new \ErrorException('', 4014);
        }
    }
    public function validateAmount($attribute, $params)
    {
        $this->product_model = Product::findOne($this->product_id);
        if(empty($this->product_model)){
            throw new \ErrorException('', 8500);
        }
        $wallet = Wallet::findOneByUserIdAndSymbol($this->user_model->id, $this->symbol);
        if(!$wallet || $this->amount > ($wallet['amount'] - $wallet['amount_lock'])){
            throw new \ErrorException('', 5041);
        }

        if($this->product_model->income->day == 0){
            $day = $this->day + 2;
            $this->endtime = strtotime(date('Y-m-d', strtotime("+".$day."day"))) - 1+43200;
        }else{
            $this->endtime = strtotime(date('Y-m-d', strtotime("+2day"))) - 1+43200;
        }

        //  判断是否超过最大限制
        if(($this->product_model->amount + $this->amount) > $this->product_model->max_amount){
            throw new \ErrorException('', 8501);
        }

        //  判断是否个人最大， 或者 最低数量
        $userAmount = Order::find()->where(['uid' => $this->user_model->id, 'product_id' => $this->product_model->id])->sum('amount');
        $userAmount = $userAmount ?: 0;
        $userAmount += $this->amount;
        if($userAmount > $this->product_model->user_amount){
            throw new \ErrorException('', 8502);
        }
        if($this->amount < $this->product_model->min_amount){
            throw new \ErrorException('', 8503);
        }

    }

    public function orderAdd(){
        if($this->validate()){
            $model = new Order();
            $model->setAttrOrderAdd($this->product_model,$this);
            if(!$model->save()){
                throw new \ErrorException('', 8504);
            }
            //  消费金额
            $model->spendAmount();
            //  锁仓银行日志
            Log::bankLog(Log::TYPE_LOCK_PRODUCT, $model);
            //  更新产品数量
            $product = Product::updateAmount($model->product_id);

            //收益币种
            //$earnSymbol = Product::getEarnSymbol($model->product_id);

            //  写入预期收益
            /*$amount = $model->amount * $product->super_rate / 100;
            SupernodeProfit::addProfit($amount , $earnSymbol, SupernodeProfit::TYPE_BANK_EXPECT, $model);*/

            $massage = [
                'product_name' => $this->product_model->name,
                'payment' => $this->amount.$this->product_model->symbol,
            ];
            //发送通知
            Message::addMessage(Message::TYPE_FINANCIAL_BUY,$this->user_model->id,$massage);

            return true;
        }else{
            throw new \ErrorException(APIFormat::popError($this->getErrors()), 8500);
        }
    }


}