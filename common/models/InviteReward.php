<?php

namespace common\models;

use api\controllers\APIFormat;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "iec_invite_reward".
 *
 * @property int $id
 * @property int $invitation_id 邀请记录ID
 * @property int $user_id_rewarded 收益人ID
 * @property int $registerer_id 注册人
 * @property int $level 层级
 * @property int $currency_id 币种ID
 * @property string $symbol 币种标识
 * @property string $amount 奖励金额
 * @property string $registerer_reward 注册人奖励
 * @property int $created_at 创建时间
 */
class InviteReward extends \yii\db\ActiveRecord
{
    public $amount_total;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_invite_reward';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['invitation_id', 'user_id_rewarded', 'registerer_id', 'level', 'currency_id', 'created_at'], 'integer'],
            [['currency_id', 'symbol'], 'required'],
            [['amount', 'registerer_reward'], 'number'],
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
            'invitation_id' => 'Invitation ID',
            'user_id_rewarded' => 'user Id Rewarded',
            'registerer_id' => 'Registerer Id',
            'level' => 'Level',
            'currency_id' => 'Currency ID',
            'symbol' => 'Symbol',
            'amount' => 'Amount',
            'registerer_reward' => 'Registerer Reward',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @param $user_id_rewarded
     * @param $registerer_id
     * @param $invitation_id
     * @param $level
     * @param $currency_id
     * @param $symbol
     * @param $amount
     * @param $registerer_reward
     */
    public function loadFromInvitePool(
        $user_id_rewarded,
        $registerer_id,
        $invitation_id,
        $level,
        $currency_id,
        $symbol,
        $amount,
        $registerer_reward
    )
    {
        $this->setAttributes([
            'invitation_id' => $invitation_id,
            'registerer_id' => $registerer_id,
            'user_id_rewarded' => $user_id_rewarded,
            'level' => $level,
            'currency_id' => $currency_id,
            'symbol' => $symbol,
            'amount' => $amount,
            'created_at' => time(),
            'registerer_reward' => $registerer_reward,
        ]);
    }

    public function search($params, $user_id)
    {
        $query = static::find()
            ->with(['registerer'])
            ->andWhere(['user_id_rewarded' => $user_id])
            ->orderBy(['created_at' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'page' => Yii::$app->request->post('page') - 1,
            ]
        ]);

        $this->load($params, '');

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }

    public function getRegisterer()
    {
        return $this->hasOne(\api\models\User::className(), ['id' => 'registerer_id']);
    }

    /**
     * @return array
     */
    public function attributeForReward()
    {
        $amount = $this->amount;

        return [
            'userid' => $this->registerer->userid,
            'nickname' => (string)$this->registerer->nicknameText,
            'username' => (string)$this->registerer->usernameText,
            'amount' => APIFormat::asMoney($amount),
            'symbol' => $this->symbol,
            'value' => APIFormat::asMoney(CurrencyPrice::convert($amount, $this->symbol)),
            'created_at' => (string)$this->created_at,
            'level' => (string)Invitation::levelText($this->level),
        ];
    }


    /**
     * @param $viewer_id
     * @return array
     */
    public function summaryAsInviter($viewer_id)
    {
        $stastastic_reward = $this->totalRewarded($viewer_id);
        $value_total = 0;
        if($stastastic_reward){
            foreach($stastastic_reward as $i => $model_reward){
                $value_total += CurrencyPrice::convert($model_reward->amount_total, $model_reward->symbol);
            }
        }

        return [
            'amount' => $value_total,
            'symbol' => 'IEC',
        ];
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function totalRewarded($user_id)
    {
        return Yii::$app->cache->getOrSet('reward-total-@' . $user_id, function () use ($user_id) {
            return static::find()
                ->select('symbol, sum(`amount`) as amount_total')
                ->filterWhere([
                    'user_id_rewarded' => $user_id
                ])
                ->groupBy('symbol')
                ->all();
        }, 60);
    }

    /**
     * 获取消息通知的备注
     *
     * @return void
     */
    public function messageDescription()
    {
        $user = \api\models\User::findOne($this->registerer_id);
        return '邀请' . $user->nickname . '注册奖励';
    }
}
