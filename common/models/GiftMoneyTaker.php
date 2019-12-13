<?php

namespace common\models;

use api\controllers\APIFormat;
use backend\models\Setting;
use common\helpers\RedPack;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "iec_gift_money_taker".
 *
 * @property int $id
 * @property int|null $taker_id 领红包用户ID, null待分配，0已释放，int已分配
 * @property int $created_at 创建时间
 * @property int $taken_at 领取时间
 * @property string $amount 领取金额
 * @property int $gift_money_id 红包ID
 * @property int $flag 锁
 * @property string $reply 领取者回复
 * @property int $reply_time 回复时间
 * @property string $symbol 币种标识
 */
class GiftMoneyTaker extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_gift_money_taker';
    }

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
    public function rules()
    {
        return [
            [['taker_id', 'created_at', 'reply_time', 'taken_at', 'gift_money_id'], 'integer'],
            [['amount'], 'number'],
            [['reply'], 'string', 'max' => 255],
            [['symbol'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'taker_id' => 'Taker ID',
            'created_at' => 'Created At',
            'taken_at' => 'Taken At',
            'amount' => 'Amount',
            'gift_money_id' => 'Gift Money Id',
            'reply' => 'Reply',
            'reply_time' => 'Reply Time',
            'symbol' => 'Symbol',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(\api\models\User::className(), ['id' => 'taker_id']);
    }

    /**
     * @param null $to_uid
     * @return array
     */
    public function attributesForView()
    {
        return [
            'amount' => APIFormat::asMoney($this->amount),
            'value' => APIFormat::asMoney(CurrencyPrice::convert($this->amount, $this->symbol)),
            'reply' => (string)$this->reply,
            'take_time' => (string)$this->taken_at ?: $this->created_at,
            'nickname' => (string)$this->user->nickname,
            'headimgurl' => (string)$this->user->headimgurl,
            'userid' => (string)$this->user->userid,
        ];
    }

    /**
     * @param $user_id
     * @param GiftMoney $model_gift_money
     * @return bool|null|GiftMoneyTaker
     */
    public static function takeGiftMoney($user_id, GiftMoney $model_gift_money)
    {
        $model_taker = static::find()->where([
            'gift_money_id' => $model_gift_money->id,
        ])->andWhere('taker_id is null')
            ->orderBy('taken_at DESC, id ASC')
            ->one();
        if (!$model_taker) {
            return false;
        }

        $model_taker->setAttributes([
            'taker_id' => $user_id,
            'taken_at' => time(),
        ]);

        return $model_taker->updateInternal() ? $model_taker : false;
    }
    
    public static function findOneGiftByUser($user_id, GiftMoney $model_gift_money){
        $giftMoneyTakerModel = static::findOne([
            'gift_money_id' => $model_gift_money->id,
            'taker_id' => $user_id,
        ]);
        return $giftMoneyTakerModel->errorBack();
    }
    /***
     * 错误回滚
     */
    public function errorBack(){
        $this->setAttributes([
            'taker_id' => null,
            'taken_at' => null,
        ]);
        return $this->save()?true:false;
    }
    public static function generateTakerAmount(GiftMoney $giftMoney, $decimal = 8)
    {
        $result = [];
        switch ($giftMoney->type) {
            case GiftMoney::TYPE_SINGLE:
                $result = [$giftMoney->amount];
                break;
            case GiftMoney::TYPE_AVERAGE:
                $amount_each = $giftMoney->amount_unit;
                $i = 0;
                while ($i++ < $giftMoney->count) {
                    $result[] = $amount_each;
                }
                break;
            case GiftMoney::TYPE_RANDOM:
                $helper = new RedPack($giftMoney->amount, $giftMoney->count, $decimal);
                $result = $helper->handle();

                break;
        }

        if ($result) {
            foreach ($result as $i => $taker_amount) {
                $result[$i] = static::attachAttributes($taker_amount, $giftMoney);
            }
        }

        return $result;
    }

    public static function attachAttributes($amount, GiftMoney $giftMoney)
    {
        return [
            'created_at' => time(),
            'amount' => $amount,
            'gift_money_id' => $giftMoney->id,
            'symbol' => $giftMoney->symbol,

//            'taker_id' => 'Taker ID',
//            'reply' => 'Reply',
//            'reply_time' => 'Reply Time',
        ];
    }

    /**
     * @param GiftMoney $giftMoney
     * @return int
     * @throws \yii\db\Exception
     */
    public static function generateTakers(GiftMoney $giftMoney)
    {
        $gift_money_decimal = Setting::read('gift_money_decimal');
        $data_takers = static::generateTakerAmount($giftMoney, $gift_money_decimal);

        return \Yii::$app->db->createCommand()
            ->batchInsert(GiftMoneyTaker::tableName(), [
                'created_at',
                'amount',
                'gift_money_id',
                'symbol',
            ], $data_takers)->execute();
    }

    /**
     * @param $gift_money_id
     * @return bool
     */
    public static function getIsTaken($gift_money_id)
    {
        return !static::find()
            ->where(['gift_money_id' => $gift_money_id])
            ->andWhere('taker_id is null')
            ->exists();
    }

    public function search($params, $gift_money_id)
    {
        $query = static::find()
            ->andWhere(['gift_money_id' => $gift_money_id])
            ->andWhere('taker_id > 0')
            ->orderBy([
                'taken_at' => SORT_ASC,
                'id' => SORT_ASC
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'page' => Yii::$app->request->post('page') - 1,
                'pageSize' => 100,
            ]
        ]);

        $this->load($params, '');

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }

    /**
     * @param $gift_money_id
     * @return int
     */
    public static function releaseTakerUntaken($gift_money_id)
    {
        $model = new GiftMoneyTaker();
        return $model->updateAll(['taker_id' => 0], "gift_money_id={$gift_money_id} AND taker_id is null");
    }

}
