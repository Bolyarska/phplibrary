<?php

use yii\helpers\Html;
use common\models\Genres;

/** @var yii\web\View $this */
/** @var common\models\Books $model */

$this->title = 'Add a new book';
$this->params['breadcrumbs'][] = ['label' => 'Books', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$genres = Genres::find()->all();
$genreList = [];
foreach ($genres as $genre) {
    $genreList[$genre->id] = $genre->genre;
}

?>

<div class="books-create">

    <h1 class='text-center'><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'genreList' => $genreList,
    ]) ?>

</div>
