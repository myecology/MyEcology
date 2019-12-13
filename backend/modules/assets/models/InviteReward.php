<?php

namespace backend\modules\assets\models;

use Yii;

/**
 * This is the model class for table "iec_invite_reward".
 *
 * @property int $id
 * @property int $invitation_id 邀请记录ID
 * @property int $level 层级
 * @property int $currency_id 币种ID
 * @property string $symbol 币种标识
 * @property string $amount 奖励金额
 * @property int $created_at 创建时间
 * @property int $user_id_rewarded 收益人
 * @property int $registerer_id 注册人ID
 * @property string $registerer_reward 注册人得奖
 */
class InviteReward extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_invite_reward';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['invitation_id', 'level', 'currency_id', 'created_at', 'user_id_rewarded', 'registerer_id'], 'integer'],
            [['currency_id', 'symbol'], 'required'],
            [['amount', 'registerer_reward'], 'number'],
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
            'invitation_id' => 'Invitation ID',
            'level' => 'Level',
            'currency_id' => 'Currency ID',
            'symbol' => 'Symbol',
            'amount' => 'Amount',
            'created_at' => 'Created At',
            'user_id_rewarded' => 'User Id Rewarded',
            'registerer_id' => 'Registerer ID',
            'registerer_reward' => 'Registerer Reward',
        ];
    }
}
