<?php

namespace common\models;

use Yii;
use common\models\Crowdfunding;
// use api\models\User;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "rc_mall_crow_log".
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $crow_id 众筹id
 * @property string $crow_name 众筹名称
 * @property int $number 数量
 * @property int $status 状态 1：已完成,2未完成
 * @property string $created_at
 * @property string $mall_symbol 购买币种
 * @property string $pay_symbol 支付币种
 * @property int $release_cycle 释放周期
 * @property int $type 释放类型=1:直接释放,=2:分期释放
 * @property int $release_time 释放次数
 * @property string $release_at 释放时间
 */
class MallCrowLog extends \yii\db\ActiveRecord
{
    public static $statusArr = [
        1 => '购买成功',
        2 => '购买失败',
    ];

    const BUY_SUCCESS = 1;
    const BUY_FAIL = 2;

    public static $typeArr = [
        1 => '直接释放',
        2 => '分期释放',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rc_mall_crow_log';
    }

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
            [['user_id', 'crow_id', 'number'], 'required'],
            [['user_id', 'crow_id', 'number', 'status', 'release_cycle', 'type', 'release_time'], 'integer'],
            [['created_at', 'release_at'], 'safe'],
            [['crow_name'], 'string', 'max' => 100],
            [['mall_symbol', 'pay_symbol','pay_price'], 'string', 'max' => 255],
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
            'crow_id' => '众筹id',
            'crow_name' => '众筹名称',
            'number' => '购买数量',
            'status' => '状态',
            'created_at' => '创建时间',
            'mall_symbol' => '购买币种',
            'pay_symbol' => '支付币种',
            'release_cycle' => '释放周期',
            'type' => '类型',
            'release_time' => '释放次数',
            'release_at' => '释放开始时间',
            'pay_price' => '支付金额',
        ];
    }

    /**
     * 添加购买记录
     */
    public static function add($crow_id,$user_id,$number){
        $model = new static();
        //查询众筹信息
        $crow = Crowdfunding::findOne(['id'=>$crow_id]);
        $data['user_id'] = $user_id;
        $data['crow_id'] = $crow_id;
        $data['crow_name'] = $crow->name;
        $data["number"] = $number;
        $data['status'] = static::BUY_FAIL;
        $data['mall_symbol'] = $crow->exchange_symbol;
        $data['pay_symbol'] = $crow->mall_symbol;
        $data['type'] = $crow->release_type;
        $data['release_at'] = $crow->release_start_at;
        $amount_arr = explode (",",$crow->mall_proportion);
        foreach($amount_arr as $key=>$val){
            $amount[] = $val*$number;
        }
        $data['pay_price'] = implode(",",$amount);
        //判断是否类型
        if($crow->release_type == 2){
            $day = $model->getCountDays(strtotime($crow->release_start_at),strtotime($crow->release_end_at));
            $release_time = $day/$crow->release_cycle;
            $data['release_time'] = $release_time;
            $data['release_cycle'] = $crow->release_cycle;
        }
        $model->setAttributes($data);
        if(!$model->save()){
            throw new \ErrorException("添加记录失败", 999);
        }
        // return true;
        return $model->id;
    }

    public static function UpdateMall($id){
        $model = static::findOne($id);
        $model->status = static::BUY_SUCCESS;
        if(!$model->save()){
            throw new \ErrorException("修改购买信息失败", 1);
        }
        return true;
    }


    public function getCountDays($sTime, $eTime){
        if($sTime >= $eTime){
            throw new \ErrorException("释放开始时间不能大于释放结束时间", 5034);
        }
        $startDt = getdate($sTime);
        $endDt = getdate($eTime);
        $sUTime = mktime(0, 0, 0, $startDt['mon'], $startDt['mday'], $startDt['year']);
        $eUTime = mktime(0, 0, 0, $endDt['mon'], $endDt['mday'], $endDt['year']);
        return round(abs($sUTime - $eUTime) / 86400);
    }

    public function getUser(){
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getCrow(){
        return $this->hasOne(Crowdfunding::className(), ['id' => 'crow_id']);
    }

    /**
     * 购买记录
     */
    public static function Record($user_id,$page=1,$pagesize=5){
        $query = static::find()
        ->andWhere(['user_id'=>$user_id])
        ->andWhere(['status'=>static::BUY_SUCCESS])
        ->orderBy(['created_at'=>SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'page' => $page - 1,
                'pageSize' => $pagesize,
            ]
        ]);
        return $dataProvider;
    }

    public function resultData(){
        return [
            'id' => $this->id,
            'crow_name' => $this->crow_name,
            'number' => $this->number,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'mall_symbol' => $this->mall_symbol,
            'pay_symbol' => $this->pay_symbol,
            'release_cycle' => $this->release_cycle,
            'type' => $this->type,
            'release_time' => $this->release_time,
            'release_at' => $this->release_at,
            'pay_price' => $this->pay_price,
        ];
    }

}
