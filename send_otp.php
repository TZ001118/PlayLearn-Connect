<?php
session_start();
header('Content-Type: application/json');

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