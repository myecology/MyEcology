<?php

namespace common\models\bank;

use Yii;

/**
 * This is the model class for table "iec_bank_mouth".
 *
 * @property int $id
 * @property int $month 月
 * @property string $amount 余额
 * @property int $created_at 创建时间
 * @property string $symbol 币种
 */
class BankMouth extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_bank_mouth';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'month', 'amount', 'created_at', 'symbol'], 'required'],
            [['id', 'month', 'created_at'], 'integer'],
            [['amount'], 'number'],
            [['symbol'], 'string', 'max' => 20],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'month' => 'Month',
            'amount' => 'Amount',
            'created_at' => 'Created At',
            'symbol' => 'Symbol',
        ];
    }
}
