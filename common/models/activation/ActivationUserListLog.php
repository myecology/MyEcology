<?php

namespace common\models\activation;

use backend\modules\member\models\User;
use common\models\shop\MallGoodsLog;
use Yii;

/**
 * This is the model class for table "iec_activation_user_list_log".
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $pid 上级id
 * @property int $number 数量
 * @property string $created_at
 * @property int $activation_id 活动id
 * @property string $goods_name 商品名称
 * @property int $mall_goods_log_id 商品记录id
 */
class ActivationUserListLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_activation_user_list_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'pid', 'activation_id', 'mall_goods_log_id'], 'required'],
            [['user_id', 'pid', 'number', 'activation_id', 'mall_goods_log_id'], 'integer'],
            [['created_at'], 'safe'],
            [['goods_name'], 'string', 'max' => 255],
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
            'pid' => '上级id',
            'number' => '数量',
            'created_at' => '创建时间',
            'activation_id' => '活动名称',
            'goods_name' => '商品名',
            'mall_goods_log_id' => '商城购买id',
        ];
    }
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getActivation()
    {
        return $this->hasOne(Activation::className(), ['id' => 'activation_id']);
    }
    public function getPuid()
    {
        return $this->hasOne(User::className(), ['id' => 'pid']);
    }
    public static function addLog($user_id,$pid, Activation $activation, MallGoodsLog $mallGoodsLog){
        $model = static::findOne([
            'user_id' => $user_id,
            'pid' => $pid,
            'activation_id' => $activation->id
        ]);
        if(empty($model)){
            $model = new static();
            $model->user_id = $user_id;
            $model->pid = $pid;
            $model->number = $mallGoodsLog->number;
            $model->goods_name = $mallGoodsLog->goods_name;
            $model->mall_goods_log_id = $mallGoodsLog->id;
            $model->activation_id = $activation->id;
            if($model->save()){
                return true;
            }else{
                throw new \ErrorException($model->getFirstError());
            }
        }
        return false;
    }
}
