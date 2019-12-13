<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "iec_dpos_order".
 *
 * @property int $id
 * @property string $order_sn 订单号
 * @property string $symbol 币种标识
 * @property string $amount 金额
 * @property int $status 支付状态 0未支付 1已支付
 * @property int $created_at 添加时间
 * @property string $telephone 电话号码
 * @property string $userid 用户唯一标识
 */
class DposOrder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_dpos_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount'], 'number'],
            [['status', 'created_at'], 'integer'],
            [['order_sn', 'symbol'], 'string', 'max' => 32],
            [['telephone'], 'string', 'max' => 11],
            [['userid'], 'string', 'max' => 255],
            [['order_sn'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_sn' => 'Order Sn',
            'symbol' => 'Symbol',
            'amount' => 'Amount',
            'status' => 'Status',
            'created_at' => 'Created At',
            'telephone' => 'Telephone',
            'userid' => 'Userid',
        ];
    }


    /**
     * 添加订单
     * @param $order_sn
     * @param $userid
     * @param $telephone
     * @param $symbol
     * @param $amount
     */
    public function create($order_sn,$userid,$telephone,$symbol,$amount)
    {
        $model = new DposOrder();
        $model->setAttributes([
            'order_sn' => $order_sn,
            'userid' => $userid,
            'telephone' => $telephone,
            'symbol' => $symbol,
            'amount' => $amount,
            'created_at' => time()
        ]);
        return $model->save() ? $model : false;
    }

    /**
     * 根据订单编号查找订单
     * @param $order_sn
     * @return DposOrder|null
     */
    public static function findByOrderSn($order_sn)
    {
        return static::findOne([
            'order_sn' => $order_sn,
        ]);
    }


    /**
     * 修改状态
     * @param $status
     * @return bool
     */
    public  function updateStatus($status)
    {
        $this->status = $status;
        return $this->save();
    }



}
