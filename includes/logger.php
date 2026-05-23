<?php
// includes/logger.php
// 核心日志记录函数：只需要在 db_conn.php 中 include 即可全系统调用

function write_log($conn, $action, $details = "", $status = "Success") {
    // 尝试从 Session 获取操作者信息
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Visitor';
    
    // 记录真实 IP 和设备信息
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

    $sql = "INSERT INTO system_logs (user_id, username, role, action, details, ip_address, user_agent, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("isssssss", $user_id, $username, $role, $action, $details, $ip, $ua, $status);
        $stmt->execute();
        $stmt->close();
    }
}
?>