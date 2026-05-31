<?php
require_once __DIR__ . '/../includes/learning_helpers.php';

pl_seed_game_metadata($conn);

$skills = pl_skill_definitions();
$skill_scores = [];
$skill_weight_totals = [];
foreach ($skills as $key => $_meta) {
    $skill_scores[$key] = 0;
    $skill_weight_totals[$key] = 0;
}

$overall_accuracy = null;
$overall_reaction = null;
$accuracy_total = 0;
$accuracy_count = 0;
$reaction_total = 0;
$reaction_count = 0;

$sql_sessions = "SELECT gs.*, l.id AS game_id, l.level_name, l.category
                 FROM game_scores gs
                 LEFT JOIN levels l ON gs.game_name = l.level_name
                 WHERE gs.user_id = " . intval($target_user_id) . "
                 ORDER BY gs.played_at DESC";
$session_res = $conn->query($sql_sessions);

if ($session_res) {
    while ($session = $session_res->fetch_assoc()) {
        $game_id = intval($session['game_id'] ?? 0);
        if ($game_id <= 0) {
            continue;
        }

        $weights = pl_get_game_weights($conn, $game_id, $session['level_name'], $session['category']);
        $score_value = max(0, intval($session['score']));
        $score_performance = min(100, round(($score_value / 2000) * 100));

        if (!empty($session['total_questions'])) {
            $accuracy = round((intval($session['correct_answers']) / max(1, intval($session['total_questions']))) * 100);
            $performance = round(($accuracy * 0.7) + ($score_performance * 0.3));
            $accuracy_total += $accuracy;
            $accuracy_count++;
        } else {
            $performance = $score_performance;
        }

        if (!empty($session['reaction_time_ms'])) {
            $reaction_total += intval($session['reaction_time_ms']);
            $reaction_count++;
        }

        foreach ($weights as $skill_key => $weight) {
            if ($weight <= 0 || !isset($skill_scores[$skill_key])) {
                continue;
            }
            $skill_scores[$skill_key] += $performance * $weight;
            $skill_weight_totals[$skill_key] += $weight;
        }
    }
}

$radar_labels = [];
$radar_values = [];
$display_scores = [];
foreach ($skills as $key => $meta) {
    $value = $skill_weight_totals[$key] > 0 ? round($skill_scores[$key] / $skill_weight_totals[$key]) : 0;
    $radar_labels[] = $meta['label'];
    $radar_values[] = $value;
    $display_scores[$key] = $value;
}

$top_key = array_key_first($display_scores);
$weak_key = array_key_first($display_scores);
foreach ($display_scores as $key => $value) {
    if ($value > $display_scores[$top_key]) $top_key = $key;
    if ($value < $display_scores[$weak_key]) $weak_key = $key;
}

$overall_accuracy = $accuracy_count > 0 ? round($accuracy_total / $accuracy_count) : null;
$overall_reaction = $reaction_count > 0 ? round($reaction_total / $reaction_count) : null;
$streak_days = pl_streak_days($conn, $target_user_id);

$total_res = $conn->query("SELECT SUM(score) AS total_pts, COUNT(*) AS total_games FROM game_scores WHERE user_id = " . intval($target_user_id));
$summary = $total_res ? $total_res->fetch_assoc() : ['total_pts' => 0, 'total_games' => 0];
?>

<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 18px;">
                <div class="small fw-bold text-muted text-uppercase">Sessions</div>
                <div class="display-6 fw-black" style="font-weight: 900; color: var(--primary-dark);"><?= number_format($summary['total_games'] ?? 0) ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 18px;">
                <div class="small fw-bold text-muted text-uppercase">Accuracy</div>
                <div class="display-6 fw-black" style="font-weight: 900; color: var(--blob-purple);"><?= $overall_accuracy !== null ? $overall_accuracy . '%' : 'N/A' ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 18px;">
                <div class="small fw-bold text-muted text-uppercase">Avg Reaction</div>
                <div class="display-6 fw-black" style="font-weight: 900; color: var(--blob-orange);"><?= $overall_reaction !== null ? number_format($overall_reaction) . ' ms' : 'N/A' ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 18px;">
                <div class="small fw-bold text-muted text-uppercase">Streak</div>
                <div class="display-6 fw-black" style="font-weight: 900; color: #00B894;"><?= $streak_days ?> days</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card p-4 border-0 shadow-sm h-100" style="border-radius: 22px;">
                <h4 class="fw-bold mb-4">Talent Radar Analysis</h4>
                <div style="position: relative; height: 450px;">
                    <canvas id="mainRadarChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card p-4 border-0 shadow-sm h-100" style="border-radius: 22px;">
                <h4 class="fw-bold mb-4">Learning Skill Map</h4>
                <?php foreach ($skills as $key => $meta): 
                    $score = $display_scores[$key];
                    $color = $meta['color'];
                ?>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-bold"><i class="fas <?= $meta['icon'] ?> me-2" style="color: <?= $color ?>"></i><?= $meta['label'] ?></span>
                            <span class="fw-bold" style="color: <?= $color ?>"><?= $score ?>%</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 10px; background-color: #f0f2f5;">
                            <div class="progress-bar" role="progressbar" style="width: <?= $score ?>%; background-color: <?= $color ?>; border-radius: 10px;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="mt-auto p-3" style="background-color: #f8fafc; border-radius: 16px; border: 1px solid var(--border-color);">
                    <h6 class="fw-bold mb-2" style="color: var(--blob-purple);">Quick Insight</h6>
                    <p class="small text-muted mb-0">
                        Strongest area: <strong><?= $skills[$top_key]['label'] ?></strong>.
                        Suggested focus: <strong><?= $skills[$weak_key]['label'] ?></strong>.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card p-4 border-0 shadow-sm" style="border-radius: 22px;">
                <h5 class="fw-bold mb-4">Training Strategy</h5>
                <div class="row g-4">
                    <div class="col-md-6 border-end">
                        <h6><span class="badge bg-success-subtle text-success me-2">STRENGTH</span> <?= $skills[$top_key]['label'] ?></h6>
                        <p class="text-muted small">This area is currently developing well. Keep one or two harder games in the weekly routine to maintain momentum.</p>
                    </div>
                    <div class="col-md-6">
                        <h6><span class="badge bg-danger-subtle text-danger me-2">FOCUS AREA</span> <?= $skills[$weak_key]['label'] ?></h6>
                        <p class="text-muted small">Use shorter sessions and easier levels here. A few confident wins are better than one long frustrating session.</p>
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
            labels: <?= json_encode($radar_labels) ?>,
            datasets: [{
                label: 'Current Proficiency',
                data: <?= json_encode($radar_values) ?>,
                backgroundColor: 'rgba(108, 63, 245, 0.13)',
                borderColor: '#6C3FF5',
                borderWidth: 3,
                pointBackgroundColor: '#FF9B6B',
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
                    angleLines: { color: '#e2e8f0' },
                    grid: { color: '#e2e8f0' },
                    suggestedMin: 0,
                    suggestedMax: 100,
                    ticks: { display: false, stepSize: 20 },
                    pointLabels: {
                        font: { size: 14, weight: '700' },
                        color: '#0f172a'
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
