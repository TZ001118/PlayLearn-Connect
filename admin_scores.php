<?php 
session_start();
require 'db_conn.php';

// 安全守卫：非管理员禁止入内
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$current_page = 'scores'; // 激活侧边栏 Player Scores 高亮
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PlayLearn Admin | Player Scores</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="css/admin_global.css?v=<?php echo time(); ?>">
</head>
<body>

    <?php include('includes/sidebar.php'); ?>

    <main class="main-content">
        <div class="header-row">
            <h2>Player Game Records</h2>
            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'cleared'): ?>
                <div style="background: rgba(76, 175, 80, 0.2); color: #4caf50; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
                    <i class="fas fa-check-circle"></i> All game records have been cleared successfully.
                </div>
            <?php endif; ?>
            <button class="studio-btn" style="background:#ff4d4d;" onclick="confirmClear()">
                <i class="fas fa-trash"></i> Clear Old Records
            </button>
        </div>
        <div id="scoreModal" class="modal-overlay">
            <div class="modal-content" style="width: 450px;">
                <div class="modal-header">
                    <h3><i class="fas fa-search-plus"></i> Record Inspection</h3>
                    <span class="close-btn" onclick="closeScoreModal()">&times;</span>
                </div>
                <div class="modal-body" id="scoreDetails" style="line-height: 2;">
                    </div>
                <div class="modal-footer">
                    <button class="studio-btn" style="background:#444;" onclick="closeScoreModal()">Close</button>
                </div>
            </div>
        </div>
        <div class="content-card">
            <span class="card-title">All Game Scores</span>
            <table id="scoresTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Record ID</th>
                        <th>Player Name</th>
                        <th>Game Played</th>
                        <th>Level</th>
                        <th>Score / Moves</th>
                        <th>Played At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // 确保你的 SQL 语句也查询了 level_reached
                    $sql = "SELECT s.*, u.username FROM game_scores s JOIN users u ON s.user_id = u.id ORDER BY s.id DESC";
                    $res = $conn->query($sql);
                    while($row = $res->fetch_assoc()):
                    ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['game_name']); ?></td>

                        <td>
                            <span style="background: #333; padding: 2px 8px; border-radius: 4px; color: #aaa; font-size: 12px;">
                                Lvl <?php echo $row['level_reached'] ?? 1; ?>
                            </span>
                        </td>

                        <td><strong><?php echo $row['score']; ?></strong></td>
                        <td><?php echo date('M d, Y - H:i', strtotime($row['played_at'])); ?></td>
                        <td><button class="studio-btn" 
                                style="padding: 2px 8px; font-size: 11px; background: #444; border: none; color: #eee; cursor: pointer;"
                                onclick="inspectRecord(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                            Inspect
                        </button>
                    </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#scoresTable').DataTable({
                pageLength: 15,
                order: [[4, 'desc']], 
                responsive: true
            });
        });

        // 🌟 核心：弹出详情逻辑
        // 🌟 修正版：直接从传入的 rowData 对象取值
        function inspectRecord(rowData) {
            console.log("Inspecting Data:", rowData); // 调试用

            // 1. 提取数据（直接从 PHP 传过来的对象里拿，不会受表格列数改变的影响）
            const id = rowData.id;
            const playerName = rowData.username;
            const gameName = rowData.game_name;
            const level = rowData.level_reached || 1;
            const score = rowData.score;
            
            // 格式化一下时间，让它更好看
            const time = new Date(rowData.played_at).toLocaleString();

            // 2. 构建美化的 HTML 内容
            const details = `
                <div style="text-align: center; margin-bottom: 20px;">
                    <i class="fas fa-trophy" style="font-size: 40px; color: #f1c40f;"></i>
                </div>
                <p><strong>Record ID:</strong> <span style="color:#888;">#${id}</span></p>
                <p><strong>Player:</strong> <span style="color:#4A90E2; font-weight:bold;">${playerName}</span></p>
                <p><strong>Game:</strong> ${gameName}</p>
                <p><strong>Level Reached:</strong> <span style="color:#aaa;">Lvl ${level}</span></p>
                <p><strong>Final Result:</strong> <span style="font-size: 20px; color: #4caf50; font-weight:bold;">${score}</span></p>
                <p><strong>Timestamp:</strong> <span style="font-size: 13px;">${time}</span></p>
                <hr style="border:0; border-top:1px solid #333; margin: 15px 0;">
                <p style="font-size: 11px; color: #666; font-style: italic;">Verified by PlayLearn Security System</p>
            `;

            // 3. 填入并显示
            document.getElementById('scoreDetails').innerHTML = details;
            document.getElementById('scoreModal').style.display = 'flex';
        }
        function closeScoreModal() {
            document.getElementById('scoreModal').style.display = 'none';
        }

        function confirmClear() {
            // 弹出确认框，防止手滑点错
            if (confirm("🚨 WARNING: This will permanently delete ALL player records! Are you sure?")) {
                window.location.href = "process_clear_scores.php";
            }
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