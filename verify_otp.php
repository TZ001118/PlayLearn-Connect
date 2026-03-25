<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_code = $_POST['otp_code'] ?? '';

    // 💡 这里是模拟：如果系统之前没有存过验证码，我们就假装刚才发的是 "123456"
    // 在真正的系统中，这个 $_SESSION['sent_otp'] 应该是在发送邮件时生成的随机数
    $real_code = $_SESSION['sent_otp'] ?? '123456'; 

    if (empty($user_code)) {
        echo json_encode(["status" => "error", "message" => "Please enter the code."]);
        exit();
    }

    // 对比用户输入的和我们存的
    if ($user_code === $real_code) {
        // 验证成功！销毁这个一次性验证码，防止重复使用
        unset($_SESSION['sent_otp']);
        echo json_encode(["status" => "success", "message" => "Code verified!"]);
    } else {
        // 验证失败
        echo json_encode(["status" => "error", "message" => "Invalid code. Please try again."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>