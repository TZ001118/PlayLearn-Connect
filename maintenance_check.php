<?php
// maintenance_check.php

// 1. 获取维护模式状态
$res = $conn->query("SELECT config_value FROM site_config WHERE config_key = 'maintenance_mode'");
$is_maintenance = false;

if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $is_maintenance = ($row['config_value'] == '1');
}

// 2. 检查是否过期 (自动解锁逻辑)
if ($is_maintenance) {
    $time_res = $conn->query("SELECT config_value FROM site_config WHERE config_key = 'maintenance_expires_at'");
    if ($time_res && $time_res->num_rows > 0) {
        $expires_at_str = $time_res->fetch_assoc()['config_value'];
        if (date('Y-m-d H:i:s') > $expires_at_str) {
            $conn->query("UPDATE site_config SET config_value = '0' WHERE config_key = 'maintenance_mode'");
            $is_maintenance = false; 
        }
    }
}

// 3. 拦截逻辑 (关键！)
$role = $_SESSION['role'] ?? 'guest';

// 只有当维护开启 且 当前用户不是管理员时，才拦截
if ($is_maintenance && $role !== 'admin') {
    // 必须跳转，不能只是 echo
    if (basename($_SERVER['PHP_SELF']) !== 'maintenance.php') {
        header("Location: maintenance.php");
        exit(); 
    }
}
?>