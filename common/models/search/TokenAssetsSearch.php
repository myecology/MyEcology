<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TokenAssets;

/**
 * TokenAssetsSearch represents the model behind the search form of `common\models\TokenAssets`.
 */
class TokenAssetsSearch extends TokenAssets
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','personnel_type','remark', 'token_type_id', 'start_time', 'end_time', 'release_cycle', 'type'], 'integer'],
            [['currency_total'], 'number'],
            [['stage_data'], 'safe'],
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
        $query = TokenAssets::find();

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
            'personnel_type' => $this->personnel_type,
            'token_type_id' => $this->token_type_id,
            'currency_total' => $this->currency_total,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'release_cycle' => $this->release_cycle,
            'type' => $this->type,
        ]);
        $query->orderBy(isset($params['sort']) ? $params['sort'] : [
            'id' => SORT_DESC,
        ]);
        $query->andFilterWhere(['like', 'stage_data', $this->stage_data]);

        return $dataProvider;
    }
}
