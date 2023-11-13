<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use Yii;
use yii\bootstrap5\BootstrapAsset;

/** @var yii\web\View $this */
/** @var common\models\User $model */

$this->title = $model->name_surname;
$this->params['breadcrumbs'][] = ['label' => 'User', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="col-10 col-sm-10 col-md-6 col-lg-7 col-xl-3 col-xxl-2 user-view mx-auto" style='min-width:500px'>

<?php
  $buttonText = 'Select Reader'; // Default button text
  $isActive = false; // Default active state

  $user_id_in_basket = Yii::$app->session->get('user_id_in_basket');

  $book_quantity_in_basket = Yii::$app->session->get('book_quantity_in_basket') ?? [];

  if ($user_id_in_basket == $model->id) {
    $buttonText = 'Deselect Reader';
    $isActive = true;
    // Disable the button if books are already in the basket
    $disableButton = !empty($book_quantity_in_basket);
  }
  ?>

<?php if ($model->is_active == 1): ?>
    <h1 class='text-center'><?= Html::encode($this->title) . ' ' . Html::a($buttonText,
            Url::to(['savedbooks/toggle-user-session', 'id' => $model->id]), [
                'class' => 'btn btn-success btn-outline-dark btn-lg toggle-button btn-responsive text-white' . ($isActive ? ' active' : ''),
                'role' => 'button',
                'id' => 'toggle-button',
                'data-id' => $model->id, // Add the 'data-id' attribute to store the user ID
                'data-disable' => isset($disableButton) && $disableButton ? 'true' : 'false',
            ]) ?>
    
    <?php
    $statusText = $model->is_active == 1 ? 'Active' : 'Inactive';
    ?>

    <?php if ($model->is_active !== 1): ?>
    <button class="btn btn-lg btn-secondary btn-responsive" disabled><?= $statusText ?></button>
    <?php endif; ?>

    </h1>

<?php else: ?>
  <?php $statusText = $model->is_active == 1 ? 'Active' : 'Inactive'; ?>
  <h1 class='text-center'><?= Html::encode($this->title) . ' ' ?>
      <button class="btn btn-lg btn-secondary btn-responsive" disabled><?= $statusText ?></button>
  </h1>
<?php endif; ?>

<div class="shadow card">
    <div class="card-header">
        <h5 class="card-title text-center">Details</h5>
    </div>
    <div class="card-body" style='padding:0px'>
      <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
          'id',
          'name_surname',
          'username',
          'email:email',
          'phone_number',
          'address',
          [
            'attribute' => 'note',
            'format' => 'raw',
            'value' => function ($model) {
                $note = $model->note;

                if ($model->type === 'Employee' && (!Yii::$app->user->can('view-admin-pages'))) {
                  return '<i style="color: red;">Hidden</i>';
                }

                $styledNote = '<i style="color: red;">' . $note . '</i>';
                return $styledNote;
            },
        ],

          'type',
        ],
      'options' => [
        'class' => 'table table-striped table-borderless',
      ],
      ]) ?>
      </div>
      </div>
      </div>


  <div class="col-lg-12 text-center mt-2">
  <?php if ($model->is_active == 1): ?>
    <?= Html::a('Update Info', ['update', 'id' => $model->id], ['class' => 'btn btn-warning btn-outline-dark btn-responsive btn-sm mt-1']) ?>
    <?php if ($user_id_in_basket == null): ?>
      <?= Html::a('Saved Books', ['savedbooks/show-reader-saved-books', 'id' => $user_id_in_basket], [
      'class' => 'btn btn-outline-dark btn-responsive btn-sm mt-1 custom-tooltip',
      'title' => 'Please select the reader first',
      'data-toggle' => 'tooltip',
      'style' => 'text-decoration: none; cursor:not-allowed;',
      ]) ?>

      <?= Html::a('Taken Books', ['takenbooks/show-reader-taken-books', 'id' => $user_id_in_basket], [
      'class' => 'btn btn-outline-dark btn-responsive btn-sm mt-1 custom-tooltip',
      'title' => 'Please select the reader first',
      'data-toggle' => 'tooltip',
      'style' => 'text-decoration: none; cursor:not-allowed;',
      ]) ?>

      <?= Html::a('Return History', ['returnedbooks/show-reader-returned-books', 'id' => $user_id_in_basket], [
      'class' => 'btn btn-outline-dark btn-responsive btn-sm mt-1 custom-tooltip',
      'title' => 'Please select the reader first',
      'data-toggle' => 'tooltip',
      'style' => 'text-decoration: none; cursor:not-allowed;',
      ]) ?>
      
    <?php else: ?>
      <?= Html::a('Saved Books', ['savedbooks/show-reader-saved-books', 'id' => $user_id_in_basket], ['class' => 'btn btn-success btn-outline-dark btn-responsive btn-sm mt-1 text-white']) ?>
      <?= Html::a('Taken Books', ['takenbooks/show-reader-taken-books', 'id' => $user_id_in_basket], ['class' => 'btn btn-success btn-outline-dark btn-responsive btn-sm mt-1 text-white']) ?>
      <?= Html::a('Return History', ['returnedbooks/show-reader-returned-books', 'id' => $user_id_in_basket], ['class' => 'btn btn-success btn-outline-dark btn-responsive btn-sm mt-1 text-white']) ?>
    <?php endif; ?>



    <?php if (Yii::$app->user->can( 'view-admin-pages' )): ?>
      <div class='col-12 text-center'>
      <?= Html::a('Deactivate Profile',[ 'deactivate-profile', 'id' => $model->id], [
          'class' => 'btn btn-danger btn-responsive btn-sm mt-1',
          'data' => [
              'confirm' => 'Are you sure you want to deactivate this profile?',
              'method' => 'post',
          ],
      ]) ?>
      </div>

    <?php elseif (Yii::$app->user->can( 'view-employee-pages' ) && ($model->type !== 'Employee')): ?>
      <div class='col-12 text-center'>
      <?= Html::a('Deactivate Profile',[ 'deactivate-profile', 'id' => $model->id], [
          'class' => 'btn btn-danger btn-responsive btn-sm mt-1',
          'data' => [
              'confirm' => 'Are you sure you want to deactivate this profile?',
              'method' => 'post',
          ],
      ]) ?>
      </div>

    <?php endif; ?>
  </div>

  <?php else: ?>

    <?= Html::a('Reactivate Profile',[ 'reactivate-profile', 'id' => $model->id], [
        'class' => 'btn btn-danger btn-responsive',
        'data' => [
            'confirm' => 'Are you sure you want to reactivate this profile?',
            'method' => 'post',
        ],
    ]) ?>
  </p>

  <?php endif; ?>
</div>

<?php
Yii::info($user_id_in_basket);

?>

<script>
  var button = document.getElementById('toggle-button');
  button.addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the default hyperlink behavior

    var disableButton = button.getAttribute('data-disable') === 'true';

    if (disableButton) {
      alert("Can't deselect reader - there are books in the basket!");
      return;
    }

    button.classList.toggle('active');

    // Get the user ID from the data-id attribute
    var userId = button.getAttribute('data-id');

    // Create a hidden form
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
  });
</script>

<style>
  .toggle-button.active {
    background-color: #28a745;
    border-color: #28a745;
  }

  @media (max-width: 576px) {
    .btn-responsive {
        padding: 5px 5px;
        font-size: 14px;
    }
}

@media (min-width: 576px) and (max-width: 768px) {
    .btn-responsive {
        font-size: 16px;
    }
}
</style>