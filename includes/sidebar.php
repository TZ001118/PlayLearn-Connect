<?php
if (isset($_SESSION['user_id'])) {
    $sid = intval($_SESSION['user_id']);
    $pref_res = $conn->query("SELECT theme_preference FROM users WHERE id = $sid");
    $pref_data = $pref_res ? $pref_res->fetch_assoc() : null;
    $saved_theme = $pref_data['theme_preference'] ?? 'dark';
}
?>

<script>
    <?php if(isset($saved_theme) && $saved_theme === 'light'): ?>
        document.body.classList.add('light-mode');
    <?php else: ?>
        document.body.classList.remove('light-mode');
    <?php endif; ?>
</script>

<nav class="sidebar">
    <div class="sidebar-header">PLAYLEARN ADMIN</div>
    <div class="menu-list">
        <a href="admin_dashboard.php" class="menu-item <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>
        <a href="admin_games.php" class="menu-item <?php echo ($current_page == 'games') ? 'active' : ''; ?>">
            <i class="fas fa-gamepad"></i> Game Management
        </a>
        <a href="admin_users.php" class="menu-item <?php echo ($current_page == 'users') ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> User Analytics
        </a>
        <a href="admin_scores.php" class="menu-item <?php echo ($current_page == 'scores') ? 'active' : ''; ?>">
            <i class="fas fa-trophy"></i> Player Scores
        </a>
        <a href="admin_reviews.php" class="menu-item <?php echo ($current_page == 'reviews') ? 'active' : ''; ?>">
            <i class="fas fa-comments"></i> Reviews
        </a>

        <div style="margin-top: 15px; padding: 0 25px; font-size: 10px; color: var(--text-gray); letter-spacing: 1px; font-weight: bold;">SYSTEM & SAFETY</div>
        
        <a href="admin_logs.php" class="menu-item <?php echo ($current_page == 'logs') ? 'active' : ''; ?>">
            <i class="fas fa-shield-alt"></i> Security Logs
        </a>

        <a href="admin_profile.php" class="menu-item <?php echo ($current_page == 'profile') ? 'active' : ''; ?>">
            <i class="fas fa-user-cog"></i> Admin Settings
        </a>
    </div>

    <a href="logout.php" class="menu-item" style="border-top: 1px solid var(--border-color); margin-top: auto;">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</nav>
