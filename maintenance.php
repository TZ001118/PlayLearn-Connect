<?php
require 'db_conn.php';
// 获取过期时间点
$eta_res = $conn->query("SELECT config_value FROM site_config WHERE config_key = 'maintenance_expires_at'");
$expires_at_str = $eta_res->fetch_assoc()['config_value'];

// 计算剩余秒数
$expires_at = strtotime($expires_at_str);
$remaining_seconds = $expires_at - time();
// 计算剩余分钟
$remaining_minutes = max(0, round($remaining_seconds / 60));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlayLearn | Maintenance</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            background: #121212; color: white; text-align: center; 
            padding-top: 10vh; font-family: 'Inter', sans-serif; 
        }
        .icon { font-size: 80px; color: #ff9800; margin-bottom: 20px; animation: pulse 2s infinite; }
        h1 { font-size: 40px; margin: 0; letter-spacing: -1px; }
        p { color: #888; font-size: 18px; max-width: 500px; margin: 15px auto; line-height: 1.5; }
        
        .eta-box {
            background: #1e1e1e;
            border: 1px solid #333;
            border-radius: 12px;
            display: inline-block;
            padding: 20px 40px;
            margin: 30px 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .eta-box h2 { color: #4A90E2; margin: 0; font-size: 32px; }
        .eta-box span { color: #aaa; font-size: 14px; text-transform: uppercase; letter-spacing: 2px; }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="icon"><i class="fas fa-hard-hat"></i></div>
    <h1>System Upgrading</h1>
    <p>We are currently updating PlayLearn with new features and improvements. Hang tight!</p>
    
    <div class="eta-box">
        <span>Estimated Wait Time</span>
        <h2 id="countdown">~ <?= $remaining_minutes ?> Minutes</h2>
    </div>

    <p style="font-size: 14px; color: #555;">Please do not refresh the page. Try logging in again later.</p>
    
    <div style="margin-top: 50px;">
        <a href="login.php" style="color: #3b5998; text-decoration: none; font-weight: 600; font-size: 14px;">
            <i class="fas fa-lock"></i> Admin Login Access
        </a>
    </div>
    <script>
    let remainingSeconds = <?= max(0, $remaining_seconds) ?>;
    
    function updateCountdown() {
        if (remainingSeconds <= 0) {
            location.reload(); // 时间到了，自动刷新页面（触发你的自动开启逻辑）
            return;
        }
        remainingSeconds--;
        let mins = Math.floor(remainingSeconds / 60);
        let secs = remainingSeconds % 60;
        document.getElementById('countdown').innerText = "~ " + mins + "m " + secs + "s";
    }
    
    // 每秒执行一次
    setInterval(updateCountdown, 1000);
</script>
</body>
</html>