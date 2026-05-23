<?php 
session_start();
require 'db_conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
$current_page = 'games'; 
?>

<style>
    /* 让标签和输入框漂亮地排队 */
    .modal-body label {
        display: block;
        margin-bottom: 5px;
        color: #ccc;
        font-size: 14px;
        text-align: left; /* 确保文字左对齐 */
    }
    .studio-input {
        width: 100%;
        padding: 10px;
        background: #222;
        border: 1px solid #444;
        color: white;
        border-radius: 6px;
        margin-bottom: 15px;
        box-sizing: border-box; /* 关键：防止输入框超出容器 */
    }
    /* 让 Difficulty 和 Game File 并排 */
    .flex-row {
        display: flex;
        gap: 15px;
    }
    .flex-row > div {
        flex: 1;
    }

    input[type="file"] {
        display: block;
        margin-top: 5px;
        margin-bottom: 25px; /* ✅ 增加这个，把底部的 Cancel/Upload 按钮推开 */
        color: #888;
    }

    .modal-footer {
        margin-top: 10px; /* ✅ 确保顶部也有间距 */
        padding-top: 15px;
        border-top: 1px solid #333; /* 可选：加一条细线显得更专业 */
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PlayLearn Admin | Game Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin_global.css?v=<?php echo time(); ?>">
    <style>
        /* ✅ 关键：确保弹窗遮罩层样式存在并默认隐藏 */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.85);
            display: none; /* 默认不显示 */
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }
        .level-card-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-top: 20px; }
        .level-card { background: var(--card-dark); border: 1px solid #333; border-radius: 12px; overflow: hidden; transition: 0.3s; }
        .level-card:hover { transform: translateY(-5px); border-color: var(--accent-blue); }
        .level-img { width: 100%; height: 150px; background: #333; background-size: cover; background-position: center; }
        .level-info { padding: 15px; }
        .diff-badge { font-size: 10px; padding: 2px 8px; border-radius: 10px; text-transform: uppercase; color: white; }
        .diff-Easy { background: #4caf50; }
        .diff-Normal { background: #ff9800; }
        .diff-Hard { background: #f44336; }
    </style>
</head>
<body>

    <?php include('includes/sidebar.php'); ?>

    <main class="main-content">
        <div class="header-row">
            <h2>Game Level Management</h2>
            <button id="addGameBtn" class="studio-btn" style="background: var(--accent-blue);" onclick="openModal()">
                <i class="fas fa-plus"></i> Add New Game
            </button>
        </div>

        <div class="level-card-grid">
            <?php
            $res = $conn->query("SELECT * FROM levels ORDER BY id DESC");
            while($level = $res->fetch_assoc()):
            ?>
            <div class="level-card">
                <div class="level-img" style="background-image: url('<?php echo $level['image_url']; ?>');"></div>
                <div class="level-info">
                    <span class="diff-badge diff-<?php echo $level['difficulty']; ?>"><?php echo $level['difficulty']; ?></span>
                    <h3 style="margin: 10px 0 5px;"><?php echo htmlspecialchars($level['level_name']); ?></h3>
                    <p style="font-size: 13px; color: #888;"><?php echo htmlspecialchars($level['level_description']); ?></p>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                        <span style="font-size: 11px; color: #666;">File: <?php echo $level['game_url']; ?></span>
                        <div>
                            <button class="studio-btn" style="padding: 5px 10px; font-size: 11px; background: var(--accent-blue);" 
                                    onclick="openEditModal(<?php echo htmlspecialchars(json_encode($level)); ?>)">
                                Edit
                            </button>
                            <a href="process_delete_game.php?id=<?php echo $level['id']; ?>" 
                            class="studio-btn" style="padding: 5px 10px; font-size: 11px; background: #ff4d4d; text-decoration:none;"
                            onclick="return confirm('Are you sure?')">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </main>

    <div id="addLevelModal" class="modal-overlay">
        <div class="modal-content" style="width: 550px;">
            <form id="gameForm" action="process_add_game.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="level_id" id="level_id"> <div class="modal-header">
                    <h3 id="modalTitle"><i class="fas fa-gamepad"></i> Add New Game</h3>
                    <span class="close-btn" onclick="closeModal()">&times;</span>
                </div>
                <div class="modal-body">
                    <div style="margin-bottom: 15px;">
                        <label>Game Title</label>
                        <input type="text" name="level_name" required class="studio-input">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label>Description</label>
                        <textarea name="level_description" class="studio-input" rows="2"></textarea>
                    </div>
                    <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                        <div style="flex: 1;">
                            <label>Difficulty</label>
                            <select name="difficulty" class="studio-input">
                                <option value="Easy">Easy</option>
                                <option value="Normal">Normal</option>
                                <option value="Hard">Hard</option>
                            </select>
                        </div>
                        <div style="flex: 1;">
                            <label>Game File (.php)</label>
                            <select name="game_url" class="studio-input">
                                <option value="games/game2048.php">Math Merge 2048</option>
                                <option value="games/memory.php">Memory Match</option>
                                <option value="games/math_pop.php">Math Pop</option>
                                <option value="games/minesweeper.php">Minesweeper</option>
                                <option value="games/odd_one_out.php">Odd One Out</option>
                                <option value="games/snake.php">Snake</option>
                                <option value="games/word_wanderer.php">Word Wanderer</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label>Cover Image</label>
                        <input type="file" name="level_image" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="studio-btn" style="background:#444;" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="studio-btn" style="background:var(--accent-blue);">Upload & Publish</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            // 重置表单，用于“新增”模式
            document.getElementById('gameForm').reset();
            document.getElementById('level_id').value = '';
            document.getElementById('modalTitle').innerText = 'Add New Game';
            document.getElementById('addLevelModal').style.display = 'flex';
            document.getElementById('addGameBtn').style.display = 'none';
        }

        function openEditModal(levelData) {
            // 进入“编辑”模式，填充数据
            document.getElementById('level_id').value = levelData.id;
            document.querySelector('input[name="level_name"]').value = levelData.level_name;
            document.querySelector('textarea[name="level_description"]').value = levelData.level_description;
            document.querySelector('select[name="difficulty"]').value = levelData.difficulty;
            document.querySelector('select[name="game_url"]').value = levelData.game_url;
            
            // 封面图在编辑时通常是可选的
            document.querySelector('input[name="level_image"]').required = false; 
            
            document.getElementById('modalTitle').innerText = 'Edit Game Level';
            document.getElementById('addLevelModal').style.display = 'flex';
            document.getElementById('addGameBtn').style.display = 'none';
        }

        function closeModal() {
            document.getElementById('addLevelModal').style.display = 'none';
            // ✅ 按钮恢复显示
            document.getElementById('addGameBtn').style.display = 'block';
        }

        // 点击遮罩背景也可以关闭
        window.onclick = function(event) {
            let modal = document.getElementById('addLevelModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>