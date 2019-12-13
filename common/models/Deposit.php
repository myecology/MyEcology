<?php

namespace common\models;

use api\controllers\APIFormat;
use api\models\WalletAddress;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "iec_deposit".
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $wallet_id 钱包ID
 * @property string $symbol 币种标识
 * @property string $amount 金额
 * @property int $created_at 创建时间
 * @property int $updated_at 最后修改时间
 * @property int $status 状态
 * @property string $source 来源标识
 */
class Deposit extends \yii\db\ActiveRecord
{
    public static $lib_status = [
        10 => '内部充值',
        20 => '外部充值',
    ];
    const STATUS_PROCECCING = 10;
    const STATUS_COMPLETED = 20;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_deposit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'wallet_id', 'address_id', 'created_at', 'updated_at', 'status'], 'integer'],
            [['txid', 'address', 'fee_symbol', 'remark', 'transaction_hash'], 'default', 'value' => ''],
            [['symbol'], 'required'],
            [['amount'], 'number'],
            [['symbol'], 'string', 'max' => 32],
            ['txid', 'string', 'max' => 128],
            [['source'], 'string', 'max' => 128],
            ['fee', 'default', 'value' => 0],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'transaction_hash'=> '交易hash',
            'user_id' => '用户id',
            'wallet_id' => '钱包地址',
            'symbol' => '币种',
            'amount' => '金额',
            'created_at' => '添加时间',
            'updated_at' => '更新时间',
            'status' => '状态',
            'source' => '说明',
            'address' => '转出地址',
            'fee' => '手续费',
            'fee_symbol' => '手续费币种',
        ];
    }

    public function getCurrency()
    {
        return $this->hasOne(Currency::className(), ['symbol' => 'symbol']);
    }

    /**
     * 用户id查充值记录
     * @param $user_id
     * @return array
     */
    public static function findByUserId($user_id)
    {
        $models = static::find()
            ->with(['currency'])
            ->where(['user_id' => $user_id])
            ->orderBy(['status' => SORT_ASC, 'updated_at' => SORT_DESC, 'created_at' => SORT_DESC])
            ->all();

        $result = [];
        foreach ($models as $i => $model) {
            $result[] = static::apiAttributes($model);
        }

        return $result;
    }

    /**
     * @param Deposit $model
     * @return array
     */
    public static function apiAttributes(Deposit $model)
    {
        return [
            'id' => strval($model->id),
            'symbol' => $model->symbol,
            'icon' => (string)(is_null($model->currency) ? null : $model->currency->iconAbs),
            'amount' => (string)$model->amount,
            'created_at' => (string)$model->created_at,
            'updated_at' => (string)$model->updated_at,
            'status' => strval(isset(static::$lib_status[$model->status]) ? $model->status : null),
            'statusText' => strval(isset(static::$lib_status[$model->status]) ? static::$lib_status[$model->status] : null),
            'source' => (string)$model->source,
        ];
    }

    /**
     * @param $id
     * @return bool|null|\yii\db\ActiveRecord
     */
    public static function findById($id)
    {
        $model = static::find()
            ->where(['id' => $id])
            ->limit(1)
            ->one();

        if (!$model) {
            return false;
        }

        return $model;
    }

    /**
     * 过滤非本人请求ID
     * @param $id
     * @return array|bool
     */
    public static function findForDepositApi($id)
    {
        $model = static::findById($id);
        return $model && $model->user_id === Yii::$app->user->getId() ? static::apiAttributes($model) : false;
    }

    /**
     * @param float $amount
     * @param Wallet $wallet
     * @param Currency $currency
     * @param string $source
     * @return Deposit
     * @throws \ErrorException
     */
    public static function depositByTransaction($amount, Wallet $wallet, Currency $currency, $eth, $source = '外部转入')
    {
        $addressModel = WalletAddress::findOne(['model' => 'ETH', 'user_id' => $wallet->user_id]);
        $model = new Deposit();
        $model->setAttributes([
            'user_id' => $wallet->user_id,
            'wallet_id' => $wallet->id,

            'symbol' => $currency->symbol,
            'icon' => $currency->icon,
            'amount' => $amount,

            'created_at' => time(),
            'updated_at' => time(),
            'status' => static::STATUS_COMPLETED,
            'source' => $source,

            'address_id' => $addressModel->id,
            'address' => $eth->from_address,
            'fee' => $eth->gas_price * $eth->gas_used / 100000000000000000,
            'fee_symbol' => 'ETH',
            'remark' => $eth->input,
            'transaction_hash' => $eth->transaction_hash,
        ]);

        if (!$model->save()) {
            throw new \ErrorException('deposit creation failed ', 10001);
        }

        if (!$wallet->earnMoney($amount, WalletLog::TYPE_DEPOSIT)) {
            throw new \ErrorException('deposit creation failed ', 10002);
        }

        //  发送通知 - 充值
        $user = \api\models\User::findOne($model->user_id);
        \common\models\Message::addMessage(\common\models\Message::TYPE_TRANSACTION_DEPOSIT, $user, $model->symbol, $model->amount, $model);

        return $model;
    }

    /***
     * 内部转账
     */
    public static function depositNei(Wallet $wallet,$amount,Withdraw $withdraw)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if (empty($params) && $withdraw) {
                $withdraw->status = Withdraw::STATUS_COMPLETED;
                if (false === $withdraw->save()) {
                    throw new \ErrorException("提现订单状态更改失败");
                }
                $user = $withdraw->user;
                // 提现到账
                \common\models\Message::addMessage(\common\models\Message::TYPE_TRANSACTION_WITHDRAW, $user, $wallet->symbol, $amount, $withdraw);

            }
            $deposit = new Deposit();
            $deposit->setAttributes([
                'user_id' => $wallet->user_id,
                'wallet_id' => $wallet->id,
                'symbol' => $wallet->symbol,
                'amount' => $amount,
                'created_at' => time(),
                'updated_at' => time(),
                'status' => Deposit::STATUS_PROCECCING,
                'source' => '内部转账',
                'txid' => 'nei' . $withdraw->id . $withdraw->address . date("YmdHis"),
                'address_id' => $wallet->address->id,
                'address' => $wallet->address->address,
                'transaction_hash' => ''
            ]);
            if (false === $deposit->save()) {
                throw new \ErrorException('业务表添加失败', 6006);
            }
            var_dump($deposit->id);
            //加钱、金额日志
            echo '加钱';
            if (false === $wallet->earnMoney($amount, WalletLog::TYPE_DEPOSIT,$deposit->id)) {
                throw new \ErrorException('加钱失败', 6006);
            }
            //  发送充值通知
            \common\models\Message::addMessage(\common\models\Message::TYPE_TRANSACTION_DEPOSIT, $wallet->user, $wallet->symbol, $amount, $deposit);
            $transaction->commit();
            return true;
        }catch (\ErrorException $e) {
            $transaction->rollBack();
            var_dump($e->getMessage());
            return false;
        }
    }



    public function searchList($params,$user_id)
    {
        $query = static::find()
            ->andWhere(['user_id' => $user_id,'symbol'=>Yii::$app->request->post('symbol')])
            ->orderBy(['created_at' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'page' => Yii::$app->request->post('page') - 1,
            ]
        ]);

        $this->load($params, '');

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;

    }

    /**
     * 关联用户
     *
     * @return void
     */
    public function getWalletAddress()
    {
        return $this->hasOne(\api\models\WalletAddress::className(), ['id' => 'address_id']);
    }

    public function getUser()
    {
        return $this->hasOne(\api\models\User::className(), ['id' => 'user_id']);
    }

    /**
     * @return array
     */
    public function attributeForReward()
    {
        return [
            'user_id' => $this->user_id,
            'symbol' => $this->symbol,
            'amount' => APIFormat::asMoney($this->amount),
            'created_at' => (string)$this->created_at,
            'txid' => $this->txid,
            'address' => $this->address,
//            'remark' => $this->remark,
        ];
    }

    /**
     * @param Wallet $wallet
     * @param \api\models\Callback $callback
     * @return bool
     * @throws \ErrorException
     * @throws \yii\base\ErrorException
     */
    public static function deposit(Wallet $wallet,\api\models\Callback $callback){
        $txid = static::findOne(['txid'=>$callback->txid]);
        if(!empty($txid)){
            throw new \ErrorException('txid重复', 6006);
        }
        $deposit = new Deposit();
        $deposit->setAttributes([
            'user_id' => $wallet->user_id,
            'wallet_id' => $wallet->id,
            'symbol' => $wallet->symbol,
            'amount' => $callback->amount,
            'created_at' => time(),
            'updated_at' => time(),
            'status' => Deposit::STATUS_COMPLETED,
            'source' => '外部转入',
            'txid' => $callback->txid,
            'address_id' => $wallet->address->id,
            'address' => $callback->to,
            'transaction_hash' => $callback->txid,
        ]);
        if (false === $deposit->save()) {
            throw new \ErrorException(APIFormat::popError($deposit->getErrors()), 6006);
        }else{
            //加钱、金额日志
            if (false === $wallet->earnMoney($callback->amount, WalletLog::TYPE_DEPOSIT,$deposit->id)) {
                throw new \ErrorException('加钱失败', 6006);
            }else{
                return true;
            }
        }
        //  发送充值通知
        \common\models\Message::addMessage(\common\models\Message::TYPE_TRANSACTION_DEPOSIT, $wallet->user, $wallet->symbol, $callback->amount, $deposit);
    }

}
