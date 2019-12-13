<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\InvitePool;

/**
 * InvitePoolSearch represents the model behind the search form of `backend\models\InvitePool`.
 */
class InvitePoolSearch extends InvitePool
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'currency_id', 'created_at', 'expired_at', 'status', 'prize_registerer', 'prize_inviter', 'prize_grand_inviter'], 'integer'],
            [['symbol', 'name', 'icon', 'background'], 'safe'],
            [['amount', 'amount_left', 'prize', 'type'], 'number'],
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
        $query = InvitePool::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'currency_id' => $this->currency_id,
            'amount' => $this->amount,
            'amount_left' => $this->amount_left,
            'created_at' => $this->created_at,
            'expired_at' => $this->expired_at,
            'status' => $this->status,
            'prize' => $this->prize,
            'type' => $this->type,
            'prize_registerer' => $this->prize_registerer,
            'prize_inviter' => $this->prize_inviter,
            'prize_grand_inviter' => $this->prize_grand_inviter,
        ]);

        $query->andFilterWhere(['like', 'symbol', $this->symbol]);
        $query->andFilterWhere(['like', 'name', $this->symbol]);
        $query->andFilterWhere(['like', 'icon', $this->symbol]);
        $query->andFilterWhere(['like', 'background', $this->symbol]);

        return $dataProvider;
    }
}
