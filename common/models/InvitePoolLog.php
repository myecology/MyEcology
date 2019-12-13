<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\Wallet;
use common\models\WalletLog;

/**
 * This is the model class for table "iec_invite_pool_log".
 *
 * @property int $id
 * @property int $uid 用户ID
 * @property int $pool_id 糖果ID
 * @property int $type 类型，0:加款
 * @property string $symbol 标示
 * @property string $amount 数量
 * @property int $created_at 创建时间
 */
class InvitePoolLog extends \yii\db\ActiveRecord
{
    const TYPE_AMOUNT_ADD = 0;                  //  添加数量
    const TYPE_AMOUNT_SUB = 10;                 //  减少数量

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_invite_pool_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'pool_id', 'type'], 'integer'],
            [['pool_id', 'symbol', 'amount'], 'required'],
            [['amount'], 'number'],
            [['symbol'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'pool_id' => 'Pool ID',
            'type' => 'Type',
            'symbol' => 'Symbol',
            'amount' => 'Amount',
            'created_at' => 'Created At',
        ];
    }

    /**
     * 模型行为
     * @return [type] [description]
     */
    public function behaviors()
    {
        return [
            //创建时间
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }


     /**
      * 添加日志
      *
      * @param [type] $type
      * @return void
      */
    public function addLog($type)
    {
        switch ($type) {
            case static::TYPE_AMOUNT_ADD:
                $data = $this->addPool();
                break;
            case static::TYPE_AMOUNT_SUB:
                $data = $this->subPool();
                break;
            default:
                $data = false;
                break;
        }
        return $data;
    }

    /**
     * 锁仓糖果
     *
     * @param [type] $amount
     * @return void
     */
    private function addPool()
    {
        $wallet = Wallet::find()
            ->where(['user_id' => $this->uid, 'symbol' => $this->symbol])
            ->one();
        if(!$wallet->spendMoney($this->amount, WalletLog::TYPE_BUY_CANDY)){
            return false;
        }
        return true;
    }

    /**
     * 退还糖果
     *
     * @param [type] $amount
     * @return void
     */
    private function subPool()
    {
        $wallet = Wallet::find()
            ->where(['user_id' => $this->uid, 'symbol' => $this->symbol])
            ->one();
        if(!$wallet->earnMoney($this->amount, WalletLog::TYPE_BACK_CANDY)){
            return false;
        }
        return true;
    }
}
