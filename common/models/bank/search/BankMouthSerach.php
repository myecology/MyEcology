<?php

namespace common\models\bank\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\bank\BankMouth;

/**
 * BankMouthSerach represents the model behind the search form of `common\models\bank\BankMouth`.
 */
class BankMouthSerach extends BankMouth
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'month', 'created_at'], 'integer'],
            [['amount'], 'number'],
            [['symbol'], 'safe'],
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
        $query = BankMouth::find();

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
            'month' => $this->month,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'symbol', $this->symbol]);

        return $dataProvider;
    }
}
