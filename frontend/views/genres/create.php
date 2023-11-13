<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Genres $model */

$this->title = 'Add a genre';
$this->params['breadcrumbs'][] = ['label' => 'Genres', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="genres-create">

    <h1 class='text-center'><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
