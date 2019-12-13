<?php

namespace backend\modules\activation\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\activation\ActivationUserList;

/**
 * ActivationUserListSearch represents the model behind the search form of `common\models\activation\ActivationUserList`.
 */
class ActivationUserListSearch extends ActivationUserList
{
    public $username;
    public $name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['level'], 'integer'],
            [['username', 'name'], 'safe'],
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
        $query = ActivationUserList::find()
            ->JoinWith('user')->JoinWith('activation');;

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
            'level' => $this->level,
        ]);
        $query->andFilterWhere(['like', 'iec_user.username', $this->username]);
        $query->andFilterWhere(['like', 'iec_activation.name', $this->name]);
        return $dataProvider;
    }
}