<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Deposit;

/**
 * DepositSearch represents the model behind the search form of `common\models\Deposit`.
 */
class DepositSearch extends Deposit
{
    public $username;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'status'], 'integer'],
            [['address_id'], 'safe'],
            [['symbol', 'source', 'txid', 'address', 'fee_symbol', 'remark', 'transaction_hash'], 'safe'],
            [['amount', 'fee'], 'number'],
            [['amount','username'],'safe']
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
        $query = Deposit::find();
        $query->JoinWith(['user','walletAddress']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
//            'user_id' => $this->user_id,
//            'wallet_id' => $this->wallet_id,
            'iec_deposit.amount' => $this->amount,
//            'status' => $this->status,
//            'fee' => $this->fee,
        ]);

        $query->andFilterWhere(['like', 'iec_deposit.symbol', $this->symbol])
            ->andFilterWhere(['like', 'iec_user.username', $this->username]);
        $query->andFilterWhere(['like','iec_wallet_address.address',$this->address]);


        return $dataProvider;
    }
}
