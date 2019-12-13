<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\DistrbuteLog;

/**
 * DistrbuteLogSearch represents the model behind the search form of `common\models\DistrbuteLog`.
 */
class DistrbuteLogSearch extends DistrbuteLog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'release_num', 'status'], 'integer'],
            [['release_symbol', 'name', 'have_symbol', 'created_at', 'updated_at','release_amount'], 'safe'],
            [['total_amount'], 'number'],
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
        $query = DistrbuteLog::find();

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
            'total_amount' => $this->total_amount,
            'release_num' => $this->release_num,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'release_symbol', $this->release_symbol])
            ->andFilterWhere(['like', 'have_symbol', $this->have_symbol])
            ->andFilterWhere(['like', 'release_amount', $this->release_amount])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
