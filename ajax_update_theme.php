<?php
session_start();
require 'db_conn.php';

if (isset($_SESSION['user_id']) && isset($_POST['theme'])) {
    $theme = $_POST['theme'];
    $uid = $_SESSION['user_id'];
    
    // 更新数据库中的偏好
    $stmt = $conn->prepare("UPDATE users SET theme_preference = ? WHERE id = ?");
    $stmt->bind_param("si", $theme, $uid);
    
    if ($stmt->execute()) {
        // 记录日志，让改动有迹可循
        write_log($conn, "Preference Change", "Admin updated UI theme to $theme", "Success");
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
    $stmt->close();
}
?>