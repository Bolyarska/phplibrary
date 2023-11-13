<?php

use common\models\User;

// receives $user;

$isSecretKeySet = !empty($user->secret_key);
$title = 'Two factor Auth';

if ($isSecretKeySet): ?>
    <div class="container justify-content-center text-center">
    <h2> <?= $title ?> </h2>
    <h2><div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" style="float:none" checked>
        <label class="form-check-label" for="flexSwitchCheckDefault"></label>
    </div></h2>
    </div>

<?php else: ?>
    <div class="container justify-content-center text-center">
    <h2> <?= $title ?> </h2>
    <h2><div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" style="float:none" unchecked>
        <label class="form-check-label" for="flexSwitchCheckDefault"></label>
    </div></h2>
    </div> 

<?php endif; ?>



<script>

    const checkbox = document.getElementById('flexSwitchCheckDefault');

    // Add an event listener for changes in the checkbox state
    checkbox.addEventListener('change', function () {

        console.log(checkbox.checked);

        if (checkbox.checked == false) {
            const confirmation = confirm("Are you sure you want to disable the 2-Step verification?");

            if (confirmation) {
                disableTwoFactorAuth();
                checkbox.checked = false;
            } else {
                checkbox.checked = true;
            }

        } else {
            const confirmation = confirm("Are you sure you want to proceed?");

            if (confirmation) {
                checkbox.checked = true;
                window.location.href = '/user/enable-two-factor-auth';
            } else {
                checkbox.checked = false;
            }

        }
    });

    function disableTwoFactorAuth() {
        const userId = <?= Yii::$app->user->identity->id ?>;

        $.ajax({
            type: 'POST',
            url: '/user/disable-two-factor-auth',
            //data: { disable_secret_key: true },
            success: function (response) {
                // Handle the response if needed
                console.log(response);
            },
            error: function (xhr, status, error) {
                // Handle errors if needed
                console.error(error);
            }
        });
    }

</script>
