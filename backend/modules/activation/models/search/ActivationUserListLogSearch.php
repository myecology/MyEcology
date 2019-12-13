<?php

namespace backend\modules\activation\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\activation\ActivationUserListLog;

/**
 * ActivationUserListLogSearch represents the model behind the search form of `common\models\activation\ActivationUserListLog`.
 */
class ActivationUserListLogSearch extends ActivationUserListLog
{
    public $name;
    public $username;
    public $pusername;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number'], 'integer'],
            [['goods_name','name','username','pusername'], 'safe'],
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
        $query = ActivationUserListLog::find()
            ->joinWith(['user as a', 'puid as b','activation']);

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
            'number' => $this->number,
        ]);

        $query->andFilterWhere(['like', 'goods_name', $this->goods_name]);
        $query->andFilterWhere(['like', 'a.username', $this->username]);
        $query->andFilterWhere(['like', 'b.username', $this->pusername]);
        $query->andFilterWhere(['like', 'iec_activation.name', $this->name]);

        return $dataProvider;
    }
}
