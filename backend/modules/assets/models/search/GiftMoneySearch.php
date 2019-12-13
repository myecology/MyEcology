<?php

namespace backend\modules\assets\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\assets\models\GiftMoney;

/**
 * GiftMoneySearch represents the model behind the search form of `backend\modules\assets\models\GiftMoney`.
 */
class GiftMoneySearch extends GiftMoney
{
    public $sendUsername;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sender_id', 'created_at', 'status', 'type', 'count', 'expired_at'], 'integer'],
            [['amount', 'amount_left', 'amount_unit'], 'number'],
            [['description', 'bind_taker', 'symbol', 'sendUsername'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = GiftMoney::find();
        $query->joinWith(['user']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $sort = $dataProvider->getSort();
        $sort->attributes['sendUsername'] = [
            'asc' => ['user.username' => SORT_ASC],
            'desc' => ['user.username' => SORT_DESC],
        ];
        $dataProvider->setSort($sort);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'amount' => $this->amount,
            'amount_left' => $this->amount_left,
            'created_at' => $this->created_at,
            'status' => $this->status,
            'type' => $this->type,
            'amount_unit' => $this->amount_unit,
            'count' => $this->count,
            'expired_at' => $this->expired_at,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'bind_taker', $this->bind_taker])
            ->andFilterWhere(['like', 'iec_user.username', $this->sendUsername])
            ->andFilterWhere(['like', 'symbol', $this->symbol]);

        return $dataProvider;
    }
}
