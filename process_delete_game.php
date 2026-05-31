<?php
session_start();
require 'db_conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { die("Unauthorized"); }

function delete_directory_safe($dir) {
    $base = realpath(__DIR__ . '/games/custom');
    $target = realpath($dir);
    if (!$base || !$target || strpos($target, $base) !== 0 || !is_dir($target)) {
        return;
    }
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($target, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($items as $item) {
        $item->isDir() ? rmdir($item->getRealPath()) : unlink($item->getRealPath());
    }
    rmdir($target);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Remove the stored cover image and packaged game folder when possible.
    $res = $conn->query("SELECT image_url, package_path FROM levels WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        if (file_exists($row['image_url'])) {
            unlink($row['image_url']); 
        }
        if (!empty($row['package_path'])) {
            delete_directory_safe(__DIR__ . '/' . $row['package_path']);
        }
    }
    
    // Remove the database record.
    $conn->query("DELETE FROM levels WHERE id = $id");
}

header("Location: admin_games.php");
?>
