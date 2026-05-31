<?php
// 1. 获取排序参数和方向参数 (默认升序 asc)
$sort = $_GET['sort'] ?? 'none';
$dir = $_GET['dir'] ?? 'asc'; 
$order_sql = "";

// 🌟 动态判断排序方向
if ($sort === 'username') {
    // 安全防御：确保方向只能是 ASC 或 DESC
    $safe_dir = ($dir === 'desc') ? 'DESC' : 'ASC';
    $order_sql = " ORDER BY u.username $safe_dir";
}

// 2. 🌟 获取学生列表 (加入了 profile_image 和 avatar_frame)
$students = [];
$sql_students = "SELECT u.id, u.username, u.email, u.birthday, u.gender, u.ic_number, u.profile_image, u.avatar_frame 
                 FROM account_links al 
                 JOIN users u ON al.child_id = u.id 
                 WHERE al.parent_id = $current_parent_id" . $order_sql;
$res_students = $conn->query($sql_students);

if ($res_students && $res_students->num_rows > 0) {
    while ($row = $res_students->fetch_assoc()) {
        $students[] = $row;
    }
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0" style="color: var(--primary-dark);">Children Account Management</h4>
        <a href="?page=settings" class="btn btn-sm rounded-pill px-4 shadow-sm text-white" style="background-color: var(--blob-purple); font-weight: 800; border: none;">
            <i class="fas fa-user-plus me-1"></i> Link New Child
        </a>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-secondary small fw-bold text-uppercase" style="width: 80px;">Profile</th>
                        <th class="py-3 text-secondary small fw-bold text-uppercase">
                            <?php
                                // 逻辑：如果当前已经是 username 的升序，下一次点击就变成降序 (desc)；否则全都是升序 (asc)
                                $next_dir = ($sort === 'username' && $dir === 'asc') ? 'desc' : 'asc';
                                
                                // 动态切换图标：A-Z 向下，Z-A 向上
                                $icon_class = 'fa-sort-alpha-down'; 
                                if ($sort === 'username' && $dir === 'desc') {
                                    $icon_class = 'fa-sort-alpha-up';
                                }
                            ?>
                            <a href="?page=students&sort=username&dir=<?php echo $next_dir; ?>" class="text-decoration-none" style="color: #64748b;">
                                Username <i class="fas <?php echo $icon_class; ?> ms-1 <?php echo ($sort === 'username') ? 'text-primary' : 'opacity-25'; ?>" style="<?php echo ($sort === 'username') ? 'color: var(--blob-purple) !important;' : ''; ?>"></i>
                            </a>
                        </th>
                        <th class="py-3 text-secondary small fw-bold text-uppercase">Email Address</th>
                        <th class="py-3 text-secondary small fw-bold text-uppercase">Gender</th>
                        <th class="py-3 text-secondary small fw-bold text-uppercase">Birthday</th>
                        <th class="py-3 text-secondary small fw-bold text-uppercase">IC / ID</th>
                        <th class="py-3 text-secondary small fw-bold text-uppercase text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted">No children linked yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($students as $s): ?>
                        <tr>
                            <td class="ps-4">
                                <div style="position: relative; width: 45px; height: 45px;">
                                    <?php if (!empty($s['profile_image'])): ?>
                                        <img src="<?= htmlspecialchars($s['profile_image']) ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 14px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                    <?php else: ?>
                                        <div style="width: 100%; height: 100%; border-radius: 14px; background: linear-gradient(135deg, var(--blob-purple), var(--blob-orange)); color: white; display: flex; align-items: center; justify-content: center; font-weight: 900; box-shadow: 0 4px 10px rgba(108, 63, 245, 0.2);">
                                            <?= strtoupper(substr($s['username'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($s['avatar_frame'])): ?>
                                        <img src="<?= htmlspecialchars($s['avatar_frame']) ?>" style="position: absolute; top: -20%; left: -20%; width: 140%; height: 140%; pointer-events: none; z-index: 10;">
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="fw-bold" style="color: var(--primary-dark); font-size: 15px;"><?php echo htmlspecialchars($s['username']); ?></td>
                            <td style="color: #64748b; font-weight: 600; font-size: 14px;"><?php echo htmlspecialchars($s['email']); ?></td>
                            <td style="color: #64748b; font-weight: 600; font-size: 14px;">
                                <?php echo !empty($s['gender']) ? ucfirst($s['gender']) : 'Not set'; ?>
                            </td>
                            <td style="color: #64748b; font-weight: 600; font-size: 14px;">
                                <?php echo !empty($s['birthday']) ? date('M d, Y', strtotime($s['birthday'])) : 'Not set'; ?>
                            </td>
                            <td style="color: #64748b; font-weight: 600; font-size: 14px;">
                                <?php echo !empty($s['ic_number']) ? htmlspecialchars($s['ic_number']) : 'Not set'; ?>
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown" style="border: 1px solid var(--border-color); color: #64748b;">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                                        <li>
                                            <a class="dropdown-item py-2 fw-bold" href="?page=overview&child_id=<?php echo $s['id']; ?>" style="color: #475569;">
                                                <i class="fas fa-chart-bar me-2" style="color: var(--blob-purple);"></i> View Report
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider m-0"></li>
                                        <li>
                                            <button class="dropdown-item py-2 fw-bold text-danger" onclick="confirmUnlink(<?php echo $s['id']; ?>, '<?php echo htmlspecialchars($s['username'], ENT_QUOTES); ?>')">
                                                <i class="fas fa-unlink me-2"></i> Unlink Child
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="unlinkModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
      <div class="modal-body p-4 text-center">
        <div class="text-danger mb-3"><i class="fas fa-user-times fa-3x"></i></div>
        <h5 class="fw-bold" style="color: var(--primary-dark);">Unlink Child?</h5>
        <p class="text-muted small fw-bold">Are you sure you want to remove <strong id="unlinkName" style="color: var(--blob-orange);"></strong> from your dashboard?</p>
        <div class="d-flex gap-2 justify-content-center mt-4">
            <button class="btn btn-light px-4 border shadow-sm btn-sm fw-bold" data-bs-dismiss="modal" style="border-radius: 12px;">Cancel</button>
            <a href="#" id="unlinkConfirmBtn" class="btn btn-danger px-4 shadow-sm btn-sm fw-bold" style="border-radius: 12px;">Confirm Unlink</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function confirmUnlink(id, name) {
    document.getElementById('unlinkName').innerText = name;
    document.getElementById('unlinkConfirmBtn').href = 'process_unlink.php?child_id=' + id;
    const myModal = new bootstrap.Modal(document.getElementById('unlinkModal'));
    myModal.show();
}
</script>
