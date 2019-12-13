<?php

namespace common\models\bank;

use api\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "iec_bank_profit".
 *
 * @property int $id
 * @property int $uid 用户ID
 * @property int $product_id 产品ID
 * @property int $order_id 订单ID
 * @property string $amount 数量
 * @property string $symbol 数量
 * @property int $created_at 创建时间
 * @property int $type 创建时间
 */
class Profit extends \yii\db\ActiveRecord
{
    public static $typeArr = [
        Profit::TYPE_PROFIT => '收益',
        Profit::TYPE_PRINCIPAL => '本金',
    ];

    const TYPE_PROFIT = 0;
    const TYPE_PRINCIPAL = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_bank_profit';
    }

    /**s
     * 场景事物支持
     * @return [type] [description]
     */
    public function transactions()
    {
        return [
            'default' => self::OP_INSERT | self::OP_UPDATE,
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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'product_id', 'order_id', 'amount', 'symbol', 'type'], 'required'],
            [['uid', 'product_id', 'order_id', 'type'], 'integer'],
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
            'uid' => '用户id',
            'product_id' => '产品id',
            'order_id' => '订单id',
            'amount' => '数量',
            'created_at' => '创建时间',
            'type' => '类型',
            'symbol' => '币种',
        ];
    }

    /**
     * 添加收益
     * @param $type
     * @param $amount
     * @param Order $order
     * @return bool
     * @throws \yii\base\ErrorException
     */
    public static function addProfit($type, $amount, Order $order)
    {

        $model = new static();

        //收益币种
        $earnSymbol = Product::getEarnSymbol($order->product_id);
        $symbol = $type == static::TYPE_PRINCIPAL ? $order->symbol : $earnSymbol;

        $model->amount = $amount;
        $model->uid = $order->uid;
        $model->product_id = $order->product_id;
        $model->order_id = $order->id;
        $model->symbol = $symbol;
        $model->type = $type;

        if(false === $model->save()){
            throw new \yii\base\ErrorException('添加收益失败');
        }

        if($type == static::TYPE_PROFIT){
            //  发送通知 - 收益利息
            $user = \api\models\User::findOne($model->uid);
            if($user){
                \common\models\Message::addMessage(\common\models\Message::TYPE_FINANCIAL_PROFIT, $user, $earnSymbol, $model->amount, $model);
            }
        }elseif($type == static::TYPE_PRINCIPAL){
            //  发送通知 - 本金
            $user = \api\models\User::findOne($model->uid);
            if($user){
                \common\models\Message::addMessage(\common\models\Message::TYPE_FINANCIAL_PRINCIPAL, $user, $order->symbol, $model->amount, $model);
            }
        }

        return true;
    }

    /**
     * 验证今天是否发送 该笔收益
     * @param $type
     * @param $order_id
     * @param $user_id
     */
    public static function checkProfit($type,$order_id,$user_id){
        $flag = static::find()->andWhere([
            'order_id' => $order_id,
            'uid' => $user_id,
            'type' => $type,
        ])->select('id')->andWhere(['between','created_at',strtotime(date('Y-m-d')),strtotime(date('Y-m-d 23:59:59'))])->one();
        if(empty($flag)){
           return true;
        }
        return false;
    }


    /**
     * 关联产品
     *
     * @return void
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * 用户
     * @return void
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }
}
