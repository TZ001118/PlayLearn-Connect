<?php
session_start();
header('Content-Type: application/json');
require 'db_conn.php';
require_once 'includes/learning_helpers.php';

pl_ensure_learning_schema($conn);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'player') {
    if (function_exists('write_log')) {
        write_log($conn, "Security Violation", "Unauthorized score submission attempt", "Unauthorized");
    }
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit();
}

$user_id = intval($_SESSION['user_id']);
$content = file_get_contents('php://input');
$json_data = json_decode($content, true);
$payload = is_array($json_data) ? $json_data : $_POST;

$game_name = trim($payload['game_name'] ?? '');
$game_id = intval($payload['game_id'] ?? 0);
$score = intval($payload['score'] ?? 0);
$level_reached = intval($payload['level_reached'] ?? 1);
$correct_answers = isset($payload['correct_answers']) ? intval($payload['correct_answers']) : null;
$total_questions = isset($payload['total_questions']) ? intval($payload['total_questions']) : null;
$reaction_time_ms = isset($payload['reaction_time_ms']) ? intval($payload['reaction_time_ms']) : null;
$duration_seconds = isset($payload['duration_seconds']) ? intval($payload['duration_seconds']) : null;

if ($game_name === '' && $game_id > 0) {
    $lookup_by_id = $conn->prepare("SELECT level_name FROM levels WHERE id = ? LIMIT 1");
    $lookup_by_id->bind_param("i", $game_id);
    $lookup_by_id->execute();
    $id_match = $lookup_by_id->get_result();
    if ($id_match && $id_match->num_rows > 0) {
        $game_name = $id_match->fetch_assoc()['level_name'];
    }
    $lookup_by_id->close();
}

if ($game_name === '') {
    echo json_encode(["status" => "error", "message" => "Game name is missing."]);
    exit();
}

$lookup = $conn->prepare("SELECT level_name FROM levels WHERE level_name = ? LIMIT 1");
$lookup->bind_param("s", $game_name);
$lookup->execute();
$level_match = $lookup->get_result();
if ($level_match->num_rows === 0 && stripos($game_name, 'memory') !== false) {
    $fallback = $conn->query("SELECT level_name FROM levels WHERE level_name LIKE '%Memory%' LIMIT 1");
    if ($fallback && $fallback->num_rows > 0) {
        $game_name = $fallback->fetch_assoc()['level_name'];
    }
}
$lookup->close();

if ($total_questions !== null && $total_questions < 0) {
    $total_questions = null;
}

if ($correct_answers !== null && $correct_answers < 0) {
    $correct_answers = null;
}

if ($correct_answers !== null && $total_questions !== null && $correct_answers > $total_questions) {
    $correct_answers = $total_questions;
}

try {
    $stmt = $conn->prepare("INSERT INTO game_scores (user_id, game_name, score, level_reached, correct_answers, total_questions, reaction_time_ms, duration_seconds, played_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("isiiiiii", $user_id, $game_name, $score, $level_reached, $correct_answers, $total_questions, $reaction_time_ms, $duration_seconds);

    if ($stmt->execute()) {
        if (function_exists('write_log')) {
            write_log($conn, "Game Score", "Saved score $score for game: $game_name (Lvl $level_reached)", "Success");
        }

        echo json_encode([
            "status" => "success",
            "message" => "Score saved successfully.",
            "details" => [
                "game" => $game_name,
                "score" => $score,
                "level" => $level_reached,
                "correct_answers" => $correct_answers,
                "total_questions" => $total_questions,
                "reaction_time_ms" => $reaction_time_ms,
                "duration_seconds" => $duration_seconds
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
