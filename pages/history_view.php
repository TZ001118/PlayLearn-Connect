<?php
// 1. 从数据库读取当前孩子的真实游戏记录
// 我们按照时间倒序排列，最新的排在最上面
$sql_history = "SELECT game_name, score, level_reached, played_at 
                FROM game_scores 
                WHERE user_id = $target_user_id 
                ORDER BY played_at DESC 
                LIMIT 15"; // 只显示最近 15 条

$res_history = $conn->query($sql_history);
// 1. 获取日期参数
$start = $_GET['start_date'] ?? null;
$end = $_GET['end_date'] ?? null;

// 2. 构建基础 SQL
$sql_history = "SELECT game_name, score, level_reached, played_at 
                FROM game_scores 
                WHERE user_id = $target_user_id";

// 3. 如果有日期，加入过滤条件
if ($start && $end) {
    // 使用 date() 确保格式安全，BETWEEN 包含开始和结束
    $sql_history .= " AND DATE(played_at) BETWEEN '$start' AND '$end'";
}

$sql_history .= " ORDER BY played_at DESC";
$res_history = $conn->query($sql_history);
?>

<div class="card p-4 border-0 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">Game History Log</h4>
        <select class="form-select w-auto">
            <option>All Games</option>
        </select>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr class="text-secondary" style="font-size: 0.9rem;">
                    <th>DATE</th>
                    <th>GAME NAME</th>
                    <th>STATUS</th>
                    <th>SCORE</th>
                    <th>LEVEL</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // 2. 检查是否有数据
                if ($res_history && $res_history->num_rows > 0): 
                    // 3. 循环输出每一行记录
                    while($row = $res_history->fetch_assoc()): 
                ?>
                    <tr class="align-middle">
                        <td class="text-secondary small">
                            <?= date('Y-m-d H:i', strtotime($row['played_at'])) ?>
                        </td>
                        <td class="fw-bold"><?= htmlspecialchars($row['game_name']) ?></td>
                        <td>
                            <span class="badge bg-success-subtle text-success rounded-pill px-3">
                                <i class="fas fa-check-circle me-1"></i> Completed
                            </span>
                        </td>
                        <td class="fw-bold text-primary"><?= number_format($row['score']) ?></td>
                        <td>
                            <span class="badge bg-light text-dark">Lv.<?= $row['level_reached'] ?></span>
                        </td>
                    </tr>
                <?php 
                    endwhile; 
                else: 
                ?>
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="60" class="opacity-25 mb-3"><br>
                            <span class="text-muted">No game records found yet. Go play some games!</span>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <form method="GET" class="d-flex align-items-center gap-2">
        <input type="hidden" name="page" value="history"> <input type="date" name="start_date" class="form-control form-control-sm" value="<?= $_GET['start_date'] ?? '' ?>">
        <span class="text-muted">to</span>
        <input type="date" name="end_date" class="form-control form-control-sm" value="<?= $_GET['end_date'] ?? '' ?>">
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        <?php if(isset($_GET['start_date'])): ?>
            <a href="?page=history" class="btn btn-outline-secondary btn-sm">Reset</a>
        <?php endif; ?>
    </form>

    <button type="button" class="btn btn-outline-danger btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#clearHistoryModal">
    <i class="fas fa-trash-alt me-1"></i> Clear All History
</button>

    <style>
    @media print {
    .sidebar, .top-nav, .btn, .no-print {
        display: none !important;
    }
    .main-content {
        margin-left: 0 !important;
        width: 100% !important;
    }
}</style>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
    <i class="fas fa-file-pdf me-1"></i> Export to PDF / Print
</button>
</div>

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
        <p class="text-muted mb-0">This will permanently delete all game history for <b><?php echo htmlspecialchars($active_child_name); ?></b>. Are you sure you want to proceed?</p>
      </div>
      
      <div class="modal-footer border-0 bg-light p-3 justify-content-center gap-3">
        <button type="button" class="btn btn-light px-4 fw-bold border shadow-sm" data-bs-dismiss="modal" style="border-radius: 12px;">
            Cancel
        </button>
        <a href="process_clear_history.php?child_id=<?php echo $target_user_id ?? 0; ?>&redirect=history" class="btn btn-danger px-4 fw-bold shadow-sm" style="border-radius: 12px;">
            Yes, Clear History
        </a>
      </div>

    </div>
  </div>
</div>
<script>
function clearHistory() {
    if (confirm("Are you sure you want to delete ALL game records? This cannot be undone.")) {
        // 跳转到一个专门处理删除的 PHP 文件，或者带参数刷新本页
        window.location.href = "process_clear_history.php"; 
    }
}
</script>