<?php

namespace common\models\activation;

use backend\modules\member\models\User;
use Yii;

/**
 * This is the model class for table "iec_activation_user_list".
 *
 * @property int $id
 * @property int $activation_id
 * @property int $user_id
 * @property int $level
 * @property string $created_at
 * @property string $updated_at
 * @property int $end_time 失效时间
 */
class ActivationUserList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_activation_user_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['activation_id', 'user_id'], 'required'],
            [['activation_id', 'user_id', 'level', 'end_time'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id', 'activation_id'], 'unique', 'targetAttribute' => ['user_id', 'activation_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'activation_id' => '活动id',
            'user_id' => '用户名',
            'level' => '等级',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'end_time' => '失效时间',
        ];
    }

    /**
     * 增加等级
     * @param $activation
     * @param $user_id
     * @param int $end_time
     * @return bool
     * @throws \ErrorException
     */
    public static function addList($activation,$user_id,$end_time = 0){
        $model = static::findOne([
            'user_id' => $user_id,
            'activation_id' => $activation->id
        ]);
        if(empty($model)){
            $model = new static();
            $model->level = 0;
            $model->user_id = $user_id;
            $model->activation_id = $activation->id;
            $model->end_time = $end_time;
            if($model->save()){
                return true;
            }else{
                throw new \ErrorException($model->getFirstError());
            }
        }else{
            if($end_time >= 0 && $end_time != $model->end_time){
                $model->end_time = $end_time;
                if($model->save()){
                    return true;
                }else{
                    throw new \ErrorException($model->getFirstError());
                }
            }
            return true;
        }
    }
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getActivation()
    {
        return $this->hasOne(Activation::className(), ['id' => 'activation_id']);
    }
    /**
     * 增加代数
     * @param $level
     * @return bool
     * @throws \ErrorException
     */
    public function addLevel($level){
        if( $this->end_time!= 0 && $this->end_time < time()){
            return true;
        }
        $this->level += $level;
        if($this->save()){
            return true;
        }else{
            throw new \ErrorException($this->getFirstError());
        }
    }
}
