<?php

namespace common\models;

use api\controllers\APIFormat;
use Yii;

/**
 * This is the model class for table "iec_transfer".
 *
 * @property int $id
 * @property int $sender_id 转出人ID
 * @property int $receiver_id 转入人ID
 * @property string $symbol 币种标识
 * @property int $currency_id 币种ID
 * @property string $amount 金额
 * @property int $created_at 创建时间
 * @property int $taken_at 接收时间
 * @property int $status 状态
 * @property string $description 描述
 */
class Transfer extends \yii\db\ActiveRecord
{
    public static $lib_status = [
        10 => '已提交',
        20 => '已接收',
        30 => '已超时',
    ];
    const STATUS_CREATED = 10;
    const STATUS_DONE = 20;
    const STATUS_TIMEOUT = 30;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_transfer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sender_id', 'receiver_id', 'currency_id', 'created_at', 'taken_at', 'status'], 'integer'],
            [['symbol', 'currency_id'], 'required'],
            [['amount'], 'number'],
            [['symbol'], 'string', 'max' => 32],
            [['description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sender_id' => 'Sender ID',
            'receiver_id' => 'Receiver ID',
            'symbol' => 'Symbol',
            'currency_id' => 'Currency ID',
            'amount' => 'Amount',
            'created_at' => 'Created At',
            'taken_at' => 'Taken At',
            'status' => 'Status',
            'description' => 'Description',
        ];
    }

    public function setDone()
    {
        $this->taken_at = time();
        $this->status = static::STATUS_DONE;
    }

    public function setTimeout()
    {
        $this->status = static::STATUS_TIMEOUT;
    }

    /**
     * @param int $sender_id
     * @param int $receiver_id
     * @param string $symbol
     * @param int $currency_id
     * @param float $amount
     * @param string $description
     * @return bool|Transfer
     */
    public static function createFromAPP(
        $sender_id,
        $receiver_id,
        $symbol,
        $currency_id,
        $amount,
        $description
    )
    {
        $now_timestamp = time();

        $model = new Transfer();
        $model->setAttributes([
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'symbol' => $symbol,
            'currency_id' => $currency_id,
            'amount' => $amount,
            'created_at' => $now_timestamp,
            'status' => static::STATUS_CREATED,
            'description' => $description,
        ]);

        //TODO 是否增加接收动作
        $model->setDone();

        return $model->save() ? $model : false;
    }

    /**
     * @param $id
     * @return array|null|Transfer
     */
    public static function findById($id)
    {
        return static::find()
            ->where(['id' => $id])
//            ->with(['receiver'])
            ->limit(1)
            ->one();
    }

    public function getSender()
    {
        return $this->hasOne(\api\models\User::className(), ['id' => 'sender_id']);
    }

    public function getReceiver()
    {
        return $this->hasOne(\api\models\User::className(), ['id' => 'receiver_id']);
    }

    public function getCurrency()
    {
        return $this->hasOne(Currency::className(), ['symbol' => 'symbol']);
    }

    public function attributesForView($viewer_id)
    {
        if (!$this->checkViewAccess($viewer_id)) { //检查是否有查看转账详情权限
            return false;
        }

        /**
         * @var \api\models\User $user_opposite
         */
        $user_opposite = $viewer_id == $this->sender_id ? $this->receiver: $this->sender;
        $result = [
            'user' => $user_opposite->attributeForTransfer(),
            'detail' => [
                'description' => (string)$this->description,
                'amount' => ($viewer_id == $this->sender_id ? '-' : '') . APIFormat::asMoney($this->amount),

                'icon' => strval($this->currency->iconAbs),
                'symbol' => strval($this->symbol),
                'created_at' => strval($this->created_at),

                'status' => strval($this->status),
                'statusText' => strval(static::$lib_status[$this->status]),
            ],
        ];

        return $result;
    }

    /**
     * @param $viewer_id
     * @return bool
     */
    public function checkViewAccess($viewer_id)
    {
        return in_array($viewer_id, [$this->sender_id, $this->receiver_id]);
    }

    /**
     * @return array
     */
    public function takersAttributesForView()
    {
        $model_receiver = $this->receiver;

        return [
            'amount' => APIFormat::asMoney($this->amount),
            'value' => APIFormat::asMoney(CurrencyPrice::convert($this->amount, $this->symbol)),

            'take_time' => (string)$this->taken_at,
            'nickname' => (string)$model_receiver->nickname,
            'headimgurl' => (string)$model_receiver->headimgurl,
            'userid' => (string)$model_receiver->userid,
        ];
    }

    /**
     * @param $user_id
     * @param $symbol
     * @return mixed
     */
    public static function amountTotalToday($user_id, $symbol)
    {
        return static::find()
            ->where([
                'sender_id' => $user_id,
                'symbol' => $symbol,
            ])->andWhere(['in', 'status', [static::STATUS_DONE, static::STATUS_CREATED]])
            ->andWhere('created_at > ' . strtotime('today'))
            ->sum('amount');
    }
}
