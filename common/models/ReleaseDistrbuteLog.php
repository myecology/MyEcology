<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "iec_release_distrbute_log".
 *
 * @property int $id
 * @property int $user_id
 * @property string $amount 释放金额
 * @property string $symbol 释放币种
 * @property int $distrbute_id 通证分红ID
 * @property string $created_at 释放时间
 * @property string $status 状态
 */
class ReleaseDistrbuteLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_release_distrbute_log';
    }

    public static $statusArr = [
        10 => "待释放",
        20 => "释放成功",
        30 => '释放失败',
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'distrbute_id','status'], 'integer'],
            [['amount', 'symbol'], 'required'],
            [['amount'], 'number'],
            [['created_at','remark'], 'safe'],
            [['symbol'], 'string', 'max' => 20],
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
            'amount' => '金额',
            'symbol' => '币种',
            'distrbute_id' => '通证id',
            'created_at' => '释放时间',
            'remark' => '备注',
            'status' => '状态',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(\api\models\User::className(), ['id' => 'user_id']);
    }

    /**
     * 添加记录
     * @param [type] $data [description]
     */
    public static function AddLog($data){
        $model = new static();
        $model->setAttributes($data);
        if(!$model->save()){
            throw new \ErrorException("添加通证分红释放记录失败", 1);
        }
        return true;
    }

    /**
     * 修改释放记录状态
     * @param [type] $id [description]
     */
    public static function UpdateLog($id){
        $model = static::findOne($id);
        $model->status = 20;
        if(!$model->save()){
            throw new \ErrorException("修改释放记录失败", 1);
        }
        return true;
    }
}
