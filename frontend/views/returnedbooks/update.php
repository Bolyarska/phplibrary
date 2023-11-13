<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Returnedbooks $model */

$this->title = 'Update Returnedbooks: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Returnedbooks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="returnedbooks-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
