<?php
session_start();
require 'db_conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$review_id = isset($_POST['review_id']) ? intval($_POST['review_id']) : intval($_GET['review_id'] ?? 0);

if ($review_id <= 0) {
    header("Location: admin_reviews.php?msg=invalid_review");
    exit();
}

$stmt = $conn->prepare("DELETE FROM game_reviews WHERE id = ?");
$stmt->bind_param("i", $review_id);

if ($stmt->execute()) {
    if (function_exists('write_log')) {
        write_log($conn, "Review Management", "Admin deleted review ID #$review_id", "Success");
    }
    header("Location: admin_reviews.php?msg=review_deleted");
    exit();
}

if (function_exists('write_log')) {
    write_log($conn, "Review Management", "Failed to delete review ID #$review_id", "Failed");
}

header("Location: admin_reviews.php?msg=delete_failed");
exit();
?>
