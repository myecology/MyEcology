<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ReleaseDistrbuteLog;

/**
 * ReleaseDistrbuteLogSearch represents the model behind the search form of `common\models\ReleaseDistrbuteLog`.
 */
class ReleaseDistrbuteLogSearch extends ReleaseDistrbuteLog
{
    public $username;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'distrbute_id','status'], 'integer'],
            [['amount'], 'number'],
            [['symbol', 'created_at', 'remark','username'], 'safe'],
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
            $query = ReleaseDistrbuteLog::find();
        }else{
            $query = ReleaseDistrbuteLog::find()
            ->where(['distrbute_id'=>$id]);
        }
        $query->JoinWith(['user']);

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
            'amount' => $this->amount,
            'iec_release_distrbute_log.status' => $this->status,
            'distrbute_id' => $this->distrbute_id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'symbol', $this->symbol])
            ->andFilterWhere(['like', 'remark', $this->remark]);
        $query->andFilterWhere(['like', 'iec_user.username',$this->username]);

        return $dataProvider;
    }
}
