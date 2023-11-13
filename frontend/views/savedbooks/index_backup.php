<?php
use common\models\Savedbooks;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Books;
use yii\web\Session;

/** @var yii\web\View $this */
/** @var common\models\SavedbooksSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'All Saved Books';
$this->params['breadcrumbs'][] = $this->title;

\yii\web\YiiAsset::register($this);

?>
<div class="col-12 col-sm-10 col-md-10 col-lg-6 text-center mx-auto p-0">
    <h1><?= Html::encode($this->title) ?></h1>

    <br>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="shadow card" style='border: 0px'>
        <div class="card-body" style='padding: 0px'>
            <div class="table-responsive">
                <table class="table table-hover table-borderless">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">ID</th>
                            <th>Name</th>
                            <th>Title</th>
                            <th>Quantity</th>
                            <th class="d-none d-md-table-cell">Saved on</th>
                            <th>Save Expiry</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataProvider->models as $model): ?>
                            <tr>
                                <td class="d-none d-md-table-cell"><?= $model->reader->id ?></td>
                                <td><?= Html::a($model->reader->name_surname, ['savedbooks/show-reader-saved-books', 'id' => $model->reader->id]) ?></td>
                                <td><?= Html::a($model->book->title, ['books/view', 'id' => $model->book_id]) ?></td>
                                <td><?= $model->book_quantity ?></td>
                                <td><?= (new DateTime($model->date_saved))->format('d/m/Y') ?></td>
                                <td><?= (new DateTime($model->expiration_time))->format('d/m/Y') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if ($dataProvider->totalCount > 0): ?>
        <div class='text-center' style='padding-top:10px'>
            <?= Html::beginForm(['savedbooks/reinstate-saved-books', 'user_id'], 'post', ['id' => 'cancel-order-form']) ?>
            <?= Html::hiddenInput('saved_books_array', json_encode($saved_books_array, true)) ?>
            <?= Html::submitButton('Cancel Orders', ['class' => 'btn btn-success btn-outline-dark text-white', 'onclick' => 'return confirm("Are you sure you want to cancel all orders?");']) ?>
            <?= Html::endForm() ?>
        </div>
    <?php endif; ?>
