<?php

namespace common\models;

use api\models\Callback;
use common\models\shop\ShopUserLog;
use Yii;
use yii\helpers\Json;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeBehavior;
use api\controllers\RongCloud;

/**
 * This is the model class for table "iec_message".
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $source_id 来源ID
 * @property int $type 类型
 * @property string $title 标题
 * @property string $symbol 币种
 * @property string $amount 数量
 * @property string $description 描述
 * @property int $created_at 创建时间
 */
class Message extends \yii\db\ActiveRecord
{

    const TYPE_MONEY_YOU_PAYMENT = 10;          //  付款
    const TYPE_MONEY_YOU_RECEIPT = 12;          //  收款
    const TYPE_TRANSACTION_WITHDRAW = 20;       //  提现
    const TYPE_TRANSACTION_DEPOSIT = 26;        //  充值
    const TYPE_FINANCIAL_BUY = 30;              //  购买理财
    const TYPE_FINANCIAL_PRINCIPAL = 34;        //  收益本金
    const TYPE_FINANCIAL_PROFIT = 36;           //  收益利息
    const TYPE_SUPERNODE_BUY = 40;              //  购买超级节点
    const TYPE_SUPERNODE_EXIT = 42;             //  退出超级节点
    const TYPE_SUPERNODE_PROFIT = 46;           //  超级节点收益
    const TYPE_HONGBAO_BACK = 50;               //  红包退回
    const TYPE_SIGNUP_REWARD = 60;              //  注册奖励
    const TYPE_BUY_GOODS = 70;                  //购买商品
    const TYPE_BUY_REWARD = 75;                  //下级购买奖励
    const TYPE_SHOP_EARN = 80;                  //商家入账
    const TYPE_SHOP_USER = 85;                  //账户转变

    const TYPE_SHARE_BONUS = 90;                  //通证分红
    const TYPE_ROLL_WITHDRAW = 100;                  //提现回滚
    const TYPE_RELEASE_CROW = 110;                 //众筹释放

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_message';
    }

    /**
     * 模型行为
     * @return [type] [description]
     */
    public function behaviors()
    {
        return [
            //创建时间
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'source_id', 'type', 'title', 'symbol', 'description'], 'required'],
            [['source_id', 'type'], 'integer'],
            [['amount'], 'number'],
            [['title', 'description'], 'string', 'max' => 255],
            [['symbol'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'source_id' => 'Source ID',
            'type' => 'Type',
            'title' => 'Title',
            'symbol' => 'Symbol',
            'amount' => 'Amount',
            'description' => 'Description',
            'created_at' => 'Created At',
        ];
    }


    /**
     * 添加消息
     *
     * @param [type] $type
     * @param [type] $description
     * @param [type] $symbol
     * @param [type] $amount
     * @param [type] $sourceModel
     * @return void
     */
    public static function addMessage($type, $user, $symbol, $amount, $sourceModel)
    {
        switch ($type) {
                //  付款
            case static::TYPE_MONEY_YOU_PAYMENT:
                $userID = Yii::$app->user->identity->id;
                $title = '@YOU付款';

                //  判断备注
                $userFriend = \api\models\UserFriend::find()->where(['in_userid' => Yii::$app->user->identity->userid, 'to_userid' => $user->userid])->one();
                if($userFriend && $userFriend->remark){
                    $nickname = $userFriend->remark;
                }else{
                    $nickname = $user->nickname;
                }

                $description = $nickname;
                $content = [
                    'content' => '支付成功通知',
                    'extra' => time(),
                    'type' => $type,
                ];
                RongCloud::getInstance()->sendPrivateServiceMessage($user->userid, Yii::$app->user->identity->userid, Json::encode($content));

                break;
                //  收款
            case static::TYPE_MONEY_YOU_RECEIPT:
                $userID = $user->id;
                $title = '@YOU收款';

                //  判断备注
                $userFriend = \api\models\UserFriend::find()->where(['in_userid' => $user->userid, 'to_userid' => Yii::$app->user->identity->userid])->one();
                if($userFriend && $userFriend->remark){
                    $nickname = $userFriend->remark;
                }else{
                    $nickname = $user->nickname;
                }

                $description = $nickname;
                $content = [
                    'content' => '到账通知', 
                    'extra' => time(),
                    'type' => $type,
                ];
                RongCloud::getInstance()->sendPrivateServiceMessage(Yii::$app->user->identity->userid, $user->userid, Json::encode($content));

                break;
            
                //  提现
            case static::TYPE_TRANSACTION_WITHDRAW:
                $userID = $user->id;
                $title = '提现';
                $description = $sourceModel->address;
                $content = [
                    'content' => '提现成功通知',
                    'extra' => time(),
                    'type' => $type,
                ];
                RongCloud::getInstance()->sendPrivateServiceMessage($user->userid, $user->userid, Json::encode($content));
                break;

                //  充值
            case static::TYPE_TRANSACTION_DEPOSIT:
                $userAddress = \api\models\WalletAddress::findOne($sourceModel->address_id);
                $userID = $user->id;
                $title = '充值';
                $description = $userAddress->address;
                $content = [
                    'content' => '到账通知',
                    'extra' => time(),
                    'type' => $type,
                ];
                RongCloud::getInstance()->sendPrivateServiceMessage($user->userid, $user->userid, Json::encode($content));
                break;

                //  购买理财
            case static::TYPE_FINANCIAL_BUY:
                $userID = $user->id;
                $title = '令牌理财';
                $description = $sourceModel->product->name;
                $content = [
                    'content' => '支付成功通知',
                    'extra' => time(),
                    'type' => $type,
                ];
                RongCloud::getInstance()->sendPrivateServiceMessage($user->userid, $user->userid, Json::encode($content));
                break;

                //  收益本金
            case static::TYPE_FINANCIAL_PRINCIPAL:
                $userID = $user->id;
                $title = '令牌理财';
                $description = $sourceModel->product->name;
                $content = [
                    'content' => '本金解锁通知',
                    'extra' => time(),
                    'type' => $type,
                ];
                RongCloud::getInstance()->sendPrivateServiceMessage($user->userid, $user->userid, Json::encode($content));
                break;

                //  收益利息
            case static::TYPE_FINANCIAL_PROFIT:
                $userID = $user->id;
                $title = '令牌理财';
                $description = $sourceModel->product->name;
                $content = [
                    'content' => '收益解锁通知',
                    'extra' => time(),
                    'type' => $type,
                ];
                RongCloud::getInstance()->sendPrivateServiceMessage($user->userid, $user->userid, Json::encode($content));
                break;

                //  购买超级节点
            case static::TYPE_SUPERNODE_BUY:
                $userID = $user->id;
                $title = '超级节点';
                $description = '超级节点竞选';
                $content = [
                    'content' => '支付成功通知',
                    'extra' => time(),
                    'type' => $type,
                ];
                RongCloud::getInstance()->sendPrivateServiceMessage($user->userid, $user->userid, Json::encode($content));
                break;

                //  退出超级节点
            case static::TYPE_SUPERNODE_EXIT:
                $userID = $user->id;
                $title = '超级节点';
                $description = '取消超级节点';
                $content = [
                    'content' => '退出成功通知',
                    'extra' => time(),
                    'type' => $type,
                ];
                RongCloud::getInstance()->sendPrivateServiceMessage($user->userid, $user->userid, Json::encode($content));
                break;

                //  超级节点收益
            case static::TYPE_SUPERNODE_PROFIT:
                $userID = $user->id;
                $title = '令牌理财';
                $description = $sourceModel->messageDescription();
                $content = [
                    'content' => '佣金收益通知',
                    'extra' => time(),
                    'type' => $type,
                ];
                RongCloud::getInstance()->sendPrivateServiceMessage($user->userid, $user->userid, Json::encode($content));
                break;

                //  红包退回
            case static::TYPE_HONGBAO_BACK:
                $userID = $user->id;
                $title = '红包退回';
                $description = '超过24小时未被领取';
                $content = [
                    'content' => '红包过期退回通知',
                    'extra' => time(),
                    'type' => $type,
                ];
                RongCloud::getInstance()->sendPrivateServiceMessage($user->userid, $user->userid, Json::encode($content));
                break;

                //  注册奖励
            case static::TYPE_SIGNUP_REWARD:
                $userID = $user->id;
                $title = '邀请奖励';
                $description = $sourceModel->messageDescription();
                $content = [
                    'content' => '奖励通知',
                    'extra' => time(),
                    'type' => $type,
                ];
                RongCloud::getInstance()->sendPrivateServiceMessage($user->userid, $user->userid, Json::encode($content));
                break;

            //  购买商品
            case static::TYPE_BUY_GOODS:
                $userID = $user->id;
                $title = ShopUserLog::$typeArr[$sourceModel->type];
                $description = ShopUserLog::$statusArr[$sourceModel->status].$sourceModel->amount.$sourceModel->symbol;
                $content = [
                    'content' => $description,
                    'extra' => time(),
                    'type' => $type,
                ];
                RongCloud::getInstance()->sendPrivateServiceMessage($user->userid, $user->userid, Json::encode($content));
                break;
            case static::TYPE_BUY_REWARD:
                $userID = $user->id;
                $title = ShopUserLog::$typeArr[$sourceModel->type];
                $description = ShopUserLog::$statusArr[$sourceModel->status].$sourceModel->amount.$sourceModel->symbol;
                $content = [
                    'content' => $description,
                    'extra' => time(),
                    'type' => $type,
                ];
                RongCloud::getInstance()->sendPrivateServiceMessage($user->userid, $user->userid, Json::encode($content));
                break;

            //  商家入账
            case static::TYPE_SHOP_EARN:
                $userID = $user->id;
                $title = '商家入账通知';
                $description = '商家入账通知';
                $content = [
                    'content' => '商家入账'.$amount.$symbol,
                    'extra' => time(),
                    'type' => $type,
                ];
                RongCloud::getInstance()->sendPrivateServiceMessage($user->userid, $user->userid, Json::encode($content));
                break;
            case static::TYPE_SHOP_USER:
                $userID = $user->id;
                $title = '商家账户转成用户账户';
                $description = '商家账户转成用户账户';
                $content = [
                    'content' => '商家账户转成用户账户'.$amount.$symbol,
                    'extra' => time(),
                    'type' => $type,
                ];
                RongCloud::getInstance()->sendPrivateServiceMessage($user->userid, $user->userid, Json::encode($content));
                break;
           //  通证分红
            case static::TYPE_SHARE_BONUS:
                $userID = $user->id;
                $title = '通证分红通知';
                $description = '通证分红通知';
                $content = [
                    'content' => '通证分红'.$amount.$symbol,
                    'extra' => time(),
                    'type' => $type,
                ];
                RongCloud::getInstance()->sendPrivateServiceMessage($user->userid, $user->userid, Json::encode($content));
                break;
            case static::TYPE_ROLL_WITHDRAW:
                $userID = $user->id;
                $title = '提现失败退回';
                $description = '提现失败退回';
                $content = [
                    'content' => '提现失败退回'.$amount.$symbol,
                    'extra' => time(),
                    'type' => $type,
                ];
                RongCloud::getInstance()->sendPrivateServiceMessage($user->userid, $user->userid, Json::encode($content));
                break;
            case static::TYPE_RELEASE_CROW:
                $userID = $user->id;
                $title = '通证到账通知';
                $description = $sourceModel->crow_name;
                $content = [
                    'content' => $description,
                    'extra' => time(),
                    'type' => $type,
                ];
                RongCloud::getInstance()->sendPrivateServiceMessage($user->userid, $user->userid, Json::encode($content));
                break;
            default:
                exit;
                break;
        }

        $model = new static();
        $model->setAttributes([
            'user_id' => $userID,
            'source_id' => $sourceModel->id,
            'type' => $type,
            'title' => $title,
            'symbol' => $symbol,
            'amount' => $amount,
            'description' => $description
        ]);

        if(false === $model->save()){
            throw new \yii\base\ErrorException(Callback::getModelError($model).'用户消息通知失败');
        }
    }
}
