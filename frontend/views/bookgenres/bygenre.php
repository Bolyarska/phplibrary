<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\LinkPager;
?>

<div style="text-align: center;">
    <?php echo '<h1>'  . Html::encode($genreName) . ' books' . '</h1>'; ?>
</div>

<!--
    <ul>
    <?php foreach ($books as $book): ?>
        <li><?= Html::encode($book->title) ?></li>
    <?php endforeach; ?>
</ul>
-->

<div class='container'>
<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemOptions' => ['class' => 'row col-md-6 col-lg-4 mb-1'],
    'itemView' => function ($model, $key, $index, $widget) {
        $url = ['bygenre', 'genreId' => $model->id];

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

        $card .= '</div>' . // Closing div for row
        '</div>' . // Closing div for card-body
        '</div>'; // Closing div for custom-card

        return $card;
    },
    'layout' => "<div class='row d-flex justify-content-evenly'>{items}</div>\n",
    ]) ?>

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
