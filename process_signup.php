<?php
header('Content-Type: application/json');
session_start();
require 'db_conn.php';
require_once 'includes/learning_helpers.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$email = trim($_POST['email'] ?? '');
$month = $_POST['month'] ?? '';
$day = $_POST['day'] ?? '';
$year = $_POST['year'] ?? '';
$gender = trim($_POST['gender'] ?? '');
$user_otp = trim($_POST['otp_code'] ?? '');
$role = 'parent';

if ($username === '' || $password === '' || $email === '' || $user_otp === '') {
    echo json_encode(["status" => "error", "message" => "All fields including OTP are required."]);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Please enter a valid email address."]);
    exit();
}

$password_error = pl_password_error($password);
if ($password_error !== null) {
    echo json_encode(["status" => "error", "message" => $password_error]);
    exit();
}

if (!isset($_SESSION['sent_otp']) || !isset($_SESSION['otp_expiry'])) {
    echo json_encode(["status" => "error", "message" => "OTP not found. Please request a new one."]);
    exit();
}

if (time() > $_SESSION['otp_expiry']) {
    echo json_encode(["status" => "error", "message" => "OTP has expired."]);
    exit();
}

if ($user_otp !== $_SESSION['sent_otp']) {
    echo json_encode(["status" => "error", "message" => "Invalid verification code."]);
    exit();
}

if (empty($year) || empty($month) || empty($day)) {
    $birthday = "2000-01-01";
} else {
    $birthday = "$year-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-" . str_pad($day, 2, "0", STR_PAD_LEFT);
}

$parent_age = pl_child_age($birthday);
if ($parent_age !== null && $parent_age < 18) {
    echo json_encode(["status" => "error", "message" => "Parent accounts must be registered by an adult."]);
    exit();
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, birthday, gender, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $email, $hashed_password, $birthday, $gender, $role);

    if ($stmt->execute()) {
        unset($_SESSION['sent_otp'], $_SESSION['otp_expiry']);
        echo json_encode(["status" => "success", "message" => "Parent registration successful."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Registration failed."]);
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1062) {
        echo json_encode(["status" => "error", "message" => "Username or Email already exists."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
}

$conn->close();
?>
