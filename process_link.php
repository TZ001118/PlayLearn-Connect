<?php
session_start();
require 'db_conn.php';
require_once 'includes/learning_helpers.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'parent') {
    die("Please log in with a parent account.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}

$code = strtoupper(trim($_POST['child_code'] ?? ''));
$parent_id = intval($_SESSION['user_id']);

if ($code === '') {
    header("Location: ParentDashboard.php?page=settings&status=invalid_code");
    exit();
}

$stmt = $conn->prepare("SELECT id, username, role FROM users WHERE link_code = ? LIMIT 1");
$stmt->bind_param("s", $code);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    header("Location: ParentDashboard.php?page=settings&status=invalid_code");
    exit();
}

$child = $res->fetch_assoc();
if ($child['role'] !== 'player') {
    header("Location: ParentDashboard.php?page=settings&status=invalid_code");
    exit();
}

$child_id = intval($child['id']);
$check = $conn->prepare("SELECT link_id FROM account_links WHERE parent_id = ? AND child_id = ?");
$check->bind_param("ii", $parent_id, $child_id);
$check->execute();

if ($check->get_result()->num_rows > 0) {
    header("Location: ParentDashboard.php?page=settings&status=already_linked");
    exit();
}

$insert = $conn->prepare("INSERT INTO account_links (parent_id, child_id) VALUES (?, ?)");
$insert->bind_param("ii", $parent_id, $child_id);

if ($insert->execute()) {
    pl_parent_control($conn, $parent_id, $child_id);
    header("Location: ParentDashboard.php?page=overview&child_id=$child_id&status=link_success");
    exit();
}

die("Database error: " . $conn->error);
?>
