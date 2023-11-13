<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\GenresSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="genres-search" style="text-align: center;">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['class' => 'form-inline'], // Add Bootstrap 'form-inline' class for inline form display
    ]); ?>

    <div class="input-group justify-content-center" style="max-width: 400px; margin: auto;">
        <?= $form->field($model, 'globalSearch', [
            'options' => ['class' => 'mb-0 me-1'], // Remove margin-bottom for a cleaner look
            'inputOptions' => ['class' => 'form-control', 'placeholder' => 'Search...'],
        ])->label(false) ?>
        <div class="input-group-append">
            <?= Html::submitButton('<i class="bi bi-search"></i>', ['class' => 'btn btn-success btn-outline-dark']) ?>
        </div>
    </div>

    <!--<div class="form-group mt-2">
    <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div> -->
    <br>
    <?php ActiveForm::end(); ?>
</div>
