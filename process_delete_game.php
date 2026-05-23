<?php
session_start();
require 'db_conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { die("Unauthorized"); }

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // 先查出图片路径，把服务器上的图片也删掉，节省空间
    $res = $conn->query("SELECT image_url FROM levels WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        if (file_exists($row['image_url'])) {
            unlink($row['image_url']); 
        }
    }
    
    // 删除数据库记录
    $conn->query("DELETE FROM levels WHERE id = $id");
}

header("Location: admin_games.php");
?>