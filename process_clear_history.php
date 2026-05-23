<?php
session_start();
require 'db_conn.php';

// 1. Check login status
if (!isset($_SESSION['user_id'])) {
    die("Error: Please log in first.");
}

$current_user_id = $_SESSION['user_id'];
$child_id = isset($_GET['child_id']) ? intval($_GET['child_id']) : 0;

if ($child_id === 0) {
    die("Error: No child account selected for deletion.");
}

// 2. 🛡️ Strict Security Verification
// Ensure the person attempting the deletion is actually the linked parent
$check_stmt = $conn->prepare("SELECT link_id FROM account_links WHERE parent_id = ? AND child_id = ?");
$check_stmt->bind_param("ii", $current_user_id, $child_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    // Return a professional English error message if sessions conflict
    die("<div style='font-family: sans-serif; padding: 40px; text-align: center;'>
            <h2 style='color: #e74c3c;'>Security Error: Unauthorized Action</h2>
            <p style='color: #555;'>You do not have permission to delete data for this account.</p>
            <p style='font-size: 12px; color: #999; margin-top: 20px;'>
                Diagnostic Info: Current Session ID #$current_user_id | Target Child ID #$child_id<br>
                <i>Hint: If you are testing both Parent and Child accounts on the same browser, your session may have overlapped. Please log out and log in as Parent again.</i>
            </p>
            <a href='ParentDashboard.php?page=settings' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background: #4A90E2; color: white; text-decoration: none; border-radius: 8px;'>Return to Settings</a>
         </div>");
}

// 3. 🗑️ Execute Deletion
$del_stmt = $conn->prepare("DELETE FROM game_scores WHERE user_id = ?");
$del_stmt->bind_param("i", $child_id);

if ($del_stmt->execute()) {
    // Successfully deleted, return to settings with a success flag
    header("Location: ParentDashboard.php?page=settings&child_id=$child_id&status=history_cleared");
    exit();
} else {
    die("Database Error: " . $conn->error);
}
?>