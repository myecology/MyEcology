<?php

namespace common\models;

use Yii;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use common\models\SendService;
use common\models\MallCrowLog;

/**
 * This is the model class for table "rc_crowdfunding".
 *
 * @property int $id
 * @property string $name 名称
 * @property string $income_img 众筹图片
 * @property string $start_time 开始时间
 * @property string $end_time 结束时间
 * @property int $status 状态1：开启，2：关闭
 * @property int $release_type 释放类型，1：直接释放，2：分期释放
 * @property string $release_start_at 释放时间
 * @property string $release_end_at 释放结束时间
 * @property int $release_cycle 释放周期
 * @property string $mall_symbol 购买币种
 * @property string $mall_proportion 购买比例
 * @property string $exchange_symbol 兑换币种
 * @property int $exchange_num 兑换数量
 * @property string $created_at
 * @property string $update_at 修改时间
 * @property int $min_buy 最小购买数
 * @property int $exchange_total 兑换总数
 * @property text $brief_introduction 通证介绍
 * @property string $remark 备注
 */
class Crowdfunding extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rc_crowdfunding';
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
            [['income_img', 'mall_symbol', 'mall_proportion', 'exchange_symbol', 'exchange_num', 'min_buy', 'exchange_total', 'release_start_at', 'name', 'start_time', 'end_time', 'brief_introduction'], 'required'],
            [['start_time', 'end_time', 'release_start_at', 'release_end_at', 'created_at', 'update_at', 'brief_introduction'], 'safe'],
            [['status', 'release_type', 'release_cycle', 'exchange_num', 'min_buy', 'exchange_total','is_end'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['income_img', 'mall_symbol', 'mall_proportion', 'remark'], 'string', 'max' => 255],
            [['exchange_symbol'], 'string', 'max' => 20],
           
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'income_img' => '图片',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'status' => '状态',
            'release_type' => '释放类型',
            'release_start_at' => '释放开始时间',
            'release_end_at' => '释放结束时间',
            'release_cycle' => '释放周期(天)',
            'mall_symbol' => '购买币种',
            'mall_proportion' => '购买比例',
            'exchange_symbol' => '兑换币种',
            'exchange_num' => '兑换数量',
            'created_at' => '创建时间',
            'update_at' => '修改时间',
            'min_buy' => '最小购买数',
            'exchange_total' => '可兑换总数',
            'brief_introduction' => '通证介绍',
            'remark' => '备注',
            'is_end' => '是否结束'
        ];
    }

    public static $statusArr = [
        1 => '开启',
        2 => '关闭'
    ];

    public static $typeArr = [
        1 => '直接释放',
        2 => '分期释放'
    ];

    /**
     * 添加
     */
    public function add(){
        $model = new Crowdfunding();
        if(strtotime($this->end_time) < strtotime($this->start_time)){
            throw new \ErrorException("结束时间不能小于开始时间", 3401);
        }
        if(strtotime($this->end_time) < time()){
            throw new \ErrorException("结束时间不能小于当前时间", 3402);
        }
        if($this->release_start_at < $this->end_time){
            throw new ErrorException("释放时间不能小于结束时间", 3050);
        }
        if($this->release_type == 2){
            if(empty($this->release_start_at) || empty($this->release_end_at)){
                throw new \ErrorException("释放时间不能为空", 5040);
            }
            if(strtotime($this->release_end_at) < strtotime($this->release_start_at)){
                throw new \ErrorException("释放结束时间不能小于释放开始时间", 3401);
            }
            $mall = new MallCrowLog();
            $day = $mall->getCountDays(strtotime($this->release_start_at),strtotime($this->release_end_at));
            if($day <= 0){
                throw new \ErrorException("释放时间不能小于一天", 1);
            }
            if(empty($this->release_cycle)){
                throw new \ErrorException("请输入释放周期", 5040);
            }
        }
        $mb = mb_strlen($this->mall_symbol,'utf-8');
        $st = strlen($this->mall_symbol);
        $amount_mb = mb_strlen($this->mall_proportion,'utf-8');
        $amount_st = strlen($this->mall_proportion);
        if(!preg_match('/[1-9]\d*,*/', $this->mall_proportion)){
            throw new \ErrorException("请输入正确的格式", 5042);
        }
        if($mb != $st || $amount_mb != $amount_st){
            throw new \ErrorException("请输入英文字符", 5041);
        }
        $this->symbol($this->mall_symbol);
        $this->symbol($this->exchange_symbol);
        $model->name = $this->name;
        $model->income_img = $this->income_img;
        $model->start_time = $this->start_time;
        $model->end_time = $this->end_time;
        $model->status = $this->status;
        $model->release_type = $this->release_type;
        $model->release_start_at = $this->release_start_at;
        $model->release_end_at = $this->release_end_at;
        $model->release_cycle = $this->release_cycle;
        $model->mall_symbol = strtoupper($this->mall_symbol);
        $model->mall_proportion = $this->mall_proportion;
        $model->exchange_symbol = strtoupper($this->exchange_symbol);
        $model->exchange_num = $this->exchange_num;
        $model->min_buy = $this->min_buy;
        $model->exchange_total = $this->exchange_total;
        $model->brief_introduction = $this->brief_introduction;
        $model->remark = $this->remark;
        if(!$model->save()){
            throw new \ErrorException("创建众筹失败", 5050);
            
        }
        return true;
    }

    /**
     * 修改
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function updatedata($id){
        $model = new Crowdfunding();
        $data = $model->findOne($id);
        if(strtotime($this->end_time) < strtotime($this->start_time)){
            throw new \ErrorException("结束时间不能小于开始时间", 3401);
        }
        if(strtotime($this->end_time) < time()){
            throw new \ErrorException("结束时间不能小于当前时间", 3402);
        }
        if(strtotime($data->start_time) < time() && strtotime($data->end_time) >time()){
            throw new \ErrorException("该众筹已经开始，不能修改", 404);   
        }
        if(strtotime($data->end_time) < time()){
            throw new \ErrorException("该众筹活动已经结束了", 3040);
        }
        if($this->release_start_at < $this->end_time){
            throw new ErrorException("释放时间不能小于结束时间", 3050);
        }
        if($this->release_type == 2){
            if(empty($this->release_end_at)){
                throw new \ErrorException("请输入释放结束时间", 5040);
            }
            if(strtotime($this->release_end_at) < strtotime($this->release_start_at)){
                throw new \ErrorException("释放结束时间不能小于释放开始时间", 3401);
            }
            $mall = new MallCrowLog();
            $day = $mall->getCountDays(strtotime($this->release_start_at),strtotime($this->release_end_at));
            
            if($day <= 0){
                throw new \ErrorException("释放时间不能小于一天", 5023);
            }
            if(empty($this->release_cycle)){
                throw new \ErrorException("请输入释放周期", 5040);
            }
        }
        $mb = mb_strlen($this->mall_symbol,'utf-8');
        $st = strlen($this->mall_symbol);
        $amount_mb = mb_strlen($this->mall_proportion,'utf-8');
        $amount_st = strlen($this->mall_proportion);
        if($mb != $st || $amount_mb != $amount_st){
            throw new \ErrorException("请输入英文字符", 5041);
        }
        if(!preg_match('/[1-9]\d*,*/', $this->mall_proportion)){
            throw new \ErrorException("请输入正确的格式", 5042);
        }

        $this->symbol($this->mall_symbol);
        $this->symbol($this->exchange_symbol);
        $data->name = $this->name;
        $data->income_img = $this->income_img;
        $data->start_time = $this->start_time;
        $data->end_time = $this->end_time;
        $data->status = $this->status;
        $data->release_type = $this->release_type;
        $data->release_start_at = $this->release_start_at;
        $data->release_end_at = $this->release_end_at;
        $data->release_cycle = $this->release_cycle;
        $data->mall_symbol = strtoupper($this->mall_symbol);
        $data->mall_proportion = $this->mall_proportion;
        $data->exchange_symbol = strtoupper($this->exchange_symbol);
        $data->exchange_num = $this->exchange_num;
        $data->min_buy = $this->min_buy;
        $data->exchange_total = $this->exchange_total;
        $data->brief_introduction = $this->brief_introduction;
        $data->remark = $this->remark;
        // var_dump($data);die;
        if(!$data->save()){
            throw new \ErrorException("修改数据失败", 5030);
        }
        return true;
    }

    /**
     * 即将开始众筹
     * @param [type] $page [description]
     */
    public static function ComingSoon($page,$pagesize=5)
    {
        $query = static::find()
            ->andWhere(['>','start_time',date("Y-m-d H:i:s")])
            ->andWhere(['<>','is_end',2])
            ->orderBy(['start_time' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'page' => $page - 1,
                'pageSize' => $pagesize,
            ]
        ]);
        return $dataProvider;
    }

    /**
     * 进行中众筹
     * @param [type] $page [description]
     */
    public static function InProgress($page,$pagesize=5)
    {
        $query = static::find()
            ->andWhere(['<=','start_time',date("Y-m-d H:i:s")])
            ->andFilterWhere(['>=','end_time',date("Y-m-d H:i:s")])
            ->andWhere(['<>','is_end',2])
            ->orderBy(['start_time' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'page' => $page - 1,
                'pageSize' => $pagesize,
            ]
        ]);
        // var_dump($dataProvider);die;
        return $dataProvider;
    }

    /**
     * 已结束众筹
     * @param [type] $page [description]
    */
    public static function HasEnded($page,$pagesize=5)
    {
        $query = static::find()
            ->andWhere(['<','end_time',date("Y-m-d H:i:s")])
            ->orwhere(['is_end'=>2])
            ->orderBy(['start_time' => SORT_DESC]);
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
            'name' => $this->name,
            'income_img' => $this->income_img,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'status' => $this->status,
            'release_type' => $this->release_type,
            'release_start_at'=> $this->release_start_at,
            'release_end_at' => $this->release_end_at,
            'release_cycle' => $this->release_cycle,
            'mall_symbol' => $this->mall_symbol,
            'mall_proportion' => $this->mall_proportion,
            'exchange_symbol' => $this->exchange_symbol,
            'exchange_num' => $this->exchange_num,
            'created_at' => $this->created_at,
            'min_buy' => $this->min_buy,
            'exchange_total' => $this->exchange_total,
            'brief_introduction' => $this->brief_introduction,
            'is_end' => $this->is_end,
        ];
    }

    /**
     * 币种验证
     * @return [type] [description]
     */
    public function symbol($symbol){
        if(YII_DEBUG){
            $url = "http://api.antc-dev.bxguo.net/index.php/node/symbol";
        }else{
            $url = "http://api.antc.bxguo.net/index.php/node/symbol";
        }
        $data = [
            'symbol' => $symbol,
        ];
        $result = SendService::curlPost($url,$data);
        if($result == false){
            throw new \ErrorException("网络错误", 404);
        }
        if(empty($result) || $result['status'] != 200){
            throw new \ErrorException($result['msg'], $result['status']);
        }
        return true;
    }

    /**
     * 修改是否结束状态
     * @param [type] $id [description]
     */
    public static function UpdateIsEnd($id){
        $model = static::findOne($id);
        $model->is_end = 2;
        if(!$model->save()){
            throw new \ErrorException(json_encode($model->getErrors()), 5203);
        }
        return ture;
    }

}
