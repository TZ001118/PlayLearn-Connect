<?php
session_start();
header('Content-Type: application/json');
require 'db_conn.php';

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized Access']));
}

$input_otp = trim($_POST['otp'] ?? '');
$pending_email = $_SESSION['update_email_pending'] ?? '';
$session_otp = $_SESSION['update_email_otp'] ?? '';
$expiry = $_SESSION['update_email_expiry'] ?? 0;

// 1. 检查是否存在待验证的请求
if (empty($pending_email) || empty($session_otp)) {
    die(json_encode(['status' => 'error', 'message' => 'Session expired. Please request a new OTP.']));
}

// 2. 检查验证码是否过期 (5分钟)
if (time() > $expiry) {
    die(json_encode(['status' => 'error', 'message' => 'OTP expired. Please request a new one.']));
}

// 3. 核对验证码是否正确
if ($input_otp === $session_otp) {
    
    // 4. 验证通过，更新 users 表中的邮箱
    $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
    $stmt->bind_param("si", $pending_email, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        // 更新成功，清理 Session，防止重复使用
        unset($_SESSION['update_email_otp']);
        unset($_SESSION['update_email_pending']);
        unset($_SESSION['update_email_expiry']);
        
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error. Failed to update.']);
    }
} else {
    // 验证码错误
    echo json_encode(['status' => 'error', 'message' => 'Invalid code.']);
}
?>