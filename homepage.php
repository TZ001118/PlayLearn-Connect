<<<<<<< HEAD
<?php
session_start();
require 'db_conn.php';
include('maintenance_check.php');
// ==========================================
// 🚀 AJAX BACKEND HANDLERS (MUST BE AT TOP)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Please login first.']);
        exit;
    }
    
    $uid = intval($_SESSION['user_id']);
    
    // 1. Toggle Favorite
    if ($_POST['action'] === 'toggle_favorite') {
        $gid = intval($_POST['game_id']);
        $check = $conn->query("SELECT id FROM user_favorites WHERE user_id = $uid AND game_id = $gid");
        if ($check->num_rows > 0) {
            $conn->query("DELETE FROM user_favorites WHERE user_id = $uid AND game_id = $gid");
            echo json_encode(['status' => 'removed']);
        } else {
            $conn->query("INSERT INTO user_favorites (user_id, game_id) VALUES ($uid, $gid)");
            echo json_encode(['status' => 'added']);
        }
        exit;
    }
    
    // 2. Record Game Click History (Before Playing)
    if ($_POST['action'] === 'record_history') {
        $gid = intval($_POST['game_id']);
        // Insert or update the timestamp if it already exists
        $conn->query("INSERT INTO user_game_history (user_id, game_id) VALUES ($uid, $gid) ON DUPLICATE KEY UPDATE last_played = CURRENT_TIMESTAMP");
        echo json_encode(['status' => 'success']);
        exit;
    }

    // 3. Claim Quest Reward
    if ($_POST['action'] === 'claim_quest') {
        $quest_key = $conn->real_escape_string($_POST['quest_key']);
        $exp = intval($_POST['exp']);
        $ap = intval($_POST['ap']);
        
        $check = $conn->query("SELECT id FROM user_quests WHERE user_id = $uid AND quest_key = '$quest_key'");
        if ($check->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Already claimed.']);
        } else {
            $conn->query("INSERT INTO user_quests (user_id, quest_key, is_claimed) VALUES ($uid, '$quest_key', 1)");
            
            $u = $conn->query("SELECT level, total_exp, weekly_exp FROM users WHERE id = $uid")->fetch_assoc();
            $new_exp = $u['total_exp'] + $exp;
            $new_ap = $u['weekly_exp'] + $ap;
            $curr_lvl = $u['level'] > 0 ? $u['level'] : 1;
            
            if ($new_exp >= ($curr_lvl * 500)) { $curr_lvl++; }
            
            $conn->query("UPDATE users SET total_exp = $new_exp, weekly_exp = $new_ap, level = $curr_lvl WHERE id = $uid");
            echo json_encode(['status' => 'success', 'new_ap' => $new_ap, 'new_lvl' => $curr_lvl]);
        }
        exit;
    }

    // 4. Claim Chest Reward
    if ($_POST['action'] === 'claim_chest') {
        $chest_key = $conn->real_escape_string($_POST['chest_key']);
        $check = $conn->query("SELECT id FROM user_quests WHERE user_id = $uid AND quest_key = '$chest_key'");
        if ($check->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Chest already opened.']);
        } else {
            $conn->query("INSERT INTO user_quests (user_id, quest_key, is_claimed) VALUES ($uid, '$chest_key', 1)");
            echo json_encode(['status' => 'success']);
        }
        exit;
    }
}

// ==========================================
// 🚀 FETCH DATA FOR UI (WITH DUPLICATE FIX)
// ==========================================
$games = [];
$seen_game_names = []; // Tracker for duplicates

$sql = "SELECT l.*, 
        (SELECT AVG(rating_score) FROM game_reviews WHERE game_id = l.id AND rating_type = 'student') as fun_avg,
        (SELECT AVG(rating_score) FROM game_reviews WHERE game_id = l.id AND rating_type = 'parent') as edu_avg
        FROM levels l ORDER BY l.id DESC";

$res = $conn->query($sql);
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        // BUG FIX: Prevent duplicate games from showing up if DB has accidental duplicate entries
        if (!in_array($row['level_name'], $seen_game_names)) {
            $row['fun_rating'] = $row['fun_avg'] !== null ? number_format($row['fun_avg'], 1) : '0.0';
            $row['edu_rating'] = $row['edu_avg'] !== null ? number_format($row['edu_avg'], 1) : '0.0';
            $games[] = $row;
            $seen_game_names[] = $row['level_name'];
        }
    }
}

// User Specific Data
$user_fav_ids = [];
$claimed_quests = [];
$weekly_ap = 0;
$user_level = 1;

if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    // Favorites
    $fav_res = $conn->query("SELECT game_id FROM user_favorites WHERE user_id = $uid");
    while ($f = $fav_res->fetch_assoc()) { $user_fav_ids[] = $f['game_id']; }
    
    // Gamification state
    $u_query = $conn->query("SELECT level, weekly_exp FROM users WHERE id = $uid");
    if ($u_query && $u_query->num_rows > 0) {
        $u_data = $u_query->fetch_assoc();
        $user_level = $u_data['level'] > 0 ? $u_data['level'] : 1;
        $weekly_ap = $u_data['weekly_exp'] ?? 0;
    }

    // Claimed Quests & Chests
    $q_res = $conn->query("SELECT quest_key FROM user_quests WHERE user_id = $uid");
    while ($q = $q_res->fetch_assoc()) { $claimed_quests[] = $q['quest_key']; }
}

// Inject Fav status into games array so JS knows about it
$games_by_id = [];
foreach ($games as &$g) {
    $g['is_fav'] = in_array($g['id'], $user_fav_ids);
    $games_by_id[$g['id']] = $g;
}
unset($g);
// 1. Featured Games Logic
$featured_game = null;
$top_fun_game = null;
$top_edu_game = null;

if (count($games) > 0) {
    foreach ($games as $g) { if (isset($g['is_featured']) && $g['is_featured'] == 1) { $featured_game = $g; break; } }
    if (!$featured_game) $featured_game = $games[0];

    $sorted_by_fun = $games; usort($sorted_by_fun, function($a, $b) { return $b['fun_rating'] <=> $a['fun_rating']; });
    $top_fun_game = $sorted_by_fun[0]['id'] === $featured_game['id'] && count($games) > 1 ? $sorted_by_fun[1] : $sorted_by_fun[0];

    $sorted_by_edu = $games; usort($sorted_by_edu, function($a, $b) { return $b['edu_rating'] <=> $a['edu_rating']; });
    foreach ($sorted_by_edu as $g) {
        if ($g['id'] !== $featured_game['id'] && $g['id'] !== $top_fun_game['id']) { $top_edu_game = $g; break; }
    }
    if (!$top_edu_game) $top_edu_game = $games[count($games)-1]; 
}

// 2. STRICT Trending Now
$trending_games = [];
foreach ($games as $g) { if (isset($g['is_trending']) && $g['is_trending'] == 1) { $trending_games[] = $g; } }

// 3. STRICT Recently Played (History) - Fetches from `user_game_history` table
$history_games = [];
if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    $hist_sql = "SELECT l.*, ugh.last_played,
        (SELECT AVG(rating_score) FROM game_reviews WHERE game_id = l.id AND rating_type = 'student') as fun_avg,
        (SELECT AVG(rating_score) FROM game_reviews WHERE game_id = l.id AND rating_type = 'parent') as edu_avg
        FROM user_game_history ugh 
        JOIN levels l ON ugh.game_id = l.id 
        WHERE ugh.user_id = $uid 
        ORDER BY ugh.last_played DESC LIMIT 6";
    $h_res = $conn->query($hist_sql);
    if ($h_res && $h_res->num_rows > 0) {
        while ($row = $h_res->fetch_assoc()) {
            $row['fun_rating'] = $row['fun_avg'] !== null ? number_format($row['fun_avg'], 1) : '0.0';
            $row['edu_rating'] = $row['edu_avg'] !== null ? number_format($row['edu_avg'], 1) : '0.0';
            $history_games[] = $row;
        }
    }
}

// 4. STRICT My Favorites
$favorite_games = [];
if (isset($_SESSION['user_id']) && count($user_fav_ids) > 0) {
    foreach ($games as $g) {
        if (in_array($g['id'], $user_fav_ids)) { $favorite_games[] = $g; }
    }
}

$today = date('Ymd');
$this_week = date('YW');
?>
=======
>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title>PlayLearn - Discover & Play</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;800;900&display=swap" rel="stylesheet">
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
        .reveal-up { opacity: 0; transform: translateY(60px) scale(0.97); transition: all 0.8s cubic-bezier(0.25, 1, 0.2, 1); }
        .reveal-up.active { opacity: 1; transform: translateY(0) scale(1); }
        .reveal-left { opacity: 0; transform: translateX(-50px); transition: all 0.8s cubic-bezier(0.25, 1, 0.2, 1); }
        .reveal-left.active { opacity: 1; transform: translateX(0); }
        .cascade-item { opacity: 0; transform: translateY(60px) scale(0.97); }
        .cascade-item.active { opacity: 1; transform: translateY(0) scale(1); }

        .smart-nav { background: transparent; transition: all 0.3s ease; }
        .smart-nav.scrolled {
            background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2); box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .swipe-track { display: flex; gap: 1.25rem; overflow-x: auto; scroll-snap-type: x mandatory; padding-bottom: 1rem; scroll-behavior: smooth; }
        .swipe-track::-webkit-scrollbar { display: none; }
        .swipe-item { scroll-snap-align: start; flex-shrink: 0; }
        
        .cursor-grabbing { cursor: grabbing !important; user-select: none; }
        .swipe-track:not(.cursor-grabbing) { cursor: grab; }

        .track-wrapper { position: relative; }
        .slide-btn { 
            position: absolute; top: 50%; transform: translateY(-50%); z-index: 30; width: 40px; height: 40px; background: white; border-radius: 50%;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15); display: flex; align-items: center; justify-content: center;
            color: #0f172a; cursor: pointer; transition: 0.2s; opacity: 0; pointer-events: none;
        }
        .track-wrapper:hover .slide-btn { opacity: 1; pointer-events: auto; }
        .slide-btn:hover { background: #0f172a; color: white; transform: translateY(-50%) scale(1.1); }
        .slide-btn-left { left: -20px; }
        .slide-btn-right { right: -20px; }

        .game-card { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .game-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); }
        .game-card:hover .play-btn-overlay { opacity: 1; transform: scale(1); }
        .game-card:hover .game-thumb { transform: scale(1.08); }
        .game-thumb { transition: transform 0.6s ease; }
        .play-btn-overlay { opacity: 0; transform: scale(0.8); transition: all 0.3s ease; }
        .rating-link { transition: 0.2s; position: relative; z-index: 20; }
        .rating-link:hover { transform: scale(1.1); }

        .quest-tab.active { background: #6C3FF5; color: white; box-shadow: 0 4px 15px rgba(108, 63, 245, 0.3); }
        .quest-tab { background: #f1f5f9; color: #64748b; transition: all 0.3s ease; }
        .progress-bar-fill { transition: width 1s cubic-bezier(0.4, 0, 0.2, 1); }
        
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        .chest-tooltip {
            opacity: 0; pointer-events: none;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform: translateY(-10px);
        }
        .group:hover .chest-tooltip { opacity: 1; transform: translateY(0); }
        
        .claimed-btn { background-color: #d1fae5 !important; color: #059669 !important; cursor: default; box-shadow: none !important; pointer-events: none; border: none; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased overflow-x-hidden">

    <script>
        const gamesData = <?php echo json_encode($games_by_id); ?>;
    </script>

    <header id="mainNav" class="smart-nav fixed w-full top-0 z-50 pt-2 pb-2">
        <div class="max-w-7xl mx-auto px-6 h-16 flex justify-between items-center">
            <a href="homepage.php" class="flex items-center gap-2 text-2xl font-black tracking-tight text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-blobPurple"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                Play<span class="text-blobOrange">Learn</span>
            </a>

            <nav class="hidden md:flex items-center gap-2 font-bold text-slate-700">
                <a href="javascript:void(0)" onclick="openQuestModal()" class="px-4 py-2 hover:bg-slate-100 rounded-xl hover:text-blobPurple transition-all flex items-center gap-1">
                    <i class="fas fa-scroll"></i> Quests
                </a>
                <a href="#trending-section" class="px-4 py-2 hover:bg-slate-100 rounded-xl hover:text-blobOrange transition-all">Games</a>
                <a href="leaderboard.php" class="px-4 py-2 hover:bg-slate-100 rounded-xl hover:text-blobYellow transition-all flex items-center gap-2">
                    <i class="fas fa-trophy"></i> Leaderboard
                </a>
            </nav>

            <div class="flex items-center gap-4">
                <?php if(isset($_SESSION['username'])): ?>
                    <div class="hidden sm:block text-sm font-bold text-primary bg-white/60 backdrop-blur px-4 py-1.5 rounded-full border border-white/40 shadow-sm flex items-center gap-2">
                        <span id="nav-lvl-badge" class="bg-blobYellow text-primary text-[10px] px-1.5 py-0.5 rounded-md font-black">Lv.<?php echo $user_level; ?></span>
                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </div>
                    <?php if(($_SESSION['role'] ?? '') === 'parent'): ?>
                        <a href="ParentDashboard.php" class="text-blobPurple hover:text-primary font-black"><i class="fas fa-chart-line"></i> Dashboard</a>
                    <?php elseif(($_SESSION['role'] ?? '') === 'player'): ?>
                        <a href="student_profile.php" class="text-blobPurple hover:text-primary font-black"><i class="fas fa-paw"></i> Profile</a>
                    <?php endif; ?>
                    <a href="javascript:void(0)" onclick="openLogoutModal()" class="w-10 h-10 flex items-center justify-center bg-white/60 backdrop-blur hover:bg-red-100 hover:text-red-500 border border-slate-200 rounded-full transition-all shadow-sm ms-3">
    				<i class="fas fa-sign-out-alt"></i>
					</a>
                <?php else: ?>
                    <a href="login.php" class="bg-primary text-white px-6 py-2 rounded-full font-bold hover:bg-slate-800 transition-transform hover:scale-105 shadow-md">Log In</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="relative w-full pt-28 pb-12 overflow-hidden">
        <div id="parallax-blob-1" class="absolute top-[-20%] left-[-10%] w-[500px] h-[500px] bg-blobPurple/10 rounded-full blur-[80px] -z-10 transition-transform duration-100"></div>
        <div id="parallax-blob-2" class="absolute top-[10%] right-[-5%] w-[400px] h-[400px] bg-blobOrange/10 rounded-full blur-[80px] -z-10 transition-transform duration-100"></div>

        <div class="max-w-7xl mx-auto px-6">
            <h1 class="text-3xl md:text-4xl font-black text-primary mb-6 flex items-center gap-3 reveal-left">
                <i class="fas fa-fire text-blobOrange"></i> Discover Today
            </h1>

            <?php if($featured_game && $top_fun_game && $top_edu_game): ?>
            <div class="flex overflow-x-auto scroll-snap-type-x gap-4 lg:grid lg:grid-cols-3 pb-4 reveal-up" style="scroll-snap-type: x mandatory; scrollbar-width: none;">
                
                <div class="game-card relative rounded-3xl overflow-hidden cursor-pointer h-[350px] lg:h-[420px] shadow-lg flex-shrink-0 w-[85%] lg:w-auto lg:col-span-2 lg:row-span-2" style="scroll-snap-align: center;" onclick="openGameDetails(<?php echo $featured_game['id']; ?>)">
                    <img src="<?php echo $featured_game['image_url']; ?>" class="absolute inset-0 w-full h-full object-cover game-thumb">
                    <div class="absolute inset-0 bg-gradient-to-t from-primary/95 via-primary/40 to-transparent"></div>
                    <div class="absolute top-4 left-4 flex gap-2"><span class="bg-blobPurple text-white px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wide border border-white/20 backdrop-blur-sm shadow-md">⭐ Editor's Choice</span></div>
                    
                    <button onclick="toggleFavorite(event, <?php echo $featured_game['id']; ?>, this)" class="absolute top-4 right-4 z-30 w-10 h-10 bg-white/30 backdrop-blur-md rounded-full flex items-center justify-center shadow-sm hover:scale-110 transition-transform border border-white/40">
                        <?php $is_fav = in_array($featured_game['id'], $user_fav_ids); ?>
                        <i class="fa-heart text-lg <?php echo $is_fav ? 'fas text-rose-500' : 'far text-white'; ?>"></i>
                    </button>

                    <div class="absolute bottom-0 p-6 lg:p-8 w-full z-10">
                        <div class="flex gap-3 mb-3">
                            <span class="rating-link bg-white/90 backdrop-blur rounded-full px-3 py-1 text-xs font-black text-blobOrange shadow-md"><i class="fas fa-gamepad"></i> <?php echo $featured_game['fun_rating']; ?></span>
                            <span class="rating-link bg-white/90 backdrop-blur rounded-full px-3 py-1 text-xs font-black text-blobPurple shadow-md"><i class="fas fa-brain"></i> <?php echo $featured_game['edu_rating']; ?></span>
                        </div>
                        <h2 class="text-3xl lg:text-4xl font-black text-white mb-2 leading-tight"><?php echo htmlspecialchars($featured_game['level_name']); ?></h2>
                        <p class="text-slate-300 text-sm lg:text-base font-semibold line-clamp-2 max-w-xl"><?php echo htmlspecialchars($featured_game['level_description']); ?></p>
                    </div>
                </div>

                <div class="game-card relative rounded-3xl overflow-hidden cursor-pointer h-[350px] lg:h-[200px] shadow-lg flex-shrink-0 w-[85%] lg:w-auto" style="scroll-snap-align: center;" onclick="openGameDetails(<?php echo $top_fun_game['id']; ?>)">
                    <img src="<?php echo $top_fun_game['image_url']; ?>" class="absolute inset-0 w-full h-full object-cover game-thumb">
                    <div class="absolute inset-0 bg-gradient-to-t from-primary/90 to-transparent"></div>
                    <div class="absolute top-4 left-4 bg-blobOrange text-white px-3 py-1 rounded-full text-[10px] font-black uppercase shadow-md">🎮 Most Fun</div>
                    
                    <button onclick="toggleFavorite(event, <?php echo $top_fun_game['id']; ?>, this)" class="absolute top-4 right-4 z-30 w-8 h-8 bg-white/30 backdrop-blur-md rounded-full flex items-center justify-center shadow-sm hover:scale-110 transition-transform border border-white/40">
                        <?php $is_fav = in_array($top_fun_game['id'], $user_fav_ids); ?>
                        <i class="fa-heart text-sm <?php echo $is_fav ? 'fas text-rose-500' : 'far text-white'; ?>"></i>
                    </button>

                    <div class="absolute bottom-0 p-5 w-full z-10">
                        <span class="rating-link inline-block bg-white/90 rounded-full px-2 py-0.5 text-[11px] font-black text-blobOrange mb-2"><i class="fas fa-star"></i> <?php echo $top_fun_game['fun_rating']; ?> / 5.0</span>
                        <h3 class="text-xl font-black text-white truncate"><?php echo htmlspecialchars($top_fun_game['level_name']); ?></h3>
                    </div>
                </div>

                <div class="game-card relative rounded-3xl overflow-hidden cursor-pointer h-[350px] lg:h-[200px] shadow-lg flex-shrink-0 w-[85%] lg:w-auto" style="scroll-snap-align: center;" onclick="openGameDetails(<?php echo $top_edu_game['id']; ?>)">
                    <img src="<?php echo $top_edu_game['image_url']; ?>" class="absolute inset-0 w-full h-full object-cover game-thumb">
                    <div class="absolute inset-0 bg-gradient-to-t from-primary/90 to-transparent"></div>
                    <div class="absolute top-4 left-4 bg-blobYellow text-primary px-3 py-1 rounded-full text-[10px] font-black uppercase shadow-md">🧠 High Edu Value</div>
                    
                    <button onclick="toggleFavorite(event, <?php echo $top_edu_game['id']; ?>, this)" class="absolute top-4 right-4 z-30 w-8 h-8 bg-white/30 backdrop-blur-md rounded-full flex items-center justify-center shadow-sm hover:scale-110 transition-transform border border-white/40">
                        <?php $is_fav = in_array($top_edu_game['id'], $user_fav_ids); ?>
                        <i class="fa-heart text-sm <?php echo $is_fav ? 'fas text-rose-500' : 'far text-white'; ?>"></i>
                    </button>

                    <div class="absolute bottom-0 p-5 w-full z-10">
                        <span class="rating-link inline-block bg-white/90 rounded-full px-2 py-0.5 text-[11px] font-black text-blobPurple mb-2"><i class="fas fa-graduation-cap"></i> <?php echo $top_edu_game['edu_rating']; ?> / 5.0</span>
                        <h3 class="text-xl font-black text-white truncate"><?php echo htmlspecialchars($top_edu_game['level_name']); ?></h3>
                    </div>
                </div>

            </div>
            <?php endif; ?>
        </div>
    </section>

    <section id="trending-section" class="max-w-7xl mx-auto px-6 mb-12">
        <h2 class="text-2xl font-black text-primary mb-4 reveal-left"><i class="fas fa-chart-line text-blobOrange mr-2"></i> Trending Now</h2>
        
        <?php if(count($trending_games) > 0): ?>
        <div class="track-wrapper reveal-up">
            <button class="slide-btn slide-btn-left" onclick="slideLeft('track-1')"><i class="fas fa-chevron-left"></i></button>
            <div id="track-1" class="swipe-track" style="scroll-behavior: smooth;">
                <?php foreach($trending_games as $game): 
                    $diffColor = strtolower($game['difficulty']) == 'hard' ? 'bg-[#fce8e6] text-[#e74c3c]' : (strtolower($game['difficulty']) == 'easy' ? 'bg-[#e6f4ea] text-[#27ae60]' : 'bg-[#fff5e6] text-[#f39c12]');
                ?>
                <div class="swipe-item w-[280px] sm:w-[320px]">
                    <div class="game-card bg-white rounded-2xl overflow-hidden cursor-pointer border border-slate-100 shadow-sm relative group" onclick="openGameDetails(<?php echo $game['id']; ?>)">
                        <div class="relative h-44 overflow-hidden bg-slate-100">
                            <img src="<?php echo $game['image_url']; ?>" class="game-thumb w-full h-full object-cover">
                            <div class="play-btn-overlay absolute inset-0 bg-primary/30 backdrop-blur-[2px] flex items-center justify-center">
                                <div class="w-14 h-14 bg-white/90 rounded-full flex items-center justify-center text-primary shadow-xl"><i class="fas fa-expand-alt text-xl"></i></div>
                            </div>
                            <div class="absolute top-3 left-3 bg-[#e6f4ea] text-[#27ae60] text-[10px] font-black px-2.5 py-1 rounded-md shadow-sm">Free Play</div>
                            
                            <button onclick="toggleFavorite(event, <?php echo $game['id']; ?>, this)" class="absolute top-3 right-3 z-30 w-8 h-8 bg-white/80 backdrop-blur rounded-full flex items-center justify-center shadow-sm hover:scale-110 transition-transform">
                                <?php $is_fav = in_array($game['id'], $user_fav_ids); ?>
                                <i class="fa-heart text-sm <?php echo $is_fav ? 'fas text-rose-500' : 'far text-slate-400'; ?>"></i>
                            </button>
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-center mb-1">
                                <h3 class="text-lg font-black text-primary truncate pr-2"><?php echo htmlspecialchars($game['level_name']); ?></h3>
                                <span class="<?php echo $diffColor; ?> px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider shrink-0"><?php echo htmlspecialchars($game['difficulty']); ?></span>
                            </div>
                            <p class="text-slate-400 text-xs font-semibold truncate mb-3"><?php echo htmlspecialchars($game['level_description']); ?></p>
                            
                            <div class="flex items-center gap-3 pt-3 border-t border-slate-100 relative z-20">
                                <span class="rating-link flex items-center gap-1 text-[11px] font-bold text-slate-500 bg-blobOrange/5 px-2 py-1 rounded"><i class="fas fa-gamepad text-blobOrange"></i> <?php echo $game['fun_rating']; ?></span>
                                <span class="rating-link flex items-center gap-1 text-[11px] font-bold text-slate-500 bg-blobPurple/5 px-2 py-1 rounded"><i class="fas fa-brain text-blobPurple"></i> <?php echo $game['edu_rating']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="slide-btn slide-btn-right" onclick="slideRight('track-1')"><i class="fas fa-chevron-right"></i></button>
        </div>
        <?php else: ?>
            <div class="reveal-up bg-slate-50 border-2 border-dashed border-slate-200 rounded-3xl py-12 flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-slate-300 text-2xl shadow-sm mb-4"><i class="fas fa-chart-line"></i></div>
                <h3 class="font-black text-slate-600 mb-1">No Trending Games</h3>
                <p class="text-sm font-bold text-slate-400">Check back later for admin recommendations.</p>
            </div>
        <?php endif; ?>
    </section>

    <section class="max-w-7xl mx-auto px-6 mb-12">
        <h2 class="text-2xl font-black text-primary mb-4 reveal-left"><i class="fas fa-history text-blobPurple mr-2"></i> Jump Back In</h2>
        <?php if(count($history_games) > 0): ?>
        <div class="track-wrapper reveal-up">
            <button class="slide-btn slide-btn-left" onclick="slideLeft('track-hist')"><i class="fas fa-chevron-left"></i></button>
            <div id="track-hist" class="swipe-track">
                <?php foreach($history_games as $game): ?>
                <div class="swipe-item w-[200px] sm:w-[220px]">
                    <div class="game-card bg-white rounded-xl overflow-hidden cursor-pointer border border-slate-100 shadow-sm relative group" onclick="openGameDetails(<?php echo $game['id']; ?>)">
                        <div class="relative h-32 overflow-hidden bg-slate-100">
                            <img src="<?php echo $game['image_url']; ?>" class="game-thumb w-full h-full object-cover">
                            <div class="play-btn-overlay absolute inset-0 bg-primary/30 backdrop-blur-[2px] flex items-center justify-center">
                                <div class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center text-primary shadow-xl"><i class="fas fa-play text-sm"></i></div>
                            </div>
                            <button onclick="toggleFavorite(event, <?php echo $game['id']; ?>, this)" class="absolute top-2 right-2 z-30 w-7 h-7 bg-white/80 backdrop-blur rounded-full flex items-center justify-center shadow-sm hover:scale-110 transition-transform">
                                <?php $is_fav = in_array($game['id'], $user_fav_ids); ?>
                                <i class="fa-heart text-xs <?php echo $is_fav ? 'fas text-rose-500' : 'far text-slate-400'; ?>"></i>
                            </button>
                        </div>
                        <div class="p-3 text-center">
                            <h3 class="text-sm font-black text-primary truncate"><?php echo htmlspecialchars($game['level_name']); ?></h3>
                            <div class="flex justify-center items-center gap-2 mt-1 relative z-20">
                                <span class="rating-link text-[10px] font-bold text-slate-500 bg-slate-50 px-1.5 py-0.5 rounded"><i class="fas fa-gamepad text-blobOrange"></i> <?php echo $game['fun_rating']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="slide-btn slide-btn-right" onclick="slideRight('track-hist')"><i class="fas fa-chevron-right"></i></button>
        </div>
        <?php else: ?>
            <div class="reveal-up bg-slate-50 border-2 border-dashed border-slate-200 rounded-3xl py-10 flex flex-col items-center justify-center text-center">
                <h3 class="font-black text-slate-600 mb-1">No Recent History</h3>
                <p class="text-sm font-bold text-slate-400">Play some games and they will appear right here!</p>
            </div>
        <?php endif; ?>
    </section>

    <section class="max-w-7xl mx-auto px-6 mb-12">
        <h2 class="text-2xl font-black text-primary mb-6 reveal-left"><i class="fas fa-heart text-rose-500 mr-2"></i> My Favorites</h2>
        <?php if(count($favorite_games) > 0): ?>
        <div id="cascade-fav" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach($favorite_games as $game): 
                $diffColor = strtolower($game['difficulty']) == 'hard' ? 'bg-[#fce8e6] text-[#e74c3c]' : (strtolower($game['difficulty']) == 'easy' ? 'bg-[#e6f4ea] text-[#27ae60]' : 'bg-[#fff5e6] text-[#f39c12]');
            ?>
                <div class="cascade-item game-card bg-white rounded-2xl overflow-hidden cursor-pointer border border-slate-100 shadow-sm relative group" onclick="openGameDetails(<?php echo $game['id']; ?>)">
                    <div class="relative h-40 overflow-hidden bg-slate-100">
                        <img src="<?php echo $game['image_url']; ?>" class="game-thumb w-full h-full object-cover">
                        <div class="play-btn-overlay absolute inset-0 bg-primary/30 backdrop-blur-[2px] flex items-center justify-center">
                            <div class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center text-primary shadow-xl"><i class="fas fa-expand-alt text-xl"></i></div>
                        </div>
                        <div class="absolute top-2 left-2 bg-[#e6f4ea] text-[#27ae60] text-[9px] font-black px-2 py-0.5 rounded shadow-sm">Free Play</div>
                        
                        <button onclick="toggleFavorite(event, <?php echo $game['id']; ?>, this)" class="absolute top-2 right-2 z-30 w-8 h-8 bg-white/80 backdrop-blur rounded-full flex items-center justify-center shadow-sm hover:scale-110 transition-transform">
                            <i class="fas fa-heart text-sm text-rose-500"></i>
                        </button>
                    </div>
                    <div class="p-4">
                        <div class="flex justify-between items-center mb-1">
                            <h3 class="text-base font-black text-primary truncate pr-2"><?php echo htmlspecialchars($game['level_name']); ?></h3>
                            <span class="<?php echo $diffColor; ?> px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider shrink-0"><?php echo htmlspecialchars($game['difficulty']); ?></span>
                        </div>
                        <div class="flex items-center gap-3 pt-2 mt-2 border-t border-slate-100 relative z-20">
                            <span class="rating-link text-[11px] font-bold text-slate-500 bg-slate-50 px-2 py-1 rounded"><i class="fas fa-gamepad text-blobOrange"></i> <?php echo $game['fun_rating']; ?></span>
                            <span class="rating-link text-[11px] font-bold text-slate-500 bg-slate-50 px-2 py-1 rounded"><i class="fas fa-brain text-blobPurple"></i> <?php echo $game['edu_rating']; ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <div class="reveal-up bg-slate-50 border-2 border-dashed border-rose-200 rounded-3xl py-12 flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-rose-200 text-2xl shadow-sm mb-4"><i class="fas fa-heart-broken"></i></div>
                <h3 class="font-black text-slate-600 mb-1">No Favorites Yet</h3>
                <p class="text-sm font-bold text-slate-400 max-w-sm">Click the heart icon on any game card to add it to your personal collection!</p>
            </div>
        <?php endif; ?>
    </section>

    <section class="max-w-7xl mx-auto px-6 pb-24">
        <div class="flex justify-between items-end mb-6 reveal-left">
            <h2 class="text-2xl font-black text-primary"><i class="fas fa-th-large text-blobYellow mr-2"></i> All Games</h2>
            
            <button onclick="openGameModal()" class="bg-white/80 backdrop-blur border border-slate-200 text-slate-600 font-bold px-5 py-2.5 rounded-full hover:bg-blobPurple hover:text-white hover:border-blobPurple transition-all shadow-sm text-sm flex items-center gap-2">
                <i class="fas fa-search"></i> Search Games
            </button>
        </div>
        <div id="cascade-all" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <?php foreach($games as $game): 
                $is_fav = in_array($game['id'], $user_fav_ids);
            ?>
                <div class="cascade-item main-game-item bg-white rounded-2xl p-3 border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all cursor-pointer relative group" data-title="<?php echo strtolower(htmlspecialchars($game['level_name'])); ?>" onclick="openGameDetails(<?php echo $game['id']; ?>)">
                    <div class="relative h-24 sm:h-32 rounded-xl overflow-hidden mb-3 bg-slate-100">
                        <img src="<?php echo $game['image_url']; ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <i class="fas fa-expand-alt text-white text-xl drop-shadow-md"></i>
                        </div>
                        
                        <button onclick="toggleFavorite(event, <?php echo $game['id']; ?>, this)" class="absolute top-1 right-1 z-30 w-7 h-7 bg-white/80 backdrop-blur rounded-full flex items-center justify-center shadow-sm hover:scale-110 transition-transform">
                            <i class="fa-heart text-xs <?php echo $is_fav ? 'fas text-rose-500' : 'far text-slate-400'; ?>"></i>
                        </button>
                    </div>
                    <h3 class="font-black text-primary text-sm line-clamp-1 mb-1"><?php echo htmlspecialchars($game['level_name']); ?></h3>
                    
                    <div class="flex justify-between items-center mt-2 flex-wrap gap-1">
                        <div class="flex gap-1">
                            <span class="text-[9px] font-black text-blobOrange bg-blobOrange/10 px-1.5 py-0.5 rounded"><i class="fas fa-gamepad"></i> <?php echo $game['fun_rating']; ?></span>
                            <span class="text-[9px] font-black text-blobPurple bg-blobPurple/10 px-1.5 py-0.5 rounded"><i class="fas fa-brain"></i> <?php echo $game['edu_rating']; ?></span>
                        </div>
                        <span class="text-[8px] font-black text-slate-400 border border-slate-200 px-1.5 py-0.5 rounded uppercase"><?php echo htmlspecialchars($game['difficulty']); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div id="mainNoResultsMsg" class="hidden py-10 text-center flex-col items-center justify-center w-full">
            <i class="fas fa-search-minus text-4xl text-slate-300 mb-3"></i>
            <p class="font-black text-slate-400">No games match your search.</p>
        </div>
    </section>

    <div id="gameDetailsModal" class="fixed inset-0 z-[110] hidden items-center justify-center">
        <div class="absolute inset-0 bg-primary/60 backdrop-blur-sm transition-opacity duration-300 opacity-0" id="gdModalOverlay" onclick="closeGameDetails()"></div>
        
        <div class="bg-white rounded-[2rem] w-11/12 max-w-2xl flex flex-col z-10 shadow-2xl scale-95 opacity-0 transition-all duration-300 ease-out overflow-hidden relative" id="gdModalContent">
            
            <button onclick="closeGameDetails()" class="absolute top-4 right-4 z-50 w-10 h-10 bg-black/40 hover:bg-black/60 backdrop-blur text-white rounded-full flex items-center justify-center transition-colors shadow-sm">
                <i class="fas fa-times"></i>
            </button>

            <div class="relative h-48 sm:h-64 w-full bg-slate-100 shrink-0">
                <img id="gd-image" src="" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-primary/90 via-primary/30 to-transparent"></div>
                
                <div class="absolute bottom-4 left-6 right-6 flex justify-between items-end">
                    <div>
                        <span id="gd-diff" class="bg-white text-primary text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider mb-2 inline-block shadow-sm">Easy</span>
                        <h2 id="gd-title" class="text-3xl font-black text-white drop-shadow-md">Game Title</h2>
                    </div>
                    <button id="gd-fav-btn" onclick="" class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center shadow-lg border border-white/40 hover:scale-110 transition-transform">
                        <i id="gd-fav-icon" class="far fa-heart text-xl text-white"></i>
                    </button>
                </div>
            </div>

            <div class="p-6 sm:p-8 flex flex-col gap-6 overflow-y-auto max-h-[60vh]">
                
                <div>
                    <h4 class="font-black text-slate-800 mb-2">About this Game</h4>
                    <p id="gd-desc" class="text-sm font-semibold text-slate-500 leading-relaxed">Game description goes here.</p>
                </div>

                <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 flex flex-col sm:flex-row gap-4 items-center justify-between">
                    <div class="flex gap-6 w-full sm:w-auto justify-center">
                        <div class="text-center">
                            <span class="block text-xs font-black text-slate-400 uppercase mb-1">Kids Fun</span>
                            <div class="flex items-center gap-1 font-black text-xl text-blobOrange"><i class="fas fa-gamepad"></i> <span id="gd-fun">0.0</span></div>
                        </div>
                        <div class="w-px bg-slate-200"></div>
                        <div class="text-center">
                            <span class="block text-xs font-black text-slate-400 uppercase mb-1">Edu Value</span>
                            <div class="flex items-center gap-1 font-black text-xl text-blobPurple"><i class="fas fa-brain"></i> <span id="gd-edu">0.0</span></div>
                        </div>
                    </div>

                    <div class="flex gap-2 w-full sm:w-auto shrink-0">
                        <a id="gd-review-kid" href="#" class="flex-1 sm:flex-none text-center bg-blobOrange/10 hover:bg-blobOrange text-blobOrange hover:text-white font-black text-xs px-4 py-2.5 rounded-xl transition-colors">
                            <i class="fas fa-star mr-1"></i> Rate Fun
                        </a>
                        <a id="gd-review-parent" href="#" class="flex-1 sm:flex-none text-center bg-blobPurple/10 hover:bg-blobPurple text-blobPurple hover:text-white font-black text-xs px-4 py-2.5 rounded-xl transition-colors">
                            <i class="fas fa-graduation-cap mr-1"></i> Rate Edu
                        </a>
                    </div>
                </div>

                <div class="mt-2">
                    <button id="gd-play-btn" onclick="" class="w-full bg-primary hover:bg-slate-800 text-white font-black text-lg py-4 rounded-2xl shadow-xl shadow-primary/30 transition-transform hover:-translate-y-1 flex items-center justify-center gap-3">
                        <i class="fas fa-play"></i> START PLAYING
                    </button>
                    <p class="text-center text-[10px] font-bold text-slate-400 mt-3"><i class="fas fa-info-circle"></i> Clicking play will record your history and launch the game.</p>
                </div>
            </div>
        </div>
    </div>

    <div id="questModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
        <div class="absolute inset-0 bg-primary/40 backdrop-blur-sm transition-opacity duration-300 opacity-0" id="questModalOverlay" onclick="closeQuestModal()"></div>
        <div class="bg-white rounded-[2rem] w-11/12 max-w-4xl max-h-[90vh] flex flex-col z-10 shadow-2xl scale-95 opacity-0 transition-all duration-300 ease-out overflow-hidden" id="questModalContent">
            
            <div class="bg-white/95 backdrop-blur-md px-6 py-5 border-b border-slate-100 flex justify-between items-center z-20">
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-primary flex items-center gap-2"><i class="fas fa-scroll text-blobPurple"></i> Quest Hub</h2>
                    <p class="text-xs text-slate-400 font-bold mt-1">Complete tasks to earn EXP and unlock exclusive weekly chests!</p>
                </div>
                <button onclick="closeQuestModal()" class="w-10 h-10 bg-slate-100 hover:bg-red-100 hover:text-red-500 rounded-full flex items-center justify-center transition-colors shrink-0"><i class="fas fa-times text-lg"></i></button>
            </div>

            <div class="p-6 overflow-y-auto bg-slate-50/50" id="questModalScroll">
                
                <?php 
                    $max_weekly = 800; 
                    $progress_percent = min(100, ($weekly_ap / $max_weekly) * 100); 
                ?>
                <div class="w-full bg-white p-5 rounded-2xl border border-slate-100 shadow-sm mb-8 relative">
                    <div class="flex justify-between items-end mb-4">
                        <span class="text-sm font-black text-slate-400 uppercase tracking-wider">Weekly Activity Points</span>
                        <span class="text-base font-black text-blobPurple"><span id="ap-counter"><?php echo $weekly_ap; ?></span> / <?php echo $max_weekly; ?> AP</span>
                    </div>
                    
                    <div class="h-4 w-full bg-slate-100 rounded-full relative">
                        <div id="ap-bar" class="h-full bg-gradient-to-r from-blobPurple to-blobOrange rounded-full progress-bar-fill shadow-inner" style="width: <?php echo $progress_percent; ?>%;"></div>
                        
                        <div class="absolute top-1/2 -translate-y-1/2 flex flex-col items-center cursor-pointer group" style="left: 18.75%;"> 
                            <?php $bronze_claimed = in_array($this_week.'_chest_1', $claimed_quests); ?>
                            <div onclick="claimChest(150, this, 'Bronze Chest', '<?php echo $this_week.'_chest_1'; ?>')" class="w-10 h-10 rounded-full <?php echo $bronze_claimed ? 'claimed-btn' : ($weekly_ap >= 150 ? 'bg-amber-600 text-white shadow-md shadow-amber-600/30' : 'bg-slate-200 text-slate-400'); ?> flex items-center justify-center text-lg z-10 transition-transform hover:scale-110">
                                <i class="fas <?php echo $bronze_claimed ? 'fa-check' : ($weekly_ap >= 150 ? 'fa-box-open animate-pulse' : 'fa-box'); ?>"></i>
                            </div>
                            <?php if(!$bronze_claimed): ?>
                            <div class="absolute top-full mt-4 w-52 bg-slate-800 text-white p-3 rounded-xl shadow-2xl z-[60] chest-tooltip">
                                <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-slate-800 rotate-45"></div>
                                <h5 class="font-black text-sm text-amber-500 mb-1 flex items-center gap-2 relative z-10"><i class="fas fa-box"></i> Bronze Chest</h5>
                                <div class="text-xs font-semibold text-slate-300 space-y-1.5 relative z-10">
                                    <p class="flex items-center gap-2"><i class="fas fa-portrait text-slate-400 w-3"></i> 1-Day Frame</p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="absolute top-1/2 -translate-y-1/2 flex flex-col items-center cursor-pointer group" style="left: 50%;"> 
                            <?php $silver_claimed = in_array($this_week.'_chest_2', $claimed_quests); ?>
                            <div onclick="claimChest(400, this, 'Silver Chest', '<?php echo $this_week.'_chest_2'; ?>')" class="w-10 h-10 rounded-full <?php echo $silver_claimed ? 'claimed-btn' : ($weekly_ap >= 400 ? 'bg-slate-400 text-white shadow-md shadow-slate-400/30' : 'bg-slate-200 text-slate-400'); ?> flex items-center justify-center text-lg z-10 transition-transform hover:scale-110">
                                <i class="fas <?php echo $silver_claimed ? 'fa-check' : ($weekly_ap >= 400 ? 'fa-box-open animate-pulse' : 'fa-box'); ?>"></i>
                            </div>
                            <?php if(!$silver_claimed): ?>
                            <div class="absolute top-full mt-4 w-52 bg-slate-800 text-white p-3 rounded-xl shadow-2xl z-[60] chest-tooltip">
                                <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-slate-800 rotate-45"></div>
                                <h5 class="font-black text-sm text-slate-300 mb-1 flex items-center gap-2 relative z-10"><i class="fas fa-box"></i> Silver Chest</h5>
                                <div class="text-xs font-semibold text-slate-300 space-y-1.5 relative z-10">
                                    <p class="flex items-center gap-2"><i class="fas fa-crown text-slate-400 w-3"></i> 3-Day Exclusive Frame</p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="absolute top-1/2 -translate-y-1/2 flex flex-col items-center cursor-pointer group" style="left: 100%; transform: translate(-100%, -50%);">
                            <?php $gold_claimed = in_array($this_week.'_chest_3', $claimed_quests); ?>
                            <div onclick="claimChest(800, this, 'Gold Chest', '<?php echo $this_week.'_chest_3'; ?>')" class="w-12 h-12 rounded-full <?php echo $gold_claimed ? 'claimed-btn' : ($weekly_ap >= 800 ? 'bg-yellow-400 text-white shadow-lg shadow-yellow-400/40' : 'bg-slate-200 text-slate-400'); ?> flex items-center justify-center text-xl z-10 transition-transform hover:scale-110">
                                <i class="fas <?php echo $gold_claimed ? 'fa-check' : ($weekly_ap >= 800 ? 'fa-treasure-chest animate-bounce' : 'fa-box'); ?>"></i>
                            </div>
                            <?php if(!$gold_claimed): ?>
                            <div class="absolute top-full right-0 mt-4 w-56 bg-slate-800 text-white p-3 rounded-xl shadow-2xl z-[60] chest-tooltip transform-none">
                                <div class="absolute -top-1.5 right-5 w-3 h-3 bg-slate-800 rotate-45"></div>
                                <h5 class="font-black text-sm text-yellow-400 mb-1 flex items-center gap-2 relative z-10"><i class="fas fa-treasure-chest"></i> Gold Chest</h5>
                                <div class="text-xs font-semibold text-slate-300 space-y-1.5 relative z-10">
                                    <p class="flex items-center gap-2"><i class="fas fa-crown text-yellow-400 w-3"></i> 7-Day Premium Frame</p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mb-6 border-b border-slate-200 pb-4">
                    <button onclick="switchQuestTab('daily')" id="btn-daily" class="quest-tab active px-5 py-2.5 rounded-full font-black text-sm">🔥 Daily Quests</button>
                    <button onclick="switchQuestTab('weekly')" id="btn-weekly" class="quest-tab px-5 py-2.5 rounded-full font-black text-sm">📅 Weekly Quests</button>
                </div>

                <?php if(!isset($_SESSION['user_id'])): ?>
                    <div class="text-center py-10 bg-white rounded-2xl border border-dashed border-slate-200 shadow-sm">
                        <i class="fas fa-lock text-4xl text-slate-300 mb-3"></i>
                        <h3 class="font-black text-slate-600 mb-2">Log in to track your Quests</h3>
                        <a href="login.php" class="inline-block bg-primary text-white font-bold px-6 py-2 rounded-full mt-2">Log In Now</a>
                    </div>
                <?php else: ?>
                    <div id="quests-daily" class="grid grid-cols-1 md:grid-cols-2 gap-4 transition-all">
                        <?php 
                        $d_quests = [
                            ['key' => $today.'_dq1', 'title' => 'First Blood', 'desc' => 'Play any game 1 time today.', 'exp' => 50, 'ap' => 50, 'icon' => 'fa-gamepad', 'color' => 'blobOrange'],
                            ['key' => $today.'_dq2', 'title' => 'Dedicated Gamer', 'desc' => 'Play games for 15 minutes total.', 'exp' => 50, 'ap' => 30, 'icon' => 'fa-hourglass-half', 'color' => 'blobPurple'],
                            ['key' => $today.'_dq3', 'title' => 'The Critic', 'desc' => 'Leave 1 review for any game.', 'exp' => 80, 'ap' => 50, 'icon' => 'fa-comment-dots', 'color' => 'blue-500']
                        ];
                        foreach($d_quests as $q):
                            $is_claimed = in_array($q['key'], $claimed_quests);
                        ?>
                        <div class="bg-white border border-slate-100 rounded-2xl p-5 flex flex-col justify-between hover:shadow-md transition-shadow shadow-sm">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="w-12 h-12 rounded-xl bg-slate-50 text-<?php echo $q['color']; ?> flex items-center justify-center text-xl shrink-0"><i class="fas <?php echo $q['icon']; ?>"></i></div>
                                <div>
                                    <h4 class="font-black text-primary text-base line-clamp-1"><?php echo $q['title']; ?></h4>
                                    <p class="text-xs font-semibold text-slate-500 mt-1"><?php echo $q['desc']; ?></p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-auto border-t border-slate-50 pt-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-[9px] font-black text-blobOrange bg-blobOrange/10 px-2 py-1 rounded">+<?php echo $q['exp']; ?> EXP</span>
                                    <span class="text-[9px] font-black text-blobPurple bg-blobPurple/10 px-2 py-1 rounded">+<?php echo $q['ap']; ?> AP</span>
                                </div>
                                <?php if($is_claimed): ?>
                                    <button class="claimed-btn text-xs font-black px-4 py-1.5 rounded-full"><i class="fas fa-check"></i> Claimed</button>
                                <?php else: ?>
                                    <button onclick="claimQuestReward(this, '<?php echo $q['key']; ?>', <?php echo $q['exp']; ?>, <?php echo $q['ap']; ?>)" class="text-xs font-black bg-blobYellow text-primary px-4 py-1.5 rounded-full shadow-sm shadow-blobYellow/40 hover:-translate-y-0.5 transition-transform animate-pulse">Claim</button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div id="quests-weekly" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 transition-all">
                        <?php 
                        $w_quests = [
                            ['key' => $this_week.'_wq1', 'title' => 'Marathon', 'desc' => 'Play games for 2 hours this week.', 'exp' => 300, 'ap' => 150, 'icon' => 'fa-stopwatch', 'color' => 'rose-500'],
                            ['key' => $this_week.'_wq2', 'title' => 'Explorer', 'desc' => 'Play 5 different games.', 'exp' => 200, 'ap' => 100, 'icon' => 'fa-compass', 'color' => 'emerald-500'],
                            ['key' => $this_week.'_wq3', 'title' => 'Top Reviewer', 'desc' => 'Leave 3 reviews this week.', 'exp' => 250, 'ap' => 120, 'icon' => 'fa-pen-nib', 'color' => 'purple-500'],
                            ['key' => $this_week.'_wq4', 'title' => 'Champion', 'desc' => 'Reach Top 3 on any Leaderboard.', 'exp' => 500, 'ap' => 200, 'icon' => 'fa-crown', 'color' => 'amber-500']
                        ];
                        foreach($w_quests as $q):
                            $is_claimed = in_array($q['key'], $claimed_quests);
                        ?>
                        <div class="bg-white border border-slate-100 rounded-2xl p-5 flex flex-col justify-between hover:shadow-md transition-shadow shadow-sm">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="w-12 h-12 rounded-xl bg-slate-50 text-<?php echo $q['color']; ?> flex items-center justify-center text-xl shrink-0"><i class="fas <?php echo $q['icon']; ?>"></i></div>
                                <div>
                                    <h4 class="font-black text-primary text-base line-clamp-1"><?php echo $q['title']; ?></h4>
                                    <p class="text-xs font-semibold text-slate-500 mt-1"><?php echo $q['desc']; ?></p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-auto border-t border-slate-50 pt-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-[9px] font-black text-blobOrange bg-blobOrange/10 px-2 py-1 rounded">+<?php echo $q['exp']; ?> EXP</span>
                                    <span class="text-[9px] font-black text-blobPurple bg-blobPurple/10 px-2 py-1 rounded">+<?php echo $q['ap']; ?> AP</span>
                                </div>
                                <?php if($is_claimed): ?>
                                    <button class="claimed-btn text-xs font-black px-4 py-1.5 rounded-full"><i class="fas fa-check"></i> Claimed</button>
                                <?php else: ?>
                                    <button onclick="claimQuestReward(this, '<?php echo $q['key']; ?>', <?php echo $q['exp']; ?>, <?php echo $q['ap']; ?>)" class="text-xs font-black bg-blobYellow text-primary px-4 py-1.5 rounded-full shadow-sm shadow-blobYellow/40 hover:-translate-y-0.5 transition-transform animate-pulse">Claim</button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div id="customAlert" class="fixed top-20 left-1/2 -translate-x-1/2 z-[9999] bg-slate-800 text-white px-6 py-3 rounded-full font-bold shadow-2xl transition-all duration-300 transform -translate-y-20 opacity-0 flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-400 text-lg"></i>
        <span id="customAlertText">Success!</span>
    </div>

    <div id="allGamesModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
        <div class="absolute inset-0 bg-primary/40 backdrop-blur-sm transition-opacity duration-300 opacity-0" id="modalOverlay" onclick="closeGameModal()"></div>
        <div class="bg-white rounded-3xl w-11/12 max-w-5xl max-h-[85vh] flex flex-col z-10 shadow-2xl scale-95 opacity-0 transition-all duration-300 ease-out" id="modalContent">
            <div class="sticky top-0 bg-white/95 backdrop-blur-md px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center rounded-t-3xl z-20 gap-4">
                <div><h2 class="text-xl md:text-2xl font-black text-primary flex items-center gap-2"><i class="fas fa-gamepad text-blobOrange"></i> Search Games</h2></div>
                <div class="relative w-full sm:w-64">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" id="modalSearchInput" placeholder="Search games..." onkeyup="filterModalGames()" class="w-full pl-9 pr-4 py-2 bg-slate-100 border-none rounded-full text-sm font-bold text-primary focus:ring-2 focus:ring-blobPurple outline-none transition-all placeholder:text-slate-400">
                </div>
                <button onclick="closeGameModal()" class="w-10 h-10 bg-slate-100 hover:bg-red-100 hover:text-red-500 rounded-full flex items-center justify-center transition-colors shrink-0">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto" id="gameModalContent">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4" id="modalGridContainer">
                    <?php foreach($games as $game): 
                        $is_fav = in_array($game['id'], $user_fav_ids);
                    ?>
                    <div data-title="<?php echo strtolower(htmlspecialchars($game['level_name'])); ?>" class="modal-game-item flex flex-col items-center gap-3 p-4 rounded-2xl border-2 border-transparent hover:border-blobPurple/30 bg-slate-50 hover:bg-slate-100 transition-all duration-200 hover:-translate-y-1 cursor-pointer relative" onclick="openGameDetails(<?php echo $game['id']; ?>)">
                        <div class="relative w-full aspect-square rounded-2xl overflow-hidden shadow-sm group">
                            <img src="<?php echo htmlspecialchars($game['image_url']); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                            <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <i class="fas fa-expand-alt text-white text-xl drop-shadow-md"></i>
                            </div>
                            <button onclick="toggleFavorite(event, <?php echo $game['id']; ?>, this)" class="absolute top-1 right-1 z-30 w-7 h-7 bg-white/80 backdrop-blur rounded-full flex items-center justify-center shadow-sm hover:scale-110 transition-transform">
                                <i class="fa-heart text-xs <?php echo $is_fav ? 'fas text-rose-500' : 'far text-slate-400'; ?>"></i>
                            </button>
                        </div>
                        <span class="font-black text-xs md:text-sm text-center text-primary line-clamp-2 leading-tight w-full"><?php echo htmlspecialchars($game['level_name']); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div id="noResultsMsg" class="hidden py-10 text-center flex-col items-center justify-center">
                    <i class="fas fa-search-minus text-4xl text-slate-300 mb-3"></i>
                    <p class="font-black text-slate-400">No games match your search.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div id="logoutModal" class="fixed inset-0 z-[9999] flex items-center justify-center hidden opacity-0 transition-all duration-300" style="background: rgba(0,0,0,0.4); backdrop-filter: blur(4px);">
        <div class="bg-white p-8 rounded-3xl text-center shadow-2xl transform scale-95 transition-all duration-300 w-full max-w-sm">
            <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <h4 class="text-xl font-black mb-2">Ready to leave?</h4>
            <p class="text-slate-500 font-bold text-sm mb-6">Are you sure you want to log out of your account?</p>
            <div class="flex gap-3">
                <button class="flex-1 bg-slate-100 hover:bg-slate-200 py-3 rounded-xl font-black transition-colors" onclick="closeLogoutModal()">Cancel</button>
                <a href="logout.php" class="flex-1 bg-red-500 hover:bg-red-600 text-white py-3 rounded-xl font-black transition-colors">Yes, Logout</a>
            </div>
        </div>
    </div>

    <script>
        let currentAP = <?php echo $weekly_ap; ?>;
        
        function openGameDetails(gameId) {
            const game = gamesData[gameId];
            if (!game) return;

            document.getElementById('gd-image').src = game.image_url;
            document.getElementById('gd-title').innerText = game.level_name;
            document.getElementById('gd-desc').innerText = game.level_description;
            document.getElementById('gd-fun').innerText = game.fun_rating;
            document.getElementById('gd-edu').innerText = game.edu_rating;
            
            const diffEl = document.getElementById('gd-diff');
            diffEl.innerText = game.difficulty;
            let dColor = game.difficulty.toLowerCase() === 'hard' ? 'text-red-500 border-red-200' : (game.difficulty.toLowerCase() === 'easy' ? 'text-emerald-500 border-emerald-200' : 'text-amber-500 border-amber-200');
            diffEl.className = `bg-white border text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider mb-2 inline-block shadow-sm ${dColor}`;

            const favBtn = document.getElementById('gd-fav-btn');
            const favIcon = document.getElementById('gd-fav-icon');
            favBtn.setAttribute('onclick', `toggleFavorite(event, ${game.id}, this)`);
            if (game.is_fav) {
                favIcon.className = "fas fa-heart text-xl text-rose-500";
            } else {
                favIcon.className = "far fa-heart text-xl text-white";
            }

            document.getElementById('gd-review-kid').href = `review.php?game_id=${game.id}&role=student`;
            document.getElementById('gd-review-parent').href = `review.php?game_id=${game.id}&role=parent`;
            document.getElementById('gd-play-btn').setAttribute('onclick', `playGame(${game.id}, '${game.game_url}')`);

            const modal = document.getElementById('gameDetailsModal');
            const overlay = document.getElementById('gdModalOverlay');
            const content = document.getElementById('gdModalContent');
            modal.classList.remove('hidden'); modal.classList.add('flex');
            setTimeout(() => {
                overlay.classList.remove('opacity-0'); overlay.classList.add('opacity-100');
                content.classList.remove('scale-95', 'opacity-0'); content.classList.add('scale-100', 'opacity-100');
            }, 10);
            document.body.style.overflow = 'hidden';
        }
        
        function openLogoutModal() {
        const modal = document.getElementById('logoutModal');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.add('opacity-100');
            modal.querySelector('div').classList.replace('scale-95', 'scale-100');
        }, 10);
    }

    function closeLogoutModal() {
        const modal = document.getElementById('logoutModal');
        modal.classList.remove('opacity-100');
        modal.querySelector('div').classList.replace('scale-100', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

        function closeGameDetails() {
            const modal = document.getElementById('gameDetailsModal');
            const overlay = document.getElementById('gdModalOverlay');
            const content = document.getElementById('gdModalContent');
            overlay.classList.remove('opacity-100'); overlay.classList.add('opacity-0');
            content.classList.remove('scale-100', 'opacity-100'); content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden'); modal.classList.remove('flex');
                document.body.style.overflow = 'auto'; 
            }, 300);
        }

        function playGame(gameId, url) {
            let formData = new FormData();
            formData.append('action', 'record_history');
            formData.append('game_id', gameId);

            fetch('homepage.php', { method: 'POST', body: formData })
            .then(() => { window.location.href = url; })
            .catch(err => { window.location.href = url; });
        }

        function toggleFavorite(e, gameId, btnElement) {
            e.preventDefault(); 
            e.stopPropagation();

            let formData = new FormData();
            formData.append('action', 'toggle_favorite');
            formData.append('game_id', gameId);

            fetch('homepage.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'error') { showToast(data.message); return; }
                const icon = btnElement.querySelector('i');
                if (data.status === 'added') {
                    icon.classList.remove('far', 'text-slate-400', 'text-white');
                    icon.classList.add('fas', 'text-rose-500');
                    gamesData[gameId].is_fav = true; 
                    showToast("Game saved to My Favorites! ❤️");
                } else if (data.status === 'removed') {
                    icon.classList.remove('fas', 'text-rose-500');
                    icon.classList.add('far', 'text-slate-400', 'text-white');
                    gamesData[gameId].is_fav = false;
                    showToast("Game removed from Favorites.");
                }
            }).catch(err => console.error(err));
        }

        function claimQuestReward(btnElement, questKey, exp, ap) {
            let formData = new FormData();
            formData.append('action', 'claim_quest');
            formData.append('quest_key', questKey);
            formData.append('exp', exp);
            formData.append('ap', ap);

            fetch('homepage.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    btnElement.className = "claimed-btn text-xs font-black px-4 py-1.5 rounded-full transition-all";
                    btnElement.innerHTML = '<i class="fas fa-check"></i> Claimed';
                    btnElement.disabled = true;
                    btnElement.onclick = null;
                    
                    showToast(`Reward Claimed! +${exp} EXP, +${ap} AP ✨`);
                    
                    currentAP = data.new_ap;
                    document.getElementById('ap-counter').innerText = currentAP;
                    if(document.getElementById('nav-lvl-badge')) {
                        document.getElementById('nav-lvl-badge').innerText = 'Lv.' + data.new_lvl;
                    }
                    let progressPercent = Math.min(100, (currentAP / 800) * 100);
                    document.getElementById('ap-bar').style.width = progressPercent + '%';
                } else {
                    showToast(data.message || "Error claiming reward.");
                }
            });
        }

        function claimChest(requiredAp, chestElement, chestName, chestKey) {
            if (currentAP < requiredAp) {
                showToast("Not enough AP to open " + chestName + ". Keep completing quests! 🔒");
                return;
            }
            if (chestElement.classList.contains('claimed-btn')) {
                showToast(chestName + " already claimed this week!");
                return;
            }

            let formData = new FormData();
            formData.append('action', 'claim_chest');
            formData.append('chest_key', chestKey);

            fetch('homepage.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    chestElement.classList.add('claimed-btn');
                    chestElement.innerHTML = '<i class="fas fa-check"></i>';
                    showToast("🎉 You opened the " + chestName + "!");
                } else {
                    showToast(data.message || "Error claiming chest.");
                }
            });
        }

        function showToast(message) {
            const alertBox = document.getElementById('customAlert');
            document.getElementById('customAlertText').innerText = message;
            alertBox.classList.remove('-translate-y-20', 'opacity-0');
            alertBox.classList.add('translate-y-0', 'opacity-100');
            setTimeout(() => {
                alertBox.classList.remove('translate-y-0', 'opacity-100');
                alertBox.classList.add('-translate-y-20', 'opacity-0');
            }, 3000);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const swipeTracks = document.querySelectorAll('.swipe-track');
            swipeTracks.forEach(track => {
                let isDown = false; let startX; let scrollLeft;
                track.addEventListener('mousedown', (e) => {
                    isDown = true; track.classList.add('cursor-grabbing');
                    startX = e.pageX - track.offsetLeft; scrollLeft = track.scrollLeft;
                });
                track.addEventListener('mouseleave', () => { isDown = false; track.classList.remove('cursor-grabbing'); });
                track.addEventListener('mouseup', () => { isDown = false; track.classList.remove('cursor-grabbing'); });
                track.addEventListener('mousemove', (e) => {
                    if (!isDown) return;
                    e.preventDefault();
                    const x = e.pageX - track.offsetLeft;
                    const walk = (x - startX) * 2; 
                    track.scrollLeft = scrollLeft - walk;
                });
            });

            const nav = document.getElementById('mainNav');
            window.addEventListener('scroll', () => {
                let scrollY = window.scrollY;
                if (scrollY > 20) nav.classList.add('scrolled'); else nav.classList.remove('scrolled');
            });

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) { entry.target.classList.add('active'); } 
                });
            }, { threshold: 0.1, rootMargin: "0px 0px -50px 0px" });
            document.querySelectorAll('.reveal-up, .reveal-left').forEach(el => observer.observe(el));

            const cascadeGrids = document.querySelectorAll('#cascade-grid, #cascade-fav, #cascade-all');
            cascadeGrids.forEach(grid => {
                const cascadeObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        const items = entry.target.querySelectorAll('.cascade-item');
                        if (entry.isIntersecting) {
                            items.forEach((item, index) => {
                                item.style.transition = `all 0.8s cubic-bezier(0.25, 1, 0.2, 1) ${index * 0.1}s`;
                                setTimeout(() => item.classList.add('active'), 50);
                            });
                            cascadeObserver.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.1 });
                cascadeObserver.observe(grid);
            });
        });

        function slideLeft(trackId) { document.getElementById(trackId).scrollBy({ left: -350, behavior: 'smooth' }); }
        function slideRight(trackId) { document.getElementById(trackId).scrollBy({ left: 350, behavior: 'smooth' }); }

        function openQuestModal() {
            const modal = document.getElementById('questModal');
            const overlay = document.getElementById('questModalOverlay');
            const content = document.getElementById('questModalContent');
            modal.classList.remove('hidden'); modal.classList.add('flex');
            setTimeout(() => {
                overlay.classList.remove('opacity-0'); overlay.classList.add('opacity-100');
                content.classList.remove('scale-95', 'opacity-0'); content.classList.add('scale-100', 'opacity-100');
            }, 10);
            document.body.style.overflow = 'hidden';
        }

        function closeQuestModal() {
            const modal = document.getElementById('questModal');
            const overlay = document.getElementById('questModalOverlay');
            const content = document.getElementById('questModalContent');
            overlay.classList.remove('opacity-100'); overlay.classList.add('opacity-0');
            content.classList.remove('scale-100', 'opacity-100'); content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden'); modal.classList.remove('flex');
                document.body.style.overflow = 'auto'; 
            }, 300);
        }

        function openGameModal() {
            const modal = document.getElementById('allGamesModal');
            const overlay = document.getElementById('modalOverlay');
            const content = document.getElementById('modalContent');
            modal.classList.remove('hidden'); modal.classList.add('flex');
            setTimeout(() => {
                overlay.classList.remove('opacity-0'); overlay.classList.add('opacity-100');
                content.classList.remove('scale-95', 'opacity-0'); content.classList.add('scale-100', 'opacity-100');
            }, 10);
            document.body.style.overflow = 'hidden';
            setTimeout(() => document.getElementById('modalSearchInput').focus(), 300);
        }

        function closeGameModal() {
            const modal = document.getElementById('allGamesModal');
            const overlay = document.getElementById('modalOverlay');
            const content = document.getElementById('modalContent');
            overlay.classList.remove('opacity-100'); overlay.classList.add('opacity-0');
            content.classList.remove('scale-100', 'opacity-100'); content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden'); modal.classList.remove('flex');
                document.body.style.overflow = 'auto'; 
                document.getElementById('modalSearchInput').value = "";
                filterModalGames();
            }, 300);
        }

        function filterModalGames() {
            const query = document.getElementById('modalSearchInput').value.toLowerCase();
            const games = document.querySelectorAll('.modal-game-item');
            let hasVisible = false;
            games.forEach(game => {
                const title = game.getAttribute('data-title');
                if (title.includes(query)) { game.style.display = 'flex'; hasVisible = true; } 
                else { game.style.display = 'none'; }
            });
            const noResults = document.getElementById('noResultsMsg');
            if (hasVisible) { noResults.classList.add('hidden'); noResults.classList.remove('flex'); } 
            else { noResults.classList.remove('hidden'); noResults.classList.add('flex'); }
        }

        function switchQuestTab(tabName) {
            const dailyBtn = document.getElementById('btn-daily');
            const weeklyBtn = document.getElementById('btn-weekly');
            const dailyQuests = document.getElementById('quests-daily');
            const weeklyQuests = document.getElementById('quests-weekly');

            if (tabName === 'daily') {
                dailyBtn.classList.add('active'); dailyBtn.classList.remove('bg-f1f5f9');
                weeklyBtn.classList.remove('active'); weeklyBtn.classList.add('bg-f1f5f9');
                dailyQuests.classList.remove('hidden'); weeklyQuests.classList.add('hidden');
            } else {
                weeklyBtn.classList.add('active'); weeklyBtn.classList.remove('bg-f1f5f9');
                dailyBtn.classList.remove('active'); dailyBtn.classList.add('bg-f1f5f9');
                weeklyQuests.classList.remove('hidden'); dailyQuests.classList.add('hidden');
            }
        }
    </script>
=======
    <title>PlayLearns - Challenge Your Mind</title>
    <style>
        :root {
            --primary: #4A90E2;
            --accent: #FF6B6B;
            --text: #2D3436;
            --bg: #FFFFFF;
            --hero-bg: #1A3C6B; 
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: var(--bg); color: var(--text); }

        header {
            background: #fff;
            padding: 1rem 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f0f0f0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .logo { font-size: 1.6rem; font-weight: 800; color: var(--primary); text-decoration: none; }
        .logo span { color: var(--accent); }
        nav a { margin-left: 20px; text-decoration: none; color: var(--text); font-weight: 500; font-size: 0.9rem; }
        .login-btn { background: var(--primary); color: white !important; padding: 6px 18px; border-radius: 8px; }

        .hero {
            height: 30vh;
            background-color: var(--hero-bg);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .hero h1 { font-size: 3rem; font-weight: 800; margin-bottom: 10px; }
        .hero p { opacity: 0.9; font-size: 1.1rem; }

        .game-section { padding: 4rem 8%; }
        .game-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 2.5rem;
        }

        .game-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
            aspect-ratio: 1 / 1; 
            cursor: pointer;
        }
        .game-card:hover { transform: translateY(-8px); box-shadow: 0 12px 24px rgba(0,0,0,0.15); }

        .game-thumb {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .game-card:hover .game-thumb { transform: scale(1.05); }

        .game-info {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.98);
            padding: 1rem 1.5rem;
            z-index: 2;
            height: 60px; 
            transition: height 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            display: flex;
            flex-direction: column;
        }

        .game-card:hover .game-info {
            height: 140px; 
        }

        .game-info h3 {
            font-size: 1.2rem;
            color: #000;
            margin-bottom: 15px; 
            white-space: nowrap;
        }

        .game-info p {
            font-size: 0.9rem;
            color: #666;
            line-height: 1.4;
            opacity: 0; 
            transition: opacity 0.3s ease;
        }

        .game-card:hover .game-info p {
            opacity: 1; 
        }

        @media (max-width: 768px) {
            .hero h1 { font-size: 2rem; }
            nav { display: none; }
        }
    </style>
</head>
<body>

    <header>
        <a href="#" class="logo">Play<span>Learns</span></a>
        <nav>
            <a href="homepage.php">Home</a>
            <a href="#">Games</a>
            <a href="#">Leaderboard</a>
            <a href="login.php" class="login-btn">Login</a> 
        </nav>
    </header>

    <section class="hero">
        <h1>Fun Way to Learn</h1>
        <p>Ready to level up your brain?</p>
    </section>

    <main class="game-section">
        <div class="game-grid">
            
            <div class="game-card" onclick="window.location.href='game2048.html'">
                <img src="img/2048.png" alt="2048" class="game-thumb">
                <div class="game-info">
                    <h3>2048 Puzzle</h3>
                    <p>Merge the numbers and get to the 2048 tile! A great way to practice powers of 2.</p>
                </div>
            </div>

            <div class="game-card" onclick="window.location.href='minesweeper.html'">
                <img src="img/扫雷.png" alt="Minesweeper" class="game-thumb">
                <div class="game-info">
                    <h3>Minesweeper</h3>
                    <p>Use logic to clear the grid without detonating any mines. Classic brain training.</p>
                </div>
            </div>

            <div class="game-card" onclick="window.location.href='snake.html'">
                <img src="img/贪吃蛇.png" alt="Snake" class="game-thumb">
                <div class="game-info">
                    <h3>Classic Snake</h3>
                    <p>Eat the food, grow longer, and don't hit the walls! Improves reaction and focus.</p>
                </div>
            </div>

            <div class="game-card" onclick="window.location.href='word_wanderer.html'">
                <img src="img/Word Wanderer.png" alt="Word Wanderer" class="game-thumb">
                <div class="game-info">
                    <h3>Word Wanderer</h3>
                    <p>Connect letters to discover hidden words. Perfect for expanding your vocabulary.</p>
                </div>
            </div>

            <div class="game-card" onclick="window.location.href='memory.html'">
                <img src="img/match.jpeg" alt="Emoji Memory Match" class="game-thumb">
                <div class="game-info">
                    <h3>Emoji Memory Match</h3>
                    <p>Train your brain by finding matching pairs of fun emojis! Great for boosting short-term memory and daily focus.</p>
                </div>
            </div>

            <div class="game-card" onclick="window.location.href='math_pop.html'">
                <img src="img/Math Pop.png" alt="Math Pop" class="game-thumb">
                <div class="game-info">
                    <h3>Math Pop</h3>
                    <p>Solve the math puzzles by popping the correct bubbles. Great for practicing quick calculations.</p>
                </div>
            </div>

            <div class="game-card" onclick="window.location.href='odd_one_out.html'">
                <img src="https://api.dicebear.com/7.x/shapes/svg?seed=OddOneOut&backgroundColor=00cec9" alt="Odd One Out" class="game-thumb">
                <div class="game-info">
                    <h3>Odd One Out</h3>
                    <p>Spot the difference! Find the emoji that doesn't belong to train your observation skills and focus.</p>
                </div>
            </div>
        </div>
    </main>

>>>>>>> 4f5123f4f40ff24341cfcb8ebd54648461a4dfed
</body>
</html>