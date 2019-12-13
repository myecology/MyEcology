<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\shop\WineActivation;

/**
 * WineActivationSearch represents the model behind the search form of `common\models\shop\WineActivation`.
 */
class WineActivationSearch extends WineActivation
{
    public $username;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'status', 'type', 'count', 'grant_count'], 'integer'],
            [['activation_code', 'updated_at', 'created_at', 'end_time', 'symbol', 'username'], 'safe'],
            [['price', 'every_amount', 'every_price', 'symbol_price'], 'number'],
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
        $query = WineActivation::find();
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
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'status' => $this->status,
            'type' => $this->type,
            'end_time' => $this->end_time,
            'count' => $this->count,
            'grant_count' => $this->grant_count,
            'price' => $this->price,
            'every_amount' => $this->every_amount,
            'every_price' => $this->every_price,
            'symbol_price' => $this->symbol_price,
        ]);

        $query->andFilterWhere(['like', 'activation_code', $this->activation_code])
            ->andFilterWhere(['like', 'symbol', $this->symbol]);
        $query->andFilterWhere(['like', 'iec_user.username',$this->username]);

        return $dataProvider;
    }
}
