<?php
session_start();
require 'db_conn.php';

// 安全守卫：非管理员禁止操作
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $user_id = intval($_GET['id']);
    $action = $_GET['action'];
    $new_status = ($action === 'ban') ? 'banned' : 'active';

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $user_id);
    
    if ($stmt->execute()) {
        // 🟢 记录：Admin 修改了玩家状态
        $action_name = ($action === 'ban') ? "Ban User" : "Unban User";
        write_log($conn, "User Management", "Admin changed status of User ID #$user_id to $new_status", "Success");

        // 更新成功，跳回用户管理页面
        header("Location: admin_users.php?status=updated");
    } else {
        write_log($conn, "User Management", "Failed to change status for User ID #$user_id. Error: " . $conn->error, "Failed");
        echo "Error updating record: " . $conn->error;
    }
    $stmt->close();
}
$conn->close();
?>