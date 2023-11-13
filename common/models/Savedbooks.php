<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "savedbooks".
 *
 * @property int $id
 * @property int|null $reader_id
 * @property int|null $book_id
 * @property int $book_quantity
 * @property string|null $date_saved
 *
 * @property Books $book
 * @property User $reader
 * @property Takenbooks[] $takenbooks
 */
class Savedbooks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'savedbooks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reader_id', 'book_id', 'book_quantity'], 'integer'],
            [['book_quantity'], 'required'],
            [['date_saved', 'expiration_time'], 'safe'],
            [['reader_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['reader_id' => 'id']],
            [['book_id'], 'exist', 'skipOnError' => true, 'targetClass' => Books::class, 'targetAttribute' => ['book_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reader_id' => 'Reader ID',
            'book_id' => 'Book ID',
            'book_quantity' => 'Book Quantity',
            'date_saved' => 'Date Saved',
            'expiration_time' => 'Expiration Time',
        ];
    }

    /**
     * Gets query for [[Book]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBook() // from Books model
    {
        return $this->hasOne(Books::class, ['id' => 'book_id']);
    }

    /**
     * Gets query for [[Reader]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReader() // from User model
    {
        return $this->hasOne(User::class, ['id' => 'reader_id']);
    }

    /**
     * Gets query for [[Takenbooks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTakenbooks()
    {
        return $this->hasMany(Takenbooks::class, ['date_saved' => 'date_saved']);
    }
}
