<?php
// 检测服务器环境
$is_localhost = ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1');

if ($is_localhost) {
    // --- XAMPP 本地配置 ---
    $sname = "localhost";
    $unmae = "root";
    $password = "";
    $db_name = "playlearn_db"; 
} else {
    // --- InfinityFree 线上配置 ---
    $sname = "sql100.infinityfree.com";      
    $unmae = "if0_41736380";               
    $password = "Tjh67076707";            
    $db_name = "if0_41736380_playlearn_db";    
}

$conn = mysqli_connect($sname, $unmae, $password, $db_name);
// 1. 强制将 PHP 的时间设为马来西亚吉隆坡时间
date_default_timezone_set('Asia/Kuala_Lumpur'); 

// 2. 强制将 MySQL 数据库的时间也同步为 UTC+8
$conn->query("SET time_zone = '+08:00'");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

// =========================================================================
// ✅ 核心修改：通过包含外部文件来加载日志功能
// 这样你全系统任何引用了 db_conn.php 的页面都可以直接调用 write_log()
// =========================================================================

if (file_exists('includes/logger.php')) {
    include_once('includes/logger.php');
} elseif (file_exists('../includes/logger.php')) {
    // 兼容在子文件夹（如 games/）中的引用
    include_once('../includes/logger.php');
}

