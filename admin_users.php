<?php 
session_start();
require 'db_conn.php';

// 安全守卫：非管理员禁止入内
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$current_page = 'users'; // 激活侧边栏 User Analytics 高亮
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PlayLearn Admin | User Analytics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="css/admin_global.css?v=<?php echo time(); ?>">
    <style>
        /* 状态标签样式 */
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-active { background: rgba(76, 175, 80, 0.2); color: #4caf50; }
        .badge-banned { background: rgba(255, 77, 77, 0.2); color: #ff4d4d; }
    </style>
        </head>
        <body>

            <?php include('includes/sidebar.php'); ?>

            <main class="main-content">
                <div class="header-row">
                    <h2>User Analytics & Management</h2>
                </div>

                <div class="content-card">
                    <span class="card-title">All Registered Users</span>
                    <table id="analyticsTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Player Info</th> <th>Email</th>
                    <th>Role</th>
                    <th>Joined Date</th>
                    <th>Account Status</th> <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT id, username, email, role, created_at, last_seen, status FROM users ORDER BY id DESC";
                $result = $conn->query($query);
                
                while($user = $result->fetch_assoc()):
                    $lastSeenTime = new DateTime($user['last_seen']);
                    $currentTime = new DateTime();
                    $diff = $currentTime->getTimestamp() - $lastSeenTime->getTimestamp();
                    $isOnline = ($diff <= 300 && $user['status'] !== 'banned');
                ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($user['username']); ?></strong><br>
                        <?php if ($isOnline): ?>
                            <span style="color: #00ff00; font-size: 10px;"><i class="fas fa-circle"></i> Online</span>
                        <?php else: ?>
                            <span style="color: #888; font-size: 10px;"><i class="far fa-circle"></i> Last seen: <?php echo date('M d, H:i', strtotime($user['last_seen'])); ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo ucfirst($user['role']); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                    <td>
                        <span class="badge <?php echo ($user['status'] == 'active') ? 'badge-active' : 'badge-banned'; ?>">
                            <?php echo strtoupper($user['status']); ?>
                        </span>
                    </td>
                    <td>
                        <button class="studio-btn" style="background:#333; margin-right:5px;" onclick="viewUserDetails(<?php echo $user['id']; ?>)">View</button>

                        <?php if ($user['status'] == 'active'): ?>
                            <a href="process_ban.php?id=<?php echo $user['id']; ?>&action=ban" 
                            class="studio-btn" style="background:#ff4d4d; text-decoration:none;" 
                            onclick="return confirm('Confirm BAN this player?')">Ban</a>
                        <?php else: ?>
                            <a href="process_ban.php?id=<?php echo $user['id']; ?>&action=unban" 
                            class="studio-btn" style="background:#4caf50; text-decoration:none;">Unban</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </main>
    <div id="userModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-id-card"></i> Player Intelligence Report</h3>
                <span class="close-btn" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body" id="modalData">
                </div>
            <div class="modal-footer">
                <button class="studio-btn" style="background:#444;" onclick="closeModal()">Dismiss</button>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#analyticsTable').DataTable({
                pageLength: 10,
                order: [[0, 'desc']], // 默认按 ID 倒序排，看到最新注册的人
                responsive: true
            });
        });

        function viewUserDetails(id) {
            // 获取行数据
            const row = event.target.closest('tr');
            const username = row.cells[1].innerText;
            const email = row.cells[2].innerText;
            const role = row.cells[3].innerText;
            const joined = row.cells[4].innerText;
            const status = row.cells[5].innerText;

            // 将内容填入弹窗
            const content = `
                <p><strong>Username:</strong> ${username}</p>
                <p><strong>Email:</strong> ${email}</p>
                <p><strong>Access Level:</strong> ${role}</p>
                <p><strong>Enlisted Date:</strong> ${joined}</p>
                <p><strong>System Status:</strong> ${status}</p>
            `;
            
            document.getElementById('modalData').innerHTML = content;
            
            // 显示弹窗
            document.getElementById('userModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('userModal').style.display = 'none';
        }
        
        // =========================================================================
        // ✨ DataTables 全局拦截器（修复版）：每次表格重绘时，确保页码跳转框存活
        // =========================================================================

        $(document).on('draw.dt', function(e, settings) {
            var api = new $.fn.dataTable.Api(settings);
            var $paginate = $(settings.nTableWrapper).find('.dataTables_paginate');
            var info = api.page.info(); // 获取当前表格的页码信息

            // 检查输入框是否还在，如果被 DataTables 刷新掉了，就立刻重新加进去
            if ($paginate.find('.custom-page-jump').length === 0) {

                var jumpHtml = '<span class="custom-page-jump" style="margin-right: 15px; font-weight: 600; color: var(--text-main);">' +
                               'Page <input type="number" min="1" max="' + info.pages + '" class="page-jump-input" ' +
                               'style="width: 55px; padding: 3px 6px; margin: 0 5px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--card-dark); color: var(--text-main); text-align: center; outline: none; transition: 0.2s;">' +
                               '</span>';

                $paginate.prepend(jumpHtml);

                // 重新绑定键盘回车(Enter)和改变(Change)事件
                $paginate.find('.page-jump-input').on('keyup change', function(e) {
                    if (e.type === 'keyup' && e.which !== 13) return; // 只监听 Enter 键

                    var page = parseInt($(this).val(), 10);
                    var currentInfo = api.page.info(); // 拿最新的总页数

                    if (page > 0 && page <= currentInfo.pages) {
                        api.page(page - 1).draw('page'); // 执行翻页
                    } else if ($(this).val() !== '') {
                        // 超出范围强行拉回当前页码
                        $(this).val(currentInfo.page + 1);
                    }
                });
            }

            // 无论如何，每次重绘完，确保框里的数字是最新的当前页码，且更新 max 限制
            $paginate.find('.page-jump-input').val(info.page + 1).attr('max', info.pages);
        });
    </script>
</body>
</html>