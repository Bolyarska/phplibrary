<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap5\Carousel;

/** @var yii\web\View $this */
/** @var common\models\Books $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Books', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$user = Yii::$app->user; // get the user
$user_id = $user->id ?? null; // get the user's id or null if guest

?>

<div class="body-content">

    <div class='d-flex justify-content-center' style='margin-top:50px'>
        <h1 class='text-center'><?= Html::encode($this->title) ?></h1>
        <?php if ($user_id === null) : ?>
            <div class="d-flex align-items-center" style="padding-left: 20px;">
                <?= Html::a('Login to save', ['/site/login'], ['class' => 'shadow btn btn-warning text-center']) ?>
            </div>
        <?php else : ?>
            <div class="d-flex align-items-center" style="padding-left: 20px;">
                <?= Html::a('Add to Basket', ['savedbooks/add-to-basket',
                    'user_id' => $user_id,
                    'book_id' => $model->id,
                    'book_title' => $model->title],
                    ['class' => 'btn btn-success btn-outline-dark', 'data' => ['method' => 'post']]) ?>
            </div>
        <?php endif; ?>
    </div>

    <br>

    <div class="container pt-0">
        <div class='row justify-content-center'>
            <div class="col-10 col-sm-8 col-md-5 col-lg-3">
                <div class="shadow card">
                    <?php
                    $images = unserialize($model->images);
                    if (!empty($images)) {
                        echo Carousel::widget([
                            'items' => array_map(function ($image) {
                                $imageUrl = Yii::getAlias('@web/' . $image);
                                return [
                                    'content' => Html::img($imageUrl, [
                                        'class' => 'carousel-image d-block w-100',
                                        'style' => 'height: 500px; width: auto;',
                                        'alt' => 'Book image',
                                    ]),
                                    'options' => ['class' => 'carousel-item'],
                                ];
                            }, $images),
                            'options' => ['class' => 'carousel slide'],
                            'controls' => [
                                '<span class="carousel-control-prev-icon" aria-hidden="true"></span>',
                                '<span class="carousel-control-next-icon" aria-hidden="true"></span>',
                            ],
                        ]);
                    }
                    ?>
                </div>
            </div>

            <div class="col-10 col-sm-8 col-md-6 col-lg-4">
                <div class="shadow card" style='height: 100%'>
                    <div class="card-header">
                        <h5 class="card-title text-center">Details</h5>
                    </div>
                    <div class="card-body" style='padding:0px'>
                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                
                                [
                                    'attribute' => 'author',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        return Html::a(Html::encode($model->author), ['books/index', 'author' => $model->author]);
                                    }
                                ],

                                [
                                    'attribute' => 'isbn',
                                    'label' => 'ISBN'
                                ],
                                'publisher',
                                'language',
                                'pages',
                                'number_available',

                                [
                                    'label' => 'Genres',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        $genres = [];
                                        foreach ($model->bookgenres as $bookgenre) {
                                            $genreName = $bookgenre->genre->genre;
                                            $genreUrl = Yii::$app->urlManager->createUrl(['/bookgenres/bygenre', 'genreId' => $bookgenre->genre->id]);
                                            $genres[] = Html::a($genreName, $genreUrl);
                                        }
                                        return implode(', ', $genres);
                                    },
                                ],
                            ],
                            'options' => [
                                'class' => 'table table-striped',
                            ],
                        ]) ?>

                        <!-- description -->
                        <div class="container p-2">
                            <div class='row'>
                                <div class='col-7'>
                                    <button id="showDescriptionButton" class="btn btn-sm btn-success btn-outline-dark text-white" onclick="toggleDescription()">Show Description</button>
                                </div>
                            </div>
                        
                            <div id="partialDescription" style="display: block; margin-top:10px">
                                <?= substr($model->description, 0, 100) ?>... <a href="#" onclick="toggleDescription()"></a>
                            </div>
                            <div id="fullDescription" style="display: none; margin-top:10px">
                                <?= $model->description ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    
.carousel-image {
    object-fit: contain;
    max-width: 100%;
    height: 100%;
}
</style>

<script>
window.onload = function () {
    // Initially hide the full description, no need to hide partialDescription
    var fullDescription = document.getElementById("fullDescription");
    fullDescription.style.display = "none";
};

function toggleDescription() {
    var partialDescription = document.getElementById("partialDescription");
    var fullDescription = document.getElementById("fullDescription");
    var showDescriptionButton = document.getElementById("showDescriptionButton");

    if (partialDescription.style.display === "block") {
        partialDescription.style.display = "none";
        fullDescription.style.display = "block";
        showDescriptionButton.innerText = "Hide Description";
    } else {
        partialDescription.style.display = "block";
        fullDescription.style.display = "none";
        showDescriptionButton.innerText = "Show Description";
    }
}

</script>
