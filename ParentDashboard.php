<?php
session_start();
require('db_conn.php'); 
include('maintenance_check.php');
// 1. 安全检查
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$current_parent_id = $_SESSION['user_id'];
$children = [];
$diagnostic_msg = ""; 

// 2. 🌟 修改查询：加入 profile_image 和 avatar_frame
$sql_all_children = "SELECT u.id, u.username, u.email, u.profile_image, u.avatar_frame 
                     FROM account_links al 
                     JOIN users u ON al.child_id = u.id 
                     WHERE al.parent_id = $current_parent_id";
$res_all = $conn->query($sql_all_children);

if ($res_all && $res_all->num_rows > 0) {
    while($row = $res_all->fetch_assoc()) {
        $children[] = $row;
    }
} else {
    if (!$res_all) {
        $diagnostic_msg = "SQL Error: " . $conn->error;
    } else {
        $test = $conn->query("SELECT * FROM account_links WHERE parent_id = $current_parent_id");
        if ($test && $test->num_rows > 0) {
            $diagnostic_msg = "Warning: Records found in account_links but no matching users found.";
        } else {
            $diagnostic_msg = "No children linked to your account yet.";
        }
    }
}

$is_linked = !empty($children);

// 3. 确定当前查看的目标
if ($is_linked) {
    $active_child_id = isset($_GET['child_id']) ? intval($_GET['child_id']) : ($children[0]['id']);
    $_SESSION['active_child_id'] = $active_child_id;
    $target_user_id = $active_child_id; 
} else {
    $active_child_id = 0;
    $target_user_id = $current_parent_id; 
}

// 🌟 提取当前选中的孩子的完整信息（包含头像）
$active_child = null;
$active_child_name = "My Account";

foreach($children as $c) {
    if($c['id'] == $active_child_id) {
        $active_child = $c;
        $active_child_name = $c['username'];
        break;
    }
}

if (!$active_child) {
    $res_name = $conn->query("SELECT username, profile_image, avatar_frame FROM users WHERE id = $target_user_id");
    if ($res_name && $row = $res_name->fetch_assoc()) {
        $active_child = $row;
        $active_child_name = $row['username'];
    }
}

$page = $_GET['page'] ?? 'overview';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlayLearn | Parent Dashboard</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root { 
            --primary-dark: #0f172a; 
            --blob-purple: #6C3FF5; 
            --blob-orange: #FF9B6B; 
            --bg-slate: #f8fafc;
            --border-color: #e2e8f0;
            --sidebar-width: 280px;
        }

        body { background-color: var(--bg-slate); font-family: 'Nunito', sans-serif; display: flex; color: var(--primary-dark); }

        /* 侧边栏样式 */
        .sidebar { 
            width: var(--sidebar-width); height: 100vh; background: #ffffff; 
            position: fixed; left: 0; top: 0; border-right: 1px solid var(--border-color); 
            padding: 32px 24px; display: flex; flex-direction: column; z-index: 1000;
        }

        .sidebar-brand { font-size: 26px; font-weight: 900; color: var(--primary-dark); text-decoration: none !important; margin-bottom: 35px; display: flex; align-items: center; letter-spacing: -0.5px; }
        .sidebar-brand span { color: var(--blob-orange); }
        .sidebar-brand i { color: var(--blob-purple); margin-right: 8px; font-size: 24px; }

        /* 孩子列表区域 */
        .children-section { background: #f8fafc; border-radius: 24px; padding: 18px; margin-bottom: 30px; border: 1px solid var(--border-color); }
        .section-title { font-size: 11px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 12px; display: block; }
        
        .child-item { display: flex; align-items: center; padding: 12px; border-radius: 16px; text-decoration: none !important; color: #475569; transition: all 0.3s ease; margin-bottom: 8px; border: 2px solid transparent; }
        .child-item:hover { background: #ffffff; border-color: var(--border-color); transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .child-item.active { background: #ffffff; border-color: var(--blob-purple); color: var(--primary-dark) !important; box-shadow: 0 10px 20px rgba(108, 63, 245, 0.12); }
        
        /* 🌟 动态头像和头像框的样式 */
        .child-avatar-box { 
            width: 42px; height: 42px; border-radius: 14px; 
            background: linear-gradient(135deg, var(--blob-purple) 0%, var(--blob-orange) 100%); 
            color: white; display: flex; align-items: center; justify-content: center; 
            margin-right: 14px; font-weight: 900; font-size: 18px; 
            box-shadow: 0 4px 10px rgba(108, 63, 245, 0.2); 
            position: relative; flex-shrink: 0;
        }
        .child-avatar-img { width: 100%; height: 100%; object-fit: cover; border-radius: inherit; }
        .child-frame-img { position: absolute; top: -20%; left: -20%; width: 140%; height: 140%; pointer-events: none; z-index: 10; }
        .child-item.active .child-avatar-box { box-shadow: 0 0 0 4px rgba(108,63,245,0.15); }

        /* 导航菜单 */
        .nav-menu { display: flex; flex-direction: column; gap: 4px; }
        .nav-link { display: flex; align-items: center; color: #64748b; padding: 14px 18px; border-radius: 16px; transition: all 0.3s ease; text-decoration: none !important; font-weight: 800; font-size: 15px; }
        .nav-link i { margin-right: 14px; width: 22px; text-align: center; font-size: 18px; transition: transform 0.3s ease; }
        .nav-link:hover { background: #f1f5f9; color: var(--primary-dark); }
        .nav-link:hover i { transform: scale(1.1); color: var(--blob-purple); }
        
        .nav-link.active { background: rgba(108, 63, 245, 0.08); color: var(--blob-purple) !important; }
        .nav-link.active i { color: var(--blob-purple); }

        /* 内容区 */
        .main-content { margin-left: var(--sidebar-width); width: calc(100% - var(--sidebar-width)); padding: 40px 50px; }
        
        /* 标题增强 */
        .page-title { font-weight: 900; color: var(--primary-dark); font-size: 28px; }
        .btn-hub { background: #ffffff; border: 1px solid var(--border-color); color: #475569; font-weight: 800; border-radius: 50px; padding: 10px 24px; transition: all 0.3s ease; text-decoration: none; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: inline-flex; align-items: center; }
        .btn-hub:hover { background: #f1f5f9; color: var(--primary-dark); transform: translateY(-2px); }

        /* 右上角精美圆形注销按钮样式 */
        .btn-logout-circle { width: 44px; height: 44px; background: #ffffff; border: 1px solid var(--border-color); color: #64748b; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; text-decoration: none !important; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .btn-logout-circle:hover { background: #fef2f2; color: #ef4444; transform: translateY(-2px); box-shadow: 0 8px 15px rgba(239, 68, 68, 0.1); }

        /* 注销高级弹窗样式 */
        .global-modal-overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); z-index: 9999; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(6px); visibility: hidden; opacity: 0; transition: opacity 0.3s ease, visibility 0.3s ease; }
        .global-modal-overlay.active { visibility: visible; opacity: 1; }
        .global-modal-card { background: white; border-radius: 28px; width: 90%; max-width: 400px; padding: 36px; text-align: center; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); transform: scale(0.95); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .global-modal-overlay.active .global-modal-card { transform: scale(1); }
        
        .modal-icon-box { width: 68px; height: 68px; background: #fef2f2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px; margin: 0 auto 20px auto; border: 1px solid #fee2e2; }
        .btn-cancel { background: #f1f5f9; color: #475569; font-weight: 900; border: none; padding: 14px; border-radius: 16px; width: 100%; transition: 0.2s; }
        .btn-cancel:hover { background: #e2e8f0; }
        .btn-confirm { background: #ef4444; color: white; font-weight: 900; border: none; padding: 14px; border-radius: 16px; width: 100%; transition: 0.2s; text-decoration: none; display: inline-block; box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3); }
        .btn-confirm:hover { background: #dc2626; color: white; transform: translateY(-2px); box-shadow: 0 10px 20px -3px rgba(239, 68, 68, 0.4); }
    </style>
</head>
<body>

    <nav class="sidebar">
        <a href="homepage.php" class="sidebar-brand">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="me-2" style="color: var(--blob-purple);"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1-1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
            Play<span>Learn</span>
        </a>

        <div class="children-section">
            <span class="section-title">My Children</span>
            
            <?php if($is_linked): ?>
                <?php foreach($children as $child): ?>
                    <a href="?page=<?= $page ?>&child_id=<?= $child['id'] ?>" class="child-item <?= $active_child_id == $child['id'] ? 'active' : '' ?>">
                        <div class="child-avatar-box">
                            <?php if (!empty($child['profile_image'])): ?>
                                <img src="<?= htmlspecialchars($child['profile_image']) ?>" class="child-avatar-img">
                            <?php else: ?>
                                <?= strtoupper(substr($child['username'], 0, 1)) ?>
                            <?php endif; ?>

                            <?php if (!empty($child['avatar_frame'])): ?>
                                <img src="<?= htmlspecialchars($child['avatar_frame']) ?>" class="child-frame-img">
                            <?php endif; ?>
                        </div>
                        <div class="overflow-hidden">
                            <div class="fw-bold text-truncate" style="font-size: 15px;"><?= htmlspecialchars($child['username']) ?></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-3">
                    <div class="w-10 h-10 bg-slate-100 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-2 text-lg"><i class="fas fa-child"></i></div>
                    <p class="small text-muted fw-bold mb-3">No children linked</p>
                    <a href="?page=settings" class="btn btn-primary btn-sm rounded-pill fw-bold w-100" style="background: var(--blob-purple); border: none;">+ Link Account</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="nav-menu">
            <a href="?page=overview<?php echo $is_linked ? "&child_id=$active_child_id" : ""; ?>" class="nav-link <?= $page=='overview'?'active':'' ?>"><i class="fas fa-th-large"></i> Overview</a>
            <a href="?page=radar<?php echo $is_linked ? "&child_id=$active_child_id" : ""; ?>" class="nav-link <?= $page=='radar'?'active':'' ?>"><i class="fas fa-bullseye"></i> Skill Radar</a>
            <a href="?page=history<?php echo $is_linked ? "&child_id=$active_child_id" : ""; ?>" class="nav-link <?= $page=='history'?'active':'' ?>"><i class="fas fa-history"></i> History Log</a>
            <a href="?page=goals<?php echo $is_linked ? "&child_id=$active_child_id" : ""; ?>" class="nav-link <?= $page=='goals'?'active':'' ?>"><i class="fas fa-tasks"></i> Learning Goals</a>
            <a href="?page=students<?php echo $is_linked ? "&child_id=$active_child_id" : ""; ?>" class="nav-link <?= $page=='students'?'active':'' ?>"><i class="fas fa-user-friends"></i> Students List</a>
            <a href="?page=settings" class="nav-link <?= $page=='settings'?'active':'' ?>"><i class="fas fa-cog"></i> Settings</a>
        </div>
    </nav>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div class="d-flex align-items-center gap-3">
                <?php if($active_child): ?>
                    <div class="child-avatar-box" style="width: 50px; height: 50px; margin: 0; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                        <?php if (!empty($active_child['profile_image'])): ?>
                            <img src="<?= htmlspecialchars($active_child['profile_image']) ?>" class="child-avatar-img">
                        <?php else: ?>
                            <?= strtoupper(substr($active_child_name, 0, 1)) ?>
                        <?php endif; ?>

                        <?php if (!empty($active_child['avatar_frame'])): ?>
                            <img src="<?= htmlspecialchars($active_child['avatar_frame']) ?>" class="child-frame-img">
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div>
                    <h3 class="page-title mb-0">Dashboard</h3>
                    <p class="text-muted fw-bold mb-0" style="font-size: 13px;">Viewing progress for <span style="color: var(--blob-purple);"><?= htmlspecialchars($active_child_name) ?></span></p>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <a href="homepage.php" class="btn-hub"><i class="fas fa-home me-2"></i> Back to Hub</a>
                <a href="javascript:void(0)" class="btn-logout-circle" onclick="openLogoutModal()" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>

        <div class="container-fluid px-0">
            <?php 
                error_reporting(E_ALL); ini_set('display_errors', 1);

                $view_file_in_folder = "pages/{$page}_view.php";
                $view_file_in_root = "{$page}_view.php";

                if (file_exists($view_file_in_folder)) {
                    include($view_file_in_folder); 
                } elseif (file_exists($view_file_in_root)) {
                    include($view_file_in_root);
                } else {
                    echo "<div class='card p-5 text-center border-0 shadow-sm' style='border-radius: 24px;'><h4 class='text-danger fw-bold'>File Not Found</h4><p>无法找到 <b>{$page}_view.php</b></p></div>";
                }
            ?>
        </div>
    </main>

    <div id="logoutModal" class="global-modal-overlay">
        <div class="global-modal-card">
            <div class="modal-icon-box">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <h4 style="font-weight: 900; color: var(--primary-dark); margin-bottom: 8px; font-size: 24px;">Ready to leave?</h4>
            <p style="color: #64748b; font-weight: 700; font-size: 15px; margin-bottom: 28px;">Are you sure you want to log out of your dashboard?</p>
            <div style="display: flex; gap: 16px;">
                <button class="btn-cancel" onclick="closeLogoutModal()">Cancel</button>
                <a href="logout.php" class="btn-confirm">Yes, Logout</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const logoutModal = document.getElementById('logoutModal');
        function openLogoutModal() { logoutModal.classList.add('active'); document.body.style.overflow = 'hidden'; }
        function closeLogoutModal() { logoutModal.classList.remove('active'); document.body.style.overflow = 'auto'; }
        logoutModal.addEventListener('click', function(e) { if (e.target === logoutModal) closeLogoutModal(); });
    </script>
</body>
</html>