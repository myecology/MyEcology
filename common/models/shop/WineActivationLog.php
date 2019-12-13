<?php

namespace common\models\shop;

use Yii;
use api\models\User;

/**
 * This is the model class for table "iec_wine_activation_log".
 *
 * @property int $id
 * @property int $activation_id 激活码id
 * @property int $user_id
 * @property string $amount 发放数量
 * @property string $symbol 币种信息
 * @property string $created_at 创建时间
 */
class WineActivationLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_wine_activation_log';
    }

    public static $typeArr = [
        1 => '酒链',
        2 => '上级返利',
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['activation_id', 'user_id', 'amount', 'symbol'], 'required'],
            [['activation_id', 'user_id'], 'integer'],
            [['amount'], 'number'],
            [['created_at'], 'safe'],
            [['symbol'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'activation_id' => '活动',
            'user_id' => '用户id',
            'amount' => '发放数量',
            'symbol' => '币种',
            'created_at' => '创建时间',
        ];
    }

    /**
     * 获取用户信息
     * @return [type] [description]
     */
    public function getUser(){
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
