<?php

namespace common\models\node;

use Yii;
use api\common\User;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "iec_reward_log".
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $type 类型1：超级节点，2备选节点
 * @property string $amount 金额
 * @property string $symbol 奖励币种
 * @property string $created_at 创建时间
 */
class RewardLog extends \yii\db\ActiveRecord
{

    public static $typeArr = [
        1 => "超级节点",
        2 => "备选节点",
    ];
    const ALTE_TYPE = 2;
    const SUPER_TYPE = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_reward_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'amount', 'symbol'], 'required'],
            [['user_id', 'type'], 'integer'],
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
            'user_id' => '用户ID',
            'type' => '类型',
            'amount' => '金额',
            'symbol' => '币种',
            'created_at' => '创建时间',
        ];
    }

    /**
     * 添加奖励记录
     */
    public static function AddLog($data){
        $model = new static();
        $model->setAttributes($data);
        if(!$model->save()){
            throw new \ErrorException("添加记录失败", 999);
        }
        return true;
    }

    /**
     * 购买记录
     */
    public static function Record($user_id,$page=1,$pagesize=5){
        $query = static::find()->where(['user_id'=>$user_id])
        ->orderBy(['created_at' => SORT_DESC]);
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
            'user_id' => $this->user_id,
            'type' => $this->type,
            'amount' => $this->amount,
            'symbol' => $this->symbol,
            'created_at' => $this->created_at,
        ];
    }
}
