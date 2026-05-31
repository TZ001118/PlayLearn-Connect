<?php
session_start();
require 'db_conn.php';
require_once 'includes/learning_helpers.php';
pl_ensure_learning_schema($conn);

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'parent') {
    die("Parent access is required.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}

$parent_id = intval($_SESSION['user_id']);
$username = trim($_POST['child_username'] ?? '');
$password = $_POST['child_password'] ?? '';
$gender = trim($_POST['child_gender'] ?? '');
$ic_number = trim($_POST['child_ic'] ?? '');
$month = intval($_POST['child_month'] ?? 0);
$day = intval($_POST['child_day'] ?? 0);
$year = intval($_POST['child_year'] ?? 0);

if ($username === '' || $password === '' || $ic_number === '' || $month <= 0 || $day <= 0 || $year <= 0) {
    header("Location: ParentDashboard.php?page=settings&status=child_missing_fields");
    exit();
}

$password_error = pl_password_error($password);
if ($password_error !== null) {
    header("Location: ParentDashboard.php?page=settings&status=child_password_invalid");
    exit();
}

$birthday = sprintf('%04d-%02d-%02d', $year, $month, $day);
if (!checkdate($month, $day, $year) || !pl_age_in_range($birthday, 4, 12)) {
    header("Location: ParentDashboard.php?page=settings&status=child_age_invalid");
    exit();
}

$parent_res = $conn->query("SELECT email FROM users WHERE id = $parent_id");
$parent_email = ($parent_res && $row = $parent_res->fetch_assoc()) ? $row['email'] : 'parent@example.com';
$child_email = 'child+' . $parent_id . '+' . preg_replace('/[^a-zA-Z0-9_]/', '', strtolower($username)) . '@playlearn.local';
$hashed = password_hash($password, PASSWORD_DEFAULT);
$role = 'player';
$link_code = strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 6));

try {
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, birthday, gender, ic_number, role, link_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $username, $child_email, $hashed, $birthday, $gender, $ic_number, $role, $link_code);
    $stmt->execute();
    $child_id = intval($conn->insert_id);
    $stmt->close();

    $link_stmt = $conn->prepare("INSERT INTO account_links (parent_id, child_id) VALUES (?, ?)");
    $link_stmt->bind_param("ii", $parent_id, $child_id);
    $link_stmt->execute();
    $link_stmt->close();

    pl_parent_control($conn, $parent_id, $child_id);

    if (function_exists('write_log')) {
        write_log($conn, "Child Account Created", "Parent created and linked child ID $child_id", "Success");
    }

    header("Location: ParentDashboard.php?page=students&child_id=$child_id&status=child_created");
    exit();
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1062) {
        header("Location: ParentDashboard.php?page=settings&status=child_duplicate");
        exit();
    }
    die("Database error: " . $e->getMessage());
}
?>
