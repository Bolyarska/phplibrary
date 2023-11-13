<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\User $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="users-form">
    <div class="row justify-content-center">
        <div class="col-10 col-md-4 col-lg-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name_surname', ['options' => ['class' => 'create-field']])->textInput(['maxlength' => true])->label('Name and Surname') ?>

        <?= $form->field($model, 'username', ['options' => ['class' => 'create-field']])->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'email', ['options' => ['class' => 'create-field']])->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'phone_number', ['options' => ['class' => 'create-field']])->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'address', ['options' => ['class' => 'create-field']])->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'note', ['options' => ['class' => 'create-field']])->textarea(['rows' => 6]) ?>

        <?php $userType = Yii::$app->user->identity->type;

        $options = [];
        if ($userType === 'Employee') {
            $options = ['Reader' => 'Reader'];
        } else {
            $options = [
                'Administrator' => 'Administrator',
                'Employee' => 'Employee',
                'Reader' => 'Reader',
            ];
        }
        ?>

        <div class='type'>
        <?= $form->field($model, 'type', ['options' => ['class' => 'create-field']])->label('Type <i class="bi bi-menu-down"></i>')->dropDownList($options, ['prompt' => ''])?>
        </div>

        <div class="form-group text-end pt-1">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success btn-outline-dark text-white']) ?>
        </div>

        <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>

<style>

    .create-field {
    margin-bottom: 17px;
    }

</style>
