<?php
header('Content-Type: application/json');

// 1. 数据库连接设置
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "playlearn_db";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

// 2. 接收前端传来的数据
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$email    = $_POST['email'] ?? ''; 
$month    = $_POST['month'] ?? '';
$day      = $_POST['day'] ?? '';
$year     = $_POST['year'] ?? '';
$gender   = $_POST['gender'] ?? '';

// 简单的后端检查
if (empty($username) || empty($password) || empty($email)) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit();
}

// ✅ 修复 1：函数名错误
// 原代码是 password()，PHP 里没有这个函数，必须使用 password_hash()
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// ✅ 修复 2：生日格式补零 (可选但推荐)
// 数据库的 DATE 类型通常期望 YYYY-MM-DD (例如 2026-01-05 而不是 2026-1-5)
$formatted_month = str_pad($month, 2, "0", STR_PAD_LEFT);
$formatted_day   = str_pad($day, 2, "0", STR_PAD_LEFT);
$birthday = "$year-$formatted_month-$formatted_day";

// 3. 插入数据库
// ✅ 确保 VALUES 后面有 5 个问号，对应你 bind_param 的 5 个 "s"
$stmt = $conn->prepare("INSERT INTO users (username, email, password, birthday, gender) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $username, $email, $hashed_password, $birthday, $gender);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Registration successful"]);
} else {
    // 检查是否因为用户名或邮箱已存在 (Unique Key 冲突)
    if ($conn->errno === 1062) {
        echo json_encode(["status" => "error", "message" => "Username or Email already exists."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
    }
}

$stmt->close();
$conn->close();
?>