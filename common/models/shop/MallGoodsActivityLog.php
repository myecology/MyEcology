<?php

namespace common\models\shop;

use api\controllers\APIFormat;
use api\models\User;
use common\models\activation\Activation;
use Yii;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "iec_mall_goods_activity_log".
 *
 * @property int $id
 * @property int $mall_goods_id 收货id
 * @property int $user_id 用户id
 * @property int $type 用户id
 * @property string $amount 奖励金额
 * @property string $symbol 奖励币种
 * @property string $created_at 创建时间
 */
class MallGoodsActivityLog extends \yii\db\ActiveRecord
{
    private static $activityArr = [
        1 => '酒链',
        2 => '上级返利',
        0 => '确认收货',
    ];

    public static $typeArr = [
        1 => '酒链',
        2 => '上级返利',
    ];

    const ACTIVITY_WINE = 1;
    const ACTIVITY_REBATE = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_mall_goods_activity_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mall_goods_id', 'user_id','type'], 'integer'],
            [['amount'], 'number'],
            [['created_at'], 'safe'],
            [['symbol'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mall_goods_id' => '收货id',
            'user_id' => '购买用户',
            'amount' => '收益数量',
            'symbol' => '收益币种',
            'created_at' => '创建时间',
            'type' => '类型'
        ];
    }

    /***
     * 活动操作
     * @param $activity
     * @param MallGoodsLog $mallGoodsLog
     * @throws ErrorException
     */
    public static function activity($activity,MallGoodsLog $mallGoodsLog){
        switch ($activity){
            case (string)static::ACTIVITY_WINE: //酒链
                static::activityWine($mallGoodsLog);
                break;
            case (string)static::ACTIVITY_REBATE://返利
                static::activityRebate($mallGoodsLog);
                break;
            default:
                Activation::runActivation($activity,$mallGoodsLog);
                break;
        }
    }

    /**
     * 执行活动
     * @param MallGoodsLog $mallGoodsLog
     * @throws ErrorException
     */
    private static function activityWine(MallGoodsLog $mallGoodsLog){
        if(WineLevelLog::exist_save($mallGoodsLog->user,$mallGoodsLog->goods_name,$mallGoodsLog->order_goods_id)){//增加代数
            WineLevel::puserList($mallGoodsLog->user->id,$mallGoodsLog);//奖励
            if(!$mallGoodsLog->updateStatus(MallGoodsLog::STATUS_ACTIVITY_SUCCESS)){
                throw new ErrorException('更改状态失败');
            }
        }else{
            throw new ErrorException('更新代数失败');
        }
    }


    /***
     * 返利活动
     * @param MallGoodsLog $mallGoodsLog
     * @throws ErrorException
     */
    private static function activityRebate(MallGoodsLog $mallGoodsLog){
        WineLevel::puserList($mallGoodsLog->shop_id,$mallGoodsLog);//奖励
        if(!$mallGoodsLog->updateStatus(MallGoodsLog::STATUS_ACTIVITY_SUCCESS)){
            throw new ErrorException('更改状态失败');
        }
    }

    /***
     * 判断改订单和用户是否加钱了
     * @param $user_id //加钱用户
     * @param $mall_goods_id //订单id
     * @param $amount
     * @param $symbol
     * @return bool
     * @throws ErrorException
     */
    public static function findUserGoods($user_id,$mall_goods_id,$amount,$symbol,$type){
        $log = static::findOne(['user_id'=>$user_id,'mall_goods_id'=>$mall_goods_id]);
        if(empty($log)){
            $data = [
                'mall_goods_id' => $mall_goods_id,
                'user_id' => $user_id,
                'amount' => $amount,
                'symbol' => $symbol,
                'type' => $type
            ];
            $activityLog = new static();
            $activityLog->setAttributes($data);
            if($activityLog->save())return true;
            throw new ErrorException(APIFormat::popError($activityLog->getErrors()));
        }
        return false;
    }


    public function getUser(){
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    public function getMall(){
        return $this->hasOne(MallGoodsLog::className(), ['id' => 'mall_goods_id']);
    }
    public static function shopSearch($user_id,$page)
    {
        $query = static::find()
            ->andWhere([
                'user_id' => $user_id,
                'type' => static::ACTIVITY_WINE
            ])
            ->orderBy(['created_at' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'page' => $page - 1,
            ]
        ]);
        return $dataProvider;
    }

    public function pageData(){
        return [
            'goods_name' => $this->mall->goods_name,
            'symbol' => $this->symbol,
            'amount' => $this->amount,
            'username' => $this->mall->user->usernameText,
            'created_at' => $this->created_at
        ];
    }
}
