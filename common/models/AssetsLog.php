<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "rc_assets_log".
 *
 * @property int $id
 * @property int $user_id
 * @property int $token_assets_id
 * @property string $amount 数量
 * @property int $created_at
 * @property int $token_id
 */
class AssetsLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rc_assets_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'token_assets_id', 'created_at','token_id'], 'integer'],
            [['amount'], 'number'],
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
            'token_assets_id' => 'Token Assets ID',
            'amount' => 'Amount',
            'created_at' => 'Created At',
        ];
    }
}
