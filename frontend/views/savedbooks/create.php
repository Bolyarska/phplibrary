<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Savedbooks $model */

$this->title = 'Create Savedbooks';
$this->params['breadcrumbs'][] = ['label' => 'Savedbooks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="savedbooks-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
