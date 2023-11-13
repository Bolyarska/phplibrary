<?php

use common\models\Authors;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\widgets\ListView;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var common\models\AuthorsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Authors';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="authors-index">

    <h1 style="text-align: center;"><?= Html::encode($this->title) ?></h1>

    <?php if (isset($searchModel)) {
        echo $this->render('_search', ['model' => $searchModel]);
    } ?>

    <div class="container">

        <?php if (Yii::$app->user->can('view-employee-pages')): ?>
            <div class="col-12 text-center">
                <?= Html::a('Add an Author' . ' ' . '<i class="bi bi-database-fill-add"></i>', ['create'], ['class' => 'btn btn-warning btn-outline-dark']) ?>
            </div>
        <?php endif; ?>

        <br>

        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'itemOptions' => ['class' => 'item col-md-6 col-lg-4 mb-1'],
            'itemView' => function ($model, $key, $index, $widget) {
                $canView = Yii::$app->user->can('view-employee-pages');

                $bookCount = $model->bookCount ?? 0; // Access the book count from the subquery

                $card = '<div class="shadow card custom-card">' .
                    '<div class="card-body">' .
                    '<div class="row d-flex align-items-center">' .
                    '<div class="col-md-8">' .
                    Html::a(Html::tag('strong', Html::encode($model->name)), ['books/index', 'author' => $model->name], ['class' => 'card-link']) . ' ' .
                    '<em>' . $bookCount . '</em> <i class="bi bi-journals"></i>' .
                    '</div>';

                if ($canView) {
                    $card .= '<div class="col-md-4 text-end">' .
                        '<div class="book-buttons">' .
                        Html::a('<i class="bi bi-eye-fill"></i>', ['view', 'id' => $model->id], ['class' => 'btn btn-success btn-outline-dark view-button']) . ' ' .
                        Html::a('<i class="bi bi-pencil-fill"></i>', ['update', 'id' => $model->id], ['class' => 'btn btn-outline-secondary update-button']) .
                        '</div>' .
                        '</div>'; // Closing div for col-md-4 text-end
                }

                $card .= '</div>' . // Closing div for row
                    '</div>' . // Closing div for card-body
                    '</div>'; // Closing div for custom-card

                return $card;
            },
            'layout' => "<div class='row'>{items}</div>\n", // Display items in a row without a wrapper
        ]) ?>
    </div>

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
