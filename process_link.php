<?php
session_start();
// 开启报错，让我们能看到具体的错误
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db_conn.php';

// 1. 检查是否登录
if (!isset($_SESSION['user_id'])) {
    die("<h2 style='color:red;'>错误：请先登录家长账号！</h2>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = strtoupper(trim($_POST['child_code']));
    $parent_id = $_SESSION['user_id'];

    // 2. 检查 account_links 表是否存在
    $table_check = $conn->query("SHOW TABLES LIKE 'account_links'");
    if ($table_check->num_rows == 0) {
        die("<div style='padding:20px; font-family:sans-serif;'>
                <h2 style='color:red;'>数据库错误：缺少关联表！</h2>
                <p>你的数据库里没有 <b>account_links</b> 这个表。请去 phpMyAdmin 运行以下 SQL 语句：</p>
                <pre style='background:#f4f4f4; padding:15px;'>CREATE TABLE account_links (
    link_id INT PRIMARY KEY AUTO_INCREMENT,
    parent_id INT NOT NULL,
    child_id INT NOT NULL,
    linked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);</pre>
            </div>");
    }

    // 3. 寻找这个代码对应的孩子
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE link_code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $child = $res->fetch_assoc();
        $child_id = $child['id'];
        
        // 4. 检查是否已经绑定过了
        $check = $conn->query("SELECT * FROM account_links WHERE parent_id = $parent_id AND child_id = $child_id");
        if ($check->num_rows == 0) {
            
            // 5. 插入数据库
            $sql = "INSERT INTO account_links (parent_id, child_id) VALUES ($parent_id, $child_id)";
            if ($conn->query($sql)) {
                // 🌟 绑定成功！跳转回 Overview 并强制带上新孩子的 ID
                header("Location: ParentDashboard.php?page=overview&child_id=$child_id&status=link_success");
                exit();
            } else {
                die("<h2 style='color:red;'>插入数据失败: " . $conn->error . "</h2>");
            }
        } else {
            // 已经绑定过
            header("Location: ParentDashboard.php?page=settings&status=already_linked");
            exit();
        }
    } else {
        die("<div style='padding:20px; font-family:sans-serif;'>
                <h2 style='color:red;'>绑定失败：找不到关联码！</h2>
                <p>数据库里没有找到 <b>$code</b> 这个码。请确认你在孩子端（My Profile）看到的码是最新的。</p>
                <a href='ParentDashboard.php?page=settings'>返回上一页</a>
            </div>");
    }
} else {
    die("无效的请求方式。");
}
?>