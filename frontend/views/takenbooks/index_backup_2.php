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

<h1 class='text-center'><?= Html::encode($this->title) ?></h1>

<br>

<div class="col-10 col-sm-10 col-md-6 col-lg-8 col-xl-3 text-center mx-auto">
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