<?php 
session_start();
require('db_conn.php');
include('maintenance_check.php');
// 读取数据库设置
$settings = [];
$res = $conn->query("SELECT * FROM site_settings");
while($row = $res->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlayLearn | Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Fredoka', sans-serif;
            background-color: #151313;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
        }

        /* 这里的背景图现在由 Admin 后台控制 */
        .hero-section {
            width: 100%;
            height: 400px;
            background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('<?php echo $settings['hero_banner_url']; ?>');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-bottom: 4px solid #3b5998;
        }

        h1 { font-size: 60px; margin: 0; letter-spacing: 2px; }
        p { font-size: 20px; color: #dcdcdc; margin-top: 10px; }

        .play-btn {
            margin-top: 30px;
            padding: 15px 50px;
            background-color: white;
            color: black;
            text-decoration: none;
            font-weight: bold;
            font-size: 24px;
            border-radius: 50px;
            transition: 0.3s;
        }
        .play-btn:hover { transform: scale(1.1); background-color: #3b5998; color: white; }
    </style>
</head>
<body>

    <div class="hero-section">
        <h1><?php echo $settings['homepage_title']; ?></h1>
        <p><?php echo $settings['homepage_slogan']; ?></p>
        <a href="homepage.php" class="play-btn">START PLAYING</a>
    </div>

    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="admin_dashboard.php" style="color: #888; margin-top: 20px; text-decoration: none;">Go to Admin Panel</a>
    <?php endif; ?>

</body>
</html>