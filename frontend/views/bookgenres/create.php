<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Bookgenres $model */

$this->title = 'Create Bookgenres';
$this->params['breadcrumbs'][] = ['label' => 'Bookgenres', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bookgenres-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
