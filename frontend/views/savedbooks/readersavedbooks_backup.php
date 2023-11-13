<?php

use common\models\Savedbooks;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use common\models\Books;
use yii\web\Session;


/** @var yii\web\View $this */
/** @var common\models\SavedbooksSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Saved Books';
$this->params['breadcrumbs'][] = $this->title;

// also receives all saved_books_array of that user -> [{"book_id":3,"quantity":1,"date_saved":"2023-07-14 11:00:45"}] and $reader_id

\yii\web\YiiAsset::register($this);
?>

<?php if ($dataProvider->totalCount > 0): ?>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="col-12 col-sm-10 col-md-8 col-lg-6 text-center mx-auto p-0">
    <h1><?= Html::encode($this->title) ?></h1>
        <p>
            <?= Html::a('Save books <i class="bi bi-folder-plus"></i>', ['/books/index'], ['class' => 'btn btn-success btn-outline-dark text-white']) ?>
        </p>

    <div class="shadow card">
    <div class="card-body" style='padding: 0px'>
        <table class="card-header table table-striped table-responsive">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Quantity</th>
                    <th>Date Saved</th>
                    <th>Save Expiration</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dataProvider->getModels() as $model): ?>
                    <tr>
                        <td><?= $model->reader->id ?></td>
                        <td><?= Html::a($model->book->title, ['books/view', 'id' => $model->book_id]) ?></td>
                        <td><?= $model->book_quantity ?></td>
                        <td><?= $model->date_saved ?></td>
                        <td><?= $model->expiration_time ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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
<?php else: ?>
    <div class='text-center'>
        <h4>This reader has no saved books!</h4>
        <p><?= Html::a('Save Books <i class="bi bi-folder-plus"></i>', ['books/index'], ['class' => 'btn btn-lg btn-success']) ?></p>
    </div>
<?php endif; ?>
    
</div>

<style>
    @media (max-width: 991px) {
        .table-responsive th:first-child {
            display: none;
        }
        .table-responsive tbody tr td:first-child {
            display: none;
        }

        .table-responsive th:nth-child(3) {
            display: none;
        }

        /* Hide the 3rd column in the table body */
        .table-responsive tbody tr td:nth-child(3) {
            display: none;
        }
            }
</style>