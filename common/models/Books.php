<?php

namespace common\models;

use Yii;
use common\models\Authors;

/**
 * This is the model class for table "books".
 *
 * @property int $id
 * @property string $title
 * @property string $author
 * @property string $isbn
 * @property string|null $publisher
 * @property string|null $language
 * @property int|null $pages
 * @property int $number_in_stock
 * @property int $number_available
 * @property string|null $images
 * @property string|null $description
 *
 * @property Bookgenres[] $bookgenres
 * @property Returnedbooks[] $returnedbooks
 * @property Savedbooks[] $savedbooks
 * @property Takenbooks[] $takenbooks
 */
class Books extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public $selectedGenres;
    public $file;

    public static function tableName()
    {
        return 'books';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'author', 'isbn', 'number_in_stock', 'number_available'], 'required'],
            [['pages', 'number_in_stock', 'number_available'], 'integer'],
            [['file'], 'file', 'maxFiles' => 4, 'maxSize' => 1024 * 1024 * 2, 'tooBig' => 'The file "{file}" is too big. Its size cannot exceed 2MB.'],
            [['images', 'selectedGenres'], 'safe'],
            [['title', 'author', 'publisher', 'language'], 'string', 'max' => 64],
            [['isbn'], 'string', 'min' => 13, 'max' => 14],
            [['description'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'author' => 'Author',
            'isbn' => 'Isbn',
            'publisher' => 'Publisher',
            'language' => 'Language',
            'pages' => 'Pages',
            'number_in_stock' => 'In Stock',
            'number_available' => 'Available',
            'file' => 'Images',
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[Bookgenres]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBookgenres()
    {
        return $this->hasMany(Bookgenres::class, ['book_id' => 'id']);
    }

    /**
     * Gets query for [[Returnedbooks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReturnedbooks()
    {
        return $this->hasMany(Returnedbooks::class, ['book_id' => 'id']);
    }

    /**
     * Gets query for [[Savedbooks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSavedbooks()
    {
        return $this->hasMany(Savedbooks::class, ['book_id' => 'id']);
    }

    /**
     * Gets query for [[Takenbooks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTakenbooks()
    {
        return $this->hasMany(Takenbooks::class, ['book_id' => 'id']);
    }

    public function getAuthorList()
    {
        $authors = Authors::find()->all();
        $authorNames = [];

        foreach ($authors as $author) {
            $authorNames[] = $author->name;
        }

        return $authorNames;
    }

}
