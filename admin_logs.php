<?php 
// admin_logs.php 完整代码
session_start();
require 'db_conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
$current_page = 'logs';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PlayLearn Admin | System Audit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="css/admin_global.css?v=<?php echo time(); ?>">
    <style>
        .log-status { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .status-Success { background: rgba(76, 175, 80, 0.1); color: #4caf50; }
        .status-Failed { background: rgba(255, 77, 77, 0.1); color: #ff4d4d; }
        .status-Unauthorized { background: rgba(255, 152, 0, 0.1); color: #ff9800; }
    </style>
</head>
<body>
    <?php include('includes/sidebar.php'); ?>
    <main class="main-content">
        <div class="header-row">
            <h2><i class="fas fa-shield-alt"></i> Security Audit Logs</h2>
        </div>
        <div class="content-card">
            <table id="logsTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Time</th><th>User</th><th>Action</th><th>Details</th><th>IP</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = $conn->query("SELECT * FROM system_logs ORDER BY log_id DESC LIMIT 1000");
                    while($l = $res->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= date('M d, H:i:s', strtotime($l['created_at'])) ?></td>
                        <td><strong><?= htmlspecialchars($l['username']) ?></strong> <small>(<?= $l['role'] ?>)</small></td>
                        <td><?= htmlspecialchars($l['action']) ?></td>
                        <td title="<?= htmlspecialchars($l['details']) ?>"><?= mb_strimwidth($l['details'], 0, 40, "...") ?></td>
                        <td style="font-family: monospace; font-size: 12px;"><?= $l['ip_address'] ?></td>
                        <td><span class="log-status status-<?= $l['status'] ?>"><?= $l['status'] ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>$(document).ready(function() { $('#logsTable').DataTable({ order: [[0, 'desc']] }); });</script>
    <script>
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