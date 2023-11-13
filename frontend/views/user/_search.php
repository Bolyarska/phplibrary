<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\UserSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-search" style="text-align: center;">
    <?php $form = ActiveForm::begin([
        'action' => ['readers'],
        'method' => 'get',
        'options' => ['class' => 'form-inline'], // for inline form display
    ]); ?>

    <div class="input-group justify-content-center" style="max-width: 400px; margin: auto;">
        <?= $form->field($model, 'globalSearch', [
            'options' => ['class' => 'mb-0 me-1'],
            'inputOptions' => ['class' => 'form-control', 'placeholder' => 'Search...'],
        ])->label(false) ?>
        <div class="input-group-append">
            <?= Html::submitButton('<i class="bi bi-search"></i>', ['class' => 'btn btn-success btn-outline-dark', 'title'=>'
            You can search by name, username, email, phone number, address, type', 'data-toggle'=>'tooltip',
            'style'=>'text-decoration: none; cursor:pointer;']) ?>
        </div>
    </div>

    <!--<div class="form-group mt-2">
    <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div> -->
    <br>

    <?php ActiveForm::end(); ?>
</div>
