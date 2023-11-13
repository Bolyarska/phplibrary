<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use common\models\User;

//VIEW RECEIVES $USER AND A BOOLEAN FOR $isSecretKeySet

// google2fa object
$_g2fa = new Google2FA();

$isSecretKeySet = !empty($user->secret_key);
$title = $isSecretKeySet ? 'The 2-Step Authorization has already been enabled on your account.' : 'Protect your account with 2-Step Verification';


// Generate a secret key for the logged-in user
$user->secret_key = $_g2fa->generateSecretKey();
$user->save();


// Provide name of application (To display to user on app)
$app_name = 'bibliotechka_test';

// Generate a custom URL from user data to provide to qr code generator
$qrCodeUrl = $_g2fa->getQRCodeUrl(
    $app_name,
    $user->email,
    $user->secret_key
);

// QR Code Generation using bacon/bacon-qr-code
// Set up image rendered and writer
$renderer = new ImageRenderer(
	new RendererStyle(250),
	new SvgImageBackEnd()
);
$writer = new Writer($renderer);

// This option will create a string with the image data and base64 encode it
$encoded_qr_data = base64_encode($writer->writeString($qrCodeUrl));

// This will provide us with the current password
$current_otp = $_g2fa->getCurrentOtp($user->secret_key);

?>

<div class="container justify-content-center">
    <h2> <?= $title ?> </h2>
    <h2><div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" style="float:none" <?= $isSecretKeySet ? 'checked' : '' ?>>
        <label class="form-check-label" for="flexSwitchCheckDefault"></label>
    </div></h2>

    <div id="instructions">
    <h5>To get started, download Google Authenticator from the Google Play Store or the iOS App Store.</h5>
    <p><img src="data:image/svg+xml;base64,<?php echo $encoded_qr_data; ?>" alt="QR Code"></p>
    <form id="verify-code-form" action="<?= Yii::$app->urlManager->createUrl(['user/verify-code']) ?>" method="post">
        <?= yii\helpers\Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
        <div style="text-align: center;">
            <p> Scan the above QR code and enter the received code to add this account to your list</p> 
            <input type="number" id="code" required class="form-control" name="code" placeholder="******" style="font-size: xx-large;width: 200px;border-radius: 0px;text-align: center;display: inline;color: #0275d8;">
            <br>
            <div id="error-message" class="text-danger" style="height: 30px"></div> <!-- Error message container -->
            <button type="button" id="verify-button" class="btn btn-md" style="width: 200px;border-radius: 3px; background-color:#16b0bf">Verify</button>
        </div>
    </form>
    </div>

</div>

<!-- Ajax for the verification button -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        $("#verify-button").click(function () {
            var code = $("#code").val();
            var csrfToken = $('meta[name="csrf-token"]').attr("content");

            $.ajax({
                type: "POST",
                url: $("#verify-code-form").attr("action"),
                data: {
                    code: code,
                    _csrf: csrfToken
                },
                success: function (data) {
                    if (data.success) {
                        // Successful verification
                        // You can handle the success response here, e.g., show a success message
                        alert(data.message);
                    } else {
                        // Verification failed
                        // Show an error message
                        $("#error-message").text(data.message).show();
                    }
                },
                error: function () {
                    // Error handling
                    alert("An error occurred while processing the request.");
                }
            });
        });
    });
</script>

<!-- JS for the switch button state -->
<script>
    //event listener for the checkbox input
    const checkbox = document.getElementById('flexSwitchCheckDefault');
    const instructionsDiv = document.getElementById('instructions');

    // Check the 'checked' attribute initially
    if (checkbox.checked) {
        instructionsDiv.style.display = 'block'; // Show the instructions
    } else {
        instructionsDiv.style.display = 'none'; // Hide the instructions
    }

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

    // Add an event listener for changes in the checkbox state
    checkbox.addEventListener('change', function () {
        if (checkbox.checked) {
            instructionsDiv.style.display = 'block'; // Show the instructions
        } else {

            const confirmation = confirm("Are you sure you want to disable the 2-Step verification?");

            if (confirmation) {
                disableTwoFactorAuth();
                instructionsDiv.style.display = 'none';
            } else {
                checkbox.checked = true;
            }
        }
    });
</script>

<!-- CSS Code -->
<style>
    .container {
        text-align: center;
    }

    .form-check-input:checked {
        background-color:#16b0bf ;
    }

    input[type=number] {
        appearance: textfield;
    }

</style>