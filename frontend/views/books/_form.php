<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\YiiAsset;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/** @var common\models\Books $model */
/** @var yii\widgets\ActiveForm $form */

// Register Bootstrap assets
//\yii\web\YiiAsset::register($this);

YiiAsset::register($this);

$authorList = $model->getAuthorList();

?>

<div class="col-10 col-md-4 col-lg-4 books-form mx-auto">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
    ])?>


    <?= $form->field($model, 'title', ['options' => ['class' => 'create-field']])->textInput(['maxlength' => true])?>

    <div class='mb-3'>
        <?= $form->field($model, 'author')->widget(Select2::classname(), [
            'data' => array_combine($authorList, $authorList),
            'language' => 'en',
            'options' => ['placeholder' => 'Select an author'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]); ?>
    </div>

    <?= $form->field($model, 'isbn', ['options' => ['class' => 'create-field']])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'publisher', ['options' => ['class' => 'create-field']])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'language', ['options' => ['class' => 'create-field']])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pages', ['options' => ['class' => 'create-field']])->textInput() ?>

    <?= $form->field($model, 'number_in_stock', ['options' => ['class' => 'create-field']])->textInput() ?>

    <?= $form->field($model, 'number_available', ['options' => ['class' => 'create-field']])->textInput() ?>

    <!-- checkbox buttons for the genres -->

    <!-- <?php echo $form->field($model, 'selectedGenres')->checkboxList($genreList); ?>-->

    <!-- Toggle Button -->
    <div class="btn col-12">

        <button type="button" class="btn btn-warning btn-outline-dark" data-bs-toggle="collapse" data-bs-target="#genreDropdown" aria-controls="genreDropdown" aria-expanded="false">Select Genres</button>

        <div class="collapse" id="genreDropdown">
            <div class="card card-body">
                <?php echo $form->field($model, 'selectedGenres')->checkboxList($genreList) ?>
            </div>
        </div>

    </div>

    <br>

    <?php
    $images = unserialize($model->images);
    if ($images && is_array($images)) {
        echo '<div class="form-group">';

        echo '<div class="col-12 image-container justify-content-center" id="image-container">'; // Container for the images

        // Loop through all images
        foreach ($images as $index => $image) {
            $imageUrl = Yii::getAlias('@web/' . $image); // Get the full URL of the image

            echo '<div class="image-wrapper text-center">'; // Wrapper for each image
            echo Html::img($imageUrl, ['class' => 'book-image']);
            if ($index === 0) {
                echo '<div class="image-index">' . ($index + 1) . '- Book Cover' . '</div>'; // Image index + Book cover label
            } else {
                echo '<div class="image-index">' . ($index + 1) . '</div>'; // Image index label
            }

            if (count($images) > 1) {

                // Button to move the current image
                $buttonLabel = ($index === count($images) - 1) ? 'Move as cover' : 'Move to the right';
                echo Html::a(
                    $buttonLabel,
                    ['move', 'id' => $model->id, 'index' => $index, 'direction' => 'right'],
                    ['class' => 'btn btn-warning btn-outline-dark movebutton', 'data-index' => $index]
                );
            }

            // Button to remove the current image
            echo ' ';
            echo Html::a(
                'X',
                ['remove-image', 'id' => $model->id, 'index' => $index],
                [
                    'class' => 'btn btn-danger removebutton',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this image?',
                        'method' => 'post',
                    ],
                ]
            );

            echo '</div>';
        }

        echo '</div>'; // image container

        echo '</div>'; // form-group
    }

    ?>

    <br>

    <div class="row text-center" style='padding-left:80px'>
        <div class="browse-button">
            <?= $form->field($model, 'file[]')->fileInput(['multiple' => true])->label('') ?>
        </div>
    </div>

    <br>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>


    <div class="form-group text-end mt-2">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success btn-outline-dark text-white']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>




<style>
    .select2-selection__clear {
        padding-right: 10px;
        font-size: 1.3rem;
        color: red !important;
    }

    .create-field {
        margin-bottom: 10px;
    }



    .image-container {
    max-width: 100%; /* Ensure container does not exceed its parent's width */
    text-align: center;
    }

    .image-wrapper {
    display: inline-block; /* Ensure the wrapper adjusts to the image size */
    max-width: 100%; /* Ensure the wrapper does not exceed its parent's width */
    }

    .book-image {
    max-width: 100%; /* Constrain image to the wrapper's width */
    height: auto; /* Maintain aspect ratio */
    }

</style>




<script>
    $(document).ready(function() {
        $('.image-container').on('click', '.movebutton', function(e) {
            e.preventDefault();

            var ajaxUrl = '/books/move-ajax';
            var index = $(this).data('index');

            params = "?id=<?= $model->id ?>" + "&index=" + index + "&direction=right";

            $.ajax({
                url: ajaxUrl + params,
                type: 'post',
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.success) {
                        updateImagesOrder(index, data.index);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Image move failed!');
                }
            });
        });
    });

    function updateImagesOrder(index, newIndex) {

        var imageContainer = $('#image-container');
        var imageWrappers = imageContainer.children('.image-wrapper');

        var currentBookImageWrapper = $(imageWrappers[index]); // image we want to move
        var nextBookImageWrapper = $(imageWrappers[newIndex]); // next image

        if (newIndex === 0) {
            currentBookImageWrapper.prependTo(imageContainer); // should move the image we want to index 0 
        } else {
            currentBookImageWrapper.insertAfter(nextBookImageWrapper); // should move the image we want to the newIndex (index + 1)
        }

        // labels
        var newImageWrappers = imageContainer.children('.image-wrapper');
        newImageWrappers.each(function(i) {
            var wrapper = $(this);
            wrapper.find('.movebutton').data('index', i);
            var newIndexText = (i + 1).toString();
            if (i === 0) {
                newIndexText += '- Book Cover';
            }
            wrapper.find('.image-index').text(newIndexText);
            var buttonLabel = (i === newImageWrappers.length - 1) ? 'Move as cover' : 'Move to the right';
            wrapper.find('.movebutton').text(buttonLabel);
        });
    }
</script>