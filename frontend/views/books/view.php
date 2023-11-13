<?php

use common\models\Books;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap5\Carousel;

/** @var yii\web\View $this */
/** @var common\models\Books $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Books', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

\yii\web\YiiAsset::register($this);

$current_user_id = Yii::$app->session->get('user_id_in_basket');

$errorMessage = 'To add books to a profile, please select a reader from the All Readers Section';

?>

<div class="container pt-0">
    <div class="text-center">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php if ($current_user_id != null): ?>
            <span class="add-to-basket-btn">
                <?= Html::a('Add to Reader\'s basket', ['savedbooks/add-to-basket',
                    //'user_id' => $current_user_id, not needed
                    'book_id' => $model->id,
                    'book_title' => $model->title],
                    ['class' => 'btn btn-success btn-outline-dark text-white', 'data' => ['method' => 'post']]) ?>
            </span>
        <?php endif; ?>
    </div>

    <?php if ($current_user_id == null): ?>
        <div class='text-center'>
            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
            Add to Reader's Basket
            </button>

            <!-- modal -->
            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Please select a reader from the All Readers section</h6>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success btn-outline-dark text-white btn-sm" onclick="goToAllReaders()">Go to All Readers section</button>
                </div>
                </div>
            </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row text-center justify-content-sm-center justify-content-md-center justify-content-lg-center mt-2">
        <div class="col-12">
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-warning btn-outline-dark']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ]
            ]) ?>
        </div>
    </div>
</div>

<div class="container pt-0">
    <div class='row justify-content-center'>
        <div class="col-10 col-sm-8 col-md-5 col-lg-3">
            <div class="shadow card">
                <?php
                $bookModel = Books::findOne($model['id']); // Retrieve the Book model
                $images = unserialize($bookModel->images);

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
                            'number_in_stock',
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

                    <!--description container-->
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

<script>
window.onload = function () {
    // hide the full description
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


<script>
    function goToAllReaders() {
        window.location.href = '/user/readers';
    }
</script>

<style>
    /*prevent image stretching */
.carousel-image {
    object-fit: contain;
    max-width: 100%;
    height: 100%;
}
</style>

