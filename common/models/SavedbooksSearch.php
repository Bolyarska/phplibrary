<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Savedbooks;

/**
 * SavedbooksSearch represents the model behind the search form of `common\models\Savedbooks`.
 */
class SavedbooksSearch extends Savedbooks
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'reader_id', 'book_id', 'book_quantity'], 'integer'],
            [['date_saved'], 'safe'],
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
        $query = Savedbooks::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false
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
            'date_saved' => $this->date_saved,
        ]);

        return $dataProvider;
    }
}
