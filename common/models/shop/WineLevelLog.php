<?php

namespace common\models\shop;

use api\controllers\APIFormat;
use api\models\User;
use Yii;
use yii\base\ErrorException;

/**
 * This is the model class for table "iec_wine_level_log".
 *
 * @property int $id
 * @property int $user_id
 * @property int $pid 父级id
 * @property int $created_at
 * @property int $number 购买数量
 * @property string $goods_name 商品名
 */
class WineLevelLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_wine_level_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'pid', 'created_at', 'number', 'goods_name'], 'required'],
            [['user_id', 'pid', 'created_at', 'number'], 'integer'],
            [['goods_name'], 'string', 'max' => 60],
            [['user_id', 'pid'], 'unique', 'targetAttribute' => ['user_id', 'pid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'pid' => '父级',
            'created_at' => '创建时间',
            'number' => '购买数量',
            'goods_name' => '商品名称',
        ];
    }
    /**
     * 判断是否是第一次 如果是就增加代数
     * @param User $user
     * @param $goods_name
     * @param $number
     * @throws ErrorException
     */
    public static function exist_save(User $user,$goods_name,$number){
        $wine_level_log = static::findOne([
            'user_id' => $user->id,
            'pid' => $user->upid
        ]);

        if(empty($wine_level_log)){
            $wine_level_log_model = WineLevel::findOne([
                'user_id' => $user->upid
            ]);
            if($user->upid > 0 && !empty($wine_level_log_model)){
                $wine_level_log = new static();
                $wine_level_log->user_id = $user->id;
                $wine_level_log->pid = $user->upid;
                $wine_level_log->created_at = time();
                $wine_level_log->goods_name = $goods_name;
                $wine_level_log->number = $number;
                if(!$wine_level_log->save()){
                    throw new ErrorException(json_encode($wine_level_log->getErrors()),999);
                }
                if(!$wine_level_log_model->addLevel()){
                    throw new ErrorException('增加代数失败',999);
                }
            }
        }
        if(WineLevel::findOneByUserIdAndCreated($user->id)) return true;
        return false;
    }

    /**
     * 获取用户信息
     * @return [type] [description]
     */
    public function getUser(){
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * 邀请人关联用户
     *
     * @return void
     */
    public function getInviterUser()
    {
        return $this->hasOne(User::className(), ['id' => 'pid']);
    }
}
