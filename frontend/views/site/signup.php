<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\SignupForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Register for an account';

?>
<div class="site-register">
    <div class="text-center"><h1><?= Html::encode($this->title) ?></h1></div>

    <br>

    <div class="row justify-content-center">
        <div class="col-lg-5">
        <?php $form = ActiveForm::begin([
            'id' => 'register-form',
            //'enableAjaxValidation' => true,
            //'layout' => 'horizontal',
            'fieldConfig' => [
                //'template' => "{label}\n<div class=\"col-lg-7\">{input}</div>\n<div class=\"col-lg-9\">{error}</div>",
                'labelOptions' => ['class' => 'control-label'],
            ],
        ]); ?>

        <?= $form->field($model, 'name_surname')->textInput(['autofocus' => true])->label('Your Name and Surname') ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label('Choose a Username')?>

        <?= $form->field($model, 'email')->textInput(['autofocus' => true])->label('Your Email')  ?>

        <?= $form->field($model, 'phone_number')->textInput(['autofocus' => true, 'maxlength' => 10])->label('Your Phone Number') ?>

        <?= $form->field($model, 'address')->textInput(['autofocus' => true, 'maxlength' => 128])->label('Your Address') ?>


        <?= $form->field($model, 'password')->passwordInput()->label('Password (Must be at least 8 characters)')  ?>

        <?= $form->field($model, 'password_repeat')->passwordInput()->label('Password Repeat')  ?>

        <div class="form-group text-end">
                <?= Html::submitButton('Register', ['class' => 'btn btn-success btn-outline-dark text-white', 'name' => 'login-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>

