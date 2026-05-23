<?php
session_start();
require 'db_conn.php';

// 1. 基础安全检查
if (!isset($_SESSION['user_id'])) {
    die("Error: Please log in.");
}

$parent_id = $_SESSION['user_id'];
$child_id = isset($_GET['child_id']) ? intval($_GET['child_id']) : 0;

if ($child_id === 0) {
    die("Error: No student selected.");
}

// 2. 🛡️ 验证并执行解绑 (只删除关联记录，不删除用户账号)
$stmt = $conn->prepare("DELETE FROM account_links WHERE parent_id = ? AND child_id = ?");
$stmt->bind_param("ii", $parent_id, $child_id);

if ($stmt->execute()) {
    // 解绑成功，跳回学生列表
    header("Location: ParentDashboard.php?page=students&status=unlinked");
    exit();
} else {
    die("Database Error: " . $conn->error);
}
?>