<?php
namespace backend\modules\bank\models;

use common\models\bank\Order;
use common\models\bank\Profit;
use common\models\WalletLog;
use yii\base\Model;

class ReissuedOrderModel extends Model
{
    public $amount;

    public function rules()
    {
        return [
            ['amount','required'],
            [['amount'],'compare', 'compareValue' => 0, 'operator' => '>'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'amount' => '补发金额',
        ];
    }

    /**
     * 补发
     * @param Order $order
     * @return bool
     * @throws \ErrorException
     * @throws \yii\db\Exception
     */
    public function reissued(Order $order){
        if(!$this->validate()){
            return false;
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try{
            $order->earnAmount($this->amount, WalletLog::TYPE_BANK_PRODUCT_PROFIT,$order->earn_symbol);
            Profit::addProfit(Profit::TYPE_PROFIT,$this->amount,$order);
            $transaction->commit();
            return true;
        }catch (\Exception $exception){
            $transaction->rollBack();
            throw new \ErrorException($exception->getMessage());
        }
    }
}