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

<?php if ($dataProvider->totalCount > 0): ?>
<div class='container pt-0'>
<div class="col-12 col-sm-10 col-md-10 col-lg-6 d-none d-lg-block text-center mx-auto p-0">
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
                            <th class='text-start'>Name</th>
                            <th class='text-start'>Title</th>
                            <th class='text-end'>Quantity</th>
                            <th class="d-none d-md-table-cell">Saved on</th>
                            <th>Save Expiry</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataProvider->models as $model): ?>
                            <tr>
                                <td class="d-none d-md-table-cell"><?= $model->reader->id ?></td>
                                <td class='text-start'><?= Html::a($model->reader->name_surname, ['savedbooks/show-reader-saved-books', 'id' => $model->reader->id]) ?></td>
                                <td class='text-start'><?= Html::a($model->book->title, ['books/view', 'id' => $model->book_id]) ?></td>
                                <td class='text-end'><?= $model->book_quantity ?></td>
                                <td><?= (new DateTime($model->date_saved))->format('m/d/Y') ?></td>
                                <td><?= (new DateTime($model->expiration_time))->format('m/d/Y') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="col-10 d-block d-lg-none text-center mx-auto p-0">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php if ($dataProvider->totalCount === 0): ?>
        <?= 'No saved books to show' ?>
    <?php else: ?>

    <br>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php foreach ($dataProvider->getModels() as $model) { ?>
        <div class="shadow card mb-3">
            <div 
                class="card-header expiry-header">expires on <?= (new DateTime($model->expiration_time))->format('m/d/Y') ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <?= Html::a($model->reader->name_surname, ['savedbooks/show-reader-saved-books', 'id' => $model->reader->id]) ?>
                    </div>
                    <div class="col-12">
                        <?= Html::a($model->book->title, ['books/view', 'id' => $model->book_id]) ?>
                    </div>
                    <div class="col-12">
                        Quantity: <?= Html::encode($model->book_quantity) ?>
                    </div>
                    <div class="col-12">
                        Taken on: <?= (new DateTime($model->date_saved))->format('m/d/Y') ?>
                    </div>
                    <div class="col-12 pb-2">
                        Due on: <?= (new DateTime($model->expiration_time))->format('m/d/Y') ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php endif; ?>
</div>

<?php if ($dataProvider->totalCount > 0): ?>
        <div class='text-center' style='padding-top:10px'>
            <?= Html::beginForm(['savedbooks/reinstate-saved-books', 'user_id'], 'post', ['id' => 'cancel-order-form']) ?>
            <?= Html::hiddenInput('saved_books_array', json_encode($saved_books_array, true)) ?>
            <?= Html::submitButton('Cancel All Orders', ['class' => 'btn btn-success btn-outline-dark text-white', 'onclick' => 'return confirm("Are you sure you want to cancel all orders?");']) ?>
            <?= Html::endForm() ?>
        </div>
    <?php endif; ?>
</div>

<?php else: ?>
    <div class='text-center'>
        <h4>No saved books to manage</h4>
        <p><?= Html::a('Save Books <i class="bi bi-folder-plus"></i>', ['books/index'], ['class' => 'btn btn-lg btn-success']) ?></p>
    </div>
<?php endif; ?>

<style>
    .card-header.expiry-header{
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 18px;
    color: white;
    background-color: #198754;
    font-weight: bold;
    text-transform: uppercase;
}

</style>