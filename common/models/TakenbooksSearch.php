<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Takenbooks;

/**
 * TakenbooksSearch represents the model behind the search form of `common\models\Takenbooks`.
 */
class TakenbooksSearch extends Takenbooks
{
    /**
     * @var string global search input
     */
    public $globalSearch;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'reader_id', 'book_id', 'book_quantity'], 'integer'],
            [['date_saved', 'date_taken', 'date_to_return', 'globalSearch'], 'safe'],
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
        $query = Takenbooks::find()
            ->select(['takenbooks.*', 'user.name_surname', 'books.title'])
            ->joinWith(['reader', 'book']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->orFilterWhere(['like', 'reader_id', $this->globalSearch])
            ->orFilterWhere(['like', 'book_id', $this->globalSearch])
            ->orFilterWhere(['like', 'book_quantity', $this->globalSearch])
            ->orFilterWhere(['like', 'date_saved', $this->globalSearch])
            ->orFilterWhere(['like', 'date_taken', $this->globalSearch])
            ->orFilterWhere(['like', 'date_to_return', $this->globalSearch])
            ->orFilterWhere(['like', 'user.name_surname', $this->globalSearch])
            ->orFilterWhere(['like', 'books.title', $this->globalSearch]);

        return $dataProvider;
    }
}


