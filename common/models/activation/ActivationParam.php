<?php

namespace common\models\activation;

use Yii;

/**
 * This is the model class for table "iec_activation_param".
 *
 * @property int $id
 * @property string $key 键
 * @property string $value 值
 * @property string $created_at
 * @property int $type 类型
 * @property string $group 分组
 * @property string $remark 备注
 * @property int $activation_id 活动id
 */
class ActivationParam extends \yii\db\ActiveRecord
{

    public static $typeArr = [
        ActivationParam::TYPE_KEY => '键值对参数',
        ActivationParam::TYPE_GROUP => '数组参数',
    ];
    const TYPE_KEY = 1;
    const TYPE_GROUP = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_activation_param';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at'], 'safe'],
            [['type', 'activation_id'], 'integer'],
            [['activation_id'], 'required'],
            [['key'], 'string', 'max' => 50],
            [['value','remark'], 'string', 'max' => 255],
            [['group'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => '键',
            'value' => '值',
            'created_at' => '创建时间',
            'type' => '类型',
            'group' => '分组名',
            'remark' => '备注',
            'activation_id' => '活动名称',
        ];
    }

    /**
     * 获取活动参数
     * @param $activation_id 活动id
     * @return array
     */
    public static function getParam($activation_id){
        $param = [];
        $key_data = static::find()->where([
            'activation_id' => $activation_id,
            'type' => ActivationParam::TYPE_KEY,
        ])->all();
        if(empty($key_data)){
            $param['key'] = [];
        }else{
            foreach ($key_data as $value){
                $param['key'][$value->key] = $value->value;
            }
        }
        $group_data = static::find()->where([
            'activation_id' => $activation_id,
            'type' => ActivationParam::TYPE_GROUP,
        ])->all();
        if(empty($group_data)){
            $param['group'] = [];
        }else{
            foreach ($group_data as $value){
                $param['group'][$value->group][] = [
                    'key' => $value->key,
                    'value' => $value->value
                ];
            }
        }
        return $param;
    }

    /**
     * 缓存数据
     * @param $activation_id
     * @return mixed
     */
    public static function getCacheParam($activation_id){
        $param = \Yii::$app->cache->getOrSet($activation_id.date('YmdHis'), function () use ($activation_id) {
            $param = [];
            $key_data = static::find()->where([
                'activation_id' => $activation_id,
                'type' => ActivationParam::TYPE_KEY,
            ])->all();
            if(empty($key_data)){
                $param['key'] = [];
            }else{
                foreach ($key_data as $value){
                    $param['key'][$value->key] = $value->value;
                }
            }
            $group_data = static::find()->where([
                'activation_id' => $activation_id,
                'type' => ActivationParam::TYPE_GROUP,
            ])->all();
            if(empty($group_data)){
                $param['group'] = [];
            }else{
                foreach ($group_data as $value){
                    $param['group'][$value->group][$value->key] = $value->value;
                }
            }
            return $param;
        },300);
        return $param;
    }


    public function getActivation()
    {
        return $this->hasOne(Activation::className(), ['id' => 'activation_id']);
    }
}
