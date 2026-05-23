<?php
session_start();
require 'db_conn.php'; 
include('maintenance_check.php');
// =================================================================================
// 1. AJAX HANDLERS (MUST BE AT THE TOP)
// =================================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
        exit;
    }
    $user_id = intval($_SESSION['user_id']);

    // --- Action 1: Upload Avatar ---
    if ($_POST['action'] === 'upload_avatar') {
        $base64_string = $_POST['avatar_base64'] ?? '';
        if (preg_match('/^data:image\/(\w+);base64,/', $base64_string, $type)) {
            $data = substr($base64_string, strpos($base64_string, ',') + 1);
            $type = strtolower($type[1]); 
            if (in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                $data = base64_decode($data);
                $upload_dir = 'uploads/avatars/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                
                $filename = $upload_dir . 'avatar_' . $user_id . '_' . time() . '.' . $type;
                $old_query = $conn->query("SELECT profile_image FROM users WHERE id = $user_id");
                if ($old_query && $row = $old_query->fetch_assoc()) {
                    if (!empty($row['profile_image']) && file_exists($row['profile_image'])) unlink($row['profile_image']); 
                }
                if (file_put_contents($filename, $data)) {
                    $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
                    $stmt->bind_param("si", $filename, $user_id);
                    if ($stmt->execute()) { echo json_encode(['status' => 'success', 'filepath' => $filename]); exit; }
                }
            }
        }
        echo json_encode(['status' => 'error', 'message' => 'Failed to process image.']);
        exit;
    }

    // --- Action 2: Update Profile (Gender & Birthday) ---
    if ($_POST['action'] === 'update_profile') {
        $gender = trim($_POST['gender'] ?? '');
        $birthday = trim($_POST['birthday'] ?? '');
        if (empty($birthday)) $birthday = NULL;

        $stmt = $conn->prepare("UPDATE users SET gender = ?, birthday = ? WHERE id = ?");
        $stmt->bind_param("ssi", $gender, $birthday, $user_id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database update failed.']);
        }
        exit;
    }

    // --- Action 3: Change Username ---
    if ($_POST['action'] === 'change_username') {
        $new_username = trim($_POST['new_username'] ?? '');
        if (empty($new_username)) {
            echo json_encode(['status' => 'error', 'message' => 'Username cannot be empty.']); exit;
        }
        
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->bind_param("si", $new_username, $user_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Username is already taken by someone else.']); exit;
        }
        
        $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->bind_param("si", $new_username, $user_id);
        $stmt->execute();
        
        $_SESSION['username'] = $new_username; 
        
        echo json_encode(['status' => 'success']);
        exit;
    }

    // --- Action 4: Change Password ---
    if ($_POST['action'] === 'change_password') {
        $old_pwd = $_POST['old_password'] ?? '';
        $new_pwd = $_POST['new_password'] ?? '';
        
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user_data = $stmt->get_result()->fetch_assoc();
        
        if (password_verify($old_pwd, $user_data['password'])) {
            $hashed = password_hash($new_pwd, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed, $user_id);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update database.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Incorrect current password.']);
        }
        exit;
    }
}
// =================================================================================

// 2. Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'player') {
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

// 3. Fetch user details
$sql = "SELECT username, email, link_code, profile_image, avatar_frame, level, total_exp, title, title_expires_at, gender, birthday, created_at FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// 4. Linking code logic
$my_code = $user['link_code'] ?? "";
if (empty($my_code)) {
    $my_code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
    $conn->query("UPDATE users SET link_code = '$my_code' WHERE id = $user_id");
}

// 5. Fetch Quick Stats
$stats_sql = "SELECT COUNT(*) as total_games, SUM(score) as total_score FROM game_scores WHERE user_id = ?";
$s_stmt = $conn->prepare($stats_sql);
$s_stmt->bind_param("i", $user_id);
$s_stmt->execute();
$stats = $s_stmt->get_result()->fetch_assoc();
$total_games = $stats['total_games'] ?? 0;
$total_score_calc = $stats['total_score'] ?? 0;

// 6. Fetch recent game history
$history_sql = "SELECT game_name, score, played_at FROM game_scores WHERE user_id = ? ORDER BY played_at DESC LIMIT 6";
$h_stmt = $conn->prepare($history_sql);
$h_stmt->bind_param("i", $user_id);
$h_stmt->execute();
$history_res = $h_stmt->get_result();

// 7. Calculate Gamification Data
$current_level = isset($user['level']) && $user['level'] > 0 ? intval($user['level']) : 1;
$total_exp = isset($user['total_exp']) ? intval($user['total_exp']) : 0;
$exp_required_for_next = $current_level * 500; 
$exp_percentage = min(100, ($total_exp / $exp_required_for_next) * 100);

$display_title = "Novice Player"; 
$title_badge_class = "bg-slate-100 text-slate-500 border-slate-200";

if (!empty($user['title'])) {
    $is_expired = false;
    if (!empty($user['title_expires_at']) && time() > strtotime($user['title_expires_at'])) {
        $is_expired = true;
    }
    if (!$is_expired) {
        $display_title = $user['title'];
        if (stripos($display_title, 'Master') !== false || stripos($display_title, 'Champion') !== false) {
            $title_badge_class = "bg-yellow-100 text-yellow-600 border-yellow-300 shadow-sm shadow-yellow-200";
        } else {
            $title_badge_class = "bg-blobPurple/10 text-blobPurple border-blobPurple/30 shadow-sm shadow-blobPurple/20";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlayLearn | My Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;800;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Nunito', 'sans-serif'] },
                    colors: { primary: '#0f172a', blobPurple: '#6C3FF5', blobOrange: '#FF9B6B', blobYellow: '#E8D754' }
                }
            }
        }
    </script>
    
    <style>
        .reveal-up { opacity: 0; transform: translateY(40px) scale(0.97); transition: all 0.8s cubic-bezier(0.25, 1, 0.2, 1); }
        .reveal-up.active { opacity: 1; transform: translateY(0) scale(1); }
        .cascade-item { opacity: 0; transform: translateY(40px) scale(0.97); }
        .cascade-item.active { opacity: 1; transform: translateY(0) scale(1); }

        .smart-nav { background: transparent; transition: all 0.3s ease; }
        .smart-nav.scrolled {
            background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2); box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .avatar-container { position: relative; display: inline-block; cursor: pointer; transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .avatar-container:hover { transform: scale(1.05); }
        
        .avatar-wrapper {
            width: 120px; height: 120px; background: linear-gradient(135deg, #6C3FF5 0%, #FF9B6B 100%);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            color: white; font-size: 3rem; font-weight: 900; overflow: hidden;
            box-shadow: 0 10px 25px rgba(108, 63, 245, 0.3); position: relative; z-index: 5;
        }
        .avatar-wrapper img { width: 100%; height: 100%; object-fit: cover; }
        
        .level-badge {
            position: absolute; bottom: -5px; left: 50%; transform: translateX(-50%);
            background: #0f172a; color: #E8D754; font-weight: 900; font-size: 0.75rem;
            padding: 2px 12px; border-radius: 20px; border: 2px solid white; z-index: 20;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .avatar-frame { position: absolute; top: -15px; left: -15px; right: -15px; bottom: -15px; pointer-events: none; z-index: 15; }

        .avatar-hover-overlay {
            position: absolute; inset: 0; background: rgba(0,0,0,0.5); border-radius: 50%; 
            display: flex; align-items: center; justify-content: center; color: white; opacity: 0; transition: opacity 0.2s; font-size: 1.5rem; z-index: 10;
        }
        .avatar-container:hover .avatar-hover-overlay { opacity: 1; }

        .progress-fill { transition: width 1s cubic-bezier(0.4, 0, 0.2, 1); }

        .global-modal-overlay {
            position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); z-index: 9999;
            display: flex; align-items: center; justify-content: center; backdrop-filter: blur(6px);
            visibility: hidden; opacity: 0; transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .global-modal-overlay.active { visibility: visible; opacity: 1; }
        .global-modal-card { 
            background: white; border-radius: 24px; width: 90%; max-width: 500px; padding: 28px; 
            text-align: center; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform: scale(0.95); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); max-height: 90vh; overflow-y: auto;
        }
        .global-modal-overlay.active .global-modal-card { transform: scale(1); }
        
        .cropper-container-div { width: 100%; height: 320px; background: #f1f5f9; border-radius: 16px; overflow: hidden; margin: 20px 0; }
        .cropper-view-box, .cropper-face { border-radius: 50% !important; }
        
        /* 🌟 Flatpickr Theme */
        .flatpickr-calendar {
            border-radius: 20px !important; box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
            border: 1px solid #f1f5f9 !important; padding: 10px !important; font-family: 'Nunito', sans-serif !important;
        }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover, .flatpickr-day.selected.prevMonthDay, .flatpickr-day.startRange.prevMonthDay, .flatpickr-day.endRange.prevMonthDay, .flatpickr-day.selected.nextMonthDay, .flatpickr-day.startRange.nextMonthDay, .flatpickr-day.endRange.nextMonthDay {
            background: #FF9B6B !important; border-color: #FF9B6B !important; font-weight: 900;
        }
        .flatpickr-day:hover { background: #fff5f0 !important; border-color: #fff5f0 !important; color: #FF9B6B !important; font-weight: 900;}
        .flatpickr-current-month .flatpickr-monthDropdown-months, .flatpickr-current-month .numInputWrapper { font-weight: 900 !important; color: #0f172a !important; }
    </style>
</head>
<body class="bg-[#f8fafc]">

    <header id="mainNav" class="smart-nav fixed w-full top-0 z-40 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 h-16 flex justify-between items-center">
            <a href="homepage.php" class="flex items-center gap-2 text-2xl font-black tracking-tight text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-blobPurple"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1-1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                Play<span class="text-blobOrange">Learn</span>
            </a>
            <div class="flex items-center gap-4">
                <a href="homepage.php" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-5 py-2 rounded-full font-bold transition-colors flex items-center gap-2"><i class="fas fa-home"></i> Back to Hub</a>
                <a href="javascript:void(0)" onclick="openLogoutModal()" class="w-10 h-10 flex items-center justify-center bg-white/60 backdrop-blur hover:bg-red-100 hover:text-red-500 border border-slate-200 rounded-full transition-all shadow-sm"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
    </header>

    <main class="relative w-full pt-28 pb-20 overflow-hidden min-h-screen">
        <div class="absolute top-[-10%] left-[-5%] w-[400px] h-[400px] bg-blobPurple/10 rounded-full blur-[80px] -z-10"></div>
        <div class="absolute top-[20%] right-[-5%] w-[300px] h-[300px] bg-blobOrange/10 rounded-full blur-[80px] -z-10"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-[2rem] p-8 shadow-xl border border-slate-100 text-center relative reveal-up">
                        
                        <div class="mb-5 relative inline-block">
                            <div class="avatar-container" onclick="document.getElementById('avatarInput').click()" title="Click to Change Avatar">
                                <div class="avatar-wrapper" id="displayAvatarWrapper">
                                    <?php if (!empty($user['profile_image'])): ?>
                                        <img id="displayAvatar" src="<?= htmlspecialchars($user['profile_image']) ?>" alt="Avatar">
                                    <?php else: ?>
                                        <span id="initialAvatar"><?= strtoupper(substr($user['username'], 0, 1)) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="avatar-hover-overlay"><i class="fas fa-camera"></i></div>
                                <div id="frameOverlayContainer">
                                    <?php if (!empty($user['avatar_frame'])): ?>
                                        <img src="<?= htmlspecialchars($user['avatar_frame']) ?>" class="avatar-frame" alt="Frame">
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="level-badge">Lv.<?php echo $current_level; ?></div>
                        </div>

                        <input type="file" id="avatarInput" accept="image/*" class="hidden">

                        <h2 id="displayUsername" class="text-2xl font-black text-primary mb-1"><?= htmlspecialchars($user['username']) ?></h2>
                        <div class="mb-4">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border <?php echo $title_badge_class; ?>">
                                <?php echo htmlspecialchars($display_title); ?>
                            </span>
                        </div>
                        
                        <p class="text-slate-500 font-semibold text-sm mb-4 bg-slate-50 py-2 rounded-xl border border-slate-100">
                            <i class="far fa-envelope text-slate-400 mr-2"></i><span id="displayEmail"><?= htmlspecialchars($user['email']) ?></span>
                        </p>

                        <div class="flex items-center justify-between bg-slate-50 p-4 rounded-xl border border-slate-100 mb-4 relative group">
                            <div class="text-left">
                                <p class="text-[10px] font-black text-slate-400 uppercase">Gender</p>
                                <p class="text-sm font-bold text-slate-700 capitalize" id="displayGender">
                                    <?= !empty($user['gender']) ? htmlspecialchars($user['gender']) : '<span class="text-slate-400 italic">Not set</span>' ?>
                                </p>
                            </div>
                            <div class="text-left border-l border-slate-200 pl-4">
                                <p class="text-[10px] font-black text-slate-400 uppercase">Birthday</p>
                                <p class="text-sm font-bold text-slate-700" id="displayBirthday">
                                    <?= !empty($user['birthday']) ? date('M d, Y', strtotime($user['birthday'])) : '<span class="text-slate-400 italic">Not set</span>' ?>
                                </p>
                            </div>
                            <button onclick="openProfileModal()" class="w-8 h-8 bg-white hover:bg-blobPurple hover:text-white rounded-full shadow-sm text-slate-400 transition-colors flex items-center justify-center border border-slate-200 absolute -top-3 -right-3 opacity-0 group-hover:opacity-100">
                                <i class="fas fa-pen text-xs"></i>
                            </button>
                        </div>

                        <button onclick="openSecurityModal()" class="w-full mb-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2.5 rounded-full transition-all flex items-center justify-center gap-2 shadow-sm border border-slate-200">
                            <i class="fas fa-shield-alt text-blobPurple"></i> Account Security
                        </button>

                        <button onclick="openFrameModal()" class="w-full mb-8 bg-white border-2 border-slate-100 hover:border-blobPurple/40 hover:bg-blobPurple/5 text-slate-600 hover:text-blobPurple font-bold py-2.5 rounded-full transition-all flex items-center justify-center gap-2 shadow-sm">
                            <i class="fas fa-magic"></i> Change Avatar Frame
                        </button>

                        <div class="text-left mb-8 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <div class="flex justify-between items-end mb-2">
                                <span class="text-xs font-black text-slate-400 uppercase">EXP to Lv.<?php echo $current_level + 1; ?></span>
                                <span class="text-sm font-black text-blobOrange"><?php echo $total_exp; ?> / <?php echo $exp_required_for_next; ?></span>
                            </div>
                            <div class="h-2.5 w-full bg-slate-200 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blobOrange to-blobYellow rounded-full progress-fill" style="width: <?php echo $exp_percentage; ?>%;"></div>
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 mt-2 text-center">Complete Quests on Homepage to level up!</p>
                        </div>

                        <div class="bg-rose-50/50 border-2 border-dashed border-rose-200 rounded-2xl p-5 mb-4 relative overflow-hidden group">
                            <p class="text-xs font-black text-rose-400 uppercase tracking-widest mb-1">Linking Code</p>
                            <div class="text-3xl font-black text-rose-500 tracking-[0.2em] my-2" id="myLinkCode"><?= $my_code ?></div>
                            <p class="text-xs text-rose-400/80 font-bold">Share this with your parent</p>
                        </div>
                        
                        <button id="copyBtn" class="w-full bg-primary text-white hover:bg-slate-800 font-bold py-3 rounded-full transition-all duration-300 flex items-center justify-center gap-2 shadow-lg" onclick="copyCode('<?= $my_code ?>')">
                            <i class="far fa-copy"></i> <span>Copy Link Code</span>
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="grid grid-cols-3 gap-4 mb-6 reveal-up">
                        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 text-center hover:-translate-y-1 transition-transform">
                            <div class="text-[10px] font-black text-slate-400 uppercase mb-1">Games Played</div>
                            <div class="text-3xl font-black text-blobPurple"><?= $total_games ?></div>
                        </div>
                        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 text-center hover:-translate-y-1 transition-transform">
                            <div class="text-[10px] font-black text-slate-400 uppercase mb-1">Total Score</div>
                            <div class="text-3xl font-black text-blobOrange"><?= $total_score_calc ?></div>
                        </div>
                        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 text-center hover:-translate-y-1 transition-transform">
                            <div class="text-[10px] font-black text-slate-400 uppercase mb-1">Joined Since</div>
                            <div class="text-xl font-black text-slate-700 mt-2"><?= date('M Y', strtotime($user['created_at'])) ?></div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2rem] p-8 shadow-xl border border-slate-100 reveal-up" style="animation-delay: 0.1s;">
                        <div class="flex items-center justify-between mb-8 border-b border-slate-100 pb-4">
                            <div>
                                <h3 class="text-2xl font-black text-primary flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blobYellow/20 text-blobYellow rounded-xl flex items-center justify-center"><i class="fas fa-trophy"></i></div>
                                    Combat History
                                </h3>
                                <p class="text-sm font-bold text-slate-400 mt-1">Your recent game achievements and score records.</p>
                            </div>
                        </div>

                        <div id="cascade-list" class="flex flex-col gap-4">
                            <?php if($history_res->num_rows > 0): ?>
                                <?php while($row = $history_res->fetch_assoc()): ?>
                                <div class="cascade-item flex items-center justify-between bg-slate-50 hover:bg-slate-100 p-4 rounded-2xl border border-slate-100 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center text-xl text-blobPurple border border-slate-100">
                                            <i class="fas fa-gamepad"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-black text-primary text-base"><?= htmlspecialchars($row['game_name']) ?></h4>
                                            <p class="text-xs font-bold text-slate-400"><i class="far fa-calendar-alt mr-1"></i><?= date('M d, Y - H:i', strtotime($row['played_at'])) ?></p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="block text-lg font-black text-emerald-500">+<?= number_format($row['score']) ?></span>
                                        <span class="text-[10px] font-black text-slate-400 uppercase">Score</span>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="text-center py-16 cascade-item">
                                    <div class="w-20 h-20 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center text-4xl mx-auto mb-4"><i class="fas fa-ghost"></i></div>
                                    <h4 class="font-black text-primary text-lg">No records yet</h4>
                                    <p class="text-sm font-bold text-slate-400 mt-1 mb-6">Your game history will appear here once you start playing.</p>
                                    <a href="homepage.php" class="inline-block bg-blobOrange text-white font-black px-8 py-3 rounded-full shadow-lg shadow-blobOrange/30 hover:-translate-y-1 transition-transform">Start Playing</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <div id="securityModal" class="global-modal-overlay">
        <div class="global-modal-card text-left">
            <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-3">
                <h4 class="text-xl font-black text-primary"><i class="fas fa-shield-alt text-blobPurple me-2"></i>Account Security</h4>
                <button onclick="closeSecurityModal()" class="text-slate-400 hover:text-red-500 transition-colors"><i class="fas fa-times text-lg"></i></button>
            </div>
            
            <div class="mb-5 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <label class="block text-xs font-black text-slate-500 uppercase mb-2">Change Username</label>
                <div class="flex gap-2">
                    <input type="text" id="secNewUsername" placeholder="New Username" value="<?= htmlspecialchars($user['username']) ?>" class="flex-1 bg-white border border-slate-200 text-slate-700 rounded-xl px-4 py-2 outline-none focus:border-blobPurple font-bold">
                    <button onclick="updateUsername()" id="btnUpdateUsername" class="bg-primary hover:bg-slate-800 text-white font-bold px-4 py-2 rounded-xl transition-colors">Update</button>
                </div>
            </div>

            <div class="mb-5 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <label class="block text-xs font-black text-slate-500 uppercase mb-2">Change Email Address</label>
                <div class="flex gap-2 mb-2">
                    <input type="email" id="secNewEmail" placeholder="New Email" value="<?= htmlspecialchars($user['email']) ?>" class="flex-1 bg-white border border-slate-200 text-slate-700 rounded-xl px-4 py-2 outline-none focus:border-blobPurple font-bold">
                    <button onclick="requestStudentOtp()" id="btnSendOtp" class="bg-blobPurple hover:bg-blobPurple/90 text-white font-bold px-4 py-2 rounded-xl transition-colors">Send OTP</button>
                </div>
                <div id="secOtpArea" class="hidden flex gap-2">
                    <input type="text" id="secOtpCode" placeholder="6-digit code" maxlength="6" class="flex-1 bg-white border border-slate-200 text-center tracking-widest text-slate-700 rounded-xl px-4 py-2 outline-none focus:border-blobPurple font-bold">
                    <button onclick="verifyStudentEmail()" id="btnVerifyOtp" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-4 py-2 rounded-xl transition-colors"><i class="fas fa-check"></i></button>
                </div>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <label class="block text-xs font-black text-slate-500 uppercase mb-3">Change Password</label>
                <input type="password" id="secOldPwd" placeholder="Current Password" class="w-full mb-2 bg-white border border-slate-200 text-slate-700 rounded-xl px-4 py-2 outline-none focus:border-blobPurple font-bold">
                <input type="password" id="secNewPwd" placeholder="New Password" class="w-full mb-2 bg-white border border-slate-200 text-slate-700 rounded-xl px-4 py-2 outline-none focus:border-blobPurple font-bold">
                <input type="password" id="secConfPwd" placeholder="Confirm New Password" class="w-full mb-3 bg-white border border-slate-200 text-slate-700 rounded-xl px-4 py-2 outline-none focus:border-blobPurple font-bold">
                <button onclick="updatePassword()" id="btnUpdatePwd" class="w-full bg-rose-500 hover:bg-rose-600 text-white font-bold py-2 rounded-xl transition-colors">Update Password</button>
            </div>
        </div>
    </div>

    <div id="editProfileModal" class="global-modal-overlay">
        <div class="global-modal-card text-left">
            <div class="flex justify-between items-center mb-5 border-b border-slate-100 pb-4">
                <h4 class="text-xl font-black text-primary"><i class="fas fa-user-edit text-blobPurple me-2"></i>Edit Basic Info</h4>
                <button onclick="closeProfileModal()" class="text-slate-400 hover:text-red-500 transition-colors bg-slate-50 hover:bg-red-50 w-8 h-8 rounded-full flex items-center justify-center"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="mb-5">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-wider mb-3">Gender</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer relative">
                        <input type="radio" name="genderOptions" value="male" class="peer sr-only" <?= ($user['gender'] ?? '') === 'male' ? 'checked' : '' ?>>
                        <div class="rounded-xl border-2 border-slate-100 bg-slate-50 py-3 px-4 text-center text-slate-400 font-bold transition-all duration-200 peer-checked:border-blobPurple peer-checked:bg-blobPurple/10 peer-checked:text-blobPurple hover:bg-slate-100 shadow-sm">
                            <i class="fas fa-mars text-xl mb-1 block"></i>
                            <span class="text-sm">Male</span>
                        </div>
                    </label>
                    <label class="cursor-pointer relative">
                        <input type="radio" name="genderOptions" value="female" class="peer sr-only" <?= ($user['gender'] ?? '') === 'female' ? 'checked' : '' ?>>
                        <div class="rounded-xl border-2 border-slate-100 bg-slate-50 py-3 px-4 text-center text-slate-400 font-bold transition-all duration-200 peer-checked:border-rose-400 peer-checked:bg-rose-50 peer-checked:text-rose-500 hover:bg-slate-100 shadow-sm">
                            <i class="fas fa-venus text-xl mb-1 block"></i>
                            <span class="text-sm">Female</span>
                        </div>
                    </label>
                </div>
            </div>
            
           <div class="mb-8">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-wider mb-3">Birthday</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-birthday-cake text-blobOrange opacity-70"></i>
                    </div>
                    <input type="text" id="editBirthday" placeholder="Select your birthday..." value="<?= htmlspecialchars($user['birthday'] ?? '') ?>" class="w-full bg-slate-50 border-2 border-slate-100 text-slate-700 rounded-xl pl-11 pr-4 py-3 outline-none focus:border-blobOrange focus:bg-white transition-all font-bold shadow-sm cursor-pointer">
                </div>
            </div>
            
            <button id="saveProfileBtn" onclick="saveProfile()" class="w-full bg-blobPurple hover:bg-blobPurple/90 text-white font-black py-3.5 rounded-xl transition-all hover:-translate-y-1 shadow-lg shadow-blobPurple/30 flex justify-center items-center gap-2">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </div>

    <div id="cropperModal" class="global-modal-overlay">
        <div class="global-modal-card">
            <h4 class="text-xl font-black text-primary mb-1"><i class="fas fa-crop-alt text-blobPurple me-2"></i>Adjust Your Avatar</h4>
            <div class="cropper-container-div border-2 border-dashed border-slate-200">
                <img id="imageToCrop" src="" style="max-width: 100%; display: block;">
            </div>
            <div class="flex gap-3 mt-4">
                <button class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black py-3 rounded-full transition-colors" onclick="closeCropperModal()">Cancel</button>
                <button class="flex-1 bg-blobPurple hover:bg-blobPurple/90 text-white font-black py-3 rounded-full transition-colors" onclick="saveCroppedImage()" id="saveAvatarBtn">Save</button>
            </div>
        </div>
    </div>

    <div id="frameModal" class="global-modal-overlay">
        <div class="global-modal-card">
            <div class="flex justify-between items-center mb-3 border-b border-slate-100 pb-4">
                <h4 class="text-xl font-black text-primary mb-0"><i class="fas fa-crown text-yellow-500 me-2"></i>Avatar Frames</h4>
                <button onclick="closeFrameModal()" class="w-8 h-8 bg-slate-100 hover:bg-red-500 hover:text-white rounded-full flex items-center justify-center transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="text-slate-500 text-sm text-left font-bold mb-4">Personalize your profile display with unlocked decorative frames.</p>
            <div class="py-10 px-4 border-2 border-dashed border-slate-200 rounded-3xl bg-slate-50/50 flex flex-col items-center justify-center my-6">
                <div class="w-16 h-16 rounded-full bg-amber-100/70 text-amber-500 flex items-center justify-center text-3xl mb-4 animate-bounce shadow-inner"><i class="fas fa-lock"></i></div>
                <h5 class="font-black text-primary text-base mb-2">No Frames Unlocked Yet</h5>
                <p class="text-xs text-slate-400 font-bold max-w-xs leading-relaxed">Keep playing and achieving top positions to unlock premium frames! 🏆</p>
            </div>
            <div class="pt-4 text-right border-t border-slate-100">
                <button class="bg-primary hover:bg-slate-800 text-white px-6 py-2 rounded-full text-sm font-black transition-colors" onclick="closeFrameModal()">Understood</button>
            </div>
        </div>
    </div>
    
    <div id="logoutModal" class="global-modal-overlay">
        <div class="global-modal-card text-center">
            <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center text-3xl mx-auto mb-4 shadow-sm border border-red-100">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <h4 class="text-xl font-black text-primary mb-2">Ready to leave?</h4>
            <p class="text-slate-500 font-bold text-sm mb-6">Are you sure you want to log out of your account?</p>
            <div class="flex gap-3">
                <button class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black py-3 rounded-xl transition-colors" onclick="closeLogoutModal()">Cancel</button>
                <a href="logout.php" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-black py-3 rounded-xl transition-colors shadow-lg shadow-red-500/30 flex justify-center items-center">Yes, Logout</a>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        // --- Toast 弹窗系统 ---
        document.body.insertAdjacentHTML('beforeend', '<div id="toastBox" class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-3 pointer-events-none"></div>');
        function showToast(msg, type = 'success') {
            const box = document.getElementById('toastBox');
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-emerald-500' : 'bg-rose-500';
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            toast.className = `${bgColor} text-white px-6 py-3 rounded-2xl shadow-xl flex items-center gap-3 transform transition-all duration-300 translate-y-10 opacity-0`;
            toast.innerHTML = `<i class="fas ${icon} text-lg"></i> <span class="font-bold text-sm">${msg}</span>`;
            box.appendChild(toast);
            setTimeout(() => { toast.classList.remove('translate-y-10', 'opacity-0'); }, 10);
            setTimeout(() => {
                toast.classList.add('translate-y-10', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // --- 安全设置弹窗逻辑 ---
        const securityModal = document.getElementById('securityModal');
        function openSecurityModal() { securityModal.classList.add('active'); document.body.style.overflow = 'hidden'; }
        function closeSecurityModal() { securityModal.classList.remove('active'); document.body.style.overflow = 'auto'; }

        function updateUsername() {
            const newVal = document.getElementById('secNewUsername').value.trim();
            if(!newVal) return showToast('Username cannot be empty', 'error');
            const btn = document.getElementById('btnUpdateUsername');
            btn.innerText = '...'; btn.disabled = true;

            let fd = new FormData(); fd.append('action', 'change_username'); fd.append('new_username', newVal);
            fetch('student_profile.php', { method: 'POST', body: fd })
            .then(r => r.json()).then(data => {
                if(data.status === 'success') {
                    showToast('Username updated successfully!', 'success');
                    document.getElementById('displayUsername').innerText = newVal;
                    const navName = document.querySelector('.nav-username');
                    if(navName) navName.innerText = newVal;
                } else showToast(data.message, 'error');
            }).finally(() => { btn.innerText = 'Update'; btn.disabled = false; });
        }

        function updatePassword() {
            const oldP = document.getElementById('secOldPwd').value;
            const newP = document.getElementById('secNewPwd').value;
            const confP = document.getElementById('secConfPwd').value;
            if(!oldP || !newP || !confP) return showToast('Please fill in all password fields.', 'error');
            if(newP !== confP) return showToast('New passwords do not match!', 'error');
            if(newP.length < 6) return showToast('New password must be at least 6 characters.', 'error');
            const btn = document.getElementById('btnUpdatePwd');
            btn.innerText = 'Updating...'; btn.disabled = true;

            let fd = new FormData(); fd.append('action', 'change_password'); fd.append('old_password', oldP); fd.append('new_password', newP);
            fetch('student_profile.php', { method: 'POST', body: fd })
            .then(r => r.json()).then(data => {
                if(data.status === 'success') {
                    showToast('Password updated successfully!', 'success');
                    document.getElementById('secOldPwd').value = ''; document.getElementById('secNewPwd').value = ''; document.getElementById('secConfPwd').value = '';
                } else showToast(data.message, 'error');
            }).finally(() => { btn.innerText = 'Update Password'; btn.disabled = false; });
        }

        // --- OTP 邮箱修改逻辑 ---
        function requestStudentOtp() {
            const email = document.getElementById('secNewEmail').value.trim();
            if(!email.includes('@')) return showToast('Valid email required.', 'error');
            const btn = document.getElementById('btnSendOtp');
            btn.innerText = '...'; btn.disabled = true;

            let fd = new FormData(); fd.append('new_email', email);
            fetch('ajax_send_setting_otp.php', { method: 'POST', body: fd })
            .then(r => r.json()).then(data => {
                if(data.status === 'success') {
                    document.getElementById('secOtpArea').classList.remove('hidden');
                    showToast('OTP sent to your email!', 'success');
                } else showToast(data.message, 'error');
            }).finally(() => { btn.innerText = 'Send OTP'; btn.disabled = false; });
        }

        function verifyStudentEmail() {
            const otp = document.getElementById('secOtpCode').value.trim();
            if(otp.length !== 6) return showToast('Enter 6-digit OTP', 'error');
            let fd = new FormData(); fd.append('otp', otp);
            fetch('ajax_update_email.php', { method: 'POST', body: fd })
            .then(r => r.json()).then(data => {
                if(data.status === 'success') {
                    showToast('Email updated successfully!', 'success');
                    document.getElementById('displayEmail').innerText = document.getElementById('secNewEmail').value.trim();
                    document.getElementById('secOtpArea').classList.add('hidden');
                } else showToast(data.message, 'error');
            });
        }

        // --- 🌟 Edit Basic Profile Logic (Gender & Birthday) ---
        const profileModal = document.getElementById('editProfileModal');
        function openProfileModal() { profileModal.classList.add('active'); document.body.style.overflow = 'hidden'; }
        function closeProfileModal() { profileModal.classList.remove('active'); document.body.style.overflow = 'auto'; }

        function saveProfile() {
            const btn = document.getElementById('saveProfileBtn');
            // 获取选中的性别 Radio Button 的值
            const gender = document.querySelector('input[name="genderOptions"]:checked')?.value || '';
            const birthday = document.getElementById('editBirthday').value;
            
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...'; btn.disabled = true;

            let fd = new FormData(); fd.append('action', 'update_profile'); fd.append('gender', gender); fd.append('birthday', birthday);
            fetch('student_profile.php', { method: 'POST', body: fd })
            .then(r => r.json()).then(data => {
                if(data.status === 'success') {
                    document.getElementById('displayGender').innerText = gender ? (gender.charAt(0).toUpperCase() + gender.slice(1)) : 'Not set';
                    if (birthday) {
                        const d = new Date(birthday);
                        document.getElementById('displayBirthday').innerText = d.toLocaleString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
                    } else document.getElementById('displayBirthday').innerHTML = '<span class="text-slate-400 italic">Not set</span>';
                    showToast('Profile updated!', 'success');
                    closeProfileModal();
                } else showToast('Error updating profile', 'error');
            }).finally(() => { 
                btn.innerHTML = '<i class="fas fa-save"></i> Save Changes'; 
                btn.disabled = false; 
            });
        }

        // --- Copy Code Logic ---
        function copyCode(code) {
            navigator.clipboard.writeText(code).then(() => {
                const btn = document.getElementById('copyBtn');
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check-circle"></i> <span>Copied to Clipboard!</span>';
                btn.classList.replace('bg-primary', 'bg-emerald-500');
                btn.classList.replace('hover:bg-slate-800', 'hover:bg-emerald-600');
                showToast('Code Copied!', 'success');
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.classList.replace('bg-emerald-500', 'bg-primary');
                    btn.classList.replace('hover:bg-emerald-600', 'hover:bg-slate-800');
                }, 2000);
            });
        }

        // --- Avatar Cropper Logic ---
        let cropper = null;
        const avatarInput = document.getElementById('avatarInput');
        const cropperModal = document.getElementById('cropperModal');
        const imageToCrop = document.getElementById('imageToCrop');

        avatarInput.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const file = files[0];
                const reader = new FileReader();
                reader.onload = function(event) {
                    imageToCrop.src = event.target.result;
                    cropperModal.classList.add('active'); 
                    if (cropper) cropper.destroy();
                    cropper = new Cropper(imageToCrop, { aspectRatio: 1, viewMode: 1, dragMode: 'move', autoCropArea: 0.9, restore: false, guides: false, center: false, highlight: false, cropBoxMovable: true, cropBoxResizable: true, toggleDragModeOnDblclick: false });
                };
                reader.readAsDataURL(file);
            }
            avatarInput.value = ''; 
        });

        function closeCropperModal() { cropperModal.classList.remove('active'); if (cropper) { setTimeout(() => { cropper.destroy(); cropper = null; }, 300); } }

        function saveCroppedImage() {
            if (!cropper) return;
            const saveBtn = document.getElementById('saveAvatarBtn');
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...'; saveBtn.disabled = true;
            const canvas = cropper.getCroppedCanvas({ width: 256, height: 256, imageSmoothingEnabled: true, imageSmoothingQuality: 'high' });
            const base64Image = canvas.toDataURL('image/png');

            let fd = new FormData(); fd.append('action', 'upload_avatar'); fd.append('avatar_base64', base64Image);
            fetch('student_profile.php', { method: 'POST', body: fd })
            .then(res => res.json()).then(data => {
                if (data.status === 'success') {
                    document.getElementById('displayAvatarWrapper').innerHTML = `<img id="displayAvatar" src="${data.filepath}?t=${new Date().getTime()}" alt="Avatar">`;
                    showToast('Avatar updated!', 'success');
                    closeCropperModal();
                } else showToast(data.message, 'error');
            }).finally(() => { saveBtn.innerHTML = 'Save'; saveBtn.disabled = false; });
        }

        // --- Frame Modal Logic ---
        const frameModal = document.getElementById('frameModal');
        function openFrameModal() { frameModal.classList.add('active'); document.body.style.overflow = 'hidden'; }
        function closeFrameModal() { frameModal.classList.remove('active'); document.body.style.overflow = 'auto'; }
        // --- Logout 弹窗逻辑 ---
        const logoutModal = document.getElementById('logoutModal');
        function openLogoutModal() { logoutModal.classList.add('active'); document.body.style.overflow = 'hidden'; }
        function closeLogoutModal() { logoutModal.classList.remove('active'); document.body.style.overflow = 'auto'; }

        // --- Flatpickr 初始化 & 页面滚动动画 ---
        document.addEventListener('DOMContentLoaded', () => {
            // 激活漂亮的 Flatpickr 日历
            flatpickr("#editBirthday", {
                dateFormat: "Y-m-d",
                maxDate: "today",
                disableMobile: true,
                animate: true
            });

            // 导航栏滚动效果
            const nav = document.getElementById('mainNav');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 20) nav.classList.add('scrolled'); else nav.classList.remove('scrolled');
            });
            
            // 滚动进入动画
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('active'); });
            }, { threshold: 0.1, rootMargin: "0px 0px -50px 0px" });
            document.querySelectorAll('.reveal-up').forEach(el => observer.observe(el));
            
            const cascadeList = document.getElementById('cascade-list');
            if (cascadeList) {
                const cascadeObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        const items = entry.target.querySelectorAll('.cascade-item');
                        if (entry.isIntersecting) {
                            items.forEach((item, index) => {
                                item.style.transition = `all 0.6s cubic-bezier(0.25, 1, 0.2, 1) ${index * 0.1}s`;
                                setTimeout(() => item.classList.add('active'), 50);
                            });
                            cascadeObserver.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.1 });
                cascadeObserver.observe(cascadeList);
            }
        });
    </script>
</body>
</html>