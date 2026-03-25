<?php
header('Content-Type: application/json');

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "playlearn_db";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit();
}

$username = $_POST['username'] ?? '';
$new_password = $_POST['new_password'] ?? '';

if (empty($username) || empty($new_password)) {
    echo json_encode(["status" => "error", "message" => "Missing data."]);
    exit();
}
$check_stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
$check_stmt->bind_param("s", $username);
$check_stmt->execute();
$current_res = $check_stmt->get_result();

if ($row = $current_res->fetch_assoc()) {
    $old_hashed_password = $row['password'];

    // 使用 password_verify 比较【新输入的明文】和【数据库里的旧哈希】
    if (password_verify($new_password, $old_hashed_password)) {
        // 如果一样，直接报错退出
        echo json_encode(["status" => "error", "message" => "New password cannot be the same as your old password."]);
        exit();
    }
}
$check_stmt->close();

// ✅ 注意这里：函数名必须是 password_hash，不是 password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// ✅ 注意这里：SET password = ?，这里的 password 是你的数据库列名
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
$stmt->bind_param("ss", $hashed_password, $username);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>