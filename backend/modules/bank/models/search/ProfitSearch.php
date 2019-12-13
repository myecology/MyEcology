<?php

namespace backend\modules\bank\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\bank\Profit;

/**
 * ProfitSearch represents the model behind the search form of `common\models\bank\Profit`.
 */
class ProfitSearch extends Profit
{
    public $username;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id','type'], 'integer'],
            [['amount'], 'number'],
            [['symbol','username'], 'safe'],
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
        $query = Profit::find()->joinWith('user');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'order_id' => $this->order_id,
            'amount' => $this->amount,
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'symbol', $this->symbol]);
        $query->andFilterWhere(['like', 'iec_user.username', $this->username]);

        return $dataProvider;
    }
}
