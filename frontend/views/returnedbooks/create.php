<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Returnedbooks $model */

$this->title = 'Create Returnedbooks';
$this->params['breadcrumbs'][] = ['label' => 'Returnedbooks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="returnedbooks-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
