<?php

namespace common\models\crow;

use Yii;
use yii\base\ErrorException;
use common\models\crow\ReleaseLog;
use common\models\Wallet;
use common\models\WalletLog;
use api\models\User;

/**
 * This is the model class for table "iec_mall_crow_log".
 *
 * @property int $id
 * @property int $crow_id 众筹id
 * @property int $user_id 用户id
 * @property string $crow_name 众筹名称
 * @property int $number 购买数量
 * @property string $pay_symbol 支付币种
 * @property string $symbol 币种
 * @property string $pay_number 支付金额
 * @property int $status 支付状态=1:成功,2:失败
 * @property int $type 释放时间1:直接释放2:分期释放
 * @property string $release_at 释放时间
 * @property int $release_cycle 释放周期
 * @property int $release_times 释放次数
 * @property string $each_release 每次释放
 * @property string $created_at 创建时间
 * @property string $release_end_at 释放结束时间
 * @property int $mall_crow_id 购买id
 */
class MallCrowLog extends \yii\db\ActiveRecord
{

    public static $statusArr = [
        1 => "支付成功",
        2 => "支付失败",
    ];

    public static $typeArr = [
        1 => '直接释放',
        2 => '分期释放',
    ];

    public static $releaseArr = [
        1 => "未释放",
        2 => '释放中',
        3 => '释放完成',
    ];

    const STATUS_SUCCESS = 1;
    const STATUS_LOSE = 2;
    const TYPE_ONE = 1;
    const TYPE_TWO = 2;
    const RELEASE_ONE = 1;
    const RELEASE_MANY = 2;
    const RELEASE_SUCCESS = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_mall_crow_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['crow_id', 'user_id', 'crow_name', 'number', 'pay_symbol', 'symbol', 'status'], 'required'],
            [['crow_id', 'user_id', 'number', 'status', 'type', 'release_cycle', 'release_times', 'mall_crow_id'], 'integer'],
            [['release_at', 'created_at', 'is_release', 'release_end_at'], 'safe'],
            [['each_release'], 'number'],
            [['crow_name', 'pay_symbol', 'pay_number'], 'string', 'max' => 255],
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
            'crow_id' => '众筹id',
            'user_id' => '用户ID',
            'crow_name' => '众筹名称',
            'number' => '数量',
            'pay_symbol' => '支付币种',
            'symbol' => '币种',
            'pay_number' => '支付数量',
            'status' => '状态',
            'type' => '类型',
            'release_at' => '释放时间',
            'release_cycle' => '释放周期',
            'release_times' => '释放次数',
            'each_release' => '每次释放',
            'created_at' => '创建时间',
            'mall_crow_id' => '购买id',
            'is_release' => '是否释放',
            'time' => '已释放次数',
            'release_end_at' => '释放结束时间',
        ];
    }

    public static function add($data){
        $model = new static();
        $data['status'] = 1;
        $model->setAttributes($data);
        if(!$model->save()){
            throw new \ErrorException("添加购买众筹记录失败", 1);
        }
        return $model;
    }

    public function getUser(){
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function ReleaseExchange($data,$crow_name){
        $static = new static();
        $static->id = $data['log_id'];
        $static->crow_name = $crow_name;
        if(!ReleaseLog::add($data,static::TYPE_ONE,$static)){
            throw new \ErrorException("添加释放记录失败", 8654);
        }
        //用户加钱
        $userWallet = Wallet::findOneByWallet($data['symbol'],$data['user_id']);
        if(empty($userWallet)){
            throw new \ErrorException('获取用户钱包失败', 888);
        }
        $res = $userWallet->earnMoney($data['amount'],WalletLog::CROW_RELEASE);
        if(!$res){
            throw new \ErrorException("释放失败", 8651);
        }
        //修改状态
        $model = static::findOne($data['log_id']);
        $model->is_release = static::RELEASE_SUCCESS;
        if(!$model->save()){
            throw new \ErrorException("释放失败", 1);
        }
        return true;
    }

    /**
     * 众筹节点直接释放
     * @return [type] [description]
     */
    public static function release(){
        //查询所有直接释放的用户
        $release_one = static::find()->where(['is_release'=>static::RELEASE_ONE])
        ->andwhere(['<=','release_at',date("Y-m-d H:i:s")])
        ->andwhere(['type'=>static::TYPE_ONE])
        ->andwhere(['is_release'=>static::RELEASE_ONE])
        ->asArray()->all();
        foreach($release_one as $key=>$val){
            $transaction = \Yii::$app->db->beginTransaction();//开启事务
            try{
                $data = [
                    'user_id' => $val['user_id'],
                    'amount' => $val['number'],
                    'symbol' => $val['symbol'],
                    'log_id' => $val["id"],
                    'mall_log_id' => $val['mall_crow_id'],
                ];
                $static = new static();
                $static->id = $val['id'];
                $static->crow_name = $val['crow_name'];
                ReleaseLog::add($data,static::TYPE_ONE,$static);
                //用户加钱
                $userWallet = Wallet::findOneByWallet($val['symbol'],$val['user_id']);
                if(empty($userWallet)){
                    throw new \ErrorException('获取用户钱包失败', 888);
                }
                $res = $userWallet->earnMoney($val['number'],WalletLog::CROW_RELEASE);
                if(!$res){
                    throw new \ErrorException("释放失败", 1);
                }
                //修改状态
                $model = static::findOne($val['id']);
                $model->is_release = static::RELEASE_SUCCESS;
                if(!$model->save()){
                    throw new \ErrorException("释放失败", 1);
                }
                // var_dump($val['crow_name']);
                $transaction->commit();
            }catch(\ErrorException $e){
                $transaction->rollBack();
                // var_dump($e->getMessage());
                Yii::error($val["id"].':'.$e->getMessage(),'release');
            }
        }
    }

    /**
     * 分期释放众筹
     */
    public static function ManyRelease(){
        //查询分期释放
        $release = static::find()
        ->where(['type'=>static::TYPE_TWO])
        ->andwhere(['>','release_times',0])
        ->andwhere(['>=','release_at',date("Y-m-d")])
        ->andwhere(["status"=>1])
        ->asArray()->all();
        foreach($release as $key=>$val){
            $transaction = \Yii::$app->db->beginTransaction();//开启事务
            try{
                $data = [
                    'user_id' => $val['user_id'],
                    'amount' => $val['each_release'],
                    'symbol' => $val['symbol'],
                    'log_id' => $val['id'],
                    'mall_log_id' => $val['mall_crow_id'],
                ];
                $static = new static();
                $static->id = $val['id'];
                $static->crow_name = $val['crow_name'];
                ReleaseLog::add($data,static::TYPE_TWO,$static,$val['release_cycle']);
                $userWallet = Wallet::findOneByWallet($val['symbol'],$val['user_id']);
                if(empty($userWallet)){
                    throw new \ErrorException('获取用户钱包失败', 888);
                }
                $res = $userWallet->earnMoney($val['each_release'],WalletLog::CROW_RELEASE);
                if(!$res){
                    throw new \ErrorException("释放失败", 1);
                }
                //修改状态
                $model = static::findOne($val['id']);
                $model->time = $val['time']+1;
                $model->release_times = $val['release_times']-1;
                if($val['release_times']-1 <= 0){
                    $model->is_release = static::RELEASE_SUCCESS;
                }else{
                    $model->is_release = static::RELEASE_MANY;
                }
                if(!$model->save()){
                    throw new \ErrorException("释放失败", 1);
                }
                $transaction->commit();
            }catch(\ErrorException $e){
                $transaction->rollBack();
                var_dump($e->getMessage());
                Yii::error($val["id"].':'.$e->getMessage(),'release');               
            }
        }
    }
    
}
