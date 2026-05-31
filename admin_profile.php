<?php
session_start();
require 'db_conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
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
                    Status:
                    <span id="statusText" style="color: <?php echo ($is_m_active === '1') ? '#ff4d4d' : '#4caf50'; ?>;">
                        <?php echo ($is_m_active === '1') ? 'ACTIVE' : 'OFF'; ?>
                    </span>
                </span>
                <button
                    id="maintenanceBtn"
                    onclick="toggleMaintenance('<?php echo $is_m_active; ?>')"
                    class="studio-btn"
                    style="background-color: <?php echo ($is_m_active === '1') ? '#4A90E2' : '#e74c3c'; ?> !important; border: 1px solid #000000 !important; color: #000000 !important;">
                    <?php echo ($is_m_active === '1') ? 'Deactivate Maintenance' : 'Activate Maintenance'; ?>
                </button>
            </div>
        </div>
    </main>

    <div id="maintenanceModal" class="modal-overlay">
        <div class="modal-content" style="width: min(440px, 92vw);">
            <div class="modal-header">
                <h3><i class="fas fa-tools"></i> Maintenance Mode</h3>
                <span class="close-btn" onclick="closeMaintenanceModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p id="maintenanceMessage" style="margin-top:0;"></p>
                <div id="maintenanceEtaArea" style="display:none;">
                    <label>Estimated duration in minutes</label>
                    <input type="number" min="1" step="1" id="maintenanceEtaInput" class="studio-input" value="15">
                    <p id="maintenanceError" style="display:none; color:#ff7675; font-weight:800;"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeMaintenanceModal()">Cancel</button>
                <button type="button" class="studio-btn" style="background:var(--accent-blue);" onclick="submitMaintenanceChange()">Confirm</button>
            </div>
        </div>
    </div>

    <script>
    var pendingMaintenanceStatus = '0';

    function toggleMaintenance(currentStatus) {
        pendingMaintenanceStatus = currentStatus;
        document.getElementById('maintenanceError').style.display = 'none';
        document.getElementById('maintenanceEtaInput').value = '15';

        if (currentStatus === '0') {
            document.getElementById('maintenanceMessage').innerText = 'Activate maintenance mode and block player access during updates.';
            document.getElementById('maintenanceEtaArea').style.display = 'block';
        } else {
            document.getElementById('maintenanceMessage').innerText = 'Deactivate maintenance mode and let players back in.';
            document.getElementById('maintenanceEtaArea').style.display = 'none';
        }

        document.getElementById('maintenanceModal').style.display = 'flex';
    }

    function closeMaintenanceModal() {
        document.getElementById('maintenanceModal').style.display = 'none';
    }

    function submitMaintenanceChange() {
        var etaMinutes = '15';

        if (pendingMaintenanceStatus === '0') {
            var input = document.getElementById('maintenanceEtaInput').value.trim();
            if (input === '' || isNaN(input) || Number(input) <= 0) {
                document.getElementById('maintenanceError').innerText = 'Please enter a valid number.';
                document.getElementById('maintenanceError').style.display = 'block';
                return;
            }
            etaMinutes = input;
        }

        var formData = new FormData();
        formData.append('eta', etaMinutes);

        fetch('ajax_toggle_maintenance.php', {
            method: 'POST',
            credentials: 'include',
            body: formData
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.status === 'success') {
                location.reload();
            }
        });
    }

    function toggleTheme() {
        var body = document.body;
        var btn = document.getElementById('themeBtn');
        var label = document.getElementById('themeLabel');
        var isCurrentlyLight = body.classList.contains('light-mode');
        var newTheme = isCurrentlyLight ? 'dark' : 'light';

        if (newTheme === 'light') {
            body.classList.add('light-mode');
            btn.innerText = "Switch to Dark Mode";
            label.innerText = "Light";
        } else {
            body.classList.remove('light-mode');
            btn.innerText = "Switch to Light Mode";
            label.innerText = "Dark";
        }

        var formData = new FormData();
        formData.append('theme', newTheme);

        fetch('ajax_update_theme.php', {
            method: 'POST',
            body: formData
        })
        .then(function(response) {
            return response.json();
        })
        .then(function() {
        });
    }

    setInterval(function() {
        fetch('check_status.php')
        .then(function(response) {
            return response.text();
        })
        .then(function(status) {
            var btn = document.getElementById('maintenanceBtn');
            var statusText = document.getElementById('statusText');
            var isCurrentlyActive = btn.innerText.includes('Deactivate');

            if (status !== (isCurrentlyActive ? '1' : '0')) {
                if (status === '0') {
                    statusText.innerText = 'OFF';
                    statusText.style.color = '#4caf50';
                    btn.innerText = 'Activate Maintenance';
                    btn.style.backgroundColor = '#e74c3c !important';
                    btn.setAttribute("onclick", "toggleMaintenance('0')");
                } else {
                    statusText.innerText = 'ACTIVE';
                    statusText.style.color = '#ff4d4d';
                    btn.innerText = 'Deactivate Maintenance';
                    btn.style.backgroundColor = '#4A90E2 !important';
                    btn.setAttribute("onclick", "toggleMaintenance('1')");
                }
            }
        });
    }, 10000);
    </script>
</body>
</html>
