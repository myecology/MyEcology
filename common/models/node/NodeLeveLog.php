<?php

namespace common\models\node;

use Yii;
use common\models\Invitation;
use common\models\node\MallNodeLog;

/**
 * This is the model class for table "iec_node_leve_log".
 *
 * @property int $id
 * @property int $inviter_id 邀请人ID
 * @property int $registerer_id 注册人ID
 * @property int $is_mall 购买
 */
class NodeLeveLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_node_leve_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inviter_id', 'registerer_id', 'is_mall'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inviter_id' => 'Inviter ID',
            'registerer_id' => 'Registerer ID',
            'is_mall' => 'Is Mall',
        ];
    }

    /**
     * 下级用户购买活动商品的时候，添加数据记录
     */
    public static function Add($user_id){
        //查询当前是否存在该用户
        $UserModel = static::findOne(['registerer_id'=>$user_id]);
        if(!empty($UserModel)){
            return true;
        }
        //获取用户关系等级
        $inviter = Inviter::find()
        ->where(['registerer_id'=>$user_id])
        ->andwhere(['between','level',1,10])
        ->asArray()->all();
        if(empty($inviter)){
            return true;
        }

        $AlteUser = MallNodeLog::find()
        ->where(['alte_status'=>2])
        ->where(["<>",'super_status','2'])
        ->asArray()->all();
        if(empty($AlteUser)){
            return true;
        }
        foreach($inviter as $key=>$val){
            foreach($AlteUser as $k=>$v){
                if($val['inviter_id'] == $v['user_id']){
                    $model = new static();
                    $data = [
                        'inviter_id' => $val["inviter_id"],
                        'registerer_id' => $user_id,
                    ];
                    $model->setAttributes($data);
                    if(!$model->save()){
                        throw new \ErrorException("添加用户关系错误", 2568);
                    }
                    continue;
                }
            }
        }
        return true;
    }

    /**
     * 用户成为备选节点的时候添加数据
     */
    public function AlteAdd($user_id){
        $user = Invitation::find()
        ->leftJoin("iec_mall_goods_log as b",'registerer_id=b.user_id')
        ->where(['inviter_id'=>$user_id])
        ->andwhere(['between','level',1,10])
        ->andwhere(['b.activity'=>1])
        ->andwhere(['b.status'=>3])
        ->asArray()->all();
        if(empty($user)){
            return true;
        }
    }
}
