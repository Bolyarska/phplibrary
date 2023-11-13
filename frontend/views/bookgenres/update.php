<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Bookgenres $model */

$this->title = 'Update Bookgenres: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Bookgenres', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bookgenres-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
