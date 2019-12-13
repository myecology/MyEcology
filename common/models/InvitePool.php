<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "iec_invite_pool".
 *
 * @property int $id
 * @property int $currency_id 币种ID
 * @property string $symbol 币种标识
 * @property string $amount 奖金池金额
 * @property string $amount_left 奖金池剩余
 * @property int $created_at 创建时间
 * @property int $expired_at 过期时间
 * @property int $status 状态
 * @property string $prize 奖金包金额
 * @property int $prize_registerer 注册人比重
 * @property int $prize_inviter 邀请人比重
 * @property int $prize_grand_inviter 父级邀请人比重
 * @property int $prize_grand_grand_inviter 爷级邀请人比重
 * @property string $name      项目名称
 * @property string $icon      图标
 * @property int $type      类型
 * @property string $background    海报背景图
 */
class InvitePool extends \yii\db\ActiveRecord
{
    public static $lib_status = [
        0 => '关闭',
        10 => '开启'
    ];
    const STATUS_OFF = 0;
    const STATUS_ON = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_invite_pool';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['currency_id', 'symbol'], 'required'],
            [['currency_id', 'type', 'created_at', 'expired_at', 'status', 'prize_registerer', 'prize_inviter', 'prize_grand_inviter', 'prize_grand_grand_inviter'], 'integer'],
            [['amount', 'amount_left', 'prize'], 'number'],
            [['symbol', 'name'], 'string', 'max' => 32],
            [['icon', 'background'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'type' => '类型',
            'icon' => '图标',
            'background' => '背景图',
            'currency_id' => 'Currency ID',
            'symbol' => 'Symbol',
            'amount' => 'Amount',
            'amount_left' => 'Amount Left',
            'created_at' => 'Created At',
            'expired_at' => 'Expired At',
            'status' => 'Status',
            'prize' => 'Prize',
            'prize_registerer' => 'Prize Registerer',
            'prize_inviter' => 'Prize Inviter',
            'prize_grand_inviter' => 'Prize Grand Inviter',
            'prize_grand_grand_inviter' => 'Prize Grand Grand Inviter',
        ];
    }

    public static function loadAvailable()
    {
        $now_time = time();

        return static::find()
            ->where("(expired_at is null) OR (expired_at=0) OR (expired_at>{$now_time})")
            ->andWhere([
                'status' => static::STATUS_ON,
            ])->andWhere(['>', 'amount_left', 0])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();
    }

    /**
     * 获取项目方糖果
     *
     * @param [type] $id
     * @return void
     */
    public static function getPool($uid, $expire = true)
    {
        $nowTime = time();

        $query = static::find()
            ->where(['status' => static::STATUS_ON]);
        
        //  是否官方项目
        if($uid == 0){
            $query->andWhere(['type' => 1]);
        }else{
            $query->andWhere(['uid' => $uid]);
        }

        //  是否需要过期时间
        if($expire){
            $query->andWhere("(expired_at is null) OR (expired_at=0) OR (expired_at>{$nowTime})");
        }

        $data = $query->orderBy('id desc')->one();

        return $data;
    }

    /**
     * @param $amount
     * @return bool
     */
    public function reduceAmount($amount)
    {
        if($this->amount_left == 0){
            return false;
        }
        //  如果数量不够直接扣除
        if($this->amount_left < $amount){
            $amount = $this->amount_left;
        }

        $this->amount_left -= $amount;
        return false === $this->save() ? false : $amount;
    }
}
