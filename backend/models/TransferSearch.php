<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Transfer;
use api\models\User;

/**
 * TransferSearch represents the model behind the search form of `backend\models\Transfer`.
 */
class TransferSearch extends Transfer
{
    public $username;
    public $rusername;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sender_id', 'receiver_id', 'currency_id', 'created_at', 'taken_at', 'status'], 'integer'],
            [['symbol', 'description', 'username', 'rusername'], 'safe'],
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
        $query = Transfer::find();
        $query->joinWith(['user as a', 'receiver as b']);

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
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'currency_id' => $this->currency_id,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'taken_at' => $this->taken_at,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'symbol', $this->symbol])
            ->andFilterWhere(['like', 'description', $this->description]);
        $query->andFilterWhere(['like', 'a.username',$this->username]);
        $query->andFilterWhere(['like', 'b.username',$this->rusername]);

        return $dataProvider;
    }
}
