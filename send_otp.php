<?php
session_start();
header('Content-Type: application/json');

// 只接收 POST 请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    // 检查邮箱格式
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email format."]);
        exit;
    }

    // 1. 生成 6 位随机验证码
    $otp = rand(100000, 999999);

    // 2. 将验证码存入 Session，并设置 5 分钟过期时间
    $_SESSION['recovery_otp'] = $otp;
    $_SESSION['recovery_email'] = $email;
    $_SESSION['otp_expires'] = time() + (5 * 60);

    // 3. 准备邮件内容
    $to = $email;
    $subject = "PlayLearn - Your Account Recovery Code";
    
    // 邮件的正文 (HTML 格式，比较美观)
    $message = "
    <html>
    <head>
        <title>Account Recovery Code</title>
    </head>
    <body style='font-family: Arial, sans-serif; color: #333;'>
        <h2>PlayLearn Account Recovery</h2>
        <p>We received a request to recover your account.</p>
        <p>Your 6-digit verification code is: <strong style='font-size: 24px; color: #0984e3; letter-spacing: 2px;'>{$otp}</strong></p>
        <p style='color: #888; font-size: 12px;'>This code will expire in 5 minutes. If you did not request this, please ignore this email.</p>
    </body>
    </html>
    ";

    // 4. 设置邮件头部信息 (声明这是一封 HTML 邮件)
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: PlayLearn Support <noreply@playlearn.com>" . "\r\n"; 

    // 5. 触发 PHP 的 mail() 函数发送邮件
    if (mail($to, $subject, $message, $headers)) {
        echo json_encode(["status" => "success", "message" => "OTP sent successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to send email. Check your local mail server."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>