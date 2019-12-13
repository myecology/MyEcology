<?php

namespace backend\models\node;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\node\NodeWt;

/**
 * NodeWtSearch represents the model behind the search form of `common\models\node\NodeWt`.
 */
class NodeWtSearch extends NodeWt
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'number', 'total_num'], 'integer'],
            [['name', 'symbol', 'node_image', 'node_symbol', 'node_rules', 'created_at'], 'safe'],
            [['amount'], 'number'],
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
        $query = NodeWt::find();

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
            'number' => $this->number,
            'total_num' => $this->total_num,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'symbol', $this->symbol])
            ->andFilterWhere(['like', 'node_image', $this->node_image])
            ->andFilterWhere(['like', 'node_symbol', $this->node_symbol])
            ->andFilterWhere(['like', 'node_rules', $this->node_rules]);

        return $dataProvider;
    }
}
