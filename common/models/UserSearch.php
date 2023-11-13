<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * UsersSearch represents the model behind the search form of `common\models\User`.
 */
class UserSearch extends User
{
    /**
     * {@inheritdoc}
     */

    public $globalSearch;

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name_surname', 'username', 'email', 'phone_number', 'address', 'password', 'note', 'type', 'globalSearch'], 'safe'],
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
        $query = User::find();

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

        $query->orFilterWhere(['like', 'name_surname', $this->globalSearch])
            ->orFilterWhere(['like', 'username', $this->globalSearch])
            ->orFilterWhere(['like', 'email', $this->globalSearch])
            ->orFilterWhere(['like', 'phone_number', $this->globalSearch])
            ->orFilterWhere(['like', 'address', $this->globalSearch])
            //->orFilterWhere(['like', 'password', $this->globalSearch])
            //->orFilterWhere(['like', 'note', $this->globalSearch])
            ->orFilterWhere(['like', 'type', $this->globalSearch]);
            //->andFilterWhere(['IN', 'type', ['Reader', 'Employee']]);

        return $dataProvider;
    }
}