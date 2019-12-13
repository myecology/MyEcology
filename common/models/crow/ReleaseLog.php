<?php

namespace common\models\crow;

use Yii;
use common\models\Message;
use api\models\User;

/**
 * This is the model class for table "iec_release_log".
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property string $amount 释放金额
 * @property string $symbol 释放币种
 * @property string $created_at 释放时间
 * @property int $mall_log_id 购买记录id
 */
class ReleaseLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_release_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'amount', 'symbol', 'log_id'], 'required'],
            [['user_id','mall_log_id'], 'integer'],
            [['amount'], 'number'],
            [['created_at'], 'safe'],
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
            'user_id' => 'User ID',
            'amount' => 'Amount',
            'symbol' => 'Symbol',
            'created_at' => 'Created At',
            'log_id' => "Log Id",
        ];
    }

    /**
     * 添加释放记录
     */
    public static function add($data,$type,$sourceModel,$cycle=""){
        if($type == 1){
            $log = static::findOne(["log_id"=>$data['log_id']]);
            if(!empty($log)){
                throw new \ErrorException("该数据已经释放", 22);
            }
        }
        if($type == 2){
            $now_time = strtotime(date("Y-m-d 00:00:00",(time()-24*3600*$cycle)));
            $log = static::find()
            ->where(['log_id'=>$data['log_id']])
            // ->andwhere(['<=','created_at',$now_time])
            ->orderBy("created_at desc")
            ->one();
            $oldtime = date("Y-m-d 00:00:00",strtotime($log->created_at));
            if(strtotime($oldtime)-1 >= $now_time){
                throw new \ErrorException("该数据没有到释放时间", 33);
            }
        }
        $model = new static();
        $model->setAttributes($data);
        if(!$model->save()){
            throw new \ErrorException("添加释放记录失败", 888);
        }
        $user = User::findOne($data['user_id']);
        Message::addMessage(Message::TYPE_RELEASE_CROW,$user,$data['symbol'],$data['amount'],$sourceModel);
        return true;
    }
}
