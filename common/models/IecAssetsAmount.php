<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "iec_assets_amount".
 *
 * @property int $id
 * @property string $amount 解锁数量
 * @property string $amount_lock 锁定数量
 * @property int $updated_at
 * @property int $user_id 用户id
 */
class IecAssetsAmount extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_assets_amount';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount', 'amount_lock'], 'number'],
            [['amount_lock', 'updated_at', 'user_id'], 'required'],
            [['updated_at', 'user_id'], 'integer'],
            [['user_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'amount' => '解锁数量',
            'amount_lock' => '锁定数量',
            'updated_at' => 'Updated At',
            'user_id' => '用户id',
        ];
    }
}
