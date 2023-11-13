<?php
use common\models\Savedbooks;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Books;
use yii\web\Session;

/** @var yii\web\View $this */
/** @var common\models\SavedbooksSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Saved Books';
$this->params['breadcrumbs'][] = $this->title;

\yii\web\YiiAsset::register($this);
?>

<?php if ($dataProvider->totalCount > 0): ?>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class='container pt-0'>
    <div class="col-12 col-sm-10 col-md-10 col-lg-6 mx-auto p-0">
        <h1 class='text-center'><?= Html::encode($dataProvider->models[0]->reader->name_surname) . '\'s' ?></h1>
        <h2 class='text-center'><?= Html::encode($this->title) ?></h2>
        <p class='text-center'>
            <?= Html::a('Save books <i class="bi bi-folder-plus"></i>', ['/books/index'], ['class' => 'btn btn-success btn-outline-dark text-white']) ?>
        </p>

        <div class="shadow card">
            <div class="card-body" style='padding: 0px'>
                <div class="table-responsive">
                    <table class="table table-hover table-borderless">
                        <thead class='text-center'>
                            <tr>
                                <th class="d-none d-md-table-cell">ID</th>
                                <th class='col-2 text-start'>Title</th>
                                <th class='col-2 text-end'>Quantity</th>
                                <th>Saved on</th>
                                <th>Save Expiry</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dataProvider->getModels() as $model): ?>
                                <tr>
                                    <td class="d-none d-md-table-cell text-center"><?= $model->reader->id ?></td>
                                    <td class='text-start'><?= Html::a($model->book->title, ['books/view', 'id' => $model->book_id]) ?></td>
                                    <td class='text-end'><?= $model->book_quantity ?></td>
                                    <td class="text-center"><?= (new DateTime($model->date_saved))->format('m/d/Y') ?></td>
                                    <td class='text-center'><?= (new DateTime($model->expiration_time))->format('m/d/Y') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 text-center" style='padding-top:10px'>
            <?= Html::beginForm(['savedbooks/reinstate-saved-books', 'user_id' => $reader_id], 'post', ['id' => 'cancel-order-form', 'class' => 'd-inline']) ?>
            <?= Html::hiddenInput('saved_books_array', json_encode($saved_books_array, true)) ?>
            <?= Html::submitButton('Cancel Order', ['class' => 'btn btn-danger', 'onclick' => 'return confirm("Are you sure you want to cancel your order?");']) ?>
            <?= Html::endForm() ?>

            <?php if (Yii::$app->user->can( 'view-employee-pages' )): ?>
                <?= Html::beginForm(['takenbooks/give-to-reader', 'user_id' => $reader_id], 'post', ['id' => 'give-to-reader-form', 'class' => 'd-inline']) ?>
                <?= Html::hiddenInput('saved_books_array', json_encode($saved_books_array, true)) ?>
                <?= Html::submitButton('Give to reader', ['class' => 'btn btn-success btn-outline-dark text-white']) ?>
                <?= Html::endForm() ?>
            <?php endif; ?>
        </div>
    </div>
    </div>
<?php else: ?>
    <div class='text-center'>
        <h4>This reader has no saved books</h4>
        <p><?= Html::a('Save Books <i class="bi bi-folder-plus"></i>', ['books/index'], ['class' => 'btn btn-lg btn-success']) ?></p>
    </div>
<?php endif; ?>
