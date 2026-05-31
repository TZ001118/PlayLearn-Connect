<?php
session_start();
require 'db_conn.php';
require_once 'includes/learning_helpers.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

pl_ensure_learning_schema($conn);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $level_id = trim($_POST['level_id'] ?? '');
    $name = trim($_POST['level_name'] ?? '');
    $desc = trim($_POST['level_description'] ?? '');
    $category = trim($_POST['category'] ?? 'Logic');
    $diff = $_POST['difficulty'] ?? 'Easy';
    $game_url = $_POST['game_url'] ?? 'games/game2048.php';
    $custom_slug = pl_normalize_slug($_POST['custom_slug'] ?? $name);
    $score = 0;
    $skill_weights = $_POST['skill_weights'] ?? [];
    $min_age = $_POST['min_age'] ?? 4;
    $max_age = $_POST['max_age'] ?? 12;

    if ($name === '') {
        die("Game title is required.");
    }

    $level_id_int = $level_id !== '' ? intval($level_id) : 0;
    $slug_check = $conn->prepare("SELECT id FROM levels WHERE custom_slug = ? AND id <> ? LIMIT 1");
    $slug_check->bind_param("si", $custom_slug, $level_id_int);
    $slug_check->execute();
    if ($slug_check->get_result()->num_rows > 0) {
        $slug_check->close();
        die("Game slug already exists.");
    }
    $slug_check->close();

    $image_update_sql = "";
    if (!empty($_FILES["level_image"]["name"])) {
        $target_dir = "assets/uploads/levels/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $file_ext = strtolower(pathinfo($_FILES["level_image"]["name"], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($file_ext, $allowed, true)) {
            die("Unsupported image type.");
        }

        $new_file_name = time() . "_" . uniqid() . "." . $file_ext;
        $target_file = $target_dir . $new_file_name;

        if (move_uploaded_file($_FILES["level_image"]["tmp_name"], $target_file)) {
            $image_update_sql = ", image_url='" . $conn->real_escape_string($target_file) . "'";
        }
    }

    if ($level_id !== '') {
        $sql = "UPDATE levels SET level_name=?, category=?, level_description=?, difficulty=?, game_url=?, custom_slug=? $image_update_sql WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $name, $category, $desc, $diff, $game_url, $custom_slug, $level_id_int);
    } else {
        $target_file = $target_file ?? '';
        $sql = "INSERT INTO levels (level_name, category, level_description, difficulty, target_score, image_url, game_url, custom_slug, package_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'published')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssisss", $name, $category, $desc, $diff, $score, $target_file, $game_url, $custom_slug);
    }

    if ($stmt->execute()) {
        $saved_game_id = $level_id !== '' ? intval($level_id) : intval($conn->insert_id);
        pl_save_game_metadata($conn, $saved_game_id, $skill_weights, $min_age, $max_age);
        header("Location: admin_games.php?msg=success");
        exit();
    }

    echo "Error: " . $conn->error;
}
?>
