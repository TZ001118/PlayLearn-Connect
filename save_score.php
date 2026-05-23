<?php
// save_score.php - 增强审计与安全版
session_start();
header('Content-Type: application/json');
require 'db_conn.php'; 

// 1. 核心权限检查：必须登录且角色必须是玩家
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'player') {
    // 🛑 记录非法刷分尝试 (只有在已加载 logger.php 的情况下有效)
    if (function_exists('write_log')) {
        write_log($conn, "Security Violation", "Unauthorized score submission attempt", "Unauthorized");
    }
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. 接收并解析数据
$game_name = '';
$score = 0;
$level_reached = 1;

// 尝试获取 JSON 输入
$content = file_get_contents('php://input');
$json_data = json_decode($content, true);

if ($json_data) {
    $game_name = $json_data['game_name'] ?? '';
    $score = intval($json_data['score'] ?? 0);
    $level_reached = intval($json_data['level_reached'] ?? 1);
} else {
    $game_name = $_POST['game_name'] ?? '';
    $score = intval($_POST['score'] ?? 0);
    $level_reached = intval($_POST['level_reached'] ?? 1);
}

// 3. 数据基本校验
if (empty($game_name)) {
    echo json_encode(["status" => "error", "message" => "Game name is missing."]);
    exit();
}

// 4. 写入数据库
try {
    $stmt = $conn->prepare("INSERT INTO game_scores (user_id, game_name, score, level_reached, played_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("isii", $user_id, $game_name, $score, $level_reached);

    if ($stmt->execute()) {
        // 🟢 记录分数审计日志
        if (function_exists('write_log')) {
            write_log($conn, "Game Score", "Saved score $score for game: $game_name (Lvl $level_reached)", "Success");
        }

        // ✅ 返回成功响应（仅此一次）
        echo json_encode([
            "status" => "success", 
            "message" => "Score saved successfully!",
            "details" => [
                "game" => $game_name,
                "score" => $score,
                "level" => $level_reached
            ]
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Execute failed: " . $stmt->error]);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}

$conn->close();
?>