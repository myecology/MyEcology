<?php

namespace backend\models;

use Yii;
use common\models\InvitePoolLog;
use common\models\Currency;
use yii\behaviors\TimestampBehavior;


/**
 * This is the model class for table "iec_invite_pool".
 *
 * @property int $id
 * @property int $currency_id 币种ID
 * @property string $symbol 币种标识
 * @property string $amount 奖金池金额
 * @property string $amount_left 奖金池剩余
 * @property int $created_at 创建时间
 * @property int $expired_at 过期时间
 * @property int $status 状态
 * @property string $prize 奖金包金额
 * @property int $prize_registerer 注册人比重
 * @property int $prize_inviter 邀请人比重
 * @property int $prize_grand_inviter 父级邀请人比重
 */
class InvitePool extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_invite_pool';
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
            [['currency_id', 'prize_registerer', 'prize_inviter', 'prize_grand_inviter', 'prize_grand_grand_inviter', 'prize', 'amount'], 'required'],
            ['currency_id', 'filter', 'filter' => function($value){
                $currency = Currency::findCurrencyById($this->currency_id);
                $this->symbol = $currency->symbol;

                return $this->currency_id;
            }],
            ['amount', function($attribute, $params){
                if(!$this->hasErrors() && !$this->isNewRecord){
                    $amount = $this->amount - $this->oldAttributes['amount'];
                    $left = $amount + $this->amount_left;
                    if($left < 0){
                        $this->addError($attribute, '剩余量不足');
                    }
                }
            }],
            ['amount', 'filter', 'filter' => function($value){
                if($this->isNewRecord){
                    $this->amount_left = $this->amount;
                }else{echo 10000;
                    $this->amount_left = $this->amount - $this->oldAttributes['amount'] + $this->amount_left;
                }
                return $this->amount;
            }],

            [['amount'], 'number'],
            [['type', 'uid'], 'integer'],
            [['icon', 'background', 'name', 'description', 'url'], 'string', 'max' => 255],
            ['background', 'default', 'value' => Yii::$app->params['imagesUrl'] . '/images/default/candy_background.png'],
            ['expired_at', 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '用户ID',
            'name' => '项目昵称',
            'type' => '项目类型',
            'icon' => '图标',
            'background' => '海报背景图',
            'url' => '白皮书地址',
            'description' => '糖果描述',
            'currency_id' => '币种',
            'symbol' => '币种',
            'amount' => '总数',
            'amount_left' => '剩余数量',
            'created_at' => '创建时间',
            'expired_at' => '过期时间',
            'status' => '状态',
            'prize' => '奖励包',
            'prize_registerer' => '注册奖励',
            'prize_inviter' => '邀请奖励',
            'prize_grand_inviter' => '二级奖励',
            'prize_grand_grand_inviter' => '三级奖励'
        ];
    }

    /**
     * 添加糖果
     *
     * @return void
     */
    public function addPool()
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if(false === $this->save()){
                throw new \yii\base\ErrorException('添加失败');
            }

            // $poolLog = new InvitePoolLog();
            // $poolLog->setAttributes([
            //     'uid' => $this->uid,
            //     'pool_id' => $this->id,
            //     'type' => InvitePoolLog::TYPE_AMOUNT_ADD,
            //     'symbol' => $this->symbol,
            //     'amount' => $this->amount,
            // ]);

            // if(false === $poolLog->save()){
            //     throw new \yii\base\ErrorException('添加日志失败');
            // }

            // //  如果是项目方那么锁仓糖果
            // if($this->uid){

            //     $user = \backend\models\User::findOne($this->uid);
            //     $user->pool_id = $this->id;
            //     if(false === $user->save(false)){
            //         throw new \yii\base\ErrorException('修改用户失败');
            //     }

            //     if(!$poolLog->addLog(InvitePoolLog::TYPE_AMOUNT_ADD)){
            //         throw new \yii\base\ErrorException('锁仓糖果失败');
            //     }
            // }

            if($this->uid){
                $user = \backend\models\User::findOne($this->uid);
                $user->pool_id = $user->id;
                if(false === $user->save(false)){
                    throw new \yii\base\ErrorException('修改用户失败');
                }
            }

            $transaction->commit();
            return true;
        } catch (\Throwable $th) {
            $transaction->rollBack();
            var_dump($th->getMessage());die;
        }
        return false;
    }


    /**
     * 更新糖果
     *
     * @return void
     */
    public function updatePool()
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if($this->expired_at){
                $this->expired_at = strtotime($this->expired_at);
            }
            
            if(false === $this->save()){
                throw new \yii\base\ErrorException('更新失败');
            }

            // if($this->amount != $this->oldAttributes['amount']){

            //     $poolLog = new InvitePoolLog();
            //     if($this->amount > $this->oldAttributes['amount']){
            //         $type = InvitePoolLog::TYPE_AMOUNT_ADD;
            //         $amount = $this->amount - $this->oldAttributes['amount'];
            //     }else{
            //         $type = InvitePoolLog::TYPE_AMOUNT_SUB;
            //         $amount = $this->oldAttributes['amount'] - $this->amount;
            //     }
            //     $poolLog->setAttribute = [
            //         'uid' => $this->uid,
            //         'pool_id' => $this->id,
            //         'type' => $type,
            //         'symbol' => $this->symbol,
            //         'amount' => $amount,
            //     ];
            //     if(false === $poolLog->save()){
            //         throw new \yii\base\ErrorException('添加日志失败');
            //     }

            //     //  如果是项目方那么锁仓糖果
            //     if($this->uid){
            //         if(!$poolLog->addLog($type)){
            //             throw new \yii\base\ErrorException('锁仓糖果失败');
            //         }
            //     }
            // }

            $transaction->commit();
            return true;
        } catch (\Throwable $th) {
            $transaction->rollBack();
            var_dump($th->getMessage());die;
        }
        return false;
    }


    public static function statusArray()
    {
        return [
            '0' => '禁用',
            '10' => '启用'
        ];
    }

    public static function typeArray()
    {
        return [
            0 => '项目方糖果',
            1 => '官方糖果',
        ];
    }
}
