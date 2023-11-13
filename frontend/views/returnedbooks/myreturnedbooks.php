<?php

use common\models\Returnedbooks;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use common\models\Books;
use yii\web\Session;
use yii\widgets\LinkPager;


/** @var yii\web\View $this */
/** @var common\models\SavedbooksSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'My Return History';
$this->params['breadcrumbs'][] = $this->title;

// also receives all saved_books_array of that user -> [{"book_id":3,"quantity":1,"date_saved":"2023-07-14 11:00:45"}] and $reader_id

\yii\web\YiiAsset::register($this);

?>

<?php if ($dataProvider->totalCount > 0): ?>
<div class='container pt-0'>
<div class="col-12 col-sm-12 col-md-10 col-lg-8 returnedbooks-index mx-auto">
    <h1 class='text-center'><?= Html::encode($this->title) ?></h1>

    <div class="card d-none d-md-block">
        <div class="card-body table-responsive p-0" style='border:0px'>
            <table class="table">
                <thead class='text-center'>
                    <tr>
                        <th class='text-start'>Book Title</th>
                        <th class='text-end'>Book Quantity</th>
                        <th class="d-none d-lg-table-cell">Due Date</th>
                        <th>Date Returned</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataProvider->getModels() as $model): ?>
                        <tr>
                            
                            <td>
                                <?php if (isset($model->book)): ?>
                                    <?= Html::a(Html::encode($model->book->title), ['books/view', 'id' => $model->book_id]) ?>
                                <?php else: ?>
                                    Book no longer exists in library
                                <?php endif; ?>
                            </td>
                            <td class='text-end'><?= Html::encode($model->book_quantity) ?></td>
                            <td class="d-none d-lg-table-cell text-center"><?= (new DateTime($model->date_to_return))->format('m/d/Y') ?></td>
                            <td class='text-center'><?= (new DateTime($model->date_returned))->format('m/d/Y') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card d-block d-md-none">
    <div class="card-body table-responsive p-0" style='border:0px'>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th class='text-end'>Quantity</th>
                    <th class='text-end'>Returned</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dataProvider->getModels() as $model): ?>
                    <tr>
                        <td><?= Html::encode($model->reader->id) ?></td>
                        <td>
                            <?php if (isset($model->book)): ?>
                                <?php
                                $title = Html::encode($model->book->title);
                                $truncatedTitle = strlen($title) > 10 ? substr($title, 0, 10) . '..' : $title;
                                ?>
                                <?= Html::a($truncatedTitle, ['books/view', 'id' => $model->book_id], ['class' => 'text-truncate']) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class='text-end'><?= Html::encode($model->book_quantity) ?></td>
                        <td class="d-none d-lg-table-cell"><?= Html::encode($model->date_to_return) ?></td>
                        <td class='text-end'><?= Html::encode($model->date_returned) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    </div>
</div>

<br>

<div class='col-12 text-center'>
    <?= LinkPager::widget([
    'pagination' => $dataProvider->pagination,
    'options' => ['class' => 'pagination justify-content-center'],
    'linkContainerOptions' => ['class' => 'page-item'],
    'linkOptions' => ['class' => 'page-link'],
    'disabledListItemSubTagOptions' => ['class' => 'btn btn-dark disabled'],
    'activePageCssClass' => 'active',
    'maxButtonCount' => 5,
    ]) ?>
</div>

</div>

<?php else: ?>
    <div class='text-center'>
        <h4>No Return History</h4>
    </div>
<?php endif; ?>


<style>
    /* Additional styles for responsiveness */
    .table {
        width: 100%;
        max-width: 100%;
        margin-bottom: 1rem;
        background-color: transparent;
    }

    .table-responsive {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        -ms-overflow-style: -ms-autohiding-scrollbar;
    }

    .table-responsive > .table {
        margin-bottom: 0;
    }

    th {
        font-weight: bold;
    }

    td, th {
        padding: 12px 15px;
        vertical-align: middle;
        border-top: 1px solid #dee2e6;
    }

    tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }
</style>
