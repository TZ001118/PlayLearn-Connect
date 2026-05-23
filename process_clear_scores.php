<?php
session_start();
require 'db_conn.php';

// 安全检查：只有管理员能执行
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized access.");
}

// 执行 SQL 清空表
if ($conn->query("TRUNCATE TABLE game_scores")) {
    // 清空成功后跳回管理页面，并带上成功的提示参数
    header("Location: admin_scores.php?msg=cleared");
} else {
    echo "Error clearing records: " . $conn->error;
}

$conn->close();
?>