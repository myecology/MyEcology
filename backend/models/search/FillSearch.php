<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Fill;
use api\models\User;
use common\models\DistrbuteLog;

/**
 * FillSearch represents the model behind the search form of `common\models\Fill`.
 */
class FillSearch extends Fill
{
    public $username;
    public $name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'status', 'is_del'], 'integer'],
            [['symbol', 'release_symbol','username','name'], 'safe'],
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
    public function search($params,$id="")
    {
        if(empty($id)){
            $query = Fill::find()->where(["is_del"=>1]);
        }else{
            $query = Fill::find()->where(["is_del"=>1])
            ->andWhere(['distrbute_id'=>$id]);
        }
        $query->JoinWith(['user as a', 'distrbute as b']);
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
            'user_id' => $this->user_id,
            // 'status' => $this->status,
            'amount' => $this->amount,
            'is_del' => $this->is_del,
        ]);

        $query->andFilterWhere(['like', 'symbol', $this->symbol])
            ->andFilterWhere(['like', 'release_symbol', $this->release_symbol]);
        $query->andFilterWhere(['like', 'a.username',$this->username]);
        $query->andFilterWhere(['like', 'b.name',$this->name]);

        return $dataProvider;
    }
}
