<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "iec_red_record".
 *
 * @property string $id
 * @property string $number 红包码段
 * @property int $uid 领取用户
 * @property string $money 红包金额
 * @property string $currency 红包币种
 * @property int $create_time 领取时间
 * @property int $rid 红包id
 * @property int $flag 锁
 */
class RedRecord extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_red_record';
    }

    /**
     * 乐观锁
     * @return null|string
     */
    public function optimisticLock()
    {
        return 'flag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number', 'uid', 'money', 'currency', 'create_time', 'rid'], 'required'],
            [['uid', 'create_time', 'rid', 'flag'], 'number'],
            [['money'], 'number'],
            [['number'], 'string', 'max' => 16],
            [['currency'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Number',
            'uid' => 'Uid',
            'money' => 'Money',
            'currency' => 'Currency',
            'create_time' => 'Create Time',
            'rid' => 'Rid',
            'flag' => 'Flag',
        ];
    }
}
