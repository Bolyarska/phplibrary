<?php

date_default_timezone_set('Europe/Helsinki');

use common\models\Takenbooks;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** receives  */

/** @var yii\web\View $this */
/** @var common\models\TakenbooksSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Expected Returns';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php if ($dataProvider->totalCount > 0): ?>
<div class="col-10 col-md-10 col-lg-6 col-xl-6 col-xxl-2 takenbooks-index mx-auto">

<h1 class='text-center'><?= Html::encode($this->title) ?></h1>

<br>

<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<div class="shadow card d-none d-lg-block", style='border: 0px'>
<?= Html::beginForm(['returnedbooks/return-books'], 'post', ['id' => 'return-form-lg']) ?>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-borderless">
                <thead class='text-center'>
                    <tr>
                        <th>Name</th>
                        <th>Title</th>
                        <th>Quantity</th>
                        <?php if (Yii::$app->user->can('view-employee-pages')) : ?>
                        <th style='width:20%'>Return Quantity</th>
                        <?php endif; ?>
                        <th>Saved on</th>
                        <th>Taken on</th>
                        <th>Due</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataProvider->models as $model): ?>
                        <?php
                        $dateToReturn = new DateTime($model->date_to_return, new DateTimeZone('Europe/Helsinki')); // Adjust 'UTC' to your server's timezone if needed
                        $today = new DateTime('now', new DateTimeZone('Europe/Helsinki')); // Get today's date in the server's timezone

                        // Set both DateTime objects to the same timezone for accurate comparison
                        $dateToReturn->setTimezone(new DateTimeZone('Europe/Helsinki')); // Adjust 'UTC' to your server's timezone if needed
                        $today->setTimezone(new DateTimeZone('Europe/Helsinki')); // Adjust 'UTC' to your server's timezone if needed

                        // Get the date part as a string from the DateTime objects
                        $dateToReturnString = $dateToReturn->format('Y-m-d');
                        $todayString = $today->format('Y-m-d');

                        $isBeforeToday = $dateToReturnString < $todayString;
                        $rowClass = $isBeforeToday ? 'pink-row' : '';
                        ?>
                        <tr class="<?= $rowClass ?>">
                            <td>
                                <?= Html::a($model->reader->name_surname, ['user/view', 'id' => $model->reader_id]) ?>
                            </td>
                            <td>
                                <?= Html::a($model->book->title, ['books/view', 'id' => $model->book_id]) ?>
                            </td>
                            <td class='text-end'><?= $model->book_quantity ?></td>
                            <?php if (Yii::$app->user->can('view-employee-pages')) : ?>
                            <td class="quantity-to-return-column d-flex justify-content-center">
                                <?= Html::input('number', "quantity_to_return[$model->id]", 0, [
                                    'class' => 'form-control',
                                    'min' => 0,
                                    'max' => $model->book_quantity,
                                    'name' => "quantity_to_return[$model->id]",
                                    'id' => "quantity-input-$model->id",
                                ]) ?>
                            </td>
                            <?php endif; ?>
                            <td class='text-center'><?= (new DateTime($model->date_saved))->format('m/d/Y') ?></td>
                            <td class='text-center'><?= (new DateTime($model->date_taken))->format('m/d/Y') ?></td>
                            <td class='text-center'><?= (new DateTime($model->date_to_return))->format('m/d/Y') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class='row d-none d-lg-block'>
<?php if (Yii::$app->user->can('view-employee-pages')) { ?>
    <div class= 'col-12 text-center' style='padding-top:10px'>
        <?= Html::submitButton('Return', [
        'class' => 'btn btn-success btn-outline-dark text-white',
        'form' => 'return-form-lg',
        'name' => 'submit-return',
        'value' => 'Return',
        ]); ?>
    </div>
    <?php } ?>

    <?= Html::endForm() ?>
</div>
</div>


<div class="col-10 col-sm-6 d-block d-lg-none text-center mx-auto">
<?= Html::beginForm(['returnedbooks/return-books'], 'post', ['id' => 'return-form-sm']) ?>
    <?php
    foreach ($dataProvider->getModels() as $model) {
        $dateToReturn = new DateTime($model->date_to_return, new DateTimeZone('Europe/Helsinki'));
        $today = new DateTime('now', new DateTimeZone('Europe/Helsinki'));

        $dateToReturn->setTimezone(new DateTimeZone('Europe/Helsinki'));
        $today->setTimezone(new DateTimeZone('Europe/Helsinki'));

        $dateToReturnString = $dateToReturn->format('Y-m-d');
        $todayString = $today->format('Y-m-d');

        $isBeforeToday = $dateToReturnString < $todayString;
        $cardClass = $isBeforeToday ? 'pink-row' : '';
        ?>

        <div class="shadow card mb-3">
            <div class="card-header <?= $cardClass ?>">
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <?= Html::a($model->reader->name_surname, ['user/view', 'id' => $model->reader_id]) ?>
                    </div>
                    <div class="col-12">
                        <?= Html::a($model->book->title, ['books/view', 'id' => $model->book_id]) ?>
                    </div>
                    <div class="col-12">
                        Due: <?= Html::encode($model->date_to_return) ?>
                    </div>
                    <div class="col-12">
                        Taken on: <?= Html::encode($model->date_taken) ?>
                    </div>
                    <div class="col-12 pb-2">
                        Quantity: <?= Html::encode($model->book_quantity) ?>
                    </div>
                    <?php if (Yii::$app->user->can('view-employee-pages')) : ?>
                    <div class="col-12">
                        <?= Html::input('number', "quantity_to_return[$model->id]", 0, [
                            'class' => 'form-control',
                            'min' => 0,
                            'max' => $model->book_quantity,
                            'name' => "quantity_to_return[$model->id]",
                            'id' => "quantity-input-$model->id",
                        ]); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (Yii::$app->user->can('view-employee-pages')) { ?>
    <div class= 'col-12 text-center' style='padding-top:10px'>
        <?= Html::submitButton('Return', [
        'class' => 'btn btn-success btn-outline-dark text-white',
        'form' => 'return-form-sm',
        'name' => 'submit-return',
        'value' => 'Return',
        ]); ?>
    </div>
    <?php } ?>

    <?= Html::endForm() ?>

</div>

<?php else: ?>
    <div class='text-center'>
        <h4>No Expected Returns</h4>
    </div>
<?php endif; ?>

<style>
    .table tbody tr.pink-row td {
    background-color: pink !important;
}

    .card-header.pink-row {
    background-color: pink !important;
    }

    .card-header.pink-row::before {
    content: 'Overdue';
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 18px;
    color: white;
    font-weight: bold;
    text-transform: uppercase;
}


</style>