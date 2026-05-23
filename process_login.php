<?php
session_start();
header('Content-Type: application/json');
require 'db_conn.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = $_POST['username'] ?? ''; 
    $pass = $_POST['password'] ?? '';

    if (empty($identifier) || empty($pass)) {
        echo json_encode(["status" => "error", "message" => "Please fill in all fields."]);
        exit();
    }

    // ✅ 关键改动 1：SQL 也要查 status 字段
    $stmt = $conn->prepare("SELECT id, username, password, role, status FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    if (password_verify($pass, $row['password'])) {
        
        if ($row['status'] === 'banned') {
            // 🛑 记录封禁用户尝试尝试
            write_log($conn, "Login Attempt", "Banned user tried to login: " . $row['username'], "Unauthorized");
            echo json_encode(["status" => "error", "message" => "Your account has been permanently banned."]);
            exit();
        }

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role']; 

        $conn->query("UPDATE users SET last_login = NOW(), last_seen = NOW() WHERE id = " . $row['id']);
        
        // 🟢 记录登录成功
        write_log($conn, "Login", "User logged in successfully", "Success");

        echo json_encode(["status" => "success", "username" => $row['username'], "role" => $row['role']]);
    } else {
        // 🔴 记录密码错误
        write_log($conn, "Login Attempt", "Failed password for: $identifier", "Failed");
        echo json_encode(["status" => "error", "message" => "Invalid username or password."]);
    }
} else {
    // 🔴 记录账号不存在
    write_log($conn, "Login Attempt", "Unknown user: $identifier", "Failed");
    echo json_encode(["status" => "error", "message" => "Invalid username or password."]);
}
    $stmt->close();
}
$conn->close();
?>