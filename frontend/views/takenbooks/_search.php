<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\TakenbooksSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="takenbooks-search" style="text-align: center;">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

<div style="display: flex; justify-content: center;">
        <?= $form->field($model, 'globalSearch', ['options' => ['style' => 'width: 200px;']])->label(false) ?>
</div>
<br>

<div class="form-group">
    <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
    <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
</div>
<br>

<?php ActiveForm::end(); ?>

</div>
