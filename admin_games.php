<?php 
session_start();
require 'db_conn.php';
require_once 'includes/learning_helpers.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
$current_page = 'games'; 
$skills = pl_skill_definitions();
pl_seed_game_metadata($conn);
$category_options = [];
$category_res = $conn->query("SELECT DISTINCT category FROM levels WHERE category <> '' ORDER BY category ASC");
if ($category_res) {
    while ($cat = $category_res->fetch_assoc()) {
        $category_options[] = $cat['category'];
    }
}
?>

<style>
    /* Keep modal labels and inputs aligned. */
    .modal-body label {
        display: block;
        margin-bottom: 5px;
        color: #ccc;
        font-size: 14px;
        text-align: left;
    }
    .studio-input {
        width: 100%;
        padding: 10px;
        background: #222;
        border: 1px solid #444;
        color: white;
        border-radius: 6px;
        margin-bottom: 15px;
        box-sizing: border-box;
    }
    /* Place difficulty and game file controls side by side. */
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
        margin-bottom: 25px;
        color: #888;
    }

    .modal-footer {
        margin-top: 10px;
        padding-top: 15px;
        border-top: 1px solid #333;
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
        /* Keep modal overlays available and hidden by default. */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.85);
            display: none;
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
        .skill-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin-bottom: 15px; }
        .skill-control { background: #1d1d1d; border: 1px solid #333; border-radius: 10px; padding: 10px; }
        .skill-control label { display: flex; justify-content: space-between; align-items: center; margin: 0 0 8px; color: #ddd; font-size: 12px; font-weight: 700; }
        .skill-control input[type="range"] { width: 100%; accent-color: var(--accent-blue); }
        .age-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 15px; margin-bottom: 15px; }
        .meta-chip { display: inline-flex; align-items: center; gap: 5px; padding: 4px 8px; border-radius: 999px; background: rgba(255,255,255,0.08); color: #bbb; font-size: 11px; font-weight: 700; margin: 8px 6px 0 0; }
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
            <button class="studio-btn" style="background:#00b894;" onclick="openPackageModal()">
                <i class="fas fa-box-open"></i> Upload Game Package
            </button>
        </div>

        <div class="level-card-grid">
            <?php
            $res = $conn->query("SELECT * FROM levels ORDER BY id DESC");
            while($level = $res->fetch_assoc()):
                $level['skill_weights'] = pl_get_game_weights($conn, $level['id'], $level['level_name'], $level['category']);
                $age_res = $conn->query("SELECT min_age, max_age FROM game_age_bands WHERE game_id = " . intval($level['id']));
                $age_band = ($age_res && $age_res->num_rows > 0) ? $age_res->fetch_assoc() : ['min_age' => 4, 'max_age' => 12];
                $level['min_age'] = intval($age_band['min_age']);
                $level['max_age'] = intval($age_band['max_age']);
                $display_image_url = pl_game_cover_url($level);
            ?>
            <div class="level-card">
                <div class="level-img" style="background-image: url('<?php echo htmlspecialchars($display_image_url); ?>');"></div>
                <div class="level-info">
                    <span class="diff-badge diff-<?php echo $level['difficulty']; ?>"><?php echo $level['difficulty']; ?></span>
                    <h3 style="margin: 10px 0 5px;"><?php echo htmlspecialchars($level['level_name']); ?></h3>
                    <p style="font-size: 13px; color: #888;"><?php echo htmlspecialchars($level['level_description']); ?></p>
                    <div>
                        <span class="meta-chip"><i class="fas fa-tags"></i> <?php echo htmlspecialchars($level['category']); ?></span>
                        <span class="meta-chip"><i class="fas fa-child"></i> Ages <?php echo $level['min_age']; ?>-<?php echo $level['max_age']; ?></span>
                        <?php if (!empty($level['custom_slug'])): ?>
                            <span class="meta-chip"><i class="fas fa-fingerprint"></i> <?php echo htmlspecialchars($level['custom_slug']); ?></span>
                        <?php endif; ?>
                        <?php foreach ($level['skill_weights'] as $skill_key => $weight): ?>
                            <?php if ($weight > 0 && isset($skills[$skill_key])): ?>
                                <span class="meta-chip"><i class="fas <?php echo $skills[$skill_key]['icon']; ?>"></i> <?php echo $skills[$skill_key]['label']; ?> <?php echo $weight; ?>%</span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                        <span style="font-size: 11px; color: #666;">File: <?php echo $level['game_url']; ?></span>
                        <div>
                            <button class="studio-btn" style="padding: 5px 10px; font-size: 11px; background: var(--accent-blue);" 
                                    onclick="openEditModal(<?php echo htmlspecialchars(json_encode($level)); ?>)">
                                Edit
                            </button>
                            <button
                                type="button"
                                class="studio-btn"
                                style="padding: 5px 10px; font-size: 11px; background: #ff4d4d;"
                                data-delete-url="process_delete_game.php?id=<?php echo $level['id']; ?>"
                                data-game-name="<?php echo htmlspecialchars($level['level_name'], ENT_QUOTES); ?>"
                                onclick="openDeleteGameModal(this)">
                                Delete
                            </button>
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
                        <label>Game Slug</label>
                        <input type="text" name="custom_slug" class="studio-input" placeholder="auto-generated-if-empty">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label>Description</label>
                        <textarea name="level_description" class="studio-input" rows="2"></textarea>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label>Game Category</label>
                        <input type="text" name="category" list="categoryOptions" value="Logic" required class="studio-input">
                        <datalist id="categoryOptions">
                            <?php foreach ($category_options as $category_name): ?>
                                <option value="<?php echo htmlspecialchars($category_name); ?>"></option>
                            <?php endforeach; ?>
                            <option value="Math"></option>
                            <option value="Logic"></option>
                            <option value="Memory"></option>
                            <option value="Focus"></option>
                            <option value="Speed"></option>
                            <option value="Creativity"></option>
                        </datalist>
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
                    <div class="age-row">
                        <div>
                            <label>Minimum Age</label>
                            <input type="number" name="min_age" min="4" max="12" value="4" class="studio-input">
                        </div>
                        <div>
                            <label>Maximum Age</label>
                            <input type="number" name="max_age" min="4" max="12" value="12" class="studio-input">
                        </div>
                    </div>
                    <label>Learning Skill Weights</label>
                    <div class="skill-grid">
                        <?php foreach ($skills as $skill_key => $skill): ?>
                            <div class="skill-control">
                                <label>
                                    <span><i class="fas <?php echo $skill['icon']; ?>" style="color: <?php echo $skill['color']; ?>"></i> <?php echo $skill['label']; ?></span>
                                    <span data-skill-value="<?php echo $skill_key; ?>">0%</span>
                                </label>
                                <input type="range" min="0" max="100" step="5" value="0" name="skill_weights[<?php echo $skill_key; ?>]" data-skill="<?php echo $skill_key; ?>" oninput="syncSkillLabels()">
                            </div>
                        <?php endforeach; ?>
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

    <div id="packageModal" class="modal-overlay">
        <div class="modal-content" style="width: 520px;">
            <form action="process_upload_game_package.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h3><i class="fas fa-box-open"></i> Upload Game Package</h3>
                    <span class="close-btn" onclick="closePackageModal()">&times;</span>
                </div>
                <div class="modal-body">
                    <p style="color:#aaa; line-height:1.6; font-size:14px; margin-top:0;">
                        Upload either a zip package or the unpacked game files. The package must include manifest.json and index.html. The manifest defines title, category, age band, skill weights, cover, and entry file.
                    </p>
                    <label>Game Package (.zip)</label>
                    <input type="file" name="game_package" accept=".zip">
                    <label>Unpacked Package Files</label>
                    <input type="file" name="game_files[]" multiple webkitdirectory directory>
                    <div style="background:#1d1d1d; border:1px solid #333; border-radius:12px; padding:12px; color:#aaa; font-size:12px;">
                        If zip upload is not available on the server, select the unpacked game folder instead. HTML, CSS, JS, JSON, image, and audio files are allowed. PHP files are not allowed in game packages.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="studio-btn" style="background:#444;" onclick="closePackageModal()">Cancel</button>
                    <button type="submit" class="studio-btn" style="background:#00b894;">Upload Package</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteGameModal" class="modal-overlay">
        <div class="modal-content" style="width: min(420px, 92vw);">
            <div class="modal-header">
                <h3><i class="fas fa-trash-alt"></i> Delete Game</h3>
                <span class="close-btn" onclick="closeDeleteGameModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p style="margin-top:0;">This game entry will be removed from the admin list.</p>
                <p style="color:#ff7675; font-weight:800;" id="deleteGameName"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeDeleteGameModal()">Cancel</button>
                <a id="deleteGameConfirm" class="studio-btn" style="background:#ff4d4d; text-decoration:none;" href="#">Delete</a>
            </div>
        </div>
    </div>

    <script>
        function openModal() {
            // Reset the form for add mode.
            document.getElementById('gameForm').reset();
            document.getElementById('level_id').value = '';
            document.getElementById('modalTitle').innerText = 'Add New Game';
            document.querySelector('input[name="custom_slug"]').value = '';
            document.querySelector('input[name="category"]').value = 'Logic';
            document.getElementById('addLevelModal').style.display = 'flex';
            document.getElementById('addGameBtn').style.display = 'none';
        }

        function openEditModal(levelData) {
            // Fill the form for edit mode.
            document.getElementById('level_id').value = levelData.id;
            document.querySelector('input[name="level_name"]').value = levelData.level_name;
            document.querySelector('input[name="custom_slug"]').value = levelData.custom_slug || '';
            document.querySelector('input[name="category"]').value = levelData.category || 'Logic';
            document.querySelector('textarea[name="level_description"]').value = levelData.level_description;
            document.querySelector('select[name="difficulty"]').value = levelData.difficulty;
            document.querySelector('select[name="game_url"]').value = levelData.game_url;
            
            // Cover image is optional when editing.
            document.querySelector('input[name="level_image"]').required = false; 
            
            document.getElementById('modalTitle').innerText = 'Edit Game Level';
            document.getElementById('addLevelModal').style.display = 'flex';
            document.getElementById('addGameBtn').style.display = 'none';
        }

        function closeModal() {
            document.getElementById('addLevelModal').style.display = 'none';
            // Restore the add button.
            document.getElementById('addGameBtn').style.display = 'block';
        }

        function openPackageModal() {
            document.getElementById('packageModal').style.display = 'flex';
        }

        function closePackageModal() {
            document.getElementById('packageModal').style.display = 'none';
        }

        function openDeleteGameModal(button) {
            document.getElementById('deleteGameConfirm').href = button.getAttribute('data-delete-url');
            document.getElementById('deleteGameName').textContent = button.getAttribute('data-game-name');
            document.getElementById('deleteGameModal').style.display = 'flex';
        }

        function closeDeleteGameModal() {
            document.getElementById('deleteGameModal').style.display = 'none';
        }

        // Close the modal when clicking the overlay background.
        window.onclick = function(event) {
            let modal = document.getElementById('addLevelModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
<script>
    const defaultSkillWeightsV2 = {
        math: 0,
        logic: 35,
        memory: 0,
        focus: 35,
        speed: 0,
        creativity: 30
    };

    function setSkillWeights(weights) {
        document.querySelectorAll('[data-skill]').forEach(function(input) {
            const skill = input.dataset.skill;
            input.value = weights && weights[skill] !== undefined ? weights[skill] : 0;
        });
        syncSkillLabels();
    }

    function syncSkillLabels() {
        document.querySelectorAll('[data-skill]').forEach(function(input) {
            const label = document.querySelector(`[data-skill-value="${input.dataset.skill}"]`);
            if (label) label.innerText = `${input.value}%`;
        });
    }

    function openModal() {
        document.getElementById('gameForm').reset();
        document.getElementById('level_id').value = '';
        document.getElementById('modalTitle').innerText = 'Add New Game';
        document.querySelector('input[name="custom_slug"]').value = '';
        document.querySelector('input[name="category"]').value = 'Logic';
        document.querySelector('input[name="min_age"]').value = 4;
        document.querySelector('input[name="max_age"]').value = 12;
        document.querySelector('input[name="level_image"]').required = true;
        setSkillWeights(defaultSkillWeightsV2);
        document.getElementById('addLevelModal').style.display = 'flex';
        document.getElementById('addGameBtn').style.display = 'none';
    }

    function openEditModal(levelData) {
        document.getElementById('level_id').value = levelData.id;
        document.querySelector('input[name="level_name"]').value = levelData.level_name;
        document.querySelector('input[name="custom_slug"]').value = levelData.custom_slug || '';
        document.querySelector('input[name="category"]').value = levelData.category || 'Logic';
        document.querySelector('textarea[name="level_description"]').value = levelData.level_description;
        document.querySelector('select[name="difficulty"]').value = levelData.difficulty;
        document.querySelector('select[name="game_url"]').value = levelData.game_url;
        document.querySelector('input[name="min_age"]').value = levelData.min_age || 4;
        document.querySelector('input[name="max_age"]').value = levelData.max_age || 12;
        document.querySelector('input[name="level_image"]').required = false;
        setSkillWeights(levelData.skill_weights || defaultSkillWeightsV2);
        document.getElementById('modalTitle').innerText = 'Edit Game Level';
        document.getElementById('addLevelModal').style.display = 'flex';
        document.getElementById('addGameBtn').style.display = 'none';
    }

    syncSkillLabels();
</script>
</body>
</html>
