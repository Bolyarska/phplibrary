<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Returnedbooks $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="returnedbooks-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'reader_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'book_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'book_quantity')->textInput() ?>

    <?= $form->field($model, 'date_taken')->textInput() ?>

    <?= $form->field($model, 'date_to_return')->textInput() ?>

    <?= $form->field($model, 'date_returned')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
