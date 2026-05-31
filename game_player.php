<?php
session_start();
require 'db_conn.php';
include('maintenance_check.php');
require_once 'includes/learning_helpers.php';
pl_ensure_learning_schema($conn);

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'player') {
    header("Location: login.php");
    exit();
}

$game_id = intval($_GET['id'] ?? 0);
if ($game_id <= 0) {
    die("Game not found.");
}

$stmt = $conn->prepare("SELECT id, level_name, level_description, package_path, game_url, package_status, package_manifest FROM levels WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $game_id);
$stmt->execute();
$game = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$game || ($game['package_status'] ?? 'published') !== 'published') {
    die("Game is not available.");
}

$manifest = json_decode($game['package_manifest'] ?? '', true);
$entry_file = is_array($manifest) ? trim($manifest['entry'] ?? 'index.html') : 'index.html';
$package_path = trim($game['package_path'] ?? '');
$entry = $package_path !== '' ? $package_path . '/' . $entry_file : '';
if ($entry === '' || strpos($entry, 'games/custom/') !== 0) {
    die("This player is only for packaged games.");
}

$separator = strpos($entry, '?') === false ? '?' : '&';
$iframe_src = $entry . $separator . http_build_query([
    'playlearn_game_id' => intval($game['id']),
    'playlearn_game_name' => $game['level_name'],
]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlayLearn | <?php echo htmlspecialchars($game['level_name']); ?></title>
    <style>
        body { margin: 0; font-family: Nunito, Arial, sans-serif; background: #f4f6f9; color: #0f172a; }
        .topbar { height: 64px; display: flex; align-items: center; justify-content: space-between; padding: 0 22px; background: #fff; box-shadow: 0 8px 24px rgba(15,23,42,0.08); }
        .brand { font-weight: 900; font-size: 20px; }
        .back { border: 0; background: #ff6b6b; color: #fff; border-radius: 14px; padding: 10px 18px; font-weight: 900; cursor: pointer; }
        iframe { width: 100%; height: calc(100vh - 64px); border: 0; display: block; background: #fff; }
    </style>
</head>
<body>
    <div class="topbar">
        <button class="back" onclick="window.location.href='homepage.php'">Back to Hub</button>
        <div class="brand"><?php echo htmlspecialchars($game['level_name']); ?></div>
        <div></div>
    </div>
    <iframe src="<?php echo htmlspecialchars($iframe_src); ?>" allow="fullscreen"></iframe>
</body>
</html>
