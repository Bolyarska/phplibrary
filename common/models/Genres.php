<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "genres".
 *
 * @property int $id
 * @property string|null $genre
 *
 * @property Bookgenres[] $bookgenres
 */
class Genres extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public $bookCount;
    
    public static function tableName()
    {
        return 'genres';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['genre'], 'string', 'max' => 64],
            [['genre'], 'unique'],
            [['genre'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'genre' => 'Genre',
        ];
    }

    /**
     * Gets query for [[Bookgenres]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBookgenres()
    {
        return $this->hasMany(Bookgenres::class, ['genre_id' => 'id']);
    }
}
