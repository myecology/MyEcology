<?php

namespace common\models\shop;

use api\controllers\APIFormat;
use api\models\Alcohol;
use Yii;
use yii\base\ErrorException;
use api\models\User;

/**
 * This is the model class for table "iec_mall_log".
 *
 * @property int $id
 * @property int $user_id
 * @property string $goods_name
 * @property int $number 数量
 * @property string $amount 价格
 * @property string $symbol 币种
 * @property string $remark 备注
 * @property string $order_sn 订单信息
 * @property int $activity 活动id
 * @property string $activity_name 活动名称
 * @property int $status 状态
 * @property string $created_at
 * @property string $updated_at
 */
class MallLog extends \yii\db\ActiveRecord
{
    public static $statusArr = [
        1 => '初始状态',
        2 => '支付成功',
        3 => '活动操作成功',
        4 => '活动操作失败',
        5 => '退款成功',
    ];

    private static $activityArr = [
        1 => '酒链',
        2 => '上级返利'
    ];

    const ACTIVITY_WINE = 1;
    const ACTIVITY_REBATE = 2;


    const STATUS_DEFAULT = 1;
    const STATUS_PAY_SUCCESS = 2;
    const STATUS_ACTIVITY_SUCCESS = 3;
    const STATUS_ACTIVITY_FAIL = 4;
    const STATUS_BACK = 5;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_mall_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'number', 'activity', 'status'], 'integer'],
            [['amount'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['goods_name', 'order_sn', 'activity_name','symbol'], 'string', 'max' => 60],
            [['remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户id',
            'goods_name' => '商品名称',
            'number' => '数量',
            'amount' => '金额',
            'remark' => '备注',
            'order_sn' => '订单号',
            'activity' => '活动id',
            'activity_name' => '活动名称',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
            'symbol' => '币种',
        ];
    }

    /**
     * 新增订单方法
     * @param $order_sn
     * @param Alcohol $alcohol
     * @return MallLog|null
     * @throws ErrorException
     */
    public static function getOrderSn($order_sn,Alcohol $alcohol){
        $orderSnMall = static::findOne([
            'order_sn' => $order_sn,
        ]);
        if(empty($orderSnMall)){
            $orderData = [
                'user_id' => $alcohol->user->id,
                'goods_name' => $alcohol->goods_name,
                'number' => $alcohol->number,
                'amount' => $alcohol->amount,
                'symbol' => $alcohol->symbol,
                'remark' => '',
                'order_sn' => $alcohol->order_sn,
                'activity' => $alcohol->activity,
                'activity_name' => $alcohol->activity_name,
                'status' => static::STATUS_DEFAULT,
            ];
            $mallLogModel = new static();
            $mallLogModel->setAttributes($orderData);
            if(!$mallLogModel->save()){
                throw new ErrorException(APIFormat::popError($mallLogModel->getErrors()),999);
            }
            $orderSnMall = $mallLogModel;
        }
        return $orderSnMall;
    }

    /**
     * 修改状态
     * @param $status
     * @param null $remark
     * @return bool
     */
    public function updateStatus($status,$remark = null){
        if(isset(static::$statusArr[$status])){
            $this->status = $status;
            if(!empty($remark)){
                $this->remark = $remark;
            }
            if($this->save()){
                return true;
            }
            return false;
        }
        return false;
    }


    public function backData(){
        return [
            'log_id' => $this->id,
            'remake' => $this->remark,
            'status_text' => static::$statusArr[$this->status],
            'status' => $this->status,
            'order_sn' => $this->order_sn,
            'amount' => $this->amount,
            'symbol' => $this->symbol
        ];
    }

    /**
     * 活动操作
     * @param $activity
     * @throws ErrorException
     */
    public static function activity($activity){
        switch ($activity){
            case static::ACTIVITY_WINE: //酒链

                break;
            case static::ACTIVITY_REBATE://返利

                break;
            default:
                throw new ErrorException('活动不存在');
                break;
        }
    }

    public function getUser(){
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }


}
