<?php

namespace backend\modules\assets\models;

use Yii;
use api\models\User;

/**
 * This is the model class for table "iec_invitation".
 *
 * @property int $id
 * @property int $registerer_id 注册人ID
 * @property int $inviter_id 邀请人ID
 * @property int $created_at 创建时间
 * @property int $level 层级
 */
class Invitation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_invitation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['registerer_id', 'inviter_id', 'created_at', 'level'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'registerer_id' => '注册ID',
            'inviter_id' => '邀请ID',
            'created_at' => '注册时间',
            'level' => '层级',
        ];
    }

    /**
     * 关联邀请记录
     *
     * @return void
     */
    public function getInviteReward()
    {
        return $this->hasMany(InviteReward::className(), ['invitation_id' => 'id']);
    }

    /**
     *  注册人关联用户
     *
     * @return void
     */
    public function getRegisterUser()
    {
        return $this->hasOne(User::className(), ['id' => 'registerer_id']);
    }

    /**
     * 邀请人关联用户
     *
     * @return void
     */
    public function getInviterUser()
    {
        return $this->hasOne(User::className(), ['id' => 'inviter_id']);
    }
}
