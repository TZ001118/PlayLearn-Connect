<?php
session_start();
require 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $level_id = $_POST['level_id']; // 如果有值，说明是编辑
    $name = $_POST['level_name'];
    $desc = $_POST['level_description'];
    $diff = $_POST['difficulty'];
    $game_url = $_POST['game_url'];
    $score = 0;

    // 处理图片上传（如果有新图片）
    $image_update_sql = "";
    if (!empty($_FILES["level_image"]["name"])) {
        $target_dir = "assets/uploads/levels/";
        $file_ext = pathinfo($_FILES["level_image"]["name"], PATHINFO_EXTENSION);
        $new_file_name = time() . "_" . uniqid() . "." . $file_ext;
        $target_file = $target_dir . $new_file_name;
        
        if (move_uploaded_file($_FILES["level_image"]["tmp_name"], $target_file)) {
            $image_update_sql = ", image_url='$target_file'";
        }
    }

    if (!empty($level_id)) {
        // ✅ 执行 UPDATE（编辑）
        $sql = "UPDATE levels SET level_name=?, level_description=?, difficulty=?, game_url=? $image_update_sql WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $desc, $diff, $game_url, $level_id);
    } else {
        // ✅ 执行 INSERT（新增）
        $target_file = isset($target_file) ? $target_file : '';
        $sql = "INSERT INTO levels (level_name, level_description, difficulty, target_score, image_url, game_url) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssiss", $name, $desc, $diff, $score, $target_file, $game_url);
    }

    if ($stmt->execute()) {
        header("Location: admin_games.php?msg=success");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>