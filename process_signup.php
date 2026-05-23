<?php
// 1. 顶部必须纯净，不能有任何空格
header('Content-Type: application/json');
session_start(); 
require 'db_conn.php';

// 2. 接收前端传来的数据
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$email    = $_POST['email'] ?? ''; 
$month    = $_POST['month'] ?? '';
$day      = $_POST['day'] ?? '';
$year     = $_POST['year'] ?? '';
$gender   = $_POST['gender'] ?? '';
$user_otp = $_POST['otp_code'] ?? ''; 
$role     = $_POST['role'] ?? 'player'; // ✅ 新增：接收角色参数

// 安全验证：确保角色只能是 player 或 parent（防止黑客直接注册为 admin）
if ($role !== 'parent') {
    $role = 'player';
}

// 3. 基础非空检查
if (empty($username) || empty($password) || empty($email) || empty($user_otp)) {
    echo json_encode(["status" => "error", "message" => "All fields including OTP are required."]);
    exit();
}

// 4. 🛡️ 核心验证：校验 OTP
if (!isset($_SESSION['sent_otp']) || !isset($_SESSION['otp_expiry'])) {
    echo json_encode(["status" => "error", "message" => "OTP not found. Please request a new one."]);
    exit();
}

if (time() > $_SESSION['otp_expiry']) {
    echo json_encode(["status" => "error", "message" => "OTP has expired."]);
    exit();
}

if ($user_otp !== $_SESSION['sent_otp']) {
    echo json_encode(["status" => "error", "message" => "Invalid verification code."]);
    exit();
}

// 5. 验证通过后，处理数据
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 家长可能不需要强制填生日，处理一下空值情况
if (empty($year) || empty($month) || empty($day)) {
    $birthday = "2000-01-01"; // 给个默认值
} else {
    $birthday = "$year-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-" . str_pad($day, 2, "0", STR_PAD_LEFT);
}

// 6. 🛡️ 尝试写入数据库，捕获可能的重复项错误
try {
    // ✅ 动态插入角色
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, birthday, gender, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $email, $hashed_password, $birthday, $gender, $role);

    if ($stmt->execute()) {
        // ✅ 注册成功逻辑
        unset($_SESSION['sent_otp']);
        unset($_SESSION['otp_expiry']);
        echo json_encode(["status" => "success", "message" => "Registration successful!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Execution failed."]);
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1062) {
        echo json_encode(["status" => "error", "message" => "Username or Email already exists."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
}

$conn->close();
?>