<?php
require_once __DIR__ . '/../includes/learning_helpers.php';
pl_ensure_learning_schema($conn);
// 1. 初始化变量，防止报错
$total_points = 0;
$total_games = 0;
$active_days = 0;
$streak_days = pl_streak_days($conn, $target_user_id);
$avg_accuracy = null;
$avg_reaction = null;
$strengthCategory = "N/A";
$childProfile = [
    'title' => "New Explorer",
    'desc' => "Keep playing games to discover cognitive talents!"
];

// 2. 获取基础统计数据
$sql_stats = "SELECT SUM(score) as total_pts, COUNT(*) as total_count FROM game_scores WHERE user_id = $target_user_id";
$res_stats = $conn->query($sql_stats);
if ($res_stats && $row = $res_stats->fetch_assoc()) {
    $total_points = $row['total_pts'] ?? 0;
    $total_games = $row['total_count'] ?? 0;
}

// 3. 活跃天数 (过去7天)
$sql_days = "SELECT COUNT(DISTINCT DATE(played_at)) as days FROM game_scores WHERE user_id = $target_user_id AND played_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
$res_days = $conn->query($sql_days);
if ($res_days && $row = $res_days->fetch_assoc()) {
    $active_days = $row['days'] ?? 0;
}

$sql_quality = "SELECT AVG(CASE WHEN total_questions > 0 THEN (correct_answers / total_questions) * 100 END) AS avg_accuracy,
                       AVG(reaction_time_ms) AS avg_reaction
                FROM game_scores
                WHERE user_id = $target_user_id";
$res_quality = $conn->query($sql_quality);
if ($res_quality && $row = $res_quality->fetch_assoc()) {
    $avg_accuracy = $row['avg_accuracy'] !== null ? round($row['avg_accuracy']) : null;
    $avg_reaction = $row['avg_reaction'] !== null ? round($row['avg_reaction']) : null;
}

// 4. 最强项分析
$sql_strength = "SELECT l.category, AVG(gs.score) as avg_score 
                 FROM game_scores gs 
                 JOIN levels l ON gs.game_name = l.level_name 
                 WHERE gs.user_id = $target_user_id 
                 GROUP BY l.category 
                 ORDER BY avg_score DESC LIMIT 1";
$res_strength = $conn->query($sql_strength);

if ($res_strength && $row = $res_strength->fetch_assoc()) {
    $strengthCategory = $row['category'];
    $childProfile['title'] = $strengthCategory . " Specialist";
    $childProfile['desc'] = "Your child is excelling in " . strtolower($strengthCategory) . " logic. Keep it up!";
}

// 🌟 5. 新增：获取孩子的等级和总经验值
$sql_level = "SELECT level, total_exp FROM users WHERE id = $target_user_id";
$res_level = $conn->query($sql_level);
$user_lvl = 1;
$total_exp = 0;
if ($res_level && $row = $res_level->fetch_assoc()) {
    $user_lvl = $row['level'] > 0 ? $row['level'] : 1;
    $total_exp = $row['total_exp'] ?? 0;
}
$next_lvl_exp = $user_lvl * 500;
$exp_percent = min(100, ($total_exp / $next_lvl_exp) * 100);

// 🌟 6. 新增：为 Chart.js 准备过去 7 天的活跃数据
$chart_labels = [];
$chart_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chart_labels[] = date('M d', strtotime($date));
    
    $sql_day = "SELECT COUNT(*) as cnt FROM game_scores WHERE user_id = $target_user_id AND DATE(played_at) = '$date'";
    $res_day = $conn->query($sql_day);
    $cnt = ($res_day && $row = $res_day->fetch_assoc()) ? $row['cnt'] : 0;
    $chart_data[] = $cnt;
}
?>

<style>
    .overview-card { border-radius: 24px; border: 1px solid var(--border-color); box-shadow: 0 10px 30px rgba(0,0,0,0.02); background: #ffffff; overflow: hidden; }
    .icon-box { width: 48px; height: 48px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .progress-fill { transition: width 1s cubic-bezier(0.4, 0, 0.2, 1); }
    .table-custom td { padding: 16px 8px; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
    .table-custom tr:last-child td { border-bottom: none; }
</style>

<div class="alert border-0 mb-4 d-flex align-items-center" style="background: rgba(108, 63, 245, 0.05); color: var(--blob-purple); border-radius: 16px;">
    <i class="fas fa-info-circle me-3 fa-lg"></i> 
    <div style="font-weight: 700; font-size: 14px;">
        You are currently viewing <strong style="font-weight: 900;"><?php echo htmlspecialchars($active_child_name ?? 'this account'); ?></strong>'s overall learning progress and recent activity.
    </div>
</div>

<div class="card p-5 text-white mb-4 border-0 shadow-lg" style="background: linear-gradient(135deg, var(--blob-purple) 0%, var(--blob-orange) 100%); border-radius: 28px;">
    <div class="row align-items-center">
        <div class="col-md-7 mb-4 mb-md-0 d-flex align-items-center gap-4">
            <div class="bg-white p-4 shadow-sm" style="border-radius: 20px;">
                <i class="fas fa-brain fa-3x" style="color: var(--blob-purple);"></i>
            </div>
            <div>
                <p class="small fw-bold mb-1 text-white-50" style="letter-spacing: 2px;">SYSTEM CALCULATED PROFILE</p>
                <h1 class="fw-bold mb-2" style="font-weight: 900; font-size: 32px;"><?php echo $childProfile['title']; ?></h1>
                <p class="mb-0" style="font-weight: 600; font-size: 15px; opacity: 0.9;"><?php echo $childProfile['desc']; ?></p>
            </div>
        </div>
        
        <div class="col-md-5">
            <div class="bg-white p-4" style="border-radius: 20px; background: rgba(255,255,255,0.15) !important; border: 1px solid rgba(255,255,255,0.3);">
                <div class="d-flex justify-content-between align-items-end mb-2">
                    <span class="small fw-bold text-white uppercase">Level <?php echo $user_lvl; ?> Progress</span>
                    <span class="fw-bold text-white"><?php echo $total_exp; ?> / <?php echo $next_lvl_exp; ?> EXP</span>
                </div>
                <div class="w-100 rounded-pill overflow-hidden" style="height: 10px; background: rgba(255,255,255,0.2);">
                    <div class="h-100 bg-white rounded-pill progress-fill" style="width: <?php echo $exp_percent; ?>%;"></div>
                </div>
                <p class="small mt-2 mb-0 text-white-50 fw-bold"><i class="fas fa-rocket me-1"></i> <?php echo ($next_lvl_exp - $total_exp); ?> EXP needed for Level <?php echo $user_lvl + 1; ?>!</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="overview-card p-4 h-100 d-flex flex-column align-items-center justify-content-center text-center transition-all hover-translate">
            <div class="icon-box mb-3" style="background: rgba(74, 144, 226, 0.1); color: #4A90E2;"><i class="fas fa-calendar-check"></i></div>
            <h2 class="fw-bold mb-0" style="color: var(--primary-dark);"><?php echo $active_days; ?></h2>
            <p class="small fw-bold text-muted mb-0 text-uppercase tracking-wider">Practice Days</p>
            <p class="small fw-bold mb-0 mt-1" style="color: var(--blob-purple);"><?php echo $streak_days; ?> day streak</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="overview-card p-4 h-100 d-flex flex-column align-items-center justify-content-center text-center">
            <div class="icon-box mb-3" style="background: rgba(39, 174, 96, 0.1); color: #27ae60;"><i class="fas fa-gamepad"></i></div>
            <h2 class="fw-bold mb-0" style="color: var(--primary-dark);"><?php echo $total_games; ?></h2>
            <p class="small fw-bold text-muted mb-0 text-uppercase tracking-wider">Games Played</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="overview-card p-4 h-100 d-flex flex-column align-items-center justify-content-center text-center">
            <div class="icon-box mb-3" style="background: rgba(243, 156, 18, 0.1); color: #f39c12;"><i class="fas fa-star"></i></div>
            <h2 class="fw-bold mb-0" style="color: var(--primary-dark);"><?php echo number_format($total_points); ?></h2>
            <p class="small fw-bold text-muted mb-0 text-uppercase tracking-wider">Total Points</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="overview-card p-4 h-100 d-flex flex-column align-items-center justify-content-center text-center">
            <div class="icon-box mb-3" style="background: rgba(231, 76, 60, 0.1); color: #e74c3c;"><i class="fas fa-medal"></i></div>
            <h2 class="fw-bold mb-0" style="color: var(--primary-dark); font-size: 20px;"><?php echo $avg_accuracy !== null ? $avg_accuracy . '%' : $strengthCategory; ?></h2>
            <p class="small fw-bold text-muted mb-0 text-uppercase tracking-wider"><?php echo $avg_accuracy !== null ? 'Avg Accuracy' : 'Top Strength'; ?></p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        
        <div class="overview-card p-4 mb-4">
            <h5 class="fw-bold mb-4" style="color: var(--primary-dark);">Activity Trend (Last 7 Days)</h5>
            <div style="height: 250px; width: 100%;">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <div class="overview-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold m-0" style="color: var(--primary-dark);">Recent Sessions</h5>
                <a href="?page=history" class="text-decoration-none small fw-bold" style="color: var(--blob-purple);">View Full Log →</a>
            </div>
            <div class="table-responsive">
                <table class="table table-borderless table-custom align-middle mb-0">
                    <tbody>
                        <?php 
                        $sql_recent = "SELECT game_name, score, played_at FROM game_scores WHERE user_id = $target_user_id ORDER BY played_at DESC LIMIT 4";
                        $res_recent = $conn->query($sql_recent);
                        if($res_recent && $res_recent->num_rows > 0):
                            while($row = $res_recent->fetch_assoc()):
                        ?>
                        <tr>
                            <td style="width: 60px;">
                                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: #f8fafc; border: 1px solid var(--border-color);">
                                    <i class="fas fa-play" style="color: var(--blob-orange);"></i>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold" style="color: var(--primary-dark); font-size: 15px;"><?php echo htmlspecialchars($row['game_name']); ?></div>
                                <div class="small fw-bold" style="color: #94a3b8;"><i class="far fa-clock me-1"></i> <?php echo date('M d, H:i', strtotime($row['played_at'])); ?></div>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold px-3 py-1 rounded-pill" style="background: rgba(39, 174, 96, 0.1); color: #27ae60; font-size: 13px;">+<?php echo $row['score']; ?> pts</span>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="3" class="text-center text-muted py-5 fw-bold"><i class="fas fa-ghost fa-2x mb-3 opacity-25"></i><br>No recent sessions found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="overview-card p-4 h-100 d-flex flex-column">
            <h5 class="fw-bold mb-4" style="color: var(--primary-dark);">Expert Feedback</h5>
            
            <div class="p-3 mb-3" style="background-color: rgba(108, 63, 245, 0.05); border-left: 4px solid var(--blob-purple); border-radius: 0 16px 16px 0;">
                <p class="small fw-bold mb-1" style="color: var(--blob-purple); letter-spacing: 1px;"><i class="fas fa-lightbulb me-2"></i>POSITIVE INSIGHT</p>
                <p class="small mb-0" style="color: #475569; font-weight: 600;">Your child is showing great potential in <b><?php echo $strengthCategory; ?></b> games. This builds excellent cognitive foundations.</p>
            </div>
            
            <div class="p-3 mb-4" style="background-color: rgba(255, 107, 107, 0.05); border-left: 4px solid var(--blob-orange); border-radius: 0 16px 16px 0;">
                <p class="small fw-bold mb-1" style="color: var(--blob-orange); letter-spacing: 1px;"><i class="fas fa-exclamation-circle me-2"></i>FOCUS AREA</p>
                <p class="small mb-0" style="color: #475569; font-weight: 600;">Try guiding them to practice more <b>Memory</b> tasks to perfectly balance their skill radar.</p>
            </div>
            
            <div class="mt-auto p-4 text-center" style="background: #f8fafc; border-radius: 20px; border: 1px dashed var(--border-color);">
                <i class="fas fa-quote-left mb-2 fa-lg" style="color: #cbd5e1;"></i>
                <p class="small mb-0" style="color: #64748b; font-weight: 700; font-style: italic;">"Encourage your child to describe their strategy out loud after a game to boost logical thinking!"</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('activityChart').getContext('2d');
    
    // PHP 传递给 JS 的数据
    const labels = <?php echo json_encode($chart_labels); ?>;
    const dataPoints = <?php echo json_encode($chart_data); ?>;
    
    // 创建渐变填充
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(108, 63, 245, 0.4)'); // 顶部紫色半透明
    gradient.addColorStop(1, 'rgba(108, 63, 245, 0.0)'); // 底部全透明

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Games Played',
                data: dataPoints,
                borderColor: '#6C3FF5', // --blob-purple
                backgroundColor: gradient,
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#6C3FF5',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4 // 让线条圆滑 (贝塞尔曲线)
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleFont: { family: 'Nunito', size: 13 },
                    bodyFont: { family: 'Nunito', size: 14, weight: 'bold' },
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, color: '#94a3b8', font: { family: 'Nunito', weight: 'bold' } },
                    grid: { color: '#f1f5f9', drawBorder: false }
                },
                x: {
                    ticks: { color: '#94a3b8', font: { family: 'Nunito', weight: 'bold' } },
                    grid: { display: false, drawBorder: false }
                }
            }
        }
    });
});
</script>
