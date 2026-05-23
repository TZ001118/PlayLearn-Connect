<?php
header('Content-Type: application/json');
session_start();
require 'db_conn.php';

$username = $_POST['username'] ?? '';
$user_otp = $_POST['otp_code'] ?? '';
$new_password = $_POST['new_password'] ?? '';

if (empty($username) || empty($user_otp) || empty($new_password)) {
    echo json_encode(["status" => "error", "message" => "All fields are required."]);
    exit();
}

// 1. 验证 OTP
if (!isset($_SESSION['forgot_otp']) || $user_otp !== $_SESSION['forgot_otp']) {
    echo json_encode(["status" => "error", "message" => "Invalid verification code."]);
    exit();
}

// 2. 验证有效期
if (time() > $_SESSION['forgot_otp_expiry']) {
    echo json_encode(["status" => "error", "message" => "OTP has expired."]);
    exit();
}

// 3. 检查新密码是否与旧密码相同
$stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    if (password_verify($new_password, $row['password'])) {
        echo json_encode(["status" => "error", "message" => "New password cannot be the same as old password."]);
        exit();
    }
}

// 4. 更新密码
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
$update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
$update_stmt->bind_param("ss", $hashed_password, $username);

if ($update_stmt->execute()) {
    unset($_SESSION['forgot_otp']);
    unset($_SESSION['forgot_otp_expiry']);
    echo json_encode(["status" => "success", "message" => "Password updated!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Update failed."]);
}
$update_stmt->close();
$conn->close();
?>