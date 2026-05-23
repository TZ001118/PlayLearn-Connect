<?php
session_start();
header('Content-Type: application/json');
require 'db_conn.php';

// 引入 PHPMailer 核心文件
require 'includes/Exception.php';
require 'includes/PHPMailer.php';
require 'includes/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized Access']));
}

$new_email = trim($_POST['new_email'] ?? '');

// 1. 验证邮箱格式
if (empty($new_email) || !filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
    die(json_encode(['status' => 'error', 'message' => 'Invalid email format.']));
}

// 2. 检查邮箱是否已经被别人使用了
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
$stmt->bind_param("si", $new_email, $_SESSION['user_id']);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    die(json_encode(['status' => 'error', 'message' => 'Email is already taken.']));
}

// 3. 生成 6 位真实的随机验证码
$otp = strval(rand(100000, 999999));

// 4. 将验证码、待修改的邮箱、和过期时间（5分钟）存入 Session
$_SESSION['update_email_otp'] = $otp;
$_SESSION['update_email_pending'] = $new_email;
$_SESSION['update_email_expiry'] = time() + 300; 

$mail = new PHPMailer(true);

try {
    // --- SMTP 服务器配置 (复用你的配置) ---
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'junhengtoh@gmail.com'; //
    $mail->Password   = 'iagt ctrs ggfa dkvg';  //
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    // --- 邮件收件人与正文内容 ---
    $mail->setFrom('playlearn@mmu.edu.my', 'PlayLearn Support'); //
    $mail->addAddress($new_email);
    $mail->isHTML(true);
    $mail->Subject = 'PlayLearn - Update Your Email';
    $mail->Body    = "
        <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
            <h2 style='color: #4A90E2;'>Email Update Request</h2>
            <p>Hello! You requested to change your account email to this address.</p>
            <p>Your verification code is:</p>
            <h1 style='background: #f9f9f9; padding: 10px; text-align: center; letter-spacing: 5px;'>$otp</h1>
            <p style='color: #888; font-size: 12px;'>This code will expire in 5 minutes. If you did not request this, please ignore this email.</p>
        </div>
    ";
    
    $mail->send();
    echo json_encode(['status' => 'success', 'message' => 'OTP Sent']);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
}
?>