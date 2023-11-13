<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Takenbooks $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="takenbooks-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'reader_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'book_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'book_quantity')->textInput() ?>

    <?= $form->field($model, 'date_saved')->textInput() ?>

    <?= $form->field($model, 'date_taken')->textInput() ?>

    <?= $form->field($model, 'date_to_return')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
