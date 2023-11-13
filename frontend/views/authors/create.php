<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Authors $model */

$this->title = 'Add a new author';
?>
<div class="authors-create">

    <h1 class='text-center'><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
