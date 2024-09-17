<?php

/* @var yii\web\View $this */
use common\models\Books;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use Yii;

$this->title = 'BookWorms';

// Fetch all the book IDs in an array from the $books array passed from the SiteController
$bookIds = array_column($books, 'id');

// Retrieve all the Book models with the wanted ids
$bookModels = Books::find()
    ->where(['id' => $bookIds])
    ->all();

?>

<div class="site-index text-center">
    <div class="mb-3 bg-transparent rounded-3">
        <div class="container-fluid text-center">
            <h1 class="display-4">Welcome to BookWorms!</h1>
            <p class="fs-5 fw-light">Choose from our readers' picks or explore our book catalogue</p>
            <br>
            <p><?= Html::a('See the Book Catalogue', ['books/index'], ['class' => ' shadow btn btn-lg btn-success btn-outline-dark text-white']) ?></p>
        </div>
    </div>

    <div class="container">
    

        <?php $count = 0; ?>
            <?php foreach ($books as $book): ?>
                <?php if ($count % 5 === 0): ?>
                    </div>
                    <div class="row justify-content-center" style="padding-top:20px;">
                <?php endif; ?>
                
                <?php
                
                $col_class_large = 'col-lg-2'; // 5 books per row for large screens
                $col_class_medium = 'col-md-4';
                $col_class_small = 'col-sm-6';
                $col_class_smallest = 'col-12';

                
                $col_class = "$col_class_large $col_class_medium $col_class_small $col_class_smallest";
                ?>

                <div class="<?= $col_class ?> img-container p-0 mb-1" style='width: 250px; height: 300px;'>
                    
                    <?php
                    $bookModel = $bookModels[array_search($book['id'], array_column($bookModels, 'id'))];
                    $images = unserialize($bookModel->images);
                    ?>

                    <?php
                    $imageUrl = null;
                    if (!empty($images)) {
                        $firstImage = reset($images);
                        $imageUrl = Yii::getAlias('@web/' . $firstImage);
                    }
                    ?>

                    
                    <div class='image'>
                        <a href="<?= Yii::$app->urlManager->createUrl(['books/view', 'id' => $bookModel->id]) ?>" class="link-with-background-image"
                        style="background-image: url('<?= $imageUrl ?>'); display: block; width: 220px; height: 300px; background-size: cover;
                        background-position: center center; border-radius: 3px">  
                        </a>
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
.img-container {
    position: relative;
    text-decoration: none;
    transition: transform 0.3s ease-in-out;
    display: flex;
    justify-content: center;
    align-items: center;
}

.img-container:hover {
    transform: translateX(15px);
}



</style>

