<?php

use PragmaRX\Google2FA\Google2FA;

use common\models\User;
use yii\web\ForbiddenHttpException;

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    header("location: 2fa_test.php");
    throw new ForbiddenHttpException;
    exit;
}

$user_id = Yii::$app->user->identity->id;
$user = User::findOne(['id' => $user_id]);

$_g2fa = new Google2FA();

if (!isset($_POST['code'])) {
    echo 'code not found';
    exit;
}

$receivedOtp = $_POST['code'];

error_log(json_encode($receivedOtp) . "\n", 3, "betina.txt");

$valid = $_g2fa->verifyKey($user->secret_key, $receivedOtp); // true/false

$response = array(
    'receivedOtp' => $receivedOtp,
    'result' => $valid // true/false
);

header('Content-Type: application/json');
echo json_encode($response); // Encode and send JSON response

