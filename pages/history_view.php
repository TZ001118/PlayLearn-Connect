<?php
require_once __DIR__ . '/../includes/learning_helpers.php';
pl_ensure_learning_schema($conn);

$start = $_GET['start_date'] ?? null;
$end = $_GET['end_date'] ?? null;
$target_user_id = intval($target_user_id);

$sql_history = "SELECT game_name, score, level_reached, correct_answers, total_questions, reaction_time_ms, duration_seconds, played_at
                FROM game_scores
                WHERE user_id = $target_user_id";

if ($start && $end) {
    $safe_start = $conn->real_escape_string($start);
    $safe_end = $conn->real_escape_string($end);
    $sql_history .= " AND DATE(played_at) BETWEEN '$safe_start' AND '$safe_end'";
}

$sql_history .= " ORDER BY played_at DESC LIMIT 60";
$res_history = $conn->query($sql_history);
?>

<div class="card p-4 border-0 shadow-sm" style="border-radius: 22px;">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h4 class="fw-bold m-0">Game History Log</h4>
        <form method="GET" class="d-flex align-items-center gap-2 flex-wrap">
            <input type="hidden" name="page" value="history">
            <?php if (!empty($active_child_id)): ?>
                <input type="hidden" name="child_id" value="<?= intval($active_child_id) ?>">
            <?php endif; ?>
            <input type="date" name="start_date" class="form-control form-control-sm" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>">
            <span class="text-muted">to</span>
            <input type="date" name="end_date" class="form-control form-control-sm" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>">
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <?php if(isset($_GET['start_date'])): ?>
                <a href="?page=history<?= !empty($active_child_id) ? '&child_id=' . intval($active_child_id) : '' ?>" class="btn btn-outline-secondary btn-sm">Reset</a>
            <?php endif; ?>
        </form>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="text-secondary" style="font-size: 0.85rem;">
                    <th>Date</th>
                    <th>Game</th>
                    <th>Score</th>
                    <th>Level</th>
                    <th>Accuracy</th>
                    <th>Reaction</th>
                    <th>Duration</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($res_history && $res_history->num_rows > 0): ?>
                    <?php while($row = $res_history->fetch_assoc()): ?>
                        <?php
                            $accuracy = null;
                            if (!empty($row['total_questions'])) {
                                $accuracy = round((intval($row['correct_answers']) / max(1, intval($row['total_questions']))) * 100);
                            }
                        ?>
                        <tr>
                            <td class="text-secondary small"><?= date('Y-m-d H:i', strtotime($row['played_at'])) ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($row['game_name']) ?></td>
                            <td class="fw-bold text-primary"><?= number_format($row['score']) ?></td>
                            <td><span class="badge bg-light text-dark">Lv.<?= intval($row['level_reached']) ?></span></td>
                            <td><?= $accuracy !== null ? $accuracy . '%' : '<span class="text-muted">N/A</span>' ?></td>
                            <td><?= !empty($row['reaction_time_ms']) ? number_format($row['reaction_time_ms']) . ' ms' : '<span class="text-muted">N/A</span>' ?></td>
                            <td><?= !empty($row['duration_seconds']) ? number_format($row['duration_seconds']) . ' sec' : '<span class="text-muted">N/A</span>' ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted fw-bold">No game records found yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-end align-items-center mt-4 gap-2 flex-wrap">
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-file-pdf me-1"></i> Export to PDF / Print
    </button>
    <button type="button" class="btn btn-outline-danger btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#clearHistoryModal">
        <i class="fas fa-trash-alt me-1"></i> Clear All History
    </button>
</div>

<style>
@media print {
    .sidebar, .btn, .modal {
        display: none !important;
    }
    .main-content {
        margin-left: 0 !important;
        width: 100% !important;
    }
}
</style>

<div class="modal fade" id="clearHistoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
      <div class="modal-header bg-danger text-white border-0 p-4">
        <h5 class="modal-title fw-bold">
            <i class="fas fa-exclamation-triangle me-2"></i> Clear History Log
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 text-center">
        <div class="mb-4 mt-2">
            <div class="bg-danger-subtle text-danger rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                <i class="fas fa-trash-alt fa-3x"></i>
            </div>
        </div>
        <h4 class="fw-bold text-dark mb-3">Clear all records?</h4>
        <p class="text-muted mb-0">This permanently deletes scores, accuracy, reaction time, and history for <b><?= htmlspecialchars($active_child_name); ?></b>.</p>
      </div>
      <div class="modal-footer border-0 bg-light p-3 justify-content-center gap-3">
        <button type="button" class="btn btn-light px-4 fw-bold border shadow-sm" data-bs-dismiss="modal" style="border-radius: 12px;">Cancel</button>
        <a href="process_clear_history.php?child_id=<?= $target_user_id ?>&redirect=history" class="btn btn-danger px-4 fw-bold shadow-sm" style="border-radius: 12px;">Yes, Clear History</a>
      </div>
    </div>
  </div>
</div>
