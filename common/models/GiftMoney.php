<?php

namespace common\models;

use api\controllers\APIFormat;
use api\models\Group;
use api\models\UserFriend;
use backend\models\Setting;
use Yii;

/**
 * This is the model class for table "iec_gift_money".
 *
 * @property int $id
 * @property int $sender_id 发红包用户ID
 * @property string $amount 金额
 * @property string $amount_left 剩余金额
 * @property int $created_at 创建时间
 * @property int $expired_at 过期时间
 * @property int $status 状态
 * @property int $type 类型
 * @property int $flag 类型
 * @property string $amount_unit 单个金额
 * @property int $count 红包个数
 * @property string $description 祝福语
 * @property string $bind_taker 红包接收对象
 * @property string $symbol 币种标识
 */
class GiftMoney extends \yii\db\ActiveRecord
{
    public static $lib_type = [
        10 => '个人红包',
        20 => '普通红包',               //群等额
        30 => '拼手气红包',             //群随机红包
    ];
    public static $lib_type_scenario = [
        10 => 'single',
        20 => 'average',
        30 => 'random',
    ];

    const TYPE_SINGLE = 10;
    const TYPE_AVERAGE = 20;
    const TYPE_RANDOM = 30;

    public static $lib_status = [
        10 => '开放',
        20 => '过期',
        30 => '已领完',
    ];
    const STATUS_OPEN = 10;
    const STATUS_CLOSED = 20;
    const STATUS_TAKEN = 30;

    const EXPIRED_DURATION = 86400;

    /**
     * 乐观锁
     * @return null|string
     */
    public function optimisticLock()
    {
        return 'flag';
    }


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_gift_money';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sender_id', 'created_at', 'expired_at', 'status', 'type', 'count'], 'integer'],
            [['amount', 'amount_left', 'amount_unit'], 'number'],
            [['description', 'bind_taker',], 'string', 'max' => 255],
            [['symbol',], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sender_id' => 'Sender ID',
            'amount' => 'Amount',
            'amount_left' => 'Amount Left',
            'created_at' => 'Created At',
            'expired_at' => 'Expired At',
            'status' => 'Status',
            'type' => 'Type',
            'amount_unit' => 'Amount Unit',
            'count' => 'Count',
            'bind_taker' => 'Bind Taker',
            'description' => 'Description',
            'symbol' => 'Symbol',
        ];
    }

    /**
     * * @param int $sender_id
     * @param float $amount
     * @param string $symbol
     * @param int $type
     * @param float $amount_unit
     * @param int $count
     * @param string $description
     * @param string $taker
     * @return bool|GiftMoney
     */
    public static function createFromAPP(
        $sender_id,
        $amount,
        $symbol,
        $type,
        $amount_unit,
        $count,
        $description,
        $taker
    )
    {
        $now_timestamp = time();

        $model = new GiftMoney();
        $model->setAttributes([
            'sender_id' => $sender_id,
            'amount' => $amount,
            'amount_left' => $amount,
            'symbol' => $symbol,
            'created_at' => $now_timestamp,
            'expired_at' => static::getExpiredDuration() ? static::getExpiredDuration() + $now_timestamp : 0,
            'status' => static::STATUS_OPEN,
            'type' => $type,
            'amount_unit' => $amount_unit,
            'count' => $count,
            'description' => $description,
            'bind_taker' => $taker,
        ]);

        return $model->save() ? $model : false;
    }

    public static function getExpiredDuration()
    {
        return static::EXPIRED_DURATION;
    }

    public function getTypeText()
    {
        return static::$lib_type[$this->type];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTakers()
    {
        return $this->hasMany(GiftMoneyTaker::className(), ['gift_money_id' => 'id'])
            ->indexBy('taker_id')
            ->limit(100);
    }

    public function getTakersReal()
    {
        return $this->getTakers()
            ->andWhere('taker_id > 0')
            ->orderBy('taken_at DESC, id ASC');
    }

    public function getAmountLeftReal()
    {
        return $this->hasMany(GiftMoneyTaker::className(), ['gift_money_id' => 'id'])
            ->where('taker_id=0 OR taker_id is null')
            ->sum('amount');
    }

    /**
     * @return null|\api\models\User
     */
    public function getSender()
    {
        return $this->hasOne(\api\models\User::className(), ['id' => 'sender_id']);
    }

    /**
     * @param $id
     * @return null|GiftMoney
     */
    public static function findById($id)
    {
        return static::find()
            ->where(['id' => $id])
            ->with(['takers'])
            ->limit(1)
            ->one();
    }

    public function getCurrency()
    {
        return $this->hasOne(Currency::className(), ['symbol' => 'symbol']);
    }

    /**
     * @return Group|\api\models\User
     */
    public function getBindTaker()
    {
        return $this->type == static::TYPE_SINGLE ? $this->hasOne(\api\models\User::className(), ['userid' => 'bind_taker']) :
            $this->hasOne(Group::className(), ['groupid' => 'bind_taker']);
    }

    public function attributesForView($viewer_id)
    {
        if (!$this->checkViewAccess($viewer_id)) { //检查是否有查看红包详情权限
            return false;
        }

        $taker_ids = array_keys($this->takers);
        /**
         * @var null|GiftMoneyTaker $viwer_as_taker
         */
        $viwer_as_taker = in_array($viewer_id, $taker_ids) ? $this->takers[$viewer_id] : null;
        $user_viewer = $viwer_as_taker ? $viwer_as_taker->user : $this->sender;

        $sender_username = UserFriend::getRemarkByUserid($user_viewer->userid, $this->sender->userid);
        $sender_username || $sender_username = $this->sender->nickname;

        $result = [
            'headimgurl' => $user_viewer->headimgurl,
            'nickname' => $user_viewer->nickname,
            'description' => $this->description,
            'amount' => APIFormat::asMoney($this->amount),
            'amount_left' => APIFormat::asMoney($this->amountLeftReal),
            'count' => strval($this->count),
            'type' => strval($this->type),
            'icon' => strval($this->currency->iconAbs),
            'symbol' => strval($this->symbol),
            'status' => strval($this->status),
            'statusText' => strval(static::$lib_status[$this->status]),
            'type_text' => strval($this->typeText),
            'taker_amount' => APIFormat::asMoney($viwer_as_taker ? $viwer_as_taker->amount : 0),
            'taker_symbol' => $this->symbol,
            'taker_value' => APIFormat::asMoney($viwer_as_taker ? CurrencyPrice::convert($viwer_as_taker->amount, $this->symbol) : 0),

            'sender_username' => (string)$sender_username,
            'sender_headimgurl' => (string)$this->sender->headimgurl,
            'sender_userid' => (string)$this->sender->userid,
        ];

        return $result;
    }

    public function takersAttributesForView($viewer_uid, $takers_data)
    {
        $result = [];
        if ($takers_data) {
            /**
             * @var GiftMoneyTaker $taker
             */
            foreach ($takers_data as $i => $taker) {
                $item = $taker->attributesForView();

                if ($viewer_uid) { // adding remark nickname for takers' nickname display
                    $user_friend = UserFriend::getRemarkByUserid($viewer_uid, $taker->user->userid);
                    $user_friend && $item['nickname'] = $user_friend;
                }

                $result[] = $item;
            }
        }

        return $result;
    }

    public function checkViewAccess($viewer_id)
    {
        $flag = false;

        if ($viewer_id == $this->sender_id) {
            $flag = true;
        }

        switch ($this->type) {
            case static::TYPE_SINGLE:
                $this->bindTaker->id == $viewer_id && $flag = true;
                break;
            case static::TYPE_AVERAGE:
            case static::TYPE_RANDOM:
                $model_group = $this->bindTaker;
                foreach ($model_group->groupUsers as $i => $group_user) {
                    if ($group_user->userLight->id == $viewer_id) {
                        $flag = true;
                        break;
                    }
                }
        }

        return $flag;
    }

    /**
     * 计算单次领取红包金额
     * @return bool
     */
    public function calcGiftMoneyAmount()
    {
        $amount = 0;

        switch ($this->type) {
            case static::TYPE_SINGLE:
                $amount = $this->amount;
                break;
            case static::TYPE_AVERAGE:
                $amount = $this->amount_unit;
                break;
            case static::TYPE_RANDOM:
                $count_taken = count($this->takers);
                if ($count_taken == $this->count) {
                    $amount = 0; //taken completely
                } elseif ($this->count == $count_taken + 1) {
                    $amount = $this->amount_left; //only 1 left
                } elseif ($this->count > $count_taken + 1) {
                    $decimal = Setting::read('gift_money_decimal');
                    $decimal_times = pow(10, $decimal);

                    $count_current_taken = $this->count - $count_taken;
                    $amount = mt_rand(1, $this->amount_left * $decimal_times - $count_current_taken + 1) / $decimal_times;
                }
                break;
        }

        return $amount ?: false;
    }

    /**
     *
     * @param $amount_take
     * @return bool
     */
    public function updateByTaker($amount_take)
    {
//        if ($amount_take < 0 || $this->amount_left - $amount_take < 0) {
//            return false;
//        }

        $attributes_updated = [
            'amount_left' => $this->amount_left - $amount_take,
        ];
        0 == $this->amountLeftReal && $attributes_updated['status'] = static::STATUS_TAKEN;

        $this->setAttributes($attributes_updated);
        return $this->updateInternal();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function queryExpiredToReturn()
    {
        return static::find()
            ->where('expired_at < ' . time())->andWhere([
                'status' => static::STATUS_OPEN,
            ])->orderBy([
                'id' => SORT_ASC,
            ]);
    }

    public function setExpired()
    {
        $this->status = static::STATUS_CLOSED;
        $this->amount_left = 0;
    }

    public function setTaken()
    {
        $this->status = static::STATUS_TAKEN;
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function actionReturn()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {

            //  发送通知 - 邀请收益
            $user = \api\models\User::findOne($this->sender_id);
            \common\models\Message::addMessage(\common\models\Message::TYPE_HONGBAO_BACK, $user, $this->symbol, $this->amount_left, $this);

            $this->setExpired();
            if (!$this->save()) {
                throw new \ErrorException('', 10000);
            }

            GiftMoneyTaker::releaseTakerUntaken($this->id);

            if ($this->amountLeftReal > 0) {
                $model_wallet = Wallet::findByUserId($this->sender_id, $this->symbol);
                if (!$model_wallet->earnMoney($this->amountLeftReal, WalletLog::TYPE_GIFTMONEY_RETURN,(string)$this->id)) {
                    throw new \ErrorException('', 10001);
                }
            }

            $transaction->commit();
            $result = true;
        } catch (\ErrorException $e) {
            $transaction->rollBack();

            $this->addError('id', $e->getCode());
            $result = false;
        }catch (\Exception $exception){
            $transaction->rollBack();

            $this->addError('id', $exception->getCode());
            $result = false;
        }

        return $result;
    }

    public function getIsExpired()
    {
        return $this->expired_at < time() || $this->status == static::STATUS_CLOSED;
    }

    /**
     * @param $user_id
     * @param $symbol
     * @return mixed
     */
    public static function amountTotalToday($user_id, $symbol)
    {
        return static::find()
            ->where([
                'sender_id' => $user_id,
                'symbol' => $symbol,
            ])->andWhere(['in', 'status', [static::STATUS_TAKEN, static::STATUS_OPEN]])
            ->andWhere('created_at > ' . strtotime('today'))
            ->sum('amount');
    }

    public function back(){
        $this->status = static::STATUS_OPEN;
        $this->save();
    }


    /**
     * 补发
     * @return bool
     * @throws \yii\db\Exception
     */
    public function actionBack($amount)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {

            //  发送通知 - 邀请收益
            $user = \api\models\User::findOne($this->sender_id);
            \common\models\Message::addMessage(\common\models\Message::TYPE_HONGBAO_BACK, $user, $this->symbol, $this->amount_left, $this);

           /* $this->setExpired();
            if (!$this->save()) {
                throw new \ErrorException('', 10000);
            }*/

//            GiftMoneyTaker::releaseTakerUntaken($this->id);

            if ($amount > 0) {
                $model_wallet = Wallet::findOneByWallet($this->symbol,$this->sender_id);
                if (!$model_wallet->earnMoney($amount, WalletLog::TYPE_GIFTMONEY_RETURN,(string)$this->id)) {
                    throw new \ErrorException('', 10001);
                }
            }

            $transaction->commit();
            $result = true;
        } catch (\ErrorException $e) {
            $transaction->rollBack();
            var_dump($e->getMessage());
            $this->addError('id', $e->getCode());
            $result = false;
        }catch (\Exception $exception){
            $transaction->rollBack();
            var_dump($exception->getMessage());
            $this->addError('id', $exception->getCode());
            $result = false;
        }

        return $result;
    }
}
