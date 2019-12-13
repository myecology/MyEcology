<?php

namespace common\models\bank\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\bank\Product;

/**
 * ProductSearch represents the model behind the search form of `common\models\bank\Product`.
 */
class ProductSearch extends Product
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'income_id', 'type', 'day', 'statime', 'endtime', 'created_at', 'status'], 'integer'],
            [['name', 'symbol', 'income_description', 'fee_explain', 'description'], 'safe'],
            [['amount', 'rate', 'min_amount', 'max_amount', 'user_amount', 'fee'], 'number'],
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
        $query = Product::find();

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
            'amount' => $this->amount,
            'rate' => $this->rate,
            'min_amount' => $this->min_amount,
            'max_amount' => $this->max_amount,
            'user_amount' => $this->user_amount,
            'income_id' => $this->income_id,
            'type' => $this->type,
            'fee' => $this->fee,
            'day' => $this->day,
            'status' => $this->status,
            'statime' => $this->statime,
            'endtime' => $this->endtime,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'symbol', $this->symbol])
            ->andFilterWhere(['like', 'income_description', $this->income_description])
            ->andFilterWhere(['like', 'fee_explain', $this->fee_explain])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
