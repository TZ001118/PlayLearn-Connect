<?php
require_once __DIR__ . '/../includes/learning_helpers.php';
pl_seed_game_metadata($conn);

$parent_id = intval($current_parent_id ?? $_SESSION['user_id']);
$child_id = intval($target_user_id);
$skills = pl_skill_definitions();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_target') {
    $skill_key = $_POST['skill_key'] ?? '';
    $target_value = max(10, min(100, intval($_POST['target_value'] ?? 70)));
    $note = trim($_POST['note'] ?? '');
    $due_date = trim($_POST['due_date'] ?? '');
    $due_date = $due_date !== '' ? $due_date : null;

    if (isset($skills[$skill_key])) {
        $stmt = $conn->prepare("INSERT INTO parent_learning_targets (parent_id, child_id, skill_key, target_value, note, due_date)
                                VALUES (?, ?, ?, ?, ?, ?)
                                ON DUPLICATE KEY UPDATE target_value = VALUES(target_value), note = VALUES(note), due_date = VALUES(due_date), status = 'active'");
        $stmt->bind_param("iisiss", $parent_id, $child_id, $skill_key, $target_value, $note, $due_date);
        $stmt->execute();
        $stmt->close();
    }
}

$skill_scores = [];
$skill_weight_totals = [];
foreach ($skills as $key => $_meta) {
    $skill_scores[$key] = 0;
    $skill_weight_totals[$key] = 0;
}

$sql_sessions = "SELECT gs.*, l.id AS game_id, l.level_name, l.category
                 FROM game_scores gs
                 LEFT JOIN levels l ON gs.game_name = l.level_name
                 WHERE gs.user_id = $child_id";
$session_res = $conn->query($sql_sessions);

if ($session_res) {
    while ($session = $session_res->fetch_assoc()) {
        $game_id = intval($session['game_id'] ?? 0);
        if ($game_id <= 0) continue;

        $weights = pl_get_game_weights($conn, $game_id, $session['level_name'], $session['category']);
        $score_performance = min(100, round((max(0, intval($session['score'])) / 2000) * 100));
        if (!empty($session['total_questions'])) {
            $accuracy = round((intval($session['correct_answers']) / max(1, intval($session['total_questions']))) * 100);
            $performance = round(($accuracy * 0.7) + ($score_performance * 0.3));
        } else {
            $performance = $score_performance;
        }

        foreach ($weights as $skill_key => $weight) {
            if ($weight <= 0 || !isset($skill_scores[$skill_key])) continue;
            $skill_scores[$skill_key] += $performance * $weight;
            $skill_weight_totals[$skill_key] += $weight;
        }
    }
}

$current_skill_values = [];
foreach ($skills as $key => $_meta) {
    $current_skill_values[$key] = $skill_weight_totals[$key] > 0 ? round($skill_scores[$key] / $skill_weight_totals[$key]) : 0;
}

$targets = [];
$target_res = $conn->query("SELECT * FROM parent_learning_targets WHERE parent_id = $parent_id AND child_id = $child_id AND status = 'active' ORDER BY updated_at DESC");
if ($target_res) {
    while ($row = $target_res->fetch_assoc()) {
        $targets[] = $row;
    }
}
?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card p-4 border-0 shadow-sm h-100" style="border-radius: 22px;">
            <h5 class="fw-bold mb-3" style="color: var(--primary-dark);">Set Learning Target</h5>
            <form method="POST" class="d-flex flex-column gap-3">
                <input type="hidden" name="action" value="save_target">
                <div>
                    <label class="form-label small fw-bold text-muted">Skill</label>
                    <select name="skill_key" class="form-select" required>
                        <?php foreach ($skills as $key => $meta): ?>
                            <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($meta['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label small fw-bold text-muted">Target Score</label>
                    <input type="number" min="10" max="100" name="target_value" value="70" class="form-control" required>
                </div>
                <div>
                    <label class="form-label small fw-bold text-muted">Due Date</label>
                    <input type="date" name="due_date" class="form-control">
                </div>
                <div>
                    <label class="form-label small fw-bold text-muted">Note</label>
                    <input type="text" maxlength="255" name="note" class="form-control" placeholder="Short parent note">
                </div>
                <button type="submit" class="btn btn-primary fw-bold">Save Target</button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card p-4 border-0 shadow-sm h-100" style="border-radius: 22px;">
            <h5 class="fw-bold mb-4" style="color: var(--primary-dark);">Active Targets</h5>
            <?php if (empty($targets)): ?>
                <div class="text-center text-muted py-5 fw-bold">No learning targets yet.</div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($targets as $target): ?>
                        <?php
                            $skill_key = $target['skill_key'];
                            $meta = $skills[$skill_key] ?? ['label' => ucfirst($skill_key), 'icon' => 'fa-bullseye', 'color' => '#6C3FF5'];
                            $current = $current_skill_values[$skill_key] ?? 0;
                            $goal = intval($target['target_value']);
                            $percent = $goal > 0 ? min(100, round(($current / $goal) * 100)) : 0;
                            $done = $current >= $goal;
                        ?>
                        <div class="col-md-6">
                            <div class="p-4 h-100" style="border: 1px solid var(--border-color); border-radius: 18px; background: #ffffff;">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-bold mb-1" style="color: var(--primary-dark);">
                                            <i class="fas <?= $meta['icon'] ?> me-2" style="color: <?= $meta['color'] ?>"></i><?= htmlspecialchars($meta['label']) ?>
                                        </h6>
                                        <div class="small text-muted fw-bold">Target <?= $goal ?>%</div>
                                    </div>
                                    <span class="badge <?= $done ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary' ?>"><?= $done ? 'Reached' : 'Active' ?></span>
                                </div>
                                <div class="progress mb-2" style="height: 10px; border-radius: 999px;">
                                    <div class="progress-bar" style="width: <?= $percent ?>%; background: <?= $meta['color'] ?>;"></div>
                                </div>
                                <div class="d-flex justify-content-between small fw-bold text-muted">
                                    <span>Current <?= $current ?>%</span>
                                    <span><?= $percent ?>%</span>
                                </div>
                                <?php if (!empty($target['due_date'])): ?>
                                    <div class="small text-muted mt-3"><i class="far fa-calendar me-1"></i> Due <?= htmlspecialchars($target['due_date']) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($target['note'])): ?>
                                    <div class="small mt-2" style="color: #475569;"><?= htmlspecialchars($target['note']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
