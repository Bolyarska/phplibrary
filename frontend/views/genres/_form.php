<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Genres $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="col-10 col-md-4 col-lg-4 genres-form mx-auto">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'genre', ['options' => ['style' => 'margin-bottom: 10px']])->textInput(['maxlength' => true]) ?>

    <div class="form-group text-end">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success btn-outline-dark text-white']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
