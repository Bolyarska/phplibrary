<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\BooksSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="books-search" style="text-align: center;">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['class' => 'form-inline'],
    ]); ?>

    <div class="input-group justify-content-center" style="max-width: 400px; margin: auto;">
        <?= $form->field($model, 'globalSearch', [
            'options' => ['class' => 'mb-0 me-1'],
            'inputOptions' => ['class' => 'form-control', 'placeholder' => 'Search...'],
        ])->label(false) ?>
        <div class="input-group-append">
            <?= Html::submitButton('<i class="bi bi-search"></i>', ['class' => 'btn btn-success btn-outline-dark', 'title'=>'
            You can search by title, author, ISBN, publisher, language, description', 'data-toggle'=>'tooltip',
            'style'=>'text-decoration: none; cursor:pointer;']) ?>
        </div>
    </div>

    <?php // echo $form->field($model, 'language') ?>

    <?php // echo $form->field($model, 'pages') ?>

    <?php // echo $form->field($model, 'number_in_stock') ?>

    <?php // echo $form->field($model, 'number_available') ?>

    <?php // echo $form->field($model, 'images') ?>

    <?php // echo $form->field($model, 'description') ?>

    <!--<div class="form-group mt-2">
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div> -->
    <br>

    <?php ActiveForm::end(); ?>

</div>
