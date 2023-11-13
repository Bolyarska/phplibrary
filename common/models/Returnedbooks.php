<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "returnedbooks".
 *
 * @property int $id
 * @property int|null $reader_id
 * @property int|null $book_id
 * @property int $book_quantity
 * @property string|null $date_taken
 * @property string|null $date_to_return
 * @property string|null $date_returned
 *
 * @property Books $book
 * @property Takenbooks $dateTaken
 * @property Takenbooks $dateToReturn
 * @property User $reader
 */
class Returnedbooks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'returnedbooks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reader_id', 'book_id', 'book_quantity'], 'integer'],
            [['book_quantity'], 'required'],
            [['date_taken', 'date_to_return', 'date_returned'], 'safe'],
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
            'date_taken' => 'Date Taken',
            'date_to_return' => 'Date To Return',
            'date_returned' => 'Date Returned',
        ];
    }

    /**
     * Gets query for [[Book]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBook()
    {
        return $this->hasOne(Books::class, ['id' => 'book_id']);
    }

    /**
     * Gets query for [[DateTaken]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDateTaken()
    {
        return $this->hasOne(Takenbooks::class, ['date_taken' => 'date_taken']);
    }

    /**
     * Gets query for [[DateToReturn]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDateToReturn()
    {
        return $this->hasOne(Takenbooks::class, ['date_to_return' => 'date_to_return']);
    }

    /**
     * Gets query for [[Reader]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReader()
    {
        return $this->hasOne(User::class, ['id' => 'reader_id']);
    }
}
