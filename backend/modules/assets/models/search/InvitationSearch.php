<?php

namespace backend\modules\assets\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\assets\models\Invitation;

/**
 * InvitationSearch represents the model behind the search form of `backend\modules\assets\models\Invitation`.
 */
class InvitationSearch extends Invitation
{

    public $registerUsername;
    public $inviterUsername;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'registerer_id', 'inviter_id', 'created_at', 'level'], 'integer'],
            [['registerUsername', 'inviterUsername'], 'safe']
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
        $query = Invitation::find();
        $query->joinWith(['registerUser as a', 'inviterUser as b']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);


        $sort = $dataProvider->getSort();
        $sort->attributes['registerUsername'] = [
            'asc' => ['registerUser.username' => SORT_ASC],
            'desc' => ['registerUser.username' => SORT_DESC],
        ];
        $sort->attributes['inviterUsername'] = [
            'asc' => ['inviterUsername.username' => SORT_ASC],
            'desc' => ['inviterUsername.username' => SORT_DESC],
        ];
        $dataProvider->setSort($sort);

        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'registerer_id' => $this->registerer_id,
            'inviter_id' => $this->inviter_id,
            'created_at' => $this->created_at,
            'level' => $this->level,
        ]);

        $query->andFilterWhere(['like', 'a.username', $this->registerUsername]);
        $query->andFilterWhere(['like', 'b.username', $this->inviterUsername]);

        return $dataProvider;
    }
}
