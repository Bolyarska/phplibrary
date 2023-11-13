<?php

use common\models\Genres;
use common\models\Books;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\ListView;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var common\models\GenresSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Genres';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="genres-index">

    <h1 style="text-align: center;"><?= Html::encode($this->title) ?></h1>

    <?php if (isset($searchModel)) {
        echo $this->render('_search', ['model' => $searchModel]);
    } ?>

    <div class="allgenres-container">

    <?php if (Yii::$app->user->can('view-employee-pages')): ?>
    <div class="col-12 text-center">
        <?= Html::a('Add a Genre' . ' ' . '<i class="bi bi-database-fill-add"></i>', ['create'], ['class' => 'btn btn-warning btn-outline-dark']) ?>
    </div>
    <?php endif; ?>

    <br>

    <?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemOptions' => ['class' => 'item'],
    'itemView' => function ($model, $key, $index, $widget) {

        $canView = Yii::$app->user->can('view-employee-pages');

        $bookCount = $model->bookCount ?? 0; // Access the book count from the subquery

        $card = '<div class="shadow card custom-card col-lg-3 mb-1">' .
            '<div class="card-body">' .
            '<div class="row d-flex align-items-center">' .
            '<div class="col-lg-5">' .
            Html::a(Html::encode($model->genre), ['bookgenres/bygenre', 'genreId' => $model->id], ['class' => 'card-link']) .
            '</div>' .
            '<div class="col-lg-2"><em><strong>' . $bookCount . '</strong></em> <i class="bi bi-journals"></i></div>'. 
            '<div class="col-lg-5 text-end">' .
            '<div class="book-buttons">';

        if ($canView) {
            $card .= Html::a('<i class="bi bi-eye-fill"></i>', ['view', 'id' => $model->id], ['class' => 'btn btn-success btn-outline-dark']) . ' ';
            $card .= Html::a('<i class="bi bi-pencil-fill"></i>', ['update', 'id' => $model->id], ['class' => 'btn btn-outline-secondary']) . ' ';
        }

        $card .= '</div>' .
                '</div>' . // Closing div for col-lg-4
                '</div>' . // Closing div for row
                '</div>' . // Closing div for card-body
                '</div>'; // Closing div for custom-card

        return '<div class="row justify-content-center">' . $card . '</div>';
    },

    'layout' => "{items}\n", // Display only the items without pagination
    ]) ?>

    <br>

   <!-- Custom Pagination -->
   <div class="row">
        <div class="col-12">
            <nav aria-label="Page navigation" class="d-flex justify-content-center">
                <ul class="pagination">
                    <?= LinkPager::widget([
                        'pagination' => $dataProvider->pagination,
                        'prevPageCssClass' => 'page-item ' . ($dataProvider->pagination->getPage() === 0 ? 'disabled' : ''),
                        'nextPageCssClass' => 'page-item ' . ($dataProvider->pagination->getPageCount() - 1 === $dataProvider->pagination->getPage() ? 'disabled' : ''),
                        'linkOptions' => ['class' => 'page-link'],
                        'disabledListItemSubTagOptions' => ['class' => 'btn btn-dark disabled'],
                        'maxButtonCount' => 5,
                    ]) ?>
                </ul>
            </nav>
        </div>
    </div>
</div>
