<?php
// --- 1. 从数据库获取实时事实 ---

// 事实 A: 总积分
$res_total = $conn->query("SELECT SUM(score) as total FROM game_scores WHERE user_id = $target_user_id");
$actual_total = $res_total->fetch_assoc()['total'] ?? 0;

// 事实 B: Logic 类型的最高分 (判断是否成为 Logic Master)
$res_logic = $conn->query("SELECT SUM(gs.score) as logic_total 
                           FROM game_scores gs 
                           JOIN levels l ON gs.game_name = l.level_name 
                           WHERE gs.user_id = $target_user_id AND l.category = 'Logic'");
$actual_logic = $res_logic->fetch_assoc()['logic_total'] ?? 0;

// 事实 C: 累计玩过的游戏局数
$res_count = $conn->query("SELECT COUNT(*) as games_count FROM game_scores WHERE user_id = $target_user_id");
$actual_count = $res_count->fetch_assoc()['games_count'] ?? 0;

// --- 2. 定义目标规则 ---
$goals = [
    [
        'title' => 'Novice Scorer',
        'desc' => 'Reach a total of 1,000 points',
        'current' => $actual_total,
        'target' => 1000,
        'icon' => 'star'
    ],
    [
        'title' => 'Logic Apprentice',
        'desc' => 'Earn 500 points in Logic games',
        'current' => $actual_logic,
        'target' => 500,
        'icon' => 'brain'
    ],
    [
        'title' => 'Active Player',
        'desc' => 'Complete 20 game sessions',
        'current' => $actual_count,
        'target' => 20,
        'icon' => 'fire'
    ]
];
?>

<div class="row g-4">
    <?php foreach($goals as $g): 
        $percent = ($g['current'] / $g['target']) * 100;
        $is_done = $percent >= 100;
        if($percent > 100) $percent = 100;
    ?>
    <div class="col-md-4">
        <div class="card p-4 border-0 shadow-sm <?= $is_done ? 'bg-light' : '' ?>">
            <div class="text-center mb-3">
                <div class="icon-box mb-2">
                    <i class="fas fa-<?= $g['icon'] ?> fa-2x <?= $is_done ? 'text-success' : 'text-primary' ?>"></i>
                </div>
                <h5 class="fw-bold"><?= $g['title'] ?></h5>
                <p class="small text-muted"><?= $g['desc'] ?></p>
            </div>
            
            <div class="progress mb-2" style="height: 10px; border-radius: 5px;">
                <div class="progress-bar <?= $is_done ? 'bg-success' : 'bg-primary' ?>" 
                     style="width: <?= $percent ?>%"></div>
            </div>
            
            <div class="d-flex justify-content-between small fw-bold">
                <span><?= number_format($g['current']) ?></span>
                <span><?= number_format($g['target']) ?></span>
            </div>
            
            <?php if($is_done): ?>
                <div class="text-success text-center mt-2 small fw-bold">
                    <i class="fas fa-check-circle"></i> Achievement Unlocked!
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>