<?php
session_start();
require 'db_conn.php';
include('maintenance_check.php');
// List of games that depend on levels
$level_based_games = ['Math Pop', 'Mine Sweeper', 'Odd One Out', 'Snake', 'Word Wanderer'];

// 1. Fetch all games with their images for the visual selector
$games_res = $conn->query("SELECT level_name, image_url FROM levels ORDER BY id ASC");
$game_list = [];
while($row = $games_res->fetch_assoc()){
    $game_list[] = $row;
}

// 2. Determine current selected game
$selected_game = isset($_GET['game']) ? $_GET['game'] : '';

// Auto-detect based on recent play history if no game is specified in URL
if (empty($selected_game) && isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    $recent_query = $conn->query("SELECT game_name FROM game_scores WHERE user_id = $uid ORDER BY played_at DESC LIMIT 1");
    if ($recent_query && $recent_query->num_rows > 0) {
        $row = $recent_query->fetch_assoc();
        $selected_game = $row['game_name'];
    }
}

// Fallback to the first game
if (empty($selected_game) && count($game_list) > 0) {
    $selected_game = $game_list[0]['level_name'];
}

$isMemory = (stripos($selected_game, 'Memory') !== false);

// 3. Determine selected level filter
$selected_level = isset($_GET['level']) ? $_GET['level'] : 'all';

$max_level = 1; 
$level_query = $conn->prepare("SELECT MAX(level_reached) as top_level FROM game_scores WHERE game_name = ?");
$level_query->bind_param("s", $selected_game);
$level_query->execute();
$level_result = $level_query->get_result()->fetch_assoc();
if ($level_result && $level_result['top_level'] > 0) {
    $max_level = $level_result['top_level'];
}

// 4. Fetch the Leaderboard Data into an Array (Now including ALL Gamification Fields)
$leaderboard_data = [];

// Added fields to Group By to prevent SQL ONLY_FULL_GROUP_BY strict mode errors
if ($isMemory) {
    $sql = "SELECT MIN(s.score) as score, s.level_reached, MAX(s.played_at) as played_at, 
            u.username, u.id as user_id, u.profile_image, u.avatar_frame, u.level, u.title, u.title_expires_at 
            FROM game_scores s JOIN users u ON s.user_id = u.id 
            WHERE s.game_name = ? AND u.status = 'active' ";
    if ($selected_level !== 'all') { $sql .= " AND s.level_reached = " . intval(str_replace('Level ', '', $selected_level)); }
    $sql .= " GROUP BY u.username, s.level_reached, u.id, u.profile_image, u.avatar_frame, u.level, u.title, u.title_expires_at 
              ORDER BY s.level_reached DESC, score ASC LIMIT 10";
} else {
    $sql = "SELECT MAX(s.score) as score, s.level_reached, MAX(s.played_at) as played_at, 
            u.username, u.id as user_id, u.profile_image, u.avatar_frame, u.level, u.title, u.title_expires_at 
            FROM game_scores s JOIN users u ON s.user_id = u.id 
            WHERE s.game_name = ? AND u.status = 'active' ";
    if ($selected_level !== 'all') { $sql .= " AND s.level_reached = " . intval(str_replace('Level ', '', $selected_level)); }
    $sql .= " GROUP BY u.username, s.level_reached, u.id, u.profile_image, u.avatar_frame, u.level, u.title, u.title_expires_at 
              ORDER BY score DESC LIMIT 10";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $selected_game);
$stmt->execute();
$result = $stmt->get_result();

while($row = $result->fetch_assoc()){
    $leaderboard_data[] = $row;
}

$top3 = array_slice($leaderboard_data, 0, 3);
$top3 = array_pad($top3, 3, null); 
$podium_order = [$top3[1], $top3[0], $top3[2]];
$rest_of_list = array_slice($leaderboard_data, 3);

// 5. Fetch Current User's Record and Gamification Info
$my_record = null;
if (isset($_SESSION['user_id'])) {
    $current_uid = intval($_SESSION['user_id']);
    
    if ($isMemory) {
        $my_sql = "SELECT MIN(score) as my_score, MAX(level_reached) as my_lvl, MAX(played_at) as my_time FROM game_scores WHERE user_id = ? AND game_name = ?";
    } else {
        $my_sql = "SELECT MAX(score) as my_score, MAX(level_reached) as my_lvl, MAX(played_at) as my_time FROM game_scores WHERE user_id = ? AND game_name = ?";
    }
    
    if ($selected_level !== 'all') {
        $my_sql .= " AND level_reached = " . intval(str_replace('Level ', '', $selected_level));
    }
    
    $my_stmt = $conn->prepare($my_sql);
    $my_stmt->bind_param("is", $current_uid, $selected_game);
    $my_stmt->execute();
    $my_res = $my_stmt->get_result()->fetch_assoc();
    
    if ($my_res && $my_res['my_score']) {
        $my_record = $my_res;
        $my_score_val = $my_res['my_score'];
        
        if ($isMemory) {
            $rank_sql = "SELECT COUNT(DISTINCT user_id) as better_players FROM game_scores WHERE game_name = ? AND score < ?";
        } else {
            $rank_sql = "SELECT COUNT(DISTINCT user_id) as better_players FROM game_scores WHERE game_name = ? AND score > ?";
        }
        
        if ($selected_level !== 'all') {
            $rank_sql .= " AND level_reached = " . intval(str_replace('Level ', '', $selected_level));
        }
        
        $rank_stmt = $conn->prepare($rank_sql);
        $rank_stmt->bind_param("si", $selected_game, $my_score_val);
        $rank_stmt->execute();
        $rank_result = $rank_stmt->get_result()->fetch_assoc();
        $my_record['rank'] = $rank_result['better_players'] + 1;

        // Fetch User's current gamification data
        $u_stmt = $conn->prepare("SELECT profile_image, avatar_frame, level, title, title_expires_at FROM users WHERE id = ?");
        $u_stmt->bind_param("i", $current_uid);
        $u_stmt->execute();
        $my_user_info = $u_stmt->get_result()->fetch_assoc();
        $my_record = array_merge($my_record, $my_user_info);
    }
}

// Helper Function to process user gamification visually
function getGamificationData($row) {
    if (!$row) return null;
    
    $data = [];
    $data['level'] = $row['level'] ?? 1;
    
    // Check if Limited Title is expired
    $data['title'] = "Novice Player";
    $data['title_color'] = "text-slate-400 bg-slate-100 border-slate-200";
    if (!empty($row['title'])) {
        $is_expired = false;
        if (!empty($row['title_expires_at'])) {
            if (time() > strtotime($row['title_expires_at'])) {
                $is_expired = true;
            }
        }
        if (!$is_expired) {
            $data['title'] = $row['title'];
            if (stripos($data['title'], 'Master') !== false || stripos($data['title'], 'Champion') !== false) {
                $data['title_color'] = "text-yellow-600 bg-yellow-100 border-yellow-300";
            } else {
                $data['title_color'] = "text-blobPurple bg-blobPurple/10 border-blobPurple/30";
            }
        }
    }

    // Process Avatar & Frame
    $data['avatar'] = !empty($row['profile_image']) ? htmlspecialchars($row['profile_image']) : "https://api.dicebear.com/7.x/bottts/svg?seed=" . urlencode($row['username']) . "&backgroundColor=e2e8f0";
    $data['frame'] = !empty($row['avatar_frame']) ? htmlspecialchars($row['avatar_frame']) : null;
    
    return $data;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlayLearn | Global Leaderboard</title>
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
        .reveal-up { opacity: 0; transform: translateY(40px) scale(0.98); transition: all 0.6s cubic-bezier(0.25, 1, 0.2, 1); }
        .reveal-up.active { opacity: 1; transform: translateY(0) scale(1); }
        .reveal-left { opacity: 0; transform: translateX(-40px); transition: all 0.6s cubic-bezier(0.25, 1, 0.2, 1); }
        .reveal-left.active { opacity: 1; transform: translateX(0); }
        .cascade-item { opacity: 0; transform: translateY(30px); }
        .cascade-item.active { opacity: 1; transform: translateY(0); }

        .fade-edges {
            -webkit-mask-image: linear-gradient(to right, transparent 0%, black 10%, black 90%, transparent 100%);
            mask-image: linear-gradient(to right, transparent 0%, black 10%, black 90%, transparent 100%);
        }

        .game-selector-track { 
            display: flex; gap: 0.75rem; overflow-x: auto; scroll-snap-type: x mandatory; 
            padding: 10px 0; scrollbar-width: none; flex-grow: 1;
        }
        .game-selector-track::-webkit-scrollbar { display: none; }
        .game-pill { scroll-snap-align: start; flex-shrink: 0; transition: all 0.3s ease; }
        .game-pill:hover { transform: translateY(-3px); }
        .game-pill.active { border-color: #6C3FF5; box-shadow: 0 10px 20px rgba(108, 63, 245, 0.2); }

        .podium-1 { height: 180px; background: linear-gradient(to top, #FFD700 0%, #fff9c4 100%); border: 2px solid #FFD700; }
        .podium-2 { height: 130px; background: linear-gradient(to top, #C0C0C0 0%, #f5f5f5 100%); border: 2px solid #C0C0C0; }
        .podium-3 { height: 100px; background: linear-gradient(to top, #CD7F32 0%, #fae7d6 100%); border: 2px solid #CD7F32; }

        .empty-podium { opacity: 0.3; filter: grayscale(1); }

        @media (max-width: 768px) {
            .podium-container { flex-direction: column; align-items: center; gap: 1.5rem; height: auto !important; }
            .podium-wrapper-1 { order: 1; }
            .podium-wrapper-2 { order: 2; }
            .podium-wrapper-3 { order: 3; }
            .podium-1, .podium-2, .podium-3 { height: auto; padding: 1rem; width: 100%; border-radius: 1rem; }
        }

        #gameModalContent::-webkit-scrollbar { width: 6px; }
        #gameModalContent::-webkit-scrollbar-track { background: transparent; }
        #gameModalContent::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased overflow-x-hidden min-h-screen flex flex-col">

    <header class="bg-white/80 backdrop-blur-md fixed w-full top-0 z-40 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 h-16 flex justify-between items-center">
            <a href="homepage.php" class="flex items-center gap-2 text-2xl font-black tracking-tight text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-blobPurple"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                Play<span class="text-blobOrange">Learn</span>
            </a>
            <a href="homepage.php" class="text-primary font-bold hover:text-blobPurple transition-colors flex items-center gap-2">
                <i class="fas fa-chevron-left"></i> Back to Games
            </a>
        </div>
    </header>

    <main class="flex-grow pt-24 pb-12 px-4 sm:px-6">
        <div class="max-w-4xl mx-auto">
            
            <div class="text-center mb-8 reveal-up">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blobYellow/20 text-blobYellow rounded-full mb-3 shadow-sm">
                    <i class="fas fa-trophy text-3xl"></i>
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-primary">Hall of Fame</h1>
                <p class="text-slate-500 font-bold mt-2">Discover the top minds of PlayLearn</p>
            </div>

            <div class="flex items-center mb-8 reveal-left">
                <button onclick="openGameModal()" class="shrink-0 flex items-center gap-3 bg-white rounded-full p-1.5 pr-5 shadow-sm border border-slate-200 hover:border-blobPurple transition-all z-20 mr-2">
                    <div class="w-10 h-10 rounded-full bg-blobPurple/10 flex items-center justify-center text-blobPurple text-lg">
                        <i class="fas fa-th-large"></i>
                    </div>
                    <span class="font-black text-sm text-primary whitespace-nowrap hidden sm:inline">All Games</span>
                </button>

                <div class="game-selector-track fade-edges" id="horizontalTrack">
                    <?php foreach($game_list as $game): 
                        $isActive = ($game['level_name'] === $selected_game);
                        $activeClass = $isActive ? 'active border-2' : 'border border-slate-200 opacity-70 hover:opacity-100';
                    ?>
                    <a href="leaderboard.php?game=<?php echo urlencode($game['level_name']); ?>" 
                       class="game-pill <?php echo $activeClass; ?> flex items-center gap-2 bg-white rounded-full p-1 pr-4 shadow-sm">
                        <img src="<?php echo htmlspecialchars($game['image_url']); ?>" class="w-8 h-8 rounded-full object-cover">
                        <span class="font-black text-sm text-primary whitespace-nowrap"><?php echo htmlspecialchars($game['level_name']); ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if ($isMemory || in_array($selected_game, $level_based_games)): ?>
            <div class="flex flex-wrap gap-2 justify-center mb-20 reveal-up">
                <a href="leaderboard.php?game=<?php echo urlencode($selected_game); ?>&level=all" 
                   class="px-4 py-1.5 rounded-full text-xs font-bold transition-colors <?php echo ($selected_level === 'all') ? 'bg-primary text-white shadow-md' : 'bg-white text-slate-500 border border-slate-200 hover:bg-slate-50'; ?>">
                    All Levels
                </a>
                <?php for($i = 1; $i <= $max_level; $i++): 
                    $lvl_str = "Level $i";
                    $isActive = ($selected_level === $lvl_str);
                ?>
                <a href="leaderboard.php?game=<?php echo urlencode($selected_game); ?>&level=<?php echo urlencode($lvl_str); ?>" 
                   class="px-4 py-1.5 rounded-full text-xs font-bold transition-colors <?php echo $isActive ? 'bg-primary text-white shadow-md' : 'bg-white text-slate-500 border border-slate-200 hover:bg-slate-50'; ?>">
                    Lvl <?php echo $i; ?>
                </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>

            <div class="podium-container flex items-end justify-center gap-2 sm:gap-4 mt-12 mb-16 min-h-[360px] pt-8 reveal-up" id="cascade-podium">
                
                <?php 
                $p2 = $podium_order[0]; 
                $g2 = getGamificationData($p2);
                ?>
                <div class="podium-wrapper-2 flex flex-col items-center w-1/3 max-w-[150px] cascade-item <?php echo !$p2 ? 'empty-podium' : ''; ?>">
                    <?php if($p2): ?>
                        <div class="relative inline-block mb-3">
                            <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-white shadow-md relative z-10 bg-slate-100">
                                <img src="<?php echo $g2['avatar']; ?>" class="w-full h-full object-cover">
                            </div>
                            <?php if($g2['frame']): ?>
                                <img src="<?php echo $g2['frame']; ?>" class="absolute -top-2 -left-2 -right-2 -bottom-2 w-[calc(100%+16px)] max-w-none pointer-events-none z-20">
                            <?php endif; ?>
                            <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-[#0f172a] text-blobYellow text-[9px] font-black px-2 py-0.5 rounded-full border border-white z-30 shadow-sm whitespace-nowrap">
                                Lv.<?php echo $g2['level']; ?>
                            </div>
                        </div>
                        <span class="font-black text-primary truncate w-full text-center text-sm mb-1"><?php echo htmlspecialchars($p2['username']); ?></span>
                        <span class="text-[9px] font-bold px-2 py-0.5 rounded border <?php echo $g2['title_color']; ?> uppercase tracking-wider mb-2"><?php echo htmlspecialchars($g2['title']); ?></span>
                        <span class="text-xs font-bold text-slate-500 mb-0.5"><?php echo number_format($p2['score']); ?> <?php echo $isMemory ? 'Moves' : 'Pts'; ?></span>
                        <span class="text-[9px] font-bold text-slate-400 mb-2 whitespace-nowrap opacity-70"><?php echo date('M d, Y', strtotime($p2['played_at'])); ?></span>
                    <?php endif; ?>
                    <div class="podium-2 w-full rounded-t-2xl flex flex-col items-center justify-start pt-4 shadow-lg relative overflow-hidden">
                        <div class="absolute inset-0 bg-white/20"></div>
                        <span class="text-4xl font-black text-[#8a8a8a] z-10">2</span>
                    </div>
                </div>

                <?php 
                $p1 = $podium_order[1]; 
                $g1 = getGamificationData($p1);
                ?>
                <div class="podium-wrapper-1 flex flex-col items-center w-1/3 max-w-[160px] cascade-item <?php echo !$p1 ? 'empty-podium' : ''; ?>">
                    <?php if($p1): ?>
                        <div class="relative inline-block mb-3">
                            <i class="fas fa-crown absolute -top-5 -right-3 text-3xl text-[#FFD700] drop-shadow-md z-40"></i>
                            <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-white shadow-xl relative z-10 bg-slate-100">
                                <img src="<?php echo $g1['avatar']; ?>" class="w-full h-full object-cover">
                            </div>
                            <?php if($g1['frame']): ?>
                                <img src="<?php echo $g1['frame']; ?>" class="absolute -top-2.5 -left-2.5 -right-2.5 -bottom-2.5 w-[calc(100%+20px)] max-w-none pointer-events-none z-20">
                            <?php endif; ?>
                            <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-[#0f172a] text-blobYellow text-[10px] font-black px-2.5 py-0.5 rounded-full border-2 border-white z-30 shadow-sm whitespace-nowrap">
                                Lv.<?php echo $g1['level']; ?>
                            </div>
                        </div>
                        <span class="font-black text-primary truncate w-full text-center text-base mb-1"><?php echo htmlspecialchars($p1['username']); ?></span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded border <?php echo $g1['title_color']; ?> uppercase tracking-wider mb-2 shadow-sm"><?php echo htmlspecialchars($g1['title']); ?></span>
                        <span class="text-sm font-black text-blobOrange mb-0.5"><?php echo number_format($p1['score']); ?> <?php echo $isMemory ? 'Moves' : 'Pts'; ?></span>
                        <span class="text-[10px] font-bold text-slate-400 mb-2 whitespace-nowrap opacity-70"><?php echo date('M d, Y', strtotime($p1['played_at'])); ?></span>
                    <?php endif; ?>
                    <div class="podium-1 w-full rounded-t-3xl flex flex-col items-center justify-start pt-4 shadow-2xl relative overflow-hidden">
                        <div class="absolute inset-0 bg-white/30"></div>
                        <span class="text-5xl font-black text-[#b89b00] z-10 drop-shadow-sm">1</span>
                    </div>
                </div>

                <?php 
                $p3 = $podium_order[2]; 
                $g3 = getGamificationData($p3);
                ?>
                <div class="podium-wrapper-3 flex flex-col items-center w-1/3 max-w-[150px] cascade-item <?php echo !$p3 ? 'empty-podium' : ''; ?>">
                    <?php if($p3): ?>
                        <div class="relative inline-block mb-3">
                            <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-white shadow-md relative z-10 bg-slate-100">
                                <img src="<?php echo $g3['avatar']; ?>" class="w-full h-full object-cover">
                            </div>
                            <?php if($g3['frame']): ?>
                                <img src="<?php echo $g3['frame']; ?>" class="absolute -top-2 -left-2 -right-2 -bottom-2 w-[calc(100%+16px)] max-w-none pointer-events-none z-20">
                            <?php endif; ?>
                            <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-[#0f172a] text-blobYellow text-[9px] font-black px-2 py-0.5 rounded-full border border-white z-30 shadow-sm whitespace-nowrap">
                                Lv.<?php echo $g3['level']; ?>
                            </div>
                        </div>
                        <span class="font-black text-primary truncate w-full text-center text-sm mb-1"><?php echo htmlspecialchars($p3['username']); ?></span>
                        <span class="text-[9px] font-bold px-2 py-0.5 rounded border <?php echo $g3['title_color']; ?> uppercase tracking-wider mb-2"><?php echo htmlspecialchars($g3['title']); ?></span>
                        <span class="text-xs font-bold text-slate-500 mb-0.5"><?php echo number_format($p3['score']); ?> <?php echo $isMemory ? 'Moves' : 'Pts'; ?></span>
                        <span class="text-[9px] font-bold text-slate-400 mb-2 whitespace-nowrap opacity-70"><?php echo date('M d, Y', strtotime($p3['played_at'])); ?></span>
                    <?php endif; ?>
                    <div class="podium-3 w-full rounded-t-xl flex flex-col items-center justify-start pt-4 shadow-md relative overflow-hidden">
                        <div class="absolute inset-0 bg-white/20"></div>
                        <span class="text-3xl font-black text-[#a06225] z-10">3</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3 mb-12" id="cascade-list">
                <?php if (count($rest_of_list) > 0): ?>
                    <?php foreach($rest_of_list as $index => $row): 
                        $rank = $index + 4; 
                        $gList = getGamificationData($row);
                    ?>
                    <div class="cascade-item flex items-center bg-white rounded-2xl p-4 shadow-sm border border-slate-100 transition-transform hover:scale-[1.02] hover:shadow-md">
                        <div class="w-10 text-center font-black text-slate-400 text-lg mr-2 shrink-0">#<?php echo $rank; ?></div>
                        
                        <div class="relative inline-block mr-4 shrink-0">
                            <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-white shadow-sm relative z-10 bg-slate-100">
                                <img src="<?php echo $gList['avatar']; ?>" class="w-full h-full object-cover">
                            </div>
                            <?php if($gList['frame']): ?>
                                <img src="<?php echo $gList['frame']; ?>" class="absolute -top-1.5 -left-1.5 -right-1.5 -bottom-1.5 w-[calc(100%+12px)] max-w-none pointer-events-none z-20">
                            <?php endif; ?>
                            <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 bg-[#0f172a] text-blobYellow text-[8px] font-black px-1.5 py-px rounded-full border border-white z-30 shadow-sm whitespace-nowrap">
                                Lv.<?php echo $gList['level']; ?>
                            </div>
                        </div>
                        
                        <div class="flex-grow min-w-0 pr-4">
                            <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                <h3 class="font-black text-primary text-sm md:text-base truncate"><?php echo htmlspecialchars($row['username']); ?></h3>
                                <span class="text-[8px] font-bold px-1.5 py-0.5 rounded border <?php echo $gList['title_color']; ?> uppercase whitespace-nowrap"><?php echo htmlspecialchars($gList['title']); ?></span>
                            </div>
                            <p class="text-xs font-bold text-slate-400 truncate"><?php echo date('M d, Y', strtotime($row['played_at'])); ?></p>
                        </div>

                        <div class="text-right shrink-0">
                            <?php if($isMemory || in_array($selected_game, $level_based_games)): ?>
                                <span class="bg-slate-100 text-slate-500 text-[10px] font-black px-2 py-1 rounded-md uppercase tracking-wide mr-2 hidden sm:inline-block">Lvl <?php echo $row['level_reached']; ?></span>
                            <?php endif; ?>
                            <span class="font-black text-primary text-lg"><?php echo number_format($row['score']); ?> <span class="text-xs text-slate-400 font-bold hidden sm:inline-block"><?php echo $isMemory ? 'Moves' : 'Pts'; ?></span></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php elseif (count($leaderboard_data) <= 3): ?>
                    <div class="text-center text-slate-400 font-bold text-sm py-8 cascade-item">No more challengers yet. Keep playing!</div>
                <?php endif; ?>
            </div>

            <?php 
            if ($my_record): 
                $my_g = getGamificationData($my_record);
                
                $my_rank = $my_record['rank'];
                $bg_class = 'bg-primary border-slate-700'; 
                $text_color = 'text-white';
                $blob_color = 'bg-blobPurple/30';
                $rank_badge = 'bg-blobOrange text-white border-primary';

                if ($my_rank == 1) { 
                    $bg_class = 'bg-gradient-to-r from-[#FFD700] to-[#FDB931] border-[#FFD700]';
                    $text_color = 'text-[#5A4000]';
                    $blob_color = 'bg-white/40';
                    $rank_badge = 'bg-white text-[#5A4000] border-[#FDB931]';
                } elseif ($my_rank == 2) { 
                    $bg_class = 'bg-gradient-to-r from-[#E0E0E0] to-[#F5F5F5] border-[#C0C0C0]';
                    $text_color = 'text-[#404040]';
                    $blob_color = 'bg-[#C0C0C0]/40';
                    $rank_badge = 'bg-white text-[#404040] border-[#E0E0E0]';
                } elseif ($my_rank == 3) { 
                    $bg_class = 'bg-gradient-to-r from-[#CD7F32] to-[#E89D5E] border-[#CD7F32]';
                    $text_color = 'text-[#4A2E00]';
                    $blob_color = 'bg-white/30';
                    $rank_badge = 'bg-white text-[#4A2E00] border-[#CD7F32]';
                }
            ?>
            <div class="reveal-up <?php echo $bg_class; ?> rounded-3xl p-6 shadow-2xl relative overflow-hidden flex items-center justify-between border mt-8">
                <div class="absolute -right-10 -top-10 w-40 h-40 <?php echo $blob_color; ?> rounded-full blur-2xl"></div>
                
                <div class="flex items-center gap-5 z-10">
                    <div class="relative inline-block shrink-0">
                        <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-white shadow-lg relative z-10 bg-slate-100">
                            <img src="<?php echo $my_g['avatar']; ?>" class="w-full h-full object-cover">
                        </div>
                        <?php if($my_g['frame']): ?>
                            <img src="<?php echo $my_g['frame']; ?>" class="absolute -top-2 -left-2 -right-2 -bottom-2 w-[calc(100%+16px)] max-w-none pointer-events-none z-20">
                        <?php endif; ?>
                        <div class="absolute -bottom-2 -right-2 text-[10px] font-black px-2 py-0.5 rounded-full border-2 shadow-sm z-30 <?php echo $rank_badge; ?>">
                            Rank #<?php echo $my_record['rank']; ?>
                        </div>
                    </div>
                    <div>
                        <h3 class="<?php echo $text_color; ?> font-black text-lg">Your Best Record</h3>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded border border-white/30 bg-white/20 <?php echo $text_color; ?> uppercase">Lv.<?php echo $my_g['level']; ?> | <?php echo htmlspecialchars($my_g['title']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="text-right z-10">
                    <?php if($isMemory || in_array($selected_game, $level_based_games)): ?>
                        <div class="<?php echo $text_color; ?> opacity-90 text-[10px] font-black uppercase tracking-widest mb-1">Level <?php echo $my_record['my_lvl']; ?></div>
                    <?php endif; ?>
                    <div class="<?php echo $text_color; ?> font-black text-2xl md:text-3xl"><?php echo number_format($my_record['my_score']); ?> <span class="text-sm opacity-80 font-bold"><?php echo $isMemory ? 'Moves' : 'Pts'; ?></span></div>
                </div>
            </div>
            <?php elseif (isset($_SESSION['user_id'])): ?>
            <div class="reveal-up bg-white rounded-3xl p-6 shadow-sm border border-slate-200 text-center mt-8">
                <div class="inline-flex w-12 h-12 bg-slate-100 rounded-full items-center justify-center text-slate-400 mb-2"><i class="fas fa-ghost"></i></div>
                <h3 class="text-primary font-black">No Records Found</h3>
                <p class="text-slate-500 text-sm font-bold mt-1">Play this game to leave your mark on the leaderboard!</p>
            </div>
            <?php endif; ?>

        </div>
    </main>

    <div id="allGamesModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
        <div class="absolute inset-0 bg-primary/40 backdrop-blur-sm transition-opacity duration-300 opacity-0" id="modalOverlay" onclick="closeGameModal()"></div>
        
        <div class="bg-white rounded-3xl w-11/12 max-w-5xl max-h-[85vh] flex flex-col z-10 shadow-2xl scale-95 opacity-0 transition-all duration-300 ease-out" id="modalContent">
            
            <div class="sticky top-0 bg-white/95 backdrop-blur-md px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center rounded-t-3xl z-20 gap-4">
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-primary flex items-center gap-2">
                        <i class="fas fa-gamepad text-blobOrange"></i> Select Game
                    </h2>
                </div>
                
                <div class="relative w-full sm:w-64">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" id="modalSearchInput" placeholder="Search games..." onkeyup="filterModalGames()" class="w-full pl-9 pr-4 py-2 bg-slate-100 border-none rounded-full text-sm font-bold text-primary focus:ring-2 focus:ring-blobPurple outline-none transition-all placeholder:text-slate-400">
                </div>

                <button onclick="closeGameModal()" class="hidden sm:flex w-10 h-10 bg-slate-100 hover:bg-red-100 hover:text-red-500 rounded-full items-center justify-center transition-colors shrink-0">
                    <i class="fas fa-times text-lg"></i>
                </button>
                <button onclick="closeGameModal()" class="sm:hidden absolute top-4 right-4 w-8 h-8 bg-slate-100 text-slate-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6 overflow-y-auto" id="gameModalContent">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4" id="modalGridContainer">
                    <?php foreach($game_list as $game): 
                        $isActive = ($game['level_name'] === $selected_game);
                        $bgClass = $isActive ? 'bg-blobPurple/5 border-blobPurple' : 'bg-slate-50 border-transparent hover:border-blobPurple/30 hover:bg-slate-100 hover:shadow-sm hover:-translate-y-1';
                    ?>
                    <a href="leaderboard.php?game=<?php echo urlencode($game['level_name']); ?>" data-title="<?php echo strtolower(htmlspecialchars($game['level_name'])); ?>" class="modal-game-item flex flex-col items-center gap-3 p-4 rounded-2xl border-2 transition-all duration-200 <?php echo $bgClass; ?>">
                        <div class="relative">
                            <img src="<?php echo htmlspecialchars($game['image_url']); ?>" class="w-16 h-16 rounded-2xl object-cover shadow-sm <?php echo $isActive ? 'ring-4 ring-blobPurple ring-offset-2' : ''; ?>">
                            <?php if($isActive): ?>
                                <div class="absolute -top-2 -right-2 bg-blobPurple text-white rounded-full w-6 h-6 flex items-center justify-center border-2 border-white shadow-sm"><i class="fas fa-check text-xs"></i></div>
                            <?php endif; ?>
                        </div>
                        <span class="font-black text-xs md:text-sm text-center text-primary line-clamp-2 leading-tight"><?php echo htmlspecialchars($game['level_name']); ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
                <div id="noResultsMsg" class="hidden py-10 text-center flex-col items-center justify-center">
                    <i class="fas fa-search-minus text-4xl text-slate-300 mb-3"></i>
                    <p class="font-black text-slate-400">No games match your search.</p>
                </div>
            </div>
            
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const horizontalTrack = document.getElementById('horizontalTrack');
            if (horizontalTrack) {
                horizontalTrack.addEventListener('wheel', function(e) {
                    if (e.deltaY !== 0) {
                        e.preventDefault(); 
                        this.scrollLeft += e.deltaY; 
                    }
                }, { passive: false }); 
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    } else {
                        entry.target.classList.remove('active');
                    }
                });
            }, { threshold: 0.1, rootMargin: "0px 0px -50px 0px" });

            document.querySelectorAll('.reveal-up, .reveal-left').forEach(el => observer.observe(el));

            const podiumGrid = document.getElementById('cascade-podium');
            if (podiumGrid) {
                const podObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        const items = entry.target.querySelectorAll('.cascade-item');
                        if (entry.isIntersecting) {
                            items.forEach((item, index) => {
                                item.style.transition = `all 0.8s cubic-bezier(0.25, 1, 0.2, 1) ${index * 0.15}s`;
                                setTimeout(() => item.classList.add('active'), 50);
                            });
                        } else {
                            items.forEach((item) => {
                                item.style.transition = `all 0.4s ease 0s`;
                                item.classList.remove('active');
                            });
                        }
                    });
                }, { threshold: 0.1 });
                podObserver.observe(podiumGrid);
            }

            const listGrid = document.getElementById('cascade-list');
            if (listGrid) {
                const listObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        const items = entry.target.querySelectorAll('.cascade-item');
                        if (entry.isIntersecting) {
                            items.forEach((item, index) => {
                                item.style.transition = `all 0.6s cubic-bezier(0.25, 1, 0.2, 1) ${index * 0.08}s`;
                                setTimeout(() => item.classList.add('active'), 50);
                            });
                        } else {
                            items.forEach((item) => {
                                item.style.transition = `all 0.4s ease 0s`;
                                item.classList.remove('active');
                            });
                        }
                    });
                }, { threshold: 0.1 });
                listObserver.observe(listGrid);
            }
        });

        function openGameModal() {
            const modal = document.getElementById('allGamesModal');
            const overlay = document.getElementById('modalOverlay');
            const content = document.getElementById('modalContent');
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
                overlay.classList.add('opacity-100');
                
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
            
            document.body.style.overflow = 'hidden';
            setTimeout(() => document.getElementById('modalSearchInput').focus(), 300);
        }

        function closeGameModal() {
            const modal = document.getElementById('allGamesModal');
            const overlay = document.getElementById('modalOverlay');
            const content = document.getElementById('modalContent');
            
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0');
            
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
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
                if (title.includes(query)) {
                    game.style.display = 'flex';
                    hasVisible = true;
                } else {
                    game.style.display = 'none';
                }
            });

            const noResults = document.getElementById('noResultsMsg');
            if (hasVisible) {
                noResults.classList.add('hidden');
                noResults.classList.remove('flex');
            } else {
                noResults.classList.remove('hidden');
                noResults.classList.add('flex');
            }
        }
    </script>
</body>
</html>