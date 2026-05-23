<?php
session_start();
// 1. ✅ 必须引入数据库连接，否则 write_log 无法工作
require 'db_conn.php'; 

// 2. ✅ 在清空 Session 之前记录日志，这样日志才能抓到当前的用户名
if (isset($_SESSION['user_id'])) {
    write_log($conn, "Logout", "User logged out safely", "Success");
}

// 3. 清空所有的 Session 变量
$_SESSION = array();

// 4. 清除 Cookie (可选但推荐)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 5. 彻底销毁 Session
session_destroy();

// 6. 跳转到登录页面
header("Location: login.php");
exit();
?>