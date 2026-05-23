<?php
session_start();
header('Content-Type: application/json');

<<<<<<< HEAD
// 1. 引入 PHPMailer 核心文件
require 'includes/Exception.php';
require 'includes/PHPMailer.php';
require 'includes/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';

    // 基础验证：确保邮箱格式正确
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email format."]);
        exit();
    }

    // 2. 生成 6 位真实的随机验证码
    $otp = strval(rand(100000, 999999));
    
    // 3. 将验证码和过期时间（5分钟）存入 Session
    $_SESSION['sent_otp'] = $otp;
    $_SESSION['otp_expiry'] = time() + 300;

    $mail = new PHPMailer(true);

    try {
        // --- SMTP 服务器配置 (以 Gmail 为例) ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'junhengtoh@gmail.com'; // 填入你的 Gmail 地址
        $mail->Password   = 'iagt ctrs ggfa dkvg'; // ⚠️ 填入你生成的 16 位 Google App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // --- 邮件收件人与正文内容 ---
        $mail->setFrom('playlearn@mmu.edu.my', 'PlayLearn Support');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Verify your PlayLearn Account';
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                <h2 style='color: #4A90E2;'>Registration OTP</h2>
                <p>Hello! Your verification code for PlayLearn is:</p>
                <h1 style='background: #f9f9f9; padding: 10px; text-align: center; letter-spacing: 5px;'>$otp</h1>
                <p style='color: #888; font-size: 12px;'>This code will expire in 5 minutes. Please do not share it with anyone.</p>
            </div>
        ";
        
        $mail->send();
        echo json_encode(["status" => "success", "message" => "A real OTP has been sent to your email!"]);

    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Mailer Error: " . $mail->ErrorInfo]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
=======
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
>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
