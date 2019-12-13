<?php

namespace api\models;

use common\models\GiftMoney;
use common\models\GiftMoneyTaker;
use common\models\UserAddress;
use common\models\Wallet;
use common\models\WalletLog;
use yii\base\Model;

/**
 * GiftMoneyTake form
 */
class GiftMoneyTakeForm extends Model
{
    public $id;

    private $gift_money;
    private $user;

    private $is_expired = null;
    private $is_taken = null;
    private $is_random = null;
    private $is_self = null;
    private $is_all_taken = null;

    private static $lib_operatons = [
        10 => '过期信息',
        20 => '金额',
        30 => '可领取',
        40 => '详情',
    ];
    const OP_EXPIRED = 10;
    const OP_TAKER = 20;
    const OP_ENABLED = 30;
    const OP_VIEW = 40;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id',], 'trim'],
            ['id', 'required'],
            ['id', function ($attribute, $param) {
                if (!$this->hasErrors() && false === $this->attributesTaker()) {
                    throw new \ErrorException('', 5900);
                }

                if (!$this->hasErrors() && $this->operationCode() !== static::OP_ENABLED) {
                    throw new \ErrorException('', 5301);
                }
            }],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '红包ID',
        ];
    }

    /**
     * @return bool
     * @throws \ErrorException
     * @throws \yii\db\Exception
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            //创建领取记录, 减少事务造成的抢红包重复
            //TODO 把生成红包记录放入redis队列
            $model_gift_money_taker = GiftMoneyTaker::takeGiftMoney($this->getUser()->getId(), $this->getGiftMoney());
            $amount_take = $model_gift_money_taker->amount;
            if (false === $model_gift_money_taker) {
                return false;
            }
            //更新红包记录
            if (false === $this->getGiftMoney()->updateByTaker($amount_take)) {
                throw new \ErrorException('', 5304);
            }

            //钱包对象
            $model_wallet = Wallet::findByUserId($this->getUser()->getId(), $this->getGiftMoney()->symbol);
            if (!$model_wallet) { //用户无此钱包
                $model_wallet = new Wallet();

                $model_wallet->loadAttributesFromApi($this->getGiftMoney()->symbol, $this->getUser()->getId());
                if (false === $model_wallet->save()) {
                    throw new \ErrorException('', 5305);
                }
            }

            $business_sn = $this->getGiftMoney()->id . "-" . $model_gift_money_taker->id;
            //用户余额增加 & 日志
            if (false === $model_wallet->earnMoney($amount_take, WalletLog::TYPE_GIFTMONEY_TAKE, $business_sn)) {
                throw new \ErrorException('', 5306);
            }

            $transaction->commit();
            return true;
        } catch (\ErrorException $e) {
            $transaction->rollBack();
//            GiftMoneyTaker::findOneGiftByUser($this->getUser()->getId(), $this->getGiftMoney());
//            $this->getGiftMoney()->back();
            throw new \ErrorException('', $e->getCode());
        }catch (\Exception $exception){

            $transaction->rollBack();
//            GiftMoneyTaker::findOneGiftByUser($this->getUser()->getId(), $this->getGiftMoney());
//            $this->getGiftMoney()->back();
            throw new \ErrorException('网路拥堵请稍后尝试', $exception->getCode());
        }

    }

    /**
     * @return GiftMoney|null
     * @throws \ErrorException
     */
    protected function getGiftMoney()
    {
        if (is_null($this->gift_money)) {
            $this->gift_money = GiftMoney::findById($this->id);
            if (!$this->gift_money) {
                throw new \ErrorException('', 5307);
            }
        }
        return $this->gift_money;
    }

    protected function getUser()
    {
        is_null($this->user) && $this->user = \Yii::$app->user->getIdentity();
        return $this->user;
    }

    protected function getIsExpired()
    {
        is_null($this->is_expired) && $this->is_expired = $this->getGiftMoney()->getIsExpired();
        return $this->is_expired;
    }

    /**
     * 本人是否领过
     * @return bool|null
     * @throws \ErrorException
     */
    protected function getIsTaken()
    {
        if (is_null($this->is_taken)) {
            $takers = $this->getGiftMoney()->takersReal;
            $this->is_taken = isset($takers[$this->getUser()->getId()]);
        }

        return $this->is_taken;
    }

    /**
     * 是否是拼手气红包
     * @return bool|null
     * @throws \ErrorException
     */
    protected function getIsRandom()
    {
        is_null($this->is_random) && $this->is_random = $this->getGiftMoney()->type == GiftMoney::TYPE_RANDOM;
        return $this->is_random;
    }

    protected function getIsSelf()
    {
        is_null($this->is_self) && $this->is_self = $this->getGiftMoney()->sender_id == $this->getUser()->getId();
        return $this->is_self;
    }

    /**
     * 全部领完
     * @return bool|null
     * @throws \ErrorException
     */
    protected function getIsAllTaken()
    {
        is_null($this->is_all_taken) && $this->is_all_taken = (
            $this->getGiftMoney()->status == GiftMoney::STATUS_TAKEN
            || GiftMoneyTaker::getIsTaken($this->getGiftMoney()->id)
        );
        return $this->is_all_taken;
    }

    public function attributesTaker()
    {
        return $this->getGiftMoney()->attributesForView($this->getUser()->getId());
    }

    public function operationCode()
    {
        if (
            ($this->getIsExpired() && !$this->getIsTaken() && $this->getIsRandom() && $this->getIsSelf() && !$this->getIsAllTaken())
            || ($this->getIsExpired() && !$this->getIsTaken() && $this->getIsRandom() && $this->getIsSelf() && $this->getIsAllTaken())
            || ($this->getIsExpired() && !$this->getIsTaken() && $this->getIsRandom() && !$this->getIsSelf() && !$this->getIsAllTaken())
            || ($this->getIsExpired() && !$this->getIsTaken() && $this->getIsRandom() && !$this->getIsSelf() && $this->getIsAllTaken())
            || ($this->getIsExpired() && !$this->getIsTaken() && !$this->getIsRandom() && $this->getIsSelf() && !$this->getIsAllTaken())
            || ($this->getIsExpired() && !$this->getIsTaken() && !$this->getIsRandom() && $this->getIsSelf() && $this->getIsAllTaken())
            || ($this->getIsExpired() && !$this->getIsTaken() && !$this->getIsRandom() && !$this->getIsSelf() && !$this->getIsAllTaken())
            || ($this->getIsExpired() && !$this->getIsTaken() && !$this->getIsRandom() && !$this->getIsSelf() && $this->getIsAllTaken())
        ) {
            return static::OP_EXPIRED;
        } elseif (
            ($this->getIsExpired() && $this->getIsTaken() && $this->getIsRandom() && $this->getIsSelf() && !$this->getIsAllTaken())
            || ($this->getIsExpired() && $this->getIsTaken() && $this->getIsRandom() && $this->getIsSelf() && $this->getIsAllTaken())
            || ($this->getIsExpired() && $this->getIsTaken() && $this->getIsRandom() && !$this->getIsSelf() && !$this->getIsAllTaken())
            || ($this->getIsExpired() && $this->getIsTaken() && $this->getIsRandom() && !$this->getIsSelf() && $this->getIsAllTaken())
            || ($this->getIsExpired() && $this->getIsTaken() && !$this->getIsRandom() && !$this->getIsSelf() && !$this->getIsAllTaken())
            || ($this->getIsExpired() && $this->getIsTaken() && !$this->getIsRandom() && !$this->getIsSelf() && $this->getIsAllTaken())
            || (!$this->getIsExpired() && $this->getIsTaken() && $this->getIsRandom() && $this->getIsSelf() && !$this->getIsAllTaken())
            || (!$this->getIsExpired() && $this->getIsTaken() && $this->getIsRandom() && $this->getIsSelf() && $this->getIsAllTaken())
            || (!$this->getIsExpired() && $this->getIsTaken() && $this->getIsRandom() && !$this->getIsSelf() && !$this->getIsAllTaken())
            || (!$this->getIsExpired() && $this->getIsTaken() && $this->getIsRandom() && !$this->getIsSelf() && $this->getIsAllTaken())
            || (!$this->getIsExpired() && $this->getIsTaken() && !$this->getIsRandom() && !$this->getIsSelf() && !$this->getIsAllTaken())
            || (!$this->getIsExpired() && $this->getIsTaken() && !$this->getIsRandom() && !$this->getIsSelf() && $this->getIsAllTaken())
        ) {
            return static::OP_TAKER;
        } elseif (
            (!$this->getIsExpired() && !$this->getIsTaken() && $this->getIsRandom() && $this->getIsSelf() && !$this->getIsAllTaken())
            || (!$this->getIsExpired() && !$this->getIsTaken() && $this->getIsRandom() && !$this->getIsSelf() && !$this->getIsAllTaken())
            || (!$this->getIsExpired() && !$this->getIsTaken() && !$this->getIsRandom() && !$this->getIsSelf() && !$this->getIsAllTaken())
        ) {
            return static::OP_ENABLED;
        } elseif (
            ($this->getIsExpired() && $this->getIsTaken() && !$this->getIsRandom() && $this->getIsSelf() && !$this->getIsAllTaken())
            || ($this->getIsExpired() && $this->getIsTaken() && !$this->getIsRandom() && $this->getIsSelf() && $this->getIsAllTaken())
            || (!$this->getIsExpired() && !$this->getIsTaken() && $this->getIsRandom() && $this->getIsSelf() && $this->getIsAllTaken())
            || (!$this->getIsExpired() && !$this->getIsTaken() && $this->getIsRandom() && !$this->getIsSelf() && $this->getIsAllTaken())
            || (!$this->getIsExpired() && !$this->getIsTaken() && !$this->getIsRandom() && $this->getIsSelf() && !$this->getIsAllTaken())
            || (!$this->getIsExpired() && !$this->getIsTaken() && !$this->getIsRandom() && $this->getIsSelf() && $this->getIsAllTaken())
            || (!$this->getIsExpired() && !$this->getIsTaken() && !$this->getIsRandom() && !$this->getIsSelf() && $this->getIsAllTaken())
            || (!$this->getIsExpired() && $this->getIsTaken() && !$this->getIsRandom() && $this->getIsSelf() && !$this->getIsAllTaken())
            || (!$this->getIsExpired() && $this->getIsTaken() && !$this->getIsRandom() && $this->getIsSelf() && $this->getIsAllTaken())
        ) {
            return static::OP_VIEW;
        } else {
            return false;
        }
    }
}
