<?php 
// 第一步：开启 Session，确保能读取用户的主题偏好设置
session_start();
require 'db_conn.php'; 

// 安全守卫：非管理员禁止入内（修补安全漏洞）
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 获取当前管理员在数据库中保存的主题偏好 (默认 dark)
$uid = $_SESSION['user_id'];
$theme_res = $conn->query("SELECT theme_preference FROM users WHERE id = $uid");
$current_theme = 'dark';
if ($theme_res && $theme_res->num_rows > 0) {
    $current_theme = $theme_res->fetch_assoc()['theme_preference'] ?? 'dark';
}

$current_page = 'dashboard'; 

// 1. 获取真实总玩家数 (只算 role 是 player 的)
$user_count_res = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'player'");
$total_players = $user_count_res->fetch_assoc()['total'];

// 2. 获取今日活跃玩家
$today_active_res = $conn->query("SELECT COUNT(*) as total FROM users WHERE DATE(last_login) = CURDATE()");
$active_today = $today_active_res ? $today_active_res->fetch_assoc()['total'] : 0;

// 3. 游戏总局数
$games_played_res = $conn->query("SELECT COUNT(*) as total FROM game_scores");
$total_games = ($games_played_res) ? $games_played_res->fetch_assoc()['total'] : 0;

// 4. 获取过去 7 天的图表数据
$dynamic_labels = [];
$counts = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dynamic_labels[] = date('D', strtotime($date)); 
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE DATE(last_login) = ?");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $res = $stmt->get_result();
    $counts[] = $res->fetch_assoc()['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlayLearn Admin | Dashboard</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    
    <link rel="stylesheet" href="css/admin_global.css?v=<?php echo time(); ?>">
</head>
<body class="<?php echo ($current_theme === 'light') ? 'light-mode' : ''; ?>">

    <?php include('includes/sidebar.php'); ?>

    <main class="main-content">
        <div class="header-row">
            <h2>Command Center Dashboard</h2>
            <div id="current-time" style="color: var(--text-gray);"></div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Players</h3>
                <p><?php echo number_format($total_players); ?></p>
            </div>
            <div class="stat-card">
                <h3>Active Today</h3>
                <p><?php echo number_format($active_today); ?></p>
            </div>
            <div class="stat-card">
                <h3>Games Played</h3>
                <p><?php echo number_format($total_games); ?></p>
            </div>
            <div class="stat-card" style="border-color: var(--border-color);">
                <h3>System Status</h3>
                <p style="color: #4caf50; font-size: 18px;">● ONLINE</p>
            </div>
        </div>

        <div class="content-card">
            <span class="card-title">Player Activity (Last 7 Days)</span>
            <canvas id="activityChart" height="100"></canvas>
        </div>

        <div class="content-card">
            <span class="card-title">Real-time Player Records</span>
            <table id="userTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th><th>Username</th><th>Role</th><th>Joined Date</th><th>Status</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $user_list_res = $conn->query("SELECT id, username, role, created_at, status FROM users ORDER BY created_at DESC");
                if($user_list_res):
                    while($user = $user_list_res->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo ucfirst($user['role']); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                    <td>
                        <span style="color: <?php echo ($user['status'] == 'active') ? '#4caf50' : '#ff4d4d'; ?>">
                            ● <?php echo ucfirst($user['status']); ?>
                        </span>
                    </td>
                    <td>
                        <button class="studio-btn" onclick="goToUserManagement(<?php echo $user['id']; ?>)">
                            View
                        </button>
                    </td>
                </tr>
                <?php 
                    endwhile; 
                endif;
                ?>
            </tbody>
            </table>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // 初始化 DataTables 表格
            $('#userTable').DataTable({
                pageLength: 5,
                responsive: true
            });

            // 初始化活跃度图表 (全面适配 CSS 变量调色)
            const ctx = document.getElementById('activityChart').getContext('2d');
            const activityChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($dynamic_labels); ?>,
                    datasets: [{
                        label: 'Active Players',
                        data: <?php echo json_encode($counts); ?>, 
                        borderColor: '#3b5998',
                        backgroundColor: 'rgba(59, 89, 152, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            grid: { color: 'var(--border-color)' }, 
                            ticks: { color: 'var(--text-gray)', stepSize: 1 } 
                        },
                        x: { grid: { display: false }, ticks: { color: 'var(--text-gray)' } }
                    }
                }
            });

            // 实时时钟
            function updateTime() {
                const now = new Date();
                const timeElement = document.getElementById('current-time');
                if(timeElement) { timeElement.innerText = now.toLocaleString(); }
            }
            setInterval(updateTime, 1000);
            updateTime();
        });

        function goToUserManagement(userId) {
            window.location.href = "admin_users.php?search=" + userId;
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