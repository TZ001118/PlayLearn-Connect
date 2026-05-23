<?php
header('Content-Type: application/json');
require 'db_conn.php'; // 统一引用

$username = $_GET['username'] ?? '';
if (empty($username)) {
    echo json_encode(["taken" => false]);
    exit();
}
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
echo json_encode(["taken" => ($result->num_rows > 0)]);
$stmt->close();
$conn->close();
?>