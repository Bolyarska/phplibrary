<?php

/* @var yii\web\View $this */

use frontend\models\Takenbooks;
use common\models\Books;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = 'BookWorms';

// Fetch all the book IDs in an array from the $books array passed from the SiteController
$bookIds = array_column($books, 'id');

// Retrieve all the Book models with the wanted ids
$bookModels = Books::find()
    ->where(['id' => $bookIds])
    ->all();

?>

<div class="site-index text-center">
    <div class="p-4 mb-4 bg-transparent rounded-3">
        <div class="container-fluid text-center">
            <h1 class="display-4">Welcome to BookWorms!</h1>
            <p class="fs-5 fw-light">Choose from our readers' picks or explore our book catalogue</p>
            <br>
            <p><?= Html::a('See the Book Catalogue', ['books/index'], ['class' => ' shadow btn btn-lg btn-success']) ?></p>
        </div>
    </div>

    <div class="body-content">
        <div class="container">
            <div class="row justify-content-center" style="padding-top:20px">

                <?php $count = 0; ?>
                <?php foreach ($books as $book): ?>
                    <?php if ($count % 5 === 0): ?>
                        </div>
                        <div class="row justify-content-center" style="padding-top:20px">
                    <?php endif; ?>

                    <?php
                    // Determine the appropriate column class based on the screen size
                    $col_class_large = 'col-lg-2'; // 5 books per row for large screens
                    $col_class_medium = 'col-md-3'; // 5 books per row for medium screens
                    $col_class_small = 'col-sm-6'; // 2 books per row for small screens
                    $col_class_smallest = 'col-12'; // 1 book per row for the smallest screens

                    // Use different column classes based on the screen size
                    $col_class = "$col_class_large $col_class_medium $col_class_small $col_class_smallest";
                    ?>

                    <div class="<?= $col_class ?>">
                        <div class="shadow card d-flex" style="width:100%; height:100%">
                        
                            <?php
                            $bookModel = $bookModels[array_search($book['id'], array_column($bookModels, 'id'))]; // choose the Book from the created array with ids
                            $images = unserialize($bookModel->images);
                            ?>

                            
                            <?php $imageUrl = null;
                            if (!empty($images)) {
                                $firstImage = reset($images); // Get the first image from the array of images for each $bookModel
                                $imageUrl = Yii::getAlias('@web/' . $firstImage); // Get the full URL of the image
                            } ?>
                            
                            <div class="image-container" style="position: relative; padding-bottom: 150%;">
                                <?php echo Html::a(Html::img($imageUrl, [
                                    'class' => 'card-img-top',
                                    'alt' => 'Book image',
                                    'style' => 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;',
                                ]), ['books/view', 'id' => $bookModel->id]);
                                ?>
                            </div>
                        
                            <div class="card-body">
                                <h4 class="card-title">
                                    <?php
                                    $title = $bookModel->title;
                                    $maxLength = 21; // Maximum number of characters to display
                                    if (strlen($title) > $maxLength) {
                                        $title = substr($title, 0, $maxLength) . '...'; // Truncate the title and add ellipsis
                                    }
                                    echo $title;
                                    ?>
                                </h4>
                                <p class="card-text"><?= $bookModel->author ?></p>
                                <?= Html::a('More details', ['books/view', 'id' => $bookModel->id], ['class' => 'btn btn-success']) ?>
                            </div>
                        </div>
                    </div>

                    <?php $count++; ?>
                <?php endforeach; ?>
            </div>

            <br>

            <!-- Pagination outside the row -->
            <div class="row">
                <div class="col-12">
                    <nav aria-label="Page navigation" class="d-flex justify-content-center">
                        <ul class="pagination">
                            <?php echo LinkPager::widget([
                                'pagination' => $pages,
                                'prevPageCssClass' => 'page-item ' . ($pages->getPage() === 0 ? 'disabled' : ''),
                                'nextPageCssClass' => 'page-item ' . ($pages->getPageCount() - 1 === $pages->getPage() ? 'disabled' : ''),
                                'linkOptions' => ['class' => 'page-link'],
                                'disabledListItemSubTagOptions' => ['class' => 'btn btn-dark disabled'],
                                'maxButtonCount' => 5,
                            ]) ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    text-decoration: none;
    transition: transform 0.3s ease-in-out;
}

.card:hover {
    transform: translateX(15px);
}

/* CSS for hiding the card body on small screens */
@media (max-width: 930px) { /* You can adjust the maximum screen width to target different screen sizes */
    .card-body {
        display: none;
    }
}
</style>

<script>
    $(document).ready(function() {
        // Make the image clickable on small screens
        $('.card-body').prev('.card-img-top').click(function() {
            window.location.href = $(this).parent().attr('href');
        });
    });
</script>