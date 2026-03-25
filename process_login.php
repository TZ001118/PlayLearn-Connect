<?php
// 1. 【重要】session_start() 必须放在页面的最顶部！
session_start();
header('Content-Type: application/json');

// 数据库连接
$servername = "localhost";
$db_username = "root"; 
$db_password = "";      
$dbname = "playlearn_db";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = $_POST['username'] ?? ''; 
    $pass = $_POST['password'] ?? '';

    if (empty($identifier) || empty($pass)) {
        echo json_encode(["status" => "error", "message" => "Please fill in all fields."]);
        exit();
    }

    // ✅ 修复 1：SQL 语句中有 2 个问号，bind_param 就只能有 2 个参数
    // 同时加上 id 字段，否则你后面赋值 $_SESSION['user_id'] 时会拿不到数据
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
    
    // ✅ 修复 2：由 "sss" 改为 "ss"，变量由 3 个改为 2 个
    $stmt->bind_param("ss", $identifier, $identifier);
    
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        
        // 验证加密密码
        if (password_verify($pass, $row['password'])) {
            // ✅ 修复 3：Session 赋值（session_start 已经在顶部执行过了）
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];

            echo json_encode(["status" => "success", "message" => "Login successful!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid username or password."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid username or password."]);
    }
    
    $stmt->close();
}

$conn->close();
?>