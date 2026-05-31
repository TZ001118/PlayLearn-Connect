<?php
session_start();
require 'db_conn.php';
require_once 'includes/learning_helpers.php';
pl_ensure_learning_schema($conn);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_REQUEST['id']) || !isset($_REQUEST['action'])) {
    header("Location: admin_users.php?status=missing_request");
    exit();
}

$user_id = intval($_REQUEST['id']);
$action = $_REQUEST['action'];

if (!in_array($action, ['ban', 'unban'], true)) {
    header("Location: admin_users.php?status=invalid_action");
    exit();
}

if ($action === 'ban') {
    $reason = trim($_POST['ban_reason'] ?? $_GET['reason'] ?? '');
    if ($reason === '') {
        header("Location: admin_users.php?status=ban_reason_required");
        exit();
    }

    $new_status = 'banned';
    $stmt = $conn->prepare("UPDATE users SET status = ?, ban_reason = ?, banned_at = NOW() WHERE id = ?");
    $stmt->bind_param("ssi", $new_status, $reason, $user_id);
} else {
    $new_status = 'active';
    $stmt = $conn->prepare("UPDATE users SET status = ?, ban_reason = NULL, banned_at = NULL WHERE id = ?");
    $stmt->bind_param("si", $new_status, $user_id);
}

if ($stmt->execute()) {
    if (function_exists('write_log')) {
        write_log($conn, "User Management", "Admin changed status of User ID #$user_id to $new_status", "Success");
    }
    header("Location: admin_users.php?status=updated");
    exit();
}

if (function_exists('write_log')) {
    write_log($conn, "User Management", "Failed to change status for User ID #$user_id. Error: " . $conn->error, "Failed");
}

echo "Error updating record: " . $conn->error;
$stmt->close();
$conn->close();
?>
