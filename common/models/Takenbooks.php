<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "takenbooks".
 *
 * @property int $id
 * @property int|null $reader_id
 * @property int|null $book_id
 * @property int $book_quantity
 * @property string|null $date_saved
 * @property string|null $date_taken
 * @property string|null $date_to_return
 *
 * @property Books $book
 * @property Savedbooks $dateSaved
 * @property User $reader
 * @property Returnedbooks[] $returnedbooks
 * @property Returnedbooks[] $returnedbooks0
 */
class Takenbooks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'takenbooks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reader_id', 'book_id', 'book_quantity'], 'integer'],
            [['book_quantity'], 'required'],
            [['book_quantity'], 'safe'],
            [['date_saved', 'date_taken', 'date_to_return'], 'safe'],
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
            'date_taken' => 'Date Taken',
            'date_to_return' => 'Date To Return',
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
     * Gets query for [[DateSaved]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDateSaved()
    {
        return $this->hasOne(Savedbooks::class, ['date_saved' => 'date_saved']);
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
     * Gets query for [[Returnedbooks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReturnedbooks()
    {
        return $this->hasMany(Returnedbooks::class, ['date_taken' => 'date_taken']);
    }

    /**
     * Gets query for [[Returnedbooks0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReturnedbooks0()
    {
        return $this->hasMany(Returnedbooks::class, ['date_to_return' => 'date_to_return']);
    }
}
