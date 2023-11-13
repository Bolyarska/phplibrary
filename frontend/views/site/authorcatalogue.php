<?php

use common\models\Books;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\widgets\ListView;

/** @var yii\web\View $this */
/** @var common\models\BooksSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'All Authors';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="authors-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <ul>
        <?php foreach ($results as $result): ?>
            <li><?= Html::encode($result['author']) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
