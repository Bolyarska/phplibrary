<?php

use common\models\Books;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\widgets\ListView;

/** @var yii\web\View $this */
/** @var common\models\BooksSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'All Books';
$this->params['breadcrumbs'][] = $this->title;
?>

<nav class="navbar navbar-light bg-light">
  <div class="container-fluid d-flex justify-content-center">
    <form class="d-flex">
      <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
      <button class="btn btn-outline-success" type="submit">Search</button>
    </form>
  </div>
</nav>


<div class="books-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <ul>
        <?php foreach ($results as $result): ?>
            <li><?= Html::encode($result['title']) ?></li>
        <?php endforeach; ?>
    </ul>
</div>

