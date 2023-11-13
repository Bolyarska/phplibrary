<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Books;

/**
 * BooksSearch represents the model behind the search form of `common\models\Books`.
 */
class BooksSearch extends Books
{

    public $globalSearch;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'pages', 'number_in_stock', 'number_available'], 'integer'],
            [['title', 'author', 'isbn', 'publisher', 'language', 'images', 'description', 'globalSearch'], 'safe'],
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

        
        $query = Books::find();

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

        /*grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'pages' => $this->pages,
            'number_in_stock' => $this->number_in_stock,
            'number_available' => $this->number_available,
        ]);*/

        $query->orFilterWhere(['like', 'title', $this->globalSearch])
            ->orFilterWhere(['like', 'author', $this->globalSearch])
            ->orFilterWhere(['like', 'isbn', $this->globalSearch])
            ->orFilterWhere(['like', 'publisher', $this->globalSearch])
            ->orFilterWhere(['like', 'language', $this->globalSearch]);

        return $dataProvider;
    }
}
