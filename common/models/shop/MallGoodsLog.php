<?php

namespace common\models\shop;

use api\controllers\APIFormat;
use api\models\Alcohol;
use api\models\User;
use Yii;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "iec_mall_goods_log".
 *
 * @property int $id
 * @property string $goods_name 商品名
 * @property string $order_sn 订单编号
 * @property string $amount 总金额
 * @property string $symbol 币种
 * @property int $order_goods_id 订单商品id
 * @property string $activity_name 活动名称
 * @property string $activity 活动简称
 * @property int $status 状态
 * @property int $number 状态
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property int $user_id 用户id
 * @property int $shop_id 商家id
 */
class MallGoodsLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_mall_goods_log';
    }

    public static $statusArr = [
        1 => '初始化',
        2 => '带执行活动',
        3 => '活动执行成功',
        4 => '活动执行失败'
    ];

    const STATUS_DEFAULT = 1;
    const STATUS_PAY_SUCCESS = 2;
    const STATUS_ACTIVITY_SUCCESS = 3;
    const STATUS_ACTIVITY_FAIL = 4;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_goods_id',  'status', 'user_id','shop_id','number'], 'integer'],
            [['created_at', 'updated_at','activity'], 'safe'],
            [['goods_name'], 'string', 'max' => 255],
            [['order_sn', 'activity_name','amount','symbol',], 'string', 'max' => 60],
            [['order_goods_id', 'order_sn','activity'], 'unique', 'targetAttribute' => ['order_goods_id', 'order_sn','activity']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_name' => '商品名称',
            'order_sn' => '订单编号',
            'order_goods_id' => '订单商品id',
            'activity_name' => '活动名称',
            'activity' => '活动ID',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'user_id' => '用户ID',
            'symbol' => '币种',
            'amount' => '总金额',
            'number' => '数量'
        ];
    }

    /**
     * 查询
     * @param $order_sn
     * @param $goods_id
     * @param $user_id
     * @param $activity
     * @return MallGoodsLog|null
     */
    public static function findOneOrderSnGoodsUserId($order_sn,$goods_id,$user_id,$activity){
        $mallGoodsLog = static::findOne([
            'order_sn' => $order_sn,
            'order_goods_id' => $goods_id,
            'user_id' => $user_id,
            'activity' => $activity
        ]);
        return $mallGoodsLog;
    }

    /**
     * 查询活动执行活动
     * @throws \yii\db\Exception
     */
    public static function activity(){
        $order_goods = static::findAll([
            'status' => static::STATUS_DEFAULT,
        ]);
        /***
         * @var MallGoodsLog $mallGoods
         */
        foreach ($order_goods as $mallGoods){
            $transaction = \Yii::$app->db->beginTransaction();//开启事务
            try{
                if(empty($mallGoods->activity)){
                    if(!$mallGoods->updateStatus(static::STATUS_ACTIVITY_SUCCESS))
                        throw new ErrorException('修改状态失败',999);
                }else{
                    MallGoodsActivityLog::activity($mallGoods->activity,$mallGoods);
                }
                $transaction->commit();
            }
            catch (\Exception $exception){
                $transaction->rollBack();
                var_dump($exception->getMessage());
                Yii::error($mallGoods->id.':'.$exception->getMessage(),'mall');
            }
        }
    }
    /**
     * 新增收货订单方法
     * @param $order_sn
     * @param Alcohol $alcohol
     * @param int $activity
     * @param null $activity_name
     * @return MallGoodsLog|null
     * @throws ErrorException
     */
    public static function getOrderGoods($order_sn,Alcohol $alcohol,$activity=0,$activity_name='无活动'){
        $mallGoodsLog = static::findOneOrderSnGoodsUserId($order_sn,$alcohol->order_goods_id,$alcohol->user->id,$alcohol->activity);
        if(empty($mallGoodsLog)){
            $orderData = [
                'goods_name' => $alcohol->goods_name,
                'order_sn' => $alcohol->order_sn,
                'order_goods_id' => $alcohol->order_goods_id,
                'activity_name' => $activity_name,
                'activity' => (string)$activity,
                'amount' => $alcohol->amount,
                'symbol' => $alcohol->symbol,
                'status' => 1,
                'user_id' => $alcohol->user->id,
                'number' => $alcohol->number,
                'shop_id' => $alcohol->shop_id
            ];
            $mallGoodsLog = new static();
            $mallGoodsLog->setAttributes($orderData);
            if(!$mallGoodsLog->save()){
                throw new ErrorException(APIFormat::popError($mallGoodsLog->getErrors()),999);
            }
        }
        return $mallGoodsLog;
    }

    /**
     * 修改状态
     * @param $status
     * @return bool
     */
    public function updateStatus($status){
        if(isset(static::$statusArr[$status])){
            $this->status = $status;
            if($this->save()){
                return true;
            }
            return false;
        }
        return false;
    }

    public function getUser(){
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function shopSearch($user_id,$page)
    {
        $query = static::find()
            ->andWhere(['user_id' => $user_id])
            ->orderBy(['created_at' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'page' => $page - 1,
            ]
        ]);
        return $dataProvider;

    }
}
