<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Crowdfunding;

/**
 * CrowdfundingSearch represents the model behind the search form of `common\models\Crowdfunding`.
 */
class CrowdfundingSearch extends Crowdfunding
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'release_type', 'release_cycle', 'exchange_num', 'min_buy', 'exchange_total'], 'integer'],
            [['name', 'income_img', 'start_time', 'end_time', 'release_start_at', 'release_end_at', 'mall_symbol', 'mall_proportion', 'exchange_symbol', 'created_at', 'update_at'], 'safe'],
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
        $query = Crowdfunding::find();

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
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'status' => $this->status,
            'release_type' => $this->release_type,
            'release_start_at' => $this->release_start_at,
            'release_end_at' => $this->release_end_at,
            'release_cycle' => $this->release_cycle,
            'exchange_num' => $this->exchange_num,
            'created_at' => $this->created_at,
            'update_at' => $this->update_at,
            'min_buy' => $this->min_buy,
            'exchange_total' => $this->exchange_total,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'income_img', $this->income_img])
            ->andFilterWhere(['like', 'mall_symbol', $this->mall_symbol])
            ->andFilterWhere(['like', 'mall_proportion', $this->mall_proportion])
            ->andFilterWhere(['like', 'exchange_symbol', $this->exchange_symbol]);

        return $dataProvider;
    }
}
