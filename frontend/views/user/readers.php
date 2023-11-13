<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Readers';

?>

<div class="search-bar">

    <h1 style="text-align: center;"><?= Html::encode($this->title) ?></h1>

    <?php
    if (isset($searchModel)) {
        echo $this->render('_search', ['model' => $searchModel]);
    }
    ?>

</div>

<div class="col-12 text-center" style='margin-bottom:15px'>
    <?= Html::a('Add a Reader', ['create'], ['class' => 'btn btn-warning btn-outline-dark']) ?>
</div>

<div class="col-lg-12 text-center mx-auto">
    <div class="container">
        <div class="row justify-content-center">
            <?php foreach ($dataProvider->getModels() as $model): ?>
                <div class="col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-header text-center">
                            <?= Html::a($model->name_surname, ['user/view', 'id' => $model->id]) ?>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 text-start">
                                    <strong>Username:</strong>
                                </div>
                                <div class="col-6 text-end">
                                    <?= $model->username ?>
                                </div>
                                <div class="col-6 text-start">
                                    <strong>Email:</strong>
                                </div>
                                <div class="col-6 text-end">
                                    <?= Html::a($model->email) ?>
                                </div>
                                <div class="col-6 text-start">
                                    <strong>Number:</strong>
                                </div>
                                <div class="col-6 text-end">
                                    <?= Html::a($model->phone_number) ?>
                                </div>
                                <div class="col-6 text-start">
                                    <strong>Address:</strong>
                                </div>
                                <div class="col-6 text-end">
                                    <?= Html::a($model->address) ?>
                                </div>

                                <?php if ($model->note !== "" && $model->note !== null): ?>

                                <div class="col-6 text-start">
                                    <strong>Note:</strong>
                                </div>

                                <div class="col-6 text-end">
                                    <?php
                                    $maxLength = 20;
                                    $note = $model->note;
                                    if ($model->type !== 'Employee' || Yii::$app->user->can('view-admin-pages')) {
                                        if (strlen($note) > $maxLength) {
                                            $truncatedNote = '<i style="color: red;">' . substr($note, 0, $maxLength) . '...</i>';
                                            echo $truncatedNote;
                                        } else {
                                            echo '<i style="color: red;">' . $note . '</i>';
                                        }
                                    } else {
                                        echo '<i style="color: red;">' . 'Hidden'. '</i>';
                                    }
                                    ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-footer" style='min-height:100px'>
                            <div class='row'>
                                <div class="col-12">
                                    <?= $model->type ?>
                                </div>

                                <div class='col-12'>
                                    <?php
                                    $buttonText = 'Select';
                                    $isActive = false; // Default active state

                                    $user_id_in_basket = Yii::$app->session->get('user_id_in_basket');
                                    $book_quantity_in_basket = Yii::$app->session->get('book_quantity_in_basket') ?? [];

                                    if ($user_id_in_basket == $model->id) {
                                        $buttonText = 'Deselect';
                                        $isActive = true;
                                        // Disable the button if books are already in the basket
                                        $disableButton = !empty($book_quantity_in_basket);
                                    }

                                    if ($model->is_active == 1) {
                                        // Render the button with the onclick attribute calling the new function
                                        echo \yii\helpers\Html::button($buttonText, [
                                            'class' => 'btn btn-success btn-outline-dark btn toggle-button' . ($isActive ? ' active' : ''),
                                            'role' => 'button',
                                            'data-id' => $model->id,
                                            'data-disable' => isset($disableButton) && $disableButton ? 'true' : 'false',
                                            'onclick' => 'submitForm(' . $model->id . ');',
                                        ]);
                                    } else {
                                        echo '<strong style="color: red;">Inactive</strong>';
                                    }
                                    ?>

                                    <?= Html::a(
                                        '<span class="bi bi-eye-fill"></span>',
                                        ['view', 'id' => $model->id],
                                        [
                                            'title' => Yii::t('yii', 'View'),
                                            'class' => 'btn btn-success btn-outline-dark me-1',
                                        ]
                                    );
                                    if (($model->is_active == 1 && Yii::$app->user->can('view-admin-pages')) || $model->type !== 'Employee') {
                                        echo \yii\helpers\Html::a(
                                            '<span class="bi bi-pencil-fill"></span>',
                                            ['update', 'id' => $model->id],
                                            [
                                                'title' => Yii::t('yii', 'Update'),
                                                'class' => 'btn btn-warning btn-outline-dark me-1',
                                            ]
                                        );
                                    }
                                    if (Yii::$app->user->can('view-admin-pages') || $model->type !== 'Employee') {
                                        echo \yii\helpers\Html::a(
                                            '<span class="bi bi-trash-fill"></span>',
                                            ['delete', 'id' => $model->id],
                                            [
                                                'title' => Yii::t('yii', 'Delete'),
                                                'class' => 'btn btn-danger btn-outline-dark me-1',
                                                'data' => [
                                                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                                    'method' => 'post',
                                                ],
                                            ]
                                        );
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php
function truncateText($text, $maxLength) {
    if (strlen($text) > $maxLength) {
        $truncatedText = substr($text, 0, $maxLength) . '...</i>';
        return $truncatedText;
    } else {
        return $text;
    }
}
?>


<script>
  function submitForm(userId) {
    var form = document.createElement('form');
    form.method = 'post'; // Set the form method to POST
    form.action = '<?= Url::to(['savedbooks/toggle-user-session']) ?>'; // Set the form action URL

    // Create a hidden input field to hold the CSRF token
    var csrfTokenInput = document.createElement('input');
    csrfTokenInput.type = 'hidden';
    csrfTokenInput.name = '<?= Yii::$app->request->csrfParam ?>';
    csrfTokenInput.value = '<?= Yii::$app->request->getCsrfToken() ?>';

    // Create a hidden input field to hold the user ID
    var inputUserId = document.createElement('input');
    inputUserId.type = 'hidden';
    inputUserId.name = 'id';
    inputUserId.value = userId;

    // Append the CSRF token input field and the user ID input field to the form
    form.appendChild(csrfTokenInput);
    form.appendChild(inputUserId);

    // Append the form to the document and submit it
    document.body.appendChild(form);
    form.submit();
}
</script>

<style>
    th a {
        text-decoration: none;
        color: black;
    }

</style>

<style>
  .toggle-button.active {
    background-color: #28a745;
    border-color: #28a745;
  }
</style>

