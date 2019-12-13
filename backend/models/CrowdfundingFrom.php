<?php

namespace backend\models;

use Yii;
use yii\base\ErrorException;
use yii\base\Model;
use common\models\Crowdfunding;

class CrowdfundingFrom extends Model{
    public $id;//
    public $name;// 名称
    public $income_img;// 众筹图片
    public $start_time;// 开始时间
    public $end_time;// 结束时间
    public $status;// 状态1：开启，2：关闭
    public $release_type;// 释放类型，1：直接释放，2：分期释放
    public $release_start_at;// 释放时间
    public $release_end_at;// 释放结束时间
    public $release_cycle;// 释放周期
    public $mall_symbol;// 购买币种
    public $mall_proportion;// 购买比例
    public $exchange_symbol;// 兑换币种
    public $exchange_num;// 兑换数量
    public $created_at;//
    public $update_at;// 修改时间
    public $min_buy;// 最小购买数
    public $exchange_total;// 兑换总数
    public $brief_introduction;// 通证介绍
    public $remark;// 备注
    public $isNewRecord = true;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['income_img', 'mall_symbol', 'mall_proportion', 'exchange_symbol', 'exchange_num', 'min_buy', 'exchange_total', 'release_start_at', 'name', 'start_time', 'end_time', 'brief_introduction'], 'required'],
            [['start_time', 'end_time', 'release_start_at', 'release_end_at', 'created_at', 'update_at', 'brief_introduction'], 'safe'],
            [['status', 'release_type', 'release_cycle', 'exchange_num', 'min_buy', 'exchange_total'], 'integer'],
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
            'release_cycle' => '释放周期',
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
        ];
    }

    public static function updatevalue($id){
        $data = Crowdfunding::findOne($id);
        $data->isNewRecord = false;
        return $data;
    }

    /**
     * 添加
     */
    public function add(){
        $model = new Crowdfunding();
        if($this->release_start_at < $this->end_time){
            throw new ErrorException("释放时间不能小于结束时间", 3050);
        }
        if($this->release_type == 2){
            if(empty($this->release_start_at) || empty($this->release_end_at)){
                throw new \ErrorException("释放时间不能为空", 5040);
            }
            if(empty($this->release_cycle)){
                throw new \ErrorException("请输入释放周期", 5040);
            }
        }
        // var_dump($this->mall_symbol);die;
        $model->name = $this->name;
        $model->income_img = $this->income_img;
        $model->start_time = $this->start_time;
        $model->end_time = $this->end_time;
        $model->status = $this->status;
        $model->release_type = $this->release_type;
        $model->release_start_at = $this->release_start_at;
        $model->release_end_at = $this->release_end_at;
        $model->release_cycle = $this->release_cycle;
        $model->mall_symbol = $this->mall_symbol;
        $model->mall_proportion = $this->mall_proportion;
        $model->exchange_symbol = $this->exchange_symbol;
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
}
