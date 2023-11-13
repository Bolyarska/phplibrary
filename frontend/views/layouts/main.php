<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use common\models\User;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">

<?php $this->beginBody() ?>

<?php // Check if a session for a user has been opened
$user_id_in_basket = Yii::$app->session->get('user_id_in_basket', null); ?>

<header>
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top',
        ],
    ]);
    $menu_items_guest = [
        ['label' => 'All Books', 'url' => ['/books/index']],
        ['label' => 'Authors', 'url' => ['/authors/index']],
        ['label' => 'Genres', 'url' => ['/genres/index']],
        ['label' => 'About', 'url' => ['/site/about']],
        ['label' => 'Signup', 'url' => ['/site/signup']]
    ];

    $menu_items_admin = [
        ['label' => 'All Employees', 'url' => ['/user/employees']],
        ['label' => 'All Readers', 'url' => ['/user/readers']],
        ['label' => 'Saved Books', 'url' => ['/savedbooks/index']],
        ['label' => 'Expected Returns', 'url' => ['/takenbooks/index']],
        ['label' => 'All Books', 'url' => ['/books/index']],
        ['label' => 'Authors', 'url' => ['/authors/index']],
        ['label' => 'Genres', 'url' => ['/genres/index']],
    ];

    $menu_items_employee = [
        ['label' => 'All Readers', 'url' => ['/user/readers']],
        ['label' => 'Saved Books', 'url' => ['/savedbooks/index']],
        ['label' => 'Expected Returns', 'url' => ['/takenbooks/index']],
        ['label' => 'All Books', 'url' => ['/books/index']],
        ['label' => 'Authors', 'url' => ['/authors/index']],
        ['label' => 'Genres', 'url' => ['/genres/index']],
        
    ];

    $menu_items_reader = [
        ['label' => 'All Books', 'url' => ['/books/index']],
        ['label' => 'Authors', 'url' => ['/authors/index']],
        ['label' => 'Genres', 'url' => ['/genres/index']],
        ['label' => 'About', 'url' => ['/site/about']],
    ];

    if (Yii::$app->user->isGuest) {
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav me-auto mb-2 mb-md-0'],
            'items' => $menu_items_guest,
            'encodeLabels' => false,
        ]);

    } elseif (Yii::$app->user->can( 'view-admin-pages' )) {
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav me-auto mb-2 mb-md-0'],
            'items' => $menu_items_admin,
            'encodeLabels' => false,
        ]);

    } elseif (Yii::$app->user->can( 'view-employee-pages' )) {
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav me-auto mb-2 mb-md-0'],
            'items' => $menu_items_employee,
            'encodeLabels' => false,
        ]);
    } else { // reader
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav me-auto mb-2 mb-md-0'],
            'items' => $menu_items_reader,
            'encodeLabels' => false,
        ]);
    }

    if (Yii::$app->user->isGuest) {
        echo Html::tag('div', Html::a('Login', ['/site/login'], ['class' => 'btn btn-link login text-decoration-none']), ['class' => 'd-flex']);
    } else {
        
        $user = Yii::$app->user; // get the user

        $user_type = $user->identity->type;

        // Profile dropdown
        echo Html::beginTag('div', ['class' => 'dropdown']);
        echo Html::button('My Profile', [
            'class' => 'btn btn-secondary dropdown-toggle btn-no-background',
            'type' => 'button',
            'id' => 'profileDropdown',
            'data-bs-toggle' => 'dropdown',
            'aria-expanded' => 'false',
            'data-bs-display' => 'static',
        ]);
        echo Html::beginTag('ul', ['class' => 'dropdown-menu', 'aria-labelledby' => 'profileDropdown']);
        echo Html::tag('li', Html::a('My Saved Books', ['/savedbooks/show-my-saved-books'], ['class' => 'dropdown-item']));
        echo Html::tag('li', Html::a('My Expected Returns', ['/takenbooks/show-my-taken-books', 'id' => $user->identity->id], ['class' => 'dropdown-item']));
        echo Html::tag('li', Html::a('My Return History', ['/returnedbooks/show-my-returned-books'], [
            'class' => 'dropdown-item',
            'data' => ['method' => 'post'],
        ]));
        echo Html::tag('li', Html::a('Change Password', ['/user/changepassword'], ['class' => 'dropdown-item']));
        echo Html::tag('li', Html::a('Add 2-Step Verification', ['/user/two-factor-auth'], ['class' => 'dropdown-item']));
        echo Html::endTag('ul');
        echo Html::endTag('div');

        // Cart icon + total number of books in the reader's basket
        
        // Retrieve the book count from the session (or set to 0 if none)
        $savedbookscount = Yii::$app->session->get('book_quantity_in_basket', 0);
        
        $bag_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-bag-heart" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M10.5 3.5a2.5 2.5 0 0 0-5 0V4h5v-.5Zm1 0V4H15v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4h3.5v-.5a3.5 3.5 0 1 1 7 0ZM14 14V5H2v9a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1ZM8 7.993c1.664-1.711 5.825 1.283 0 5.132-5.825-3.85-1.664-6.843 0-5.132Z"/>
        </svg>';
        
        echo Html::a($bag_icon, ['/savedbooks/userbasket'], ['class' => 'cart-btn text-decoration-none', 'style' => 'vertical-align: middle; padding-left: 10px']);
        echo Html::tag('span', $savedbookscount, ['class' => 'badge', 'style' => 'vertical-align: middle; padding-top: 10px']);

        // Logout button
        echo Html::beginForm(['/site/logout'], 'post', ['class' => 'd-flex']);
        echo Html::submitButton(
            'Logout (' . Yii::$app->user->identity->username . ')',
            ['class' => 'btn btn-link logout text-decoration-none']
        );
        echo Html::endForm();
    }
    NavBar::end();
    ?>

</header>

<main role="main" class="flex-shrink-0">

    <?php $user_type = Yii::$app->user->identity->type ?? null; ?>

    <div class="container pb-0">

        <?php if ($user_type != null && ($user_type === 'Employee' || $user_type === 'Administrator')): ?>
            <div class='container d-flex justify-content-end align-items-end'>
                <div class='col-12 col-sm-12 col-md-5 col-lg-3'>
                    <?php if ($user_id_in_basket === null): ?>
                        <div class="shadow card">
                            <div class="card-body text-center p-0 pt-2">
                                <div class='row'><b><?= 'Current selected profile: '?></b></div>
                                <div class='row justify-content-center'><?= 'No selection' ?></div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php $selected_user = User::find()->where(['id' => $user_id_in_basket])->one();?>
                        <div class="shadow card">
                            <div class="card-header text-center">
                                <?= "Current selected profile:" ?>
                                <?= Html::a($selected_user->name_surname, ['user/view', 'id' => $selected_user->id]) ?>
                            </div>

                            <div class="card-body pb-1 pt-1">
                                <div class="col-12 text-center mt-1">
                                    <?php if ($selected_user->is_active == 1): ?>
                                        <?= Html::a('<span class="bi bi-pencil-fill"></span>', ['user/update', 'id' => $selected_user->id], ['class' => 'btn btn-warning btn-outline-dark btn-responsive btn-sm mt-1']) ?>
                                        <?php if ($user_id_in_basket == null): ?>
                                            <?= Html::a('Saved', ['savedbooks/show-reader-saved-books', 'id' => $user_id_in_basket], [
                                                'class' => 'btn btn-outline-dark btn-responsive btn-sm mt-1 custom-tooltip',
                                                'title' => 'Please select the reader first',
                                                'data-toggle' => 'tooltip',
                                                'style' => 'text-decoration: none; cursor:not-allowed;',
                                            ]) ?>

                                            <?= Html::a('Taken', ['takenbooks/show-reader-taken-books', 'id' => $user_id_in_basket], [
                                                'class' => 'btn btn-outline-dark btn-responsive btn-sm mt-1 custom-tooltip',
                                                'title' => 'Please select the reader first',
                                                'data-toggle' => 'tooltip',
                                                'style' => 'text-decoration: none; cursor:not-allowed;',
                                            ]) ?>

                                            <?= Html::a('History', ['returnedbooks/show-reader-returned-books', 'id' => $user_id_in_basket], [
                                                'class' => 'btn btn-outline-dark btn-responsive btn-sm mt-1 custom-tooltip',
                                                'title' => 'Please select the reader first',
                                                'data-toggle' => 'tooltip',
                                                'style' => 'text-decoration: none; cursor:not-allowed;',
                                            ]) ?>

                                        <?php else: ?>
                                            <?= Html::a('Saved', ['savedbooks/show-reader-saved-books', 'id' => $user_id_in_basket], ['class' => 'btn btn-success btn-outline-dark btn-responsive btn-sm mt-1 text-white']) ?>
                                            <?= Html::a('Taken', ['takenbooks/show-reader-taken-books', 'id' => $user_id_in_basket], ['class' => 'btn btn-success btn-outline-dark btn-responsive btn-sm mt-1 text-white']) ?>
                                            <?= Html::a('History', ['returnedbooks/show-reader-returned-books', 'id' => $user_id_in_basket], ['class' => 'btn btn-success btn-outline-dark btn-responsive btn-sm mt-1 text-white']) ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php echo '<strong style="color: red;">Inactive</strong>'; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <br>
        <?php endif; ?>

        <?= $content ?>
    </div>
</main>

<footer class="footer mt-auto py-3 text-muted">
    <div class="container">
        <div class="row">
            <div class="col-6">
                <p class="">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>
            </div>
            <div class="col-6 text-end">
                <p><?= 2023 ?></p>
            </div>
        </div>
    </div>
</footer>


<style>
    @media (min-width: 800px) and (max-width: 1200px) {
        body {
            padding-top: 70px;
        }
    }

    .help-block {
    color: red !important;
    position:block;
    height:10px;
    }
    
    

</style>

<script>
$(function () { 
    $("[data-toggle='tooltip']").tooltip(); 
});

</script>

<script>
    $(document).ready(function () {
        
        $(".alert").fadeIn();

        // timeout
        setTimeout(function () {
            $(".alert").fadeOut("slow", function () {
                $(this).remove();
            });
        }, 1000); // 1000 milliseconds = 1 second
    });
</script>

<!-- Include the CSRF token for AJAX requests -->
<?= Html::csrfMetaTags() ?>

<?php $this->endBody() ?>
</body>


</html>
<?php $this->endPage();
