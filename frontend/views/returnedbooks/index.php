<?php

use common\models\Returnedbooks;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\ReturnedbooksSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Returnedbooks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="returnedbooks-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'reader_id',
            'book_id',
            'book_quantity',
            'date_taken',
            'date_to_return',
            'date_returned',
            /*[
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Returnedbooks $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],*/
        ],
    ]); ?>


</div>
