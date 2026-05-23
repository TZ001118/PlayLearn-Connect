<?php
$categories = ['Logic', 'Memory', 'Creativity', 'Focus', 'Speed'];
// 🌟 核心修复：预设所有维度为 0，防止报错
$db_stats = array_fill_keys($categories, 0);

$sql_radar = "SELECT l.category, AVG(gs.score) as avg_score 
              FROM game_scores gs 
              JOIN levels l ON gs.game_name = l.level_name 
              WHERE gs.user_id = $target_user_id 
              GROUP BY l.category";
$res_radar = $conn->query($sql_radar);

if($res_radar) {
    while($row = $res_radar->fetch_assoc()) {
        // 将分数标准化（例如 2000 分为 100%）
        $score = ($row['avg_score'] / 2000) * 100;
        $db_stats[$row['category']] = ($score > 100) ? 100 : round($score);
    }
}

$radar_values = array_values($db_stats); // 获取纯数字数组供 Chart.js 使用
$top_skill = array_search(max($db_stats), $db_stats);
$weak_skill = array_search(min($db_stats), $db_stats);

// 1. 计算总分和总局数
$sql_overview = "SELECT SUM(score) as total_pts, COUNT(*) as total_games FROM game_scores WHERE user_id = $target_user_id";
$res_ov = $conn->query($sql_overview);
$ov_data = $res_ov->fetch_assoc();

$total_points = $ov_data['total_pts'] ?? 0;
$games_played = $ov_data['total_games'] ?? 0;

// 2. 获取最近一局
$sql_recent = "SELECT game_name, score FROM game_scores WHERE user_id = $target_user_id ORDER BY played_at DESC LIMIT 1";
$res_recent = $conn->query($sql_recent);
$recent = $res_recent->fetch_assoc();

?>

<div class="container-fluid">
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card p-4 border-0 shadow-sm h-100">
                <h4 class="fw-bold mb-4">Talent Radar Analysis</h4>
                <div style="position: relative; height: 450px;">
                    <canvas id="mainRadarChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card p-4 border-0 shadow-sm h-100">
                <h4 class="fw-bold mb-4">Performance Metrics</h4>
                <?php foreach ($categories as $index => $cat): 
                    $score = $radar_values[$index];
                    // 根据分数动态改变颜色
                    $color = ($score > 80) ? '#2ecc71' : (($score > 50) ? '#5d5fef' : '#e74c3c');
                ?>
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-bold"><?= $cat ?></span>
                        <span class="fw-bold" style="color: <?= $color ?>"><?= $score ?>%</span>
                    </div>
                    <div class="progress" style="height: 10px; border-radius: 10px; background-color: #f0f2f5;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: <?= $score ?>%; background-color: <?= $color ?>; border-radius: 10px;"></div>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="mt-auto p-3 rounded-4" style="background-color: #f8f9fa;">
                    <h6 class="fw-bold text-primary mb-2">💡 Quick Insight</h6>
                    <p class="small text-muted mb-0">
                        Based on current performance, your child's <strong><?= $top_skill ?></strong> is the strongest. 
                        Try focusing on <strong><?= $weak_skill ?></strong> to ensure balanced development.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card p-4 border-0 shadow-sm">
                <h5 class="fw-bold mb-4">Expert Training Strategy</h5>
                <div class="row g-4">
                    <div class="col-md-6 border-end">
                        <h6><span class="badge bg-success-subtle text-success me-2">STRENGTH</span> <?= $top_skill ?></h6>
                        <p class="text-muted small">Your child shows a natural aptitude for <?= strtolower($top_skill) ?>. They can process complex patterns quickly. We recommend advancing to higher level challenges in this area.</p>
                    </div>
                    <div class="col-md-6">
                        <h6><span class="badge bg-danger-subtle text-danger me-2">FOCUS AREA</span> <?= $weak_skill ?></h6>
                        <p class="text-muted small">The current data suggests <?= strtolower($weak_skill) ?> tasks are a bit challenging. This is normal! Consistent practice with simple puzzles in this category will help them grow.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('mainRadarChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: <?= json_encode($categories) ?>,
            datasets: [{
                label: 'Current Proficiency',
                data: <?= json_encode($radar_values) ?>,
                backgroundColor: 'rgba(93, 95, 239, 0.15)',
                borderColor: '#5d5fef',
                borderWidth: 3,
                pointBackgroundColor: '#5d5fef',
                pointBorderColor: '#fff',
                pointHoverRadius: 6,
                fill: true,
                tension: 0.2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    angleLines: { color: '#eee' },
                    grid: { color: '#eee' },
                    suggestMin: 0,
                    suggestMax: 100,
                    ticks: { display: false, stepSize: 20 },
                    pointLabels: {
                        font: { size: 14, weight: '600' },
                        color: '#2c3e50'
                    }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>