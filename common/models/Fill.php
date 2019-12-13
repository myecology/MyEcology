<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "iec_fill".
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $symbol 拥有币种
 * @property int $status 状态，1：待补发，2：补发成功
 * @property string $release_symbol 奖励币种
 * @property string $amount 奖励金额
 */
class Fill extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_fill';
    }

    public static $statusArr = [
        '1' => "待补发",
        '2' => '补发成功',
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id','distrbute_id'], 'required'],
            [['user_id', 'status','is_del','distrbute_id'], 'integer'],
            [['amount'], 'number'],
            [['symbol', 'release_symbol'], 'string', 'max' => 20],
            [['created_at'],'safe'],
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
            'symbol' => '释放币种',
            'status' => '状态',
            'release_symbol' => '持有币种',
            'amount' => '通证总金额',
            'is_del' => "删除",
        ];
    }

    public function getUser()
    {
        return $this->hasOne(\api\models\User::className(), ['id' => 'user_id']);
    }

    public function getDistrbute()
    {
        return $this->hasOne(\common\models\DistrbuteLog::className(), ['id' => 'distrbute_id']);
    }

    public static function AddFill($data){
        $model = new static();
        $model->setAttributes($data);
        if(!$model->save()){
            throw new \ErrorException("添加失败", 1);
        }
        return true;
    }

    /**
     * 删除补发信息
     * @param [type] $id [description]
     */
    public static function Del($id){
        $model = static::findOne($id);
        $model->status = 2;
        $model->is_del = 2;
        if(!$model->save()){
            throw new \ErrorException("删除失败", 1);
        }
        return true;
    }
}
