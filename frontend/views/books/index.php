<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var common\models\BooksSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'All Books';
$genres = [];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="books-index">

    <h1 style="text-align: center;"><?= Html::encode($this->title) ?></h1>

    <?php if (isset($searchModel)) {
        echo $this->render('_search', ['model' => $searchModel]);
    } ?>

    <?php if (Yii::$app->user->can('view-employee-pages')): ?>
        <div class="container text-center">
            <?= Html::a('Add a Book' . ' ' . '<i class="bi bi-database-fill-add"></i>', ['create'], ['class' => 'btn btn-warning btn-outline-dark']) ?>
    <?php endif; ?>

    <div class="col-12 text-end" style='padding-right: 5px'>
        <div class="btn-group btn-group-sm" role="group" aria-label="Button Group">
            <?= $sort->link('title', ['class' => 'btn btn-light btn-outline-dark']) .  
            $sort->link('author', ['class' => 'btn btn-light btn-outline-dark']); ?>
        </div>
    </div>

    </div>

    <br>

    <div class="container">
        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'itemOptions' => ['class' => 'row col-md-5 col-lg-4 mb-1'],
            'itemView' => function ($model, $key, $index, $widget) {
                $canView = Yii::$app->user->can('view-employee-pages');

                $genres = [];
                foreach ($model->bookgenres as $bookgenre) {
                    $genres[] = $bookgenre->genre->genre;
                }

                $genres = implode(', ', $genres);

                $description = substr($model->description, 0, 80);

                $card = '<div class="shadow card custom-card">' .
                    '<div class="card-body">' .
                    '<div class="row">' . // d-flex align-items-center
                    '<div class="col-12 mb-2">' .
                    Html::a(Html::tag('strong', Html::encode($model->title)), ['view', 'id' => $model->id], ['class' => 'card-link']) .
                    '</div>'
                    .
                    '<div class="col-6 col-md-6" style="font-style: italic;">' .
                    Html::a(Html::encode($model->author), ['view', 'id' => $model->id], ['class' => 'card-link']) . ' ' .
                    '<div class="pt-1" style="color:gray;">' .
                    '<em>' . $genres . '</em>' .
                    '</div>' . ' ' .
                    '<div class="book-description pt-1">' . 
                    $description . '...' .
                    '</div>' . 
                    '</div>';
                
                if ($canView) {
                    $card .= '<div class="col-md-6 text-end">' .
                        '<div class="book-buttons">' .
                        Html::a('<i class="bi bi-eye-fill"></i>', ['view', 'id' => $model->id], ['class' => 'btn btn-success btn-outline-dark']) . ' ' .
                        Html::a('<i class="bi bi-pencil-fill"></i>', ['update', 'id' => $model->id], ['class' => 'btn btn-outline-secondary']) .
                        '</div>' .
                        '</div>'; // Closing div for col-md-6 text-end
                } else {
                    
                    $images = unserialize($model->images);
                    $imageUrl = null;
                    
                    if (!empty($images)) {
                        $firstImage = reset($images);
                        $imageUrl = Yii::getAlias('@web/' . $firstImage);
                    } 
                
                    $card .= '<div class="col-6 col-md-6 col-lg-6 text-end", style="display: block; background-size: cover;
                    background-position: center center;">';

                    $card .= "<div class='image'>" . 
                    "<a href='" . Yii::$app->urlManager->createUrl(['books/view', 'id' => $model->id]) . 
                    "' class='img img-fluid' style='background-image: url(\"" . $imageUrl . "\"); 
                    display: block; width: 180px; height: 240px; background-size: cover; background-position: center center; border-radius: 3px'></a>"
                    . "</div>";

                    $card .= "<div class='book-isbn text-center pt-1'>" . $model->isbn . "</div>";
                    
                    $card .= '</div>';
                      
                } ?>
                
                <?php

                $card .= '</div>' . // Closing div for row
                    '</div>' . // Closing div for card-body
                    '</div>'; // Closing div for custom-card

                return $card;
            },
            'layout' => "<div class='row d-flex justify-content-evenly'>{items}</div>\n", // Display items in a row without a wrapper
        ]) ?>
    </div>

    <br>

    <!-- Custom Pagination -->
    <div class="row">
        <div class="col-12">
            <nav aria-label="Page navigation" class="d-flex justify-content-center">
                <ul class="pagination">
                    <?= LinkPager::widget([
                        'pagination' => $dataProvider->pagination,
                        'prevPageCssClass' => 'page-item ' . ($dataProvider->pagination->getPage() === 0 ? 'disabled' : ''),
                        'nextPageCssClass' => 'page-item ' . ($dataProvider->pagination->getPageCount() - 1 === $dataProvider->pagination->getPage() ? 'disabled' : ''),
                        'linkOptions' => ['class' => 'page-link'],
                        'disabledListItemSubTagOptions' => ['class' => 'btn btn-dark disabled'],
                        'maxButtonCount' => 5,
                    ]) ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<style>

.col-md-6.text-end img {
    max-width: 100%; /* Make sure the image does not exceed its parent container's width */
    max-height: 100%; /* Make sure the image does not exceed its parent container's height */
    object-fit: contain; /* Adjust the value as needed (contain, cover, fill, etc.) */
    display: flex; /* Use flexbox to center the image */
    justify-content: center;
}



</style>




