<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ExchangeWt;
use api\models\User;

/**
 * ExchangeWtSearch represents the model behind the search form of `common\models\ExchangeWt`.
 */
class ExchangeWtSearch extends ExchangeWt
{
    public $username;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id'], 'integer'],
            [['e_symbol', 'create_time', 'username'], 'safe'],
            [['amount', 'wt_number', 'symbol_price', 'fee'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = ExchangeWt::find();
        $query->JoinWith(['user']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
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
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'wt_number' => $this->wt_number,
            'create_time' => $this->create_time,
            'symbol_price' => $this->symbol_price,
            'fee' => $this->fee,
        ]);

        $query->andFilterWhere(['like', 'e_symbol', $this->e_symbol]);
        $query->andFilterWhere(['like', 'iec_user.username',$this->username]);

        return $dataProvider;
    }
}
