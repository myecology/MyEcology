<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\Wallet;
use common\models\WalletLog;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeBehavior;

/**
 * This is the model class for table "iec_supernode".
 *
 * @property int $id
 * @property int $uid 用户ID
 * @property int $status 状态：10开启/0关闭
 * @property int $lvl 超级节点等级
 * @property string $amount 数量
 * @property string $description 描述
 * @property int $created_at
 */
class Supernode extends ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_supernode';
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
                    self::EVENT_BEFORE_UPDATE => ['created_at'],
                ],
            ],
            //  uid
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'uid',
                ],
                'value' => function ($event) {
                    return Yii::$app->user->identity->id;
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['amount', 'required'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            ['status', function($attribute, $params){
                if($this->isNewRecord){
                    $data = static::find()->where(['uid' => Yii::$app->user->identity->id, 'status' => self::STATUS_ACTIVE])->one();
                    if($data){
                        $this->addError($attribute, '您已经是超级节点');
                    }
                }
            }],
            [['amount'], 'number'],
            ['description', 'default', 'value' => ''],
            [['description'], 'string', 'max' => 255],
        ];
    }

    /**
     * 写入后事件
     *
     * @param [type] $insert
     * @param [type] $changedAttributes
     * @return void
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if($insert){
            if(!$this->buySupernode()){
                throw new \yii\base\ErrorException('支付失败');
            }
        }
        if(!$insert && $this->status == static::STATUS_DELETED){
            if(!$this->askSupernode()){
                throw new \yii\base\ErrorException('退款失败');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'status' => 'Status',
            'lvl' => 'Lvl',
            'amount' => 'Amount',
            'description' => 'Description',
            'created_at' => 'Created At',
        ];
    }


    /**
     * 购买超级节点
     *
     * @return void
     */
    public function buySupernode()
    {
        $symbol = \backend\models\Setting::read('supernode_symbol');
        $wallet = Wallet::find()
            ->where(['user_id' => $this->uid, 'symbol' => $symbol])
            ->one();
        if(!$wallet->spendMoney($this->amount, WalletLog::TYPE_BUY_SUPERNODE)){
            return false;
        }
        return true;
    }

    /**
     * 退还超级节点费用
     *
     * @return void
     */
    public function askSupernode()
    {
        $symbol = \backend\models\Setting::read('supernode_symbol');
        $wallet = Wallet::find()
            ->where(['user_id' => $this->uid, 'symbol' => $symbol])
            ->one();
        if(!$wallet->earnMoney($this->amount, WalletLog::TYPE_ASK_SUPERNODE)){
            return false;
        }
        return true;
    }
}
