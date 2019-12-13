<?php

namespace backend\modules\activation\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\activation\ActivationParam;

/**
 * ActivationParamSearch represents the model behind the search form of `common\models\activation\ActivationParam`.
 */
class ActivationParamSearch extends ActivationParam
{
    public $name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type'],'integer'],
            [['key', 'value','group','name','remark'], 'safe'],
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
        $query = ActivationParam::find()->JoinWith('activation');

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
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'key', $this->key])
            ->andFilterWhere(['like', 'value', $this->value])
            ->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'iec_activation.name', $this->name])
            ->andFilterWhere(['like', 'group', $this->group]);

        return $dataProvider;
    }
}
