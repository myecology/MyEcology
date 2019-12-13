<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "iec_distrbute_log".
 *
 * @property int $id
 * @property string $release_symbol 释放币种
 * @property string $have_symbol 拥有币种
 * @property string $total_amount 奖励金额
 * @property int $release_num 奖励人数
 * @property int $status 状态1：初始化，2：释放中，3：完成
 * @property string $created_at 创建时间
 * @property string $updated_at 修改时间
 */
class DistrbuteLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_distrbute_log';
    }

    public static $statusArr = [
        1 => "未释放",
        2 => '释放中',
        3 => '释放完成',
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['release_symbol', 'have_symbol', 'total_amount', 'release_num'], 'required'],
            [['id', 'release_num', 'status'], 'integer'],
            [['total_amount'], 'number'],
            [['created_at', 'updated_at','name','release_amount'], 'safe'],
            [['release_symbol', 'have_symbol'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'release_symbol' => '释放币种',
            'have_symbol' => '持有币种',
            'total_amount' => '总金额',
            'release_num' => '释放人数',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
            'name' => '名称',
            'release_amount' => "已释放金额",
        ];
    }

    /**
     * 添加任务
     * @param [type] $data [description]
     */
    public static function Add($data){
        $model = new static();
        $model->setAttributes($data);
        if(!$model->save()){
            // var_dump($model->getErrors());die;
            throw new \ErrorException("添加数据失败", 1);
        }
        return $model;
    }

    /**
     * 修改通证分红记录
     */
    public static function UpdateOne($amount,$id){
        $model = static::findOne($id);
        $model->status = 3;
        $model->release_amount = $amount;
        if(!$model->save()){
            throw new \ErrorException("修改失败", 1);
        }
        return true;
    }

    
}
