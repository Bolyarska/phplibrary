<?php

use yii\helpers\Html;
use common\models\Genres;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var common\models\Books $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Books', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';

$genres = Genres::find()->all();
$genreList = [];
foreach ($genres as $genre) {
    $genreList[$genre->id] = $genre->genre;
}


$model->selectedGenres = ArrayHelper::getColumn($model->bookgenres, 'genre_id');

?>
<div class="books-update">

    <h1 style="text-align: center;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model, // this also contains the selectedGenres
        'genreList' => $genreList,
    ]) ?>

</div>
