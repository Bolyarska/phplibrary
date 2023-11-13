<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="container site-error text-center">

    <h1><?= Html::encode($this->title) ?></h1>

   <!-- <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div> -->

    <p>
        Oops! Seems like something went wrong.
    </p>

    <p>
        <h1><i class="bi bi-bricks"></i><i class="bi bi-bricks"></i><i class="bi bi-bricks"></i><i class="bi bi-bricks"></i></h1>
    </p>

</div>
