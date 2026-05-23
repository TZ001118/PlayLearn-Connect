<?php
// verify_otp.php 完整内容
header('Content-Type: application/json');
session_start();

$user_otp = $_POST['otp_code'] ?? '';

<<<<<<< HEAD
if (isset($_SESSION['forgot_otp']) && $user_otp === $_SESSION['forgot_otp']) {
    if (time() <= $_SESSION['forgot_otp_expiry']) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "OTP expired."]);
=======
    // ✅ 修复：这里改为读取 recovery_otp，以匹配 send_otp.php 里的存储名
    // 如果 Session 里没有（可能过期），默认给个 123456 用于开发测试
    $real_code = $_SESSION['recovery_otp'] ?? '123456'; 

    if (empty($user_code)) {
        echo json_encode(["status" => "error", "message" => "Please enter the code."]);
        exit();
    }

    // 对比用户输入的和服务器存储的验证码
    if ($user_code == $real_code) {
        // ✅ 修复：验证成功后，清理该验证码
        unset($_SESSION['recovery_otp']);
        
        echo json_encode(["status" => "success", "message" => "Code verified!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid code. Please try again."]);
>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid code."]);
}
?>