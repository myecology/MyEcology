<?php

namespace common\models\shop;

use api\controllers\APIFormat;
use backend\models\Setting;
use common\models\CurrencyPrice;
use common\models\Invitation;
use common\models\Wallet;
use common\models\WalletLog;
use api\models\User;
use Yii;
use yii\base\ErrorException;
use yii\db\StaleObjectException;

/**
 * This is the model class for table "iec_wine_level".
 *
 * @property int $id
 * @property int $level 代数
 * @property int $user_id
 * @property int $effective_time 生效时间
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $status 状态值 10为生效，20为失效
 */
class WineLevel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_wine_level';
    }



    const STATUS_YES = 10;
    const STATUS_NO = 20;

    public static $statusArr = [
        WineLevel::STATUS_YES => '有效',
        WineLevel::STATUS_NO => '无效'
    ];
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['level', 'user_id', 'effective_time', 'created_at', 'updated_at', 'status'], 'integer'],
            [['user_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'level' => '有效人数',
            'user_id' => '用户',
            'effective_time' => '生效时间',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'status' => '状态',
        ];
    }

    /**
     * 增加代数
     * @return bool
     */
    public function addLevel(){
        $this->level += 1;
        $this->updated_at = time();
        if($this->save()){
            return true;
        }
        return false;
    }

    /**
     * @param $status
     * @return bool
     */
    public function updateStatus($status){
        if(in_array($status,static::$statusArr)){
            $this->status = $status;
            $this->updated_at = time();
            if($this->save()){
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     * 修改到期时间
     */
    public function updateEffectiveTime(){
        $this->updated_at = time();
        $this->effective_time = time();
        if($this->save()){
            return true;
        }
        return false;
    }

    /**
     * 购买服务人创建生效代数
     * 已有就更新生效时间
     * @param $user_id
     * @return WineLevel|null
     * @throws ErrorException
     */
    public static function findOneByUserIdAndCreated($user_id){
        $wineLevel = static::findOne([
            'user_id' => $user_id
        ]);
        if(empty($wineLevel)){
            $wineLevelModel = new static();
            $wineLevelModel->updated_at = time();
            $wineLevelModel->created_at = time();
            $wineLevelModel->effective_time = time();
            $wineLevelModel->level = 0;
            $wineLevelModel->status = static::STATUS_YES;
            $wineLevelModel->user_id = $user_id;
            if(!$wineLevelModel->save()){
                throw new ErrorException(APIFormat::popError($wineLevelModel->getErrors()),999);
            }
            return $wineLevelModel;
        }
        $wineLevel->effective_time = time();
        $wineLevel->updated_at = time();
        if($wineLevel->save()){
            return $wineLevel;
        }
        throw new ErrorException(APIFormat::popError($wineLevel->getErrors()),999);
    }

    /**
     * 返利
     * @param $user_id
     * @param MallGoodsLog $goodsLog
     * @return bool
     * @throws ErrorException
     */
    public static function puserList($user_id,MallGoodsLog $goodsLog){

        $pusers = Invitation::findAll(['registerer_id'=>$user_id]);
        if(empty($pusers))return true;
        switch ($goodsLog->activity){
            case MallGoodsActivityLog::ACTIVITY_WINE:
                $wineSymbol = Setting::read('wine_symbol','wine');
                $wineLevel = explode(',',Setting::read('wine','wine'));
                $wineSymbolPrice = CurrencyPrice::getPriceBySymbol($wineSymbol);
                if(empty($wineSymbolPrice)){
                    throw new ErrorException('币种价格参数失效');
                };
                /**
                 * @var Invitation $user
                 */
                foreach ($pusers as $user){
                    if(!empty($user->wine) && $user->level <= $user->wine->level && $user->wine->status == static::STATUS_YES){
                        $price = isset($wineLevel[$user->level-1])?$wineLevel[$user->level-1]:0;
                        if(empty($price)){
                            continue;
                        }
                        if($user->wine->effective_time + 86400*90 < time()){
                            if($user->wine->updateStatus(static::STATUS_NO)){
                                throw new ErrorException('修改状态失败');
                            }
                        }
                        $amount = $price*$goodsLog->number/$wineSymbolPrice;
                        if(!MallGoodsActivityLog::findUserGoods($user->wine->user_id,$goodsLog->id,$amount,$wineSymbol,$goodsLog->activity)){
                            continue;
                        }

                        static::addMoney($wineSymbol,$user->wine->user_id,$amount,WalletLog::TYPE_WINE_ACTIVITY,$goodsLog->id.'-'.$wineSymbolPrice);
                        //发送消息


                    }
                }
                break;
            case MallGoodsActivityLog::ACTIVITY_REBATE:
                $mallSymbol = Setting::read('mall_symbol','mall');
                $mallRebate = Setting::read('mall_rebate','mall');
                $mallLevel = explode(',',Setting::read('mall','mall'));
                $mallSymbolPrice = CurrencyPrice::getPriceBySymbol($mallSymbol);
                $goodsAmount = CurrencyPrice::getPriceBySymbol($goodsLog->symbol);
                $sumAmount = $goodsLog->amount * $mallRebate/100*$goodsAmount;
                /**
                 * @var Invitation $user
                 */
                foreach ($pusers as $key => $user){
                    $price = isset($mallLevel[$user->level-1])?$mallLevel[$user->level-1]:0;
                    if(empty($price)){
                        continue;
                    }
                    $amount = $sumAmount*($price/100)/$mallSymbolPrice;
                    if(!MallGoodsActivityLog::findUserGoods($user->inviter_id,$goodsLog->id,$amount,$mallSymbol,$goodsLog->activity)){
                        continue;
                    }
                    static::addMoney($mallSymbol,$user->inviter_id,$amount,WalletLog::TYPE_MALL_ACTIVITY,$goodsLog->id);
                    //发送消息
                }
                break;
            default:
                throw new ErrorException('没有这个活动');
        }

    }

    /**
     * 获取用户信息
     * @return [type] [description]
     */
    public function getUser(){
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }


    public static function addMoney($symbol,$user_id,$amount,$type,$business_sn){
        var_dump(123);
        try{
            /***
             * @var Wallet $userWallet
             */
            $userWallet = Wallet::findOneByWallet($symbol,$user_id);
            if(empty($userWallet)){
                throw new ErrorException('用户创建钱包失败'.$user_id);
            }
            if(!$userWallet->earnMoney($amount,$type,$business_sn)){
                throw new ErrorException('加钱失败'.$user_id);
            }
        }catch (StaleObjectException $exception){
            var_dump(456);
            static::addMoney($symbol,$user_id,$amount,$type,$business_sn);
        }
    }
}
