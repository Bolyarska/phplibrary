<?php 
    use yii\helpers\Html; 
    use yii\widgets\ActiveForm;
    use frontend\models\ChangePasswordForm;
     
    /* @var $this yii\web\View */ 
    /* @var $model frontend\models\ChangePasswordForm */ 
    /* @var $form ActiveForm */ 
     
    $this->title = 'Change Password'; 
    ?> 
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4 user-changepassword"> 
                <?php $form = ActiveForm::begin(); ?> 
            
                    <?= $form->field($model, 'password')->passwordInput() ?> 
                    <?= $form->field($model, 'confirm_password')->passwordInput() ?>

                    <br>
            
                    <div class="form-group text-center"> 
                        <?= Html::submitButton('Change', ['class' => 'btn btn-success']) ?> 
                    </div> 
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
