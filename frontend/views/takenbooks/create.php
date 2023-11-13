<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Takenbooks $model */

$this->title = 'Create Takenbooks';
$this->params['breadcrumbs'][] = ['label' => 'Takenbooks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="takenbooks-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
