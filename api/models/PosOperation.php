<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/8/3
 * Time: 10:27 AM
 */

namespace api\models;


use api\controllers\APIFormat;
use common\models\Currency;
use common\models\Shop;
use common\models\shop\ShopUserLog;
use common\models\shop\ShopWalletLog;
use common\models\Wallet;
use yii\base\ErrorException;
use yii\base\Model;

class PosOperation extends Model
{
    public $orderNo;
    public $orderType;
    public $orderContents;

    public static $typeArr = [ // 来源
        'POS_WATER' => ShopUserLog::STATUS_POS,
        'BOX' => ShopUserLog::STATUS_SHOP,
        'TOTAL' => ShopUserLog::STATUS_REWARD,
        'POS_SALE'=> ShopUserLog::STATUS_POS_SALE,
    ];

    public static $roleArr = [ // 角色
        '_CLIENT_ORDER' => '用户',
        '_SHOP_ORDER' => '商家',
        '_DAILI_ORDER' => '代理',
    ];

    public function rules()
    {
       return [
           [['orderNo','orderType','orderContents'], 'required'],
           ['orderType',function(){
                if(!isset(static::$typeArr[$this->orderType])){
                    throw new ErrorException('订单类型不合格',444);
                }
           }]
       ];
    }


    public function operation(){
        $result['orderNo'] = $this->orderNo;
        $result['orderType'] = $this->orderType;
        $result['orderContents'] = $this->orderContents;
        if($this->validate()){
            $result['orderContents'] = [];
            $transaction = \Yii::$app->db->beginTransaction();
            try{
                foreach($this->orderContents as $key => $orderContents){
                    $orderContents;
                    try{
                        $symbolModel = Currency::findCurrencyBySymbol($orderContents['symbol']);
                        if(empty($symbolModel)){
                            throw new ErrorException('操作币种不存在',1111);
                        }
                        switch ($orderContents['role']){
                            case '_CLIENT_ORDER':
                                $userModel = User::findOne([
                                    'username' => $orderContents['phoneNo']
                                ]);
                                if(empty($userModel)){
                                    throw new ErrorException('用户不存在',1111);
                                }
                                $flag = ShopUserLog::findByorderSn($this->orderNo,$userModel->id);
                                if(!empty($flag)){
                                    throw new ErrorException("订单已操作",1111);
                                }
                                $userWalletModel = Wallet::findOneByWallet($orderContents['symbol'],$userModel->id);
                                if(empty($userWalletModel)){
                                    throw new ErrorException('用户钱包创建失败',1111);
                                }
                                if(abs($orderContents['amount']) == 0){
                                    throw new ErrorException('操作金额不能为0',1111);
                                }
                                ShopUserLog::addLog(ShopUserLog::TYPE_WITHDRAW,$userWalletModel,$userModel->id,$this,$userModel,$orderContents['amount'],static::$typeArr[$this->orderType]);
                                break;
                            case '_SHOP_ORDER':
                                $shopLog = ShopWalletLog::findByOrderSn($this->orderNo);
                                if(!empty($shopLog)){
                                    throw new ErrorException("订单已操作",1111);
                                }
                                $shopModel = Shop::findOne([
                                    'phone' => $orderContents['phoneNo']
                                ]);
                                if(empty($shopModel)){
                                    throw new ErrorException("商家不存在",1111);
                                }
                                if($orderContents['amount'] == 0){
                                    throw new ErrorException('操作金额不能为0',1111);
                                }
                                $type = $orderContents['amount'] > 0 ? ShopWalletLog::TYPE_DEPOSIT : ShopWalletLog::TYPE_WITHDRAW;
                                $amount = abs($orderContents['amount']);
                                ShopWalletLog::addLog($type,$amount,$orderContents['symbol'],$shopModel->user->id,$shopModel->id,$this->orderNo,static::$typeArr[$this->orderType]);
                                break;
                            case '_DAILI_ORDER':

                                $userModel = User::findOne([
                                    'username' => $orderContents['phoneNo']
                                ]);
                                if(empty($userModel)){
                                    throw new ErrorException('用户不存在',1111);
                                }
                                $flag = ShopUserLog::findByorderSn($this->orderNo,$userModel->id);
                                if(!empty($flag)){
                                    throw new ErrorException("订单已操作",1111);
                                }
                                $userWalletModel = Wallet::findOneByWallet($orderContents['symbol'],$userModel->id);
                                if(empty($userWalletModel)){
                                    throw new ErrorException('用户钱包创建失败',1111);
                                }
                                if(abs($orderContents['amount']) == 0){
                                    throw new ErrorException('操作金额不能为0',1111);
                                }
                                ShopUserLog::addLog(ShopUserLog::TYPE_WITHDRAW,$userModel,$userModel->id,$this,$userModel,$orderContents['amount'],static::$typeArr[$this->orderType]);
                                break;
                            default:
                                throw new ErrorException('角色不存在',1111);
                        }

                        $orderContents['status'] = 200;
                        $orderContents['msg'] = '';
                        $orderContents['data'] = 'success';
                        $result['orderContents'][] = $orderContents;
                    }catch (\ErrorException $exception){
                        $orderContents['status'] = $exception->getCode();
                        $orderContents['msg'] = $exception->getMessage();
                        $orderContents['data'] = 'fall';
                        $result['orderContents'][] = $orderContents;
                        throw new ErrorException($exception->getMessage(),$exception->getCode());
                    }catch (\Exception $exception){
                        $orderContents['status'] = $exception->getCode();
                        $orderContents['msg'] = $exception->getMessage();
                        $orderContents['data'] = 'fall';
                        $result['orderContents'][] = $orderContents;
                        throw new ErrorException($exception->getMessage(),$exception->getCode());
                    }
                }
                $result['status'] = 200;
                $result['msg'] = '';
                $result['data'] = 'success';
                $transaction->commit();
            }catch (\ErrorException $exception){
                $result['status'] = $exception->getCode();
                $result['msg'] = $exception->getMessage();
                $result['data'] = 'fall';
                $transaction->rollBack();
            }catch (\Exception $exception){
                $result['status'] = $exception->getCode();
                $result['msg'] = $exception->getMessage();
                $result['data'] = 'fall';
                $transaction->rollBack();
            }
        }else{
            $result['status'] = APIFormat::popError($this->getErrors());
            $result['msg'] = 444;
            $result['data'] = 'fall';
        }
        return $result;
    }
}