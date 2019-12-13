<?php

namespace common\models\node;

use Yii;
use yii\base\ErrorException;
use api\controllers\APIFormat;
use common\models\Verification;
use common\models\Invitation;
use common\models\node\RewardLog;
use common\models\Wallet;
use common\models\WalletLog;
use backend\models\Setting;
use api\models\User;
use common\models\shop\MallGoodsLog;
use common\models\CurrencyPrice;
use common\models\SendService;
/**
 * This is the model class for table "iec_mall_node_log".
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $alte_price 备选价格
 * @property string $alte_symbol 备选币种
 * @property int $alte_status 1:初始化，2已完成，3未完成
 * @property string $alte_at 备选时间
 * @property string $super_price 超级节点价格
 * @property string $super_symbol 超级节点币种
 * @property int $super_status 1初始化，2已完成，3未完成
 * @property string $super_at 超级节点时间
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class MallNodeLog extends \yii\db\ActiveRecord
{

    public static $altestatusArr = [
        1 => '初始状态',
        2 => '支付成功',
        3 => '支付失败',
    ];

    public static $superstatusArr = [
        1 => '初始状态',
        2 => '支付成功',
        3 => '支付失败',
    ];
    const ALTE_STATUS_DEFAULT = 1;
    const ALTE_STATUS_SUCCESS = 2;
    const ALTE_STATUS_LOSE = 3;

    const SUPER_STATUS_DEFAULT = 1;
    const SUPER_STATUS_SUCCESS = 2;
    const SUPER_STATUS_LOSE = 3;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_mall_node_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'alte_price', 'alte_symbol', 'alte_status'], 'required'],
            [['user_id', 'alte_status', 'super_status'], 'integer'],
            [['alte_price', 'super_price'], 'number'],
            [['alte_at', 'super_at', 'created_at', 'updated_at'], 'safe'],
            [['alte_symbol', 'super_symbol'], 'string', 'max' => 20],
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
            'alte_price' => '备选价格',
            'alte_symbol' => '备选币种',
            'alte_status' => '备选状态',
            'alte_at' => '备选时间',
            'super_price' => '超级价格',
            'super_symbol' => '超级币种',
            'super_status' => '超级状态',
            'super_at' => '超级时间',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 添加备选节点购买信息
     * @param [type] $data [description]
     */
    public static function AddMallLog($data,$user_id){
        $model = static::findOne(['user_id'=>$user_id]);
        if(empty($model)){
            $mall = new static();
            $datas = [
                'alte_price' => $data->amount,
                'alte_symbol' => $data->symbol,
                'user_id' => $user_id,
                'alte_status' => static::ALTE_STATUS_DEFAULT,
            ];
            // var_dump($datas);die;
            $mall->setAttributes($datas);
            if(!$mall->save()){
                throw new \ErrorException("添加购买记录失败", 4999);
            }
            return $mall;
        }else{
            if($model->alte_status == static::ALTE_STATUS_SUCCESS){
                throw new \ErrorException("请不要重复购买", 4777);
            }
            $model->alte_price = $data->amount;
            $model->alte_symbol = $data->symbol;
            $model->alte_status = static::ALTE_STATUS_DEFAULT;
            if(!$model->save()){
                throw new \ErrorException("修改购买记录失败", 4888);
            }
            return $model;
        }
    }

    /**
     * 修改备选节点信息
     * @param [type] $data [description]
     * @param [type] $id   [description]
     */
    public static function UpdateAlte($id){
        $model = static::findOne($id);
        // var_dump(static::ALTE_STATUS_SUCCESS);die;
        $model->alte_status = static::ALTE_STATUS_SUCCESS;
        $model->alte_at = date("Y-m-d H:i:s");
        if(!$model->save()){
            throw new \ErrorException("修改购买记录失败", 4888);
        }
        return true;
    }

    /**
     * 修改超级节点
     * @param [type] $data [description]
     * @param [type] $id   [description]
     */
    public static function UpdateSuper($data,$id){
        $model = static::findOne($id);
        if(empty($model)){
            throw new \ErrorException("请先购买备选节点", 4777);
        }
        $model->super_price = $data->amount;
        $model->super_symbol = $data->symbol;
        $model->super_status = static::SUPER_STATUS_SUCCESS;
        $model->super_at = date("Y-m-d H:i:s");
        if(!$model->save()){
            throw new \ErrorException("修改购买记录失败", 4888);
        }
        return true;
    }

    /**
     * 人数排序
     */
    public function SortConut($user){
        //查询购买酒链活动的时间
        /*$activity = MallGoodsLog::findOne(['user_id'=>$user_id]);
        if(empty($activity)){
            return 0;           
        }*/
        sort($user);
        $count = Invitation::find()
        ->leftJoin("iec_mall_goods_log as b",'registerer_id=b.user_id')
        ->select("count(1)  count,inviter_id")
        ->where(['in','inviter_id',$user])
        ->andwhere(['between','level',1,10])
        ->andwhere(['b.activity'=>1])
        ->andwhere(['b.status'=>3])
        ->orderBy("count desc")
        ->groupBy("iec_invitation.inviter_id")
        ->asArray()->all();
        return $count;
    }

    /**
     * 获取用户信息
     * @return [type] [description]
     */
    public function getUser(){
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * 超级节点奖励
     */
    public static function SuperReward($amount,$symbol){
        if(YII_DEBUG){
            $url = "http://api.millcp.bxguo.net/verify/total";
        }else{
            $url = "http://zcapi.qiqianyi.com/verify/total";
        }
        //获取超级节点数
        $result = json_decode(SendService::curlGet($url),true);
        $count = $result['data'];
        //查询所有的超级节点用户
        $user = static::find()->where(['super_status'=>static::SUPER_STATUS_SUCCESS])->all();
        if(empty($user)){
            return true;
        }
        $reward = Setting::read('super_reward','node');
        //获取当前人名币价值
        $symbol_price = CurrencyPrice::getPriceBySymbol($symbol);
        $total_amount = ($amount*($reward/100))*$symbol_price;
        $symbol = Setting::read('super_reward_symbol','node');
        $price = CurrencyPrice::getPriceBySymbol($symbol);
        $amount = ($total_amount/$price)/$count;//每一个用户分到的金额
        foreach($user as $key=>$val){
            $model = new RewardLog();
            $data = [
                'user_id' => $val['user_id'],
                'amount' => $amount,
                'symbol' => $symbol,
                'type' => RewardLog::SUPER_TYPE,
            ];
            $model->setAttributes($data);
            if(!$model->save()){
                throw new \ErrorException("添加奖励记录失败", 4003);
            }
            //给用户加钱
            $userWallet = Wallet::findOneByWallet($symbol,$val['user_id']);
            if(empty($userWallet)){
                throw new \ErrorException("获取用户钱包失败", 4004);
            }
            $res = $userWallet->earnMoney($amount,WalletLog::SUPER_REWAED);
            if(!$res){
                throw new \ErrorException("奖励分成失败", 4005);
            }
        }
        return true;
    }
}
