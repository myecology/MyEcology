<?php

namespace backend\modules\assets\models;

use Yii;
use api\models\User;


/**
 * This is the model class for table "iec_gift_money".
 *
 * @property int $id
 * @property int $sender_id 发红包用户ID
 * @property string $amount 金额
 * @property string $amount_left 剩余金额
 * @property int $created_at 创建时间
 * @property int $status 状态
 * @property int $type 类型
 * @property string $amount_unit 单个金额
 * @property int $count 红包个数
 * @property int $expired_at 过期时间
 * @property string $description 红包祝福语
 * @property string $bind_taker 绑定接收对象
 * @property string $symbol 币种标识
 */
class GiftMoney extends \yii\db\ActiveRecord
{

    public static $lib_type = [
        10 => '个人红包',
        20 => '普通红包',//群等额
        30 => '拼手气红包',//群随机红包
    ];
    public static $lib_type_scenario = [
        10 => 'single',
        20 => 'average',
        30 => 'random',
    ];

    public static $lib_status = [
        10 => '开放',
        20 => '过期',
        30 => '已领完',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_gift_money';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sender_id', 'created_at', 'status', 'type', 'count', 'expired_at'], 'integer'],
            [['amount', 'amount_left', 'amount_unit'], 'number'],
            [['bind_taker'], 'required'],
            [['description', 'bind_taker'], 'string', 'max' => 255],
            [['symbol'], 'string', 'max' => 32],
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
            'amount' => '金额',
            'amount_left' => '剩余金额',
            'created_at' => '发送时间',
            'status' => '状态',
            'type' => '类型',
            'amount_unit' => '平均金额',
            'count' => '数量',
            'expired_at' => '过期时间',
            'description' => '描述',
            'bind_taker' => '绑定Taker',
            'symbol' => '币种',
        ];
    }


    /**
     * 关联发送人ID
     *
     * @return void
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'sender_id']);
    }

    /**
     * 关联领取ID
     *
     * @return void
     */
    public function getGiftMoneyTaker()
    {
        return $this->hasMany(GiftMoneyTaker::className(), ['gift_money_id' => 'id']);
    }

}
