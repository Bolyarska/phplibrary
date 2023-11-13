<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Takenbooks $model */

$this->title = 'Update Takenbooks: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Takenbooks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="takenbooks-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
