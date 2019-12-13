<?php

namespace common\models\bank\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\bank\Order;

/**
 * OrderSearch represents the model behind the search form of `common\models\bank\Order`.
 */
class OrderSearch extends Order
{

    public $username;
    public $productName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'uid', 'product_id', 'status', 'day', 'endtime', 'created_at'], 'integer'],
            [['rate', 'amount'], 'number'],
            [['username', 'productName'], 'safe']
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
        $query = Order::find()->orderBy('id desc');
        $query->joinWith(['user', 'product']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['username'] = [
            'asc' => ['user.username' => SORT_ASC],
            'desc' => ['user.username' => SORT_DESC],
        ];


        $dataProvider->sort->attributes['productName'] = [
            'asc' => ['product.name' => SORT_ASC],
            'desc' => ['product.name' => SORT_DESC]
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'iec_bank_order.id' => $this->id,
            'uid' => $this->uid,
            'product_id' => $this->product_id,
            'iec_bank_order.rate' => $this->rate,
            'iec_bank_order.amount' => $this->amount,
            'iec_bank_order.status' => $this->status,
            'iec_bank_order.day' => $this->day,
            'endtime' => $this->endtime,
            'iec_bank_order.created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'iec_user.username', $this->username]);
        $query->andFilterWhere(['like', 'iec_bank_product.name', $this->productName]);

        return $dataProvider;
    }
}
