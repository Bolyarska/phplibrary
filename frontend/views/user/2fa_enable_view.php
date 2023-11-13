<div class="container justify-content-center text-center">
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

<style>

    input[type=number] {
    appearance: textfield;
    }

</style>