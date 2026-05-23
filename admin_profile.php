<?php 
session_start();
require 'db_conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}

$uid = $_SESSION['user_id'];
$res = $conn->query("SELECT * FROM users WHERE id = $uid");
$user = $res->fetch_assoc();

$current_theme = $user['theme_preference'] ?? 'dark';
$current_page = 'profile';
$m_check = $conn->query("SELECT config_value FROM site_config WHERE config_key = 'maintenance_mode'");
$is_m_active = '0';
if ($m_check && $m_check->num_rows > 0) {
    $is_m_active = $m_check->fetch_assoc()['config_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PlayLearn Admin | Settings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin_global.css?v=<?php echo time(); ?>">
</head>
<body class="<?php echo ($current_theme === 'light') ? 'light-mode' : ''; ?>">

    <?php include('includes/sidebar.php'); ?>

    <main class="main-content" style="width: 100%; padding: 40px;">
        <div class="header-row">
            <h2><i class="fas fa-user-cog"></i> Admin Settings</h2>
        </div>

        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
            <div class="content-card">
                <span class="card-title"><i class="fas fa-palette"></i> Display Preference</span>
                <p>Customize how the dashboard looks for you.</p>
                <div style="margin-top: 20px; display: flex; align-items: center; justify-content: space-between;">
                    <span>Interface Mode: <strong id="themeLabel"><?php echo ucfirst($current_theme); ?></strong></span>
                    <button onclick="toggleTheme()" class="studio-btn" id="themeBtn" style="background: var(--accent-blue);">
                        Switch to <?php echo ($current_theme === 'dark') ? 'Light' : 'Dark'; ?> Mode
                    </button>
                </div>
            </div>

            <div class="content-card">
                <span class="card-title"><i class="fas fa-shield-alt"></i> Account Info</span>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Role:</strong> <span style="color: #4caf50;">Super Administrator</span></p>
            </div>
        </div>

       <div class="content-card">
            <span class="card-title"><i class="fas fa-tools"></i> System Maintenance</span>
            <p>Activate this to block player access during updates. Admins can still login.</p>
            <div style="margin-top: 15px;">
    <span style="font-weight:bold; margin-right:20px;">
        Status: <span id="statusText" style="color: <?= ($is_m_active === '1') ? '#ff4d4d' : '#4caf50' ?>;">
            <?= ($is_m_active === '1') ? 'ACTIVE' : 'OFF' ?>
        </span>
    </span>
    <button id="maintenanceBtn"
        onclick="toggleMaintenance('<?= $is_m_active ?>')" 
        class="studio-btn" 
        style="background-color: <?= ($is_m_active === '1') ? '#4A90E2' : '#e74c3c' ?> !important; border: 1px solid #000000 !important; color: #000000 !important;">
        <?= ($is_m_active === '1') ? 'Deactivate Maintenance' : 'Activate Maintenance' ?>
    </button>
</div>
        </div>

    </main>

    <script>
        
    function toggleMaintenance(currentStatus) {
        let etaMinutes = "15"; // 默认 15 分钟

        if (currentStatus === '0') {
            // 如果目前是关闭的，准备开启，弹窗询问时间
            let input = prompt("Enter estimated maintenance duration (in minutes):", "15");
            if (input === null) return; // 管理员按了取消
            if (input.trim() === "" || isNaN(input)) {
                alert("Please enter a valid number.");
                return;
            }
            etaMinutes = input.trim();
        } else {
            // 如果目前是开启的，准备关闭
            if(!confirm("Are you sure you want to deactivate maintenance mode and let players back in?")) return;
        }
        
        // 把时间数据一起发给后端
        let formData = new FormData();
        formData.append('eta', etaMinutes);

        fetch('ajax_toggle_maintenance.php', { 
            method: 'POST',
            credentials: 'include',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if(data.status === 'success') {
                location.reload(); // 刷新以显示最新状态
                } 
		});
    }
        
    function toggleTheme() {
        const body = document.body;
        const btn = document.getElementById('themeBtn');
        const label = document.getElementById('themeLabel');
        
        const isCurrentlyLight = body.classList.contains('light-mode');
        const newTheme = isCurrentlyLight ? 'dark' : 'light';
        
        // 1. 立即更新 UI (无刷新切换)
        if (newTheme === 'light') {
            body.classList.add('light-mode');
            btn.innerText = "Switch to Dark Mode";
            label.innerText = "Light";
        } else {
            body.classList.remove('light-mode');
            btn.innerText = "Switch to Light Mode";
            label.innerText = "Dark";
        }

        // 2. 发送 AJAX 请求到后端保存设置
        let formData = new FormData();
        formData.append('theme', newTheme);

        fetch('ajax_update_theme.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                console.log("Theme preference saved!");
            }
        });
    }
        
        setInterval(function() {
    fetch('check_status.php') // 确保这个文件里 echo 的是单纯的 '0' 或 '1'
    .then(r => r.text())
    .then(status => {
        let btn = document.getElementById('maintenanceBtn');
        let statusText = document.getElementById('statusText');
        
        // 获取当前按钮的状态 (根据按钮现在的文字判断)
        let isCurrentlyActive = (btn.innerText.includes('Deactivate'));

        // 如果数据库状态与当前页面不一致，则更新 UI
        if (status !== (isCurrentlyActive ? '1' : '0')) {
            if (status === '0') {
                // 更新为 OFF 状态
                statusText.innerText = 'OFF';
                statusText.style.color = '#4caf50';
                btn.innerText = 'Activate Maintenance';
                btn.style.backgroundColor = '#e74c3c !important';
                btn.setAttribute("onclick", "toggleMaintenance('0')");
            } else {
                // 更新为 ACTIVE 状态
                statusText.innerText = 'ACTIVE';
                statusText.style.color = '#ff4d4d';
                btn.innerText = 'Deactivate Maintenance';
                btn.style.backgroundColor = '#4A90E2 !important';
                btn.setAttribute("onclick", "toggleMaintenance('1')");
            }
        }
    });
}, 10000); // 10秒检查一次
    </script>
</body>
</html>