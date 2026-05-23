<?php
// verify_otp.php 完整内容
header('Content-Type: application/json');
session_start();

$user_otp = $_POST['otp_code'] ?? '';

if (isset($_SESSION['forgot_otp']) && $user_otp === $_SESSION['forgot_otp']) {
    if (time() <= $_SESSION['forgot_otp_expiry']) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "OTP expired."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid code."]);
}
?>