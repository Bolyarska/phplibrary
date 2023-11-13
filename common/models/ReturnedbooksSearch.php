<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Returnedbooks;

/**
 * ReturnedbooksSearch represents the model behind the search form of `common\models\Returnedbooks`.
 */
class ReturnedbooksSearch extends Returnedbooks
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'reader_id', 'book_id', 'book_quantity'], 'integer'],
            [['date_taken', 'date_to_return', 'date_returned'], 'safe'],
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
        $query = Returnedbooks::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'reader_id' => $this->reader_id,
            'book_id' => $this->book_id,
            'book_quantity' => $this->book_quantity,
            'date_taken' => $this->date_taken,
            'date_to_return' => $this->date_to_return,
            'date_returned' => $this->date_returned,
        ]);

        return $dataProvider;
    }
}
