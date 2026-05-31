<?php
session_start();
require 'db_conn.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'parent') {
    die("Error: Parent access is required.");
}

$current_user_id = intval($_SESSION['user_id']);
$child_id = isset($_GET['child_id']) ? intval($_GET['child_id']) : 0;
$redirect = $_GET['redirect'] ?? 'settings';
$safe_redirect = in_array($redirect, ['settings', 'history'], true) ? $redirect : 'settings';

if ($child_id === 0) {
    die("Error: No child account selected for deletion.");
}

$check_stmt = $conn->prepare("SELECT link_id FROM account_links WHERE parent_id = ? AND child_id = ?");
$check_stmt->bind_param("ii", $current_user_id, $child_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    die("<div style='font-family: sans-serif; padding: 40px; text-align: center;'>
            <h2 style='color: #e74c3c;'>Security Error: Unauthorized Action</h2>
            <p style='color: #555;'>You do not have permission to delete data for this account.</p>
            <a href='ParentDashboard.php?page=settings' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background: #4A90E2; color: white; text-decoration: none; border-radius: 8px;'>Return to Settings</a>
         </div>");
}

$del_stmt = $conn->prepare("DELETE FROM game_scores WHERE user_id = ?");
$del_stmt->bind_param("i", $child_id);

if ($del_stmt->execute()) {
    if (function_exists('write_log')) {
        write_log($conn, "Parent Data Reset", "Parent cleared game history for child ID $child_id", "Success");
    }
    header("Location: ParentDashboard.php?page=$safe_redirect&child_id=$child_id&status=history_cleared");
    exit();
}

die("Database Error: " . $conn->error);
?>
