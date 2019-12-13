<?php

namespace common\models\shop;

use api\controllers\APIFormat;
use common\models\Currency;
use Yii;

/**
 * This is the model class for table "iec_shop_wallet".
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $flag 用户id
 * @property string $amount 余额
 * @property string $symbol 币种
 * @property string $model 模型
 * @property string $deposit 收钱金额
 * @property string $withdraw_amount 提现金额
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class ShopWallet extends \yii\db\ActiveRecord
{

    const TYPE_WITHDRAW = 10;
    const TYPE_DEPOSIT = 20;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_shop_wallet';
    }

    /**
     * 乐观锁
     * @return null|string
     */
    public function optimisticLock()
    {
        return 'flag';
    }

    /**
    * {@inheritdoc}
    */
    public function rules()
    {
        return [
            [['user_id', 'symbol', 'model', 'deposit', 'withdraw_amount'], 'required'],
            [['user_id','flag'], 'integer'],
            [['amount', 'deposit', 'withdraw_amount'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['symbol', 'model'], 'string', 'max' => 30]
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
            'amount' => 'Amount',
            'symbol' => 'Symbol',
            'model' => 'Model',
            'deposit' => 'Deposit',
            'withdraw_amount' => 'Withdraw Amount',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 创建并获取钱包
     * @param $user_id
     * @param $symbol
     * @return ShopWallet|null
     * @throws \ErrorException
     */
    public static function createdWallet($user_id,$symbol){
        $wallet = static::findOne(['user_id' => $user_id,'symbol' => $symbol]);
        if(empty($wallet)){
            $model = Currency::findCurrencyBySymbol($symbol)->model;
            $wallet = new static();
            $walletData = [
                'user_id' => $user_id,
                'amount' => 0,
                'flag' => 0,
                'symbol' => $symbol,
                'model' => $model,
                'deposit' => 0,
                'withdraw_amount' => 0,
            ];
            $wallet->setAttributes($walletData);
            if(!$wallet->save()){
                throw new \ErrorException(APIFormat::popError($wallet->getErrors()),999);
            }
        }
        return $wallet;
    }

    /**
     * 操作余额
     * @param $amount
     * @param $type
     * @return bool
     */
    public function operation($amount,$type){
        $balance = $this->amount;
        $amount = abs($amount);

        if($amount <= 0){
            return false;
        }

        switch ($type){
            case static::TYPE_WITHDRAW:
                $this->amount -= $amount;
                $this->withdraw_amount += $amount;
                break;
            case static::TYPE_DEPOSIT:
                $this->amount += $amount;
                $this->deposit += $amount;
                break;
            default :
                return false;
        }
        if($this->amount < 0){
            return false;
        }
        if($this->updateInternal()){
            return  $balance;
        }
        return false;
    }
}
