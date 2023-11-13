<?php
use common\models\Books;
use yii\helpers\Html;
use yii\helpers\FileHelper;
use yii\helpers\Url;
?>

<?php
$this->title = 'Basket' . ' ' . '<i class="bi bi-bag-heart"></i>';

/* Receives the following from SavedbooksController/actionUserbasket or actionSavechanges:

    return $this->render('userbasket', [
        'user_id_in_basket' => $user_id_in_basket,
        'book_id_in_basket' => $book_id_in_basket,
        'book_title_in_basket' => $book_title_in_basket,
        'book_quantity_in_basket' => $book_quantity_in_basket,
        'individual_title_count' => $individual_title_count,
        'books_in_basket' => $books_in_basket,
        'insufficient_books' => $insufficient_books
    ]);
}
*/

?>

<div class='container justify-content-center pt-0'>
<?php if (empty($books_in_basket)): ?>
    <div class='text-center'>
        <h4>There are no books in your basket!</h4>
        <p><?= Html::a('See our Book Catalogue', ['books/index'], ['class' => 'btn btn-lg btn-success']) ?></p>
    </div>
<?php else: ?>
    <div class='row text-center'><h2><?= $this->title ?></h2></div>
    <div class="col-12 col-sm-12 col-md-10 col-lg-6 user-view mx-auto">
    <div class="shadow card" style='max-width: 600px; margin: auto'>
    <div class="card-body">
    <form action="<?= Url::to(['savedbooks/savechanges']) ?>" method="POST">
        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
        <table class="table table-success table-striped table-hover table-responsive">
            <tr>
                <th>Title</th>
                <th>Quantity</th>
            </tr>
            <?php foreach ($books_in_basket as $book_id => $bookData): ?>
                <?php $book = $bookData['book']; ?>
                <?php $quantity = $bookData['quantity']; ?>
                <?= Yii::info("quantity: " . $quantity, 'app'); ?>

                <?php
                $numberAvailable = $book->number_available;
                $inputOptions = [
                    'id' => $book_id,
                    'class' => 'form-control',
                    'data-id' => $book_id,
                    'min' => 0,
                    'max' => $numberAvailable,
                    'name' => "_quantity[$book_id]",
                ];
                ?>

                <tr>
                    <td><?= $book->title ?></td>
                    <td style='width:120px'><?= Html::input('number', "_quantity[$book_id]", $quantity, $inputOptions);?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <table class="table table-success table-striped">
            <tr>
                <td class="align-middle"><h4>Total Books:</h4></td>
                <td class="align-middle text-end"><h4><?= $book_quantity_in_basket ?></h4></td>
            </tr>
        </table>
    </div>
    </div>

        <div class="text-center mt-1">
        <p style='color:red'> Please save any changes before confirming your order.</p>
        <form action="<?= Url::to(['savedbooks/savechanges']) ?>" method="POST" style="display: inline;">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
            <?= Html::submitButton('Save changes', ['class' => 'btn btn-success btn-outline-dark text-white']) ?>
        </form>
        <form action="<?= Url::to(['savedbooks/create']) ?>" method="POST" style="display: inline;">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
            <?= Html::submitButton('Confirm your order', ['class' => 'btn btn-success btn-outline-dark text-white']) ?>
        </form>
        </div>
    </div>
    </div>

<?php endif; ?>
</div>







