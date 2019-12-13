<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "iec_shop_withdraw".
 *
 * @property string $id
 * @property int $user_id 用户id
 * @property string $money 提现金额
 * @property string $symbol 提现币种
 * @property int $create_time 提现时间
 */
class ShopWithdraw extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_shop_withdraw';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'create_time'], 'integer'],
            [['money'], 'number'],
            [['symbol'], 'string', 'max' => 20],
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
            'money' => 'Money',
            'symbol' => 'Symbol',
            'create_time' => 'Create Time',
        ];
    }

    /**
     * 查询每天提现次数
     * @return [type] [description]
     */
    public static function withdrawcount($user_id){
        $count = static::find()->andWhere(['user_id' => $user_id])->andWhere([
                'between','create_time',date('Y-m-d 0:0:0'),date('Y-m-d 23:59:59')
            ])->count();
        return $count;
    }
}
