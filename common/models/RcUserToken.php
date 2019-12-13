<?php

namespace common\models;

use Yii;
use yii\base\ErrorException;

/**
 * This is the model class for table "rc_user_token".
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $token_assets_id 代币id
 * @property int $token_type_id 代币类型id
 * @property string $available_balance 可用余额
 * @property string $locked_balance 锁定余额
 * @property string $asset_number 资产编号
 * @property string $every_time_number 每次释放数
 * @property int $created_at 创建时间
 * @property int $updated_at 跟新时间
 * @property int $deleted_at 删除时间
 * @property int $count 删除时间
 * @property int $status 删除时间
 */
class RcUserToken extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rc_user_token';
    }

    const STATUS_ING = 0;
    const STATUS_END = 10;
    public static $statusArr = [
        0 => '发放中',
        10 => '已完成',
    ];
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'token_assets_id'], 'required'],
            [['user_id', 'token_assets_id', 'created_at','updated_at'], 'integer'],
            [['available_balance', 'locked_balance'], 'number'],
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
            'token_type_id' => '代币类型id',
            'token_assets_id' => '代币id',
            'available_balance' => '可用余额',
            'locked_balance' => '锁定余额',
            'asset_number' => '资产编号',
            'every_time_number' => '每次释放数',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'deleted_at' => '删除时间',
        ];
    }

    /***
     * 脚本
     * @param $amount
     * @return bool
     * @throws ErrorException
     * @throws \ErrorException
     */
    public function queue($amount){
        if($this->tokenAssets->type == TokenAssets::TYPE_PHASE_RELEASE){
            $amount = $amount != $this->every_time_number ? $this->every_time_number : $amount;
        }
        //修改该用户该资产的释放数和锁仓数
        $this->available_balance += $amount;
        if($amount > $this->locked_balance){
            $amount = $this->locked_balance;
        }
        if($amount <= 0){
            throw new ErrorException('locked_balance: 已为0已发放完成');
        }
        $this->count++;
        $this->locked_balance -= $amount;
        $this->locked_balance = round($this->locked_balance,4);
        if($this->status != static::STATUS_END && $this->locked_balance<= 0){
            $this->status = static::STATUS_END;
        }
        if($this->locked_balance < 0){
            throw new ErrorException('user_token: 金额为负数');
        }
        $this->updated_at = time();
        if(!$this->save()){
            throw new ErrorException('user_token: 修改失败');
        }
        //修改该用户总资产的释放数和锁仓数
        $user_assets_amount = IecAssetsAmount::findOne(['user_id'=>$this->user_id]);
        $user_assets_amount->amount += $amount;
        if($user_assets_amount->amount_lock < $amount){
            throw new ErrorException('IecAssetsAmount: 小于锁定金额');
        }
        $user_assets_amount->amount_lock -= $amount;
        if( $user_assets_amount->amount_lock < 0){
            throw new ErrorException('IecAssetsAmount: 金额为负数');
        }
        $user_assets_amount->updated_at = time();
        if(!$user_assets_amount->save()){
            throw new ErrorException('IecAssetsAmount: 修改失败');
        }
        //修改用户MFCC钱包的金额和锁定金额
        $user_wallet = Wallet::createWallet($this->user_id,'MFCC');
        $user_wallet->amount_lock -= $amount;
        if( $user_wallet->amount_lock < 0){
            throw new ErrorException('Wallet: 金额为负数');
        }
        //钱包记录
        if(!$user_wallet->earnMoney($amount,WalletLog::TYPE_TOKEN_SCHEME)){
            throw new ErrorException('Wallet: 修改金额失败',9999);
        }
        //添加放币记录信息
        $locked_balance = $this ->locked_balance;
        if($locked_balance < 0){
            throw new ErrorException('RcUserToken: locked_balance 为负数');
        }

        $assetsLogModel = new AssetsLog();
        $logData = [
            'user_id' => $this->user_id,
            'token_assets_id' => $this->tokenAssets->id,
            'token_id' => $this->id,
            'amount' => $amount,
            'created_at' => time(),
        ];
        $assetsLogModel->setAttributes($logData);
        if(!$assetsLogModel->save()){
            throw new ErrorException('assets_log: 报错出错');
        }

        $message = [
            'asset_number'=>$this ->asset_number,//编号
            'type_name'=>$this->tokenAssets->personnel_type,//人员类型
            'release_total_number'=>$this->tokenAssets->currency_total,//释放总数量
            'unlock_number' => $amount,//解锁数量
            'locked_balance' => $locked_balance,//待解锁数量
        ];
        $message_res = Message::addAssetsMessage(Message::TYPE_UNLOCK_NOTICE,$this->user_id,$message);
        if(!$message_res){
            throw new ErrorException('message: 保存出错');
        }
        var_dump('queue end');
        return true;
    }
    
    //关联用户
    public function getUser(){
        return $this->hasOne(\api\models\User::className(),['id'=>'user_id'])->select('id,username,nickname');
    }
    //关联资产
    public function getTokenAssets(){
        return $this->hasOne(TokenAssets::className(),['id'=>'token_assets_id']);
    }
    //关联资产类型
    public function getTokenType(){
        return $this->hasOne(RcTokenType::className(),['id'=>'token_type_id']);
    }

    public function endStatus(){
        $this->status = static::STATUS_END;
        return $this->save();
    }
}
