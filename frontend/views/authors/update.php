<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Authors $model */


$this->params['breadcrumbs'][] = ['label' => 'Authors', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="authors-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
