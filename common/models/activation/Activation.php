<?php

namespace common\models\activation;

use backend\modules\activation\models\ActivationParamForm;
use common\models\CurrencyPrice;
use common\models\Invitation;
use common\models\shop\MallGoodsLog;
use common\models\Wallet;
use common\models\WalletLog;
use Yii;
use yii\base\ErrorException;
use yii\db\StaleObjectException;

/**
 * This is the model class for table "iec_activation".
 *
 * @property int $id
 * @property int $status 状态
 * @property string $name 活动名称
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property string $as_name 简称
 */
class Activation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_activation';
    }


    public static $statusArr = [
        Activation::STATUS_NO => '关闭',
        Activation::STATUS_YES => '开启'
    ];

    const STATUS_NO = 1;
    const STATUS_YES = 2;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 50],
            [['as_name'], 'string', 'max' => 20],
            [['as_name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => '状态',
            'name' => '活动名称',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
            'as_name' => '简称',
        ];
    }


    /***
     * 指向活动
     * @param $activation
     * @param MallGoodsLog $mallGoodsLog
     * @return bool
     * @throws \ErrorException
     */
    public static function runActivation($activation,MallGoodsLog $mallGoodsLog){
        echo '活动开始';
        $model = static::findOne([
            'as_name' => trim($activation)
        ]);
        if(empty($model) || $model->status == static::STATUS_NO){
            if(!$mallGoodsLog->updateStatus(MallGoodsLog::STATUS_ACTIVITY_SUCCESS))
                throw new \ErrorException('修改状态失败',999);
            return  true;
        }
        $modelParam = ActivationParam::getCacheParam($model->id);
        if(empty($modelParam['key'])){
            throw new \ErrorException('活动参数缺失',999);
        }
        if(empty($modelParam['key']['mode'])){
            throw new \ErrorException('活动模式缺失',999);
        }
        switch ($modelParam['key']['mode']){
            case ActivationParamForm::MODE_ROYALTY:
                //返利模式
                static::royalty($mallGoodsLog,$modelParam,$model);
                break;
            case ActivationParamForm::MODE_LEVEL:
                //等级模式
                static::level($mallGoodsLog,$modelParam,$model);
                break;
            default:
                break;
        }
        if(!$mallGoodsLog->updateStatus(MallGoodsLog::STATUS_ACTIVITY_SUCCESS))
            throw new \ErrorException('修改状态失败',999);

        return true;
    }


    /***
     * 返利模式
     * @param MallGoodsLog $mallGoodsLog
     * @param $modelParam
     * @param Activation $activation
     * @return bool
     * @throws \ErrorException
     */
    public static function royalty(MallGoodsLog $mallGoodsLog,$modelParam,Activation $activation){
        //确定返利模式
        $user_id = false;
        switch ($modelParam['key']['royalty_type']){
            case ActivationParamForm::ROYALTY_TYPE_USER:
                //返利用户上级
                $user_id = $mallGoodsLog->user_id;
                break;
            case ActivationParamForm::ROYALTY_TYPE_SHOP:
                //返利商家上级
                $user_id = $mallGoodsLog->shop_id;
                break;
            default:
                return true;
                break;
        }
        if(empty($user_id)){
            throw new \ErrorException('返利模式不正确 不能确定返利上级',999);
        }
        $price = CurrencyPrice::getPriceBySymbol($mallGoodsLog->symbol);
        $royalty_amount = $mallGoodsLog->amount*$price*$modelParam['key']['royalty']/100;//提成金额
        $pusers = Invitation::findAll(['registerer_id'=>$user_id]);
        if(empty($pusers)){
            return true;
        }
        /**
         * @var Invitation $user
         */
        $royalty_symbol_price = CurrencyPrice::getPriceBySymbol($modelParam['key']['reward_symbol']);
        foreach ($pusers as $user){
            $royalty = empty($modelParam['group']['reward_level'][$user->level]) ? 0 : $modelParam['group']['reward_level'][$user->level];
            if($royalty < 0){
                continue;
            }
            $amount = $royalty_amount*$royalty/100/$royalty_symbol_price*$mallGoodsLog->number;
            if($amount>0){
                if(!static::addAmount($user->inviter_id,$amount,$modelParam['key']['reward_symbol'],$mallGoodsLog->id,$activation)){
                    throw new \ErrorException('用户加钱失败',999);
                }
            }
            continue;
        }
        return  true;
    }

    /**
     * 活动加钱
     * @param $user_id
     * @param $amount
     * @param $symbol
     * @param $mall_goods_log_id
     * @param $activation
     * @return bool
     * @throws \ErrorException
     */
    public static function addAmount($user_id,$amount,$symbol,$mall_goods_log_id,Activation $activation){
        $model = ActivationRewardLog::addLog($user_id,$activation->id,$mall_goods_log_id,$amount,$symbol);
        $userWallet = Wallet::findOneByWallet($symbol,$user_id);
        try{
            if($userWallet->earnMoney($amount,WalletLog::ACTIVATION,(string)$model->id)){
                return true;
            }
        }catch (StaleObjectException $exception){
            static::addAmount($user_id,$amount,$symbol,$mall_goods_log_id,$activation);
        }
        return false;
    }

    /**
     * 等级奖励
     * @param MallGoodsLog $mallGoodsLog
     * @param $modelParam
     * @param Activation $activation
     * @return bool
     * @throws \ErrorException
     */
    public static function level(MallGoodsLog $mallGoodsLog,$modelParam,Activation $activation){
        //确定一级代表几级
        $levelCount = $modelParam['key']['level_proportion'];
        echo $levelCount;
        //确定是否有失效时间
        $expiration_time = $modelParam['key']['expiration_time'];
        if(empty($expiration_time) || $expiration_time <= 0){
            ActivationUserList::addList($activation,$mallGoodsLog->user_id);
        }else{
            $end_time = strtotime("+{$expiration_time}day");
            ActivationUserList::addList($activation,$mallGoodsLog->user_id,$end_time);
        }
        $pusers = Invitation::findAll(['registerer_id'=>$mallGoodsLog->user_id]);
        if(empty($pusers)){
            return true;
        }
        /**
         * @var Invitation $user
         */
        $royalty_symbol_price = CurrencyPrice::getPriceBySymbol($modelParam['key']['reward_symbol']);
        foreach ($pusers as $user){
            $activationUserListModel = ActivationUserList::findOne([
                'activation_id' => $activation->id,
                'user_id' => $user->inviter_id
            ]);
            if(empty($activationUserListModel)){
                continue;
            }
            if($user->level == 1){
                if(!empty($activationUserListModel)){
                    echo '增加等级';
                    if(ActivationUserListLog::addLog($mallGoodsLog->user_id,$user->inviter_id,$activation,$mallGoodsLog)){
                        $activationUserListModel->addLevel($levelCount);
                        echo '增加等级节点';
                    }
                }
            }
            if($activationUserListModel->end_time !=0 && $activationUserListModel->end_time<time()){
                continue;
            }
            if($activationUserListModel->level<$user->level){
                continue;
            }
            $price = empty($modelParam['group']['reward_level'][$user->level]) ? 0 : $modelParam['group']['reward_level'][$user->level];
            if($price < 0){
                continue;
            }
            $amount = $price/$royalty_symbol_price*$mallGoodsLog->number;
            if($amount <= 0){
                continue;
            }
            if(!static::addAmount($user->inviter_id,$amount,$modelParam['key']['reward_symbol'],$mallGoodsLog->id,$activation)){
                throw new \ErrorException('用户加钱失败',999);
            }
        }
        return true;
    }
}
