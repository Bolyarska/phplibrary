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

<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemOptions' => ['class' => 'item mb-1'],
    'itemView' => function ($model, $key, $index, $widget) {
        $url = ['bygenre', 'genreId' => $model->id];

        $card = '<div class="shadow card custom-card col-lg-6">' .
            '<div class="card-body">' .
            '<div class="row d-flex align-items-center">' .
            '<div class="col-lg-8">' .
            Html::a(Html::encode($model->title), ['books/view', 'id' => $model->id], ['class' => 'card-link']) .  // links to the details of each book
            '</div>' .
            '<div class="col-lg-4 text-end">' .
            '<div class="book-buttons">';
        $card .= '</div>' .
        '</div>' . // Closing div for col-lg-4
        '</div>' . // Closing div for row
        '</div>' . // Closing div for card-body
        '</div>'; // Closing div for custom-card

        return '<div class="row justify-content-center">' . $card . '</div>';
    },
    'layout' => "{items}\n", // Display only the items without pagination
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
