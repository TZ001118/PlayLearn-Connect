<?php
session_start();
require 'db_conn.php';

// 只有管理员能执行
if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error']);
    exit();
}

$eta = $_POST['eta'] ?? '15'; // 接收前端传来的分钟数

// 1. 切换开启/关闭状态
$conn->query("UPDATE site_config SET config_value = 1 - config_value WHERE config_key = 'maintenance_mode'");

// 2. 获取最新状态
$res = $conn->query("SELECT config_value FROM site_config WHERE config_key = 'maintenance_mode'");
$new_active = $res->fetch_assoc()['config_value'];

// 3. 如果是开启状态，计算出过期的时间点 (当前时间 + 分钟数)
if ($new_active === '1') {
    // 转换为日期格式 Y-m-d H:i:s
    $expires_at = date('Y-m-d H:i:s', strtotime("+$eta minutes"));
    
    $stmt = $conn->prepare("INSERT INTO site_config (config_key, config_value) VALUES ('maintenance_expires_at', ?) ON DUPLICATE KEY UPDATE config_value = ?");
    $stmt->bind_param("ss", $expires_at, $expires_at);
    $stmt->execute();
    $stmt->close();
}

// 4. 记录日志
$status_text = ($new_active === '1') ? "ENABLED (Estimated: $eta mins)" : "DISABLED";
if (function_exists('write_log')) {
    write_log($conn, "System Maintenance", "Admin $status_text maintenance mode", "Success");
}

echo json_encode(["status" => "success"]);
?>