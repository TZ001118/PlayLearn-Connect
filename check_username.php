<?php
header('Content-Type: application/json');

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "playlearn_db";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

$username = $_GET['username'] ?? '';

if (empty($username)) {
    echo json_encode(["taken" => false]);
    exit();
}

// 去数据库查找有没有这个名字
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // 找到了！说明名字被占用了
    echo json_encode(["taken" => true]);
} else {
    // 没找到，名字可用
    echo json_encode(["taken" => false]);
}

$stmt->close();
$conn->close();
?>