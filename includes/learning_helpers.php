<?php
function pl_skill_definitions() {
    return [
        'math' => ['label' => 'Math', 'icon' => 'fa-calculator', 'color' => '#4A90E2'],
        'logic' => ['label' => 'Logic', 'icon' => 'fa-brain', 'color' => '#6C3FF5'],
        'memory' => ['label' => 'Memory', 'icon' => 'fa-layer-group', 'color' => '#00B894'],
        'focus' => ['label' => 'Focus', 'icon' => 'fa-bullseye', 'color' => '#FF9B6B'],
        'speed' => ['label' => 'Speed', 'icon' => 'fa-bolt', 'color' => '#F4B400'],
        'creativity' => ['label' => 'Creativity', 'icon' => 'fa-wand-magic-sparkles', 'color' => '#E84393'],
    ];
}

function pl_default_game_covers() {
    return [
        '2048' => 'assets/game_covers/math_merge_2048.svg',
        'memory' => 'assets/game_covers/emoji_memory_match.svg',
        'math_pop' => 'assets/game_covers/math_pop.svg',
        'minesweeper' => 'assets/game_covers/mine_sweeper.svg',
        'mine' => 'assets/game_covers/mine_sweeper.svg',
        'odd_one_out' => 'assets/game_covers/odd_one_out.svg',
        'odd' => 'assets/game_covers/odd_one_out.svg',
        'snake' => 'assets/game_covers/snake_dash.svg',
        'word' => 'assets/game_covers/word_wanderer.svg',
    ];
}

function pl_normalize_slug($value) {
    $slug = strtolower(trim($value));
    $slug = preg_replace('/[^a-z0-9_-]+/', '-', $slug);
    $slug = trim($slug, '-_');
    return $slug !== '' ? $slug : 'game-' . time();
}

function pl_game_cover_url($game) {
    $name = strtolower($game['level_name'] ?? '');
    $url = strtolower($game['game_url'] ?? '');
    $covers = pl_default_game_covers();

    foreach ($covers as $needle => $cover) {
        if (strpos($name, $needle) !== false || strpos($url, $needle) !== false) {
            return $cover;
        }
    }

    return $game['image_url'] ?? 'img/default-game.png';
}

function pl_default_skill_weights($game_name = '', $category = '') {
    $name = strtolower($game_name);
    $category = strtolower($category);
    $weights = array_fill_keys(array_keys(pl_skill_definitions()), 0);

    if (strpos($name, 'math') !== false || $category === 'math') {
        $weights['math'] = 55;
        $weights['logic'] = 25;
        $weights['focus'] = 20;
    } elseif (strpos($name, 'word') !== false) {
        $weights['creativity'] = 35;
        $weights['memory'] = 25;
        $weights['focus'] = 25;
        $weights['logic'] = 15;
    } elseif (strpos($name, 'memory') !== false || $category === 'memory') {
        $weights['memory'] = 55;
        $weights['focus'] = 25;
        $weights['speed'] = 20;
    } elseif (strpos($name, 'snake') !== false || $category === 'speed') {
        $weights['speed'] = 45;
        $weights['focus'] = 35;
        $weights['logic'] = 20;
    } elseif (strpos($name, 'mine') !== false || strpos($name, '2048') !== false || $category === 'logic') {
        $weights['logic'] = 50;
        $weights['math'] = 20;
        $weights['focus'] = 20;
        $weights['memory'] = 10;
    } elseif (strpos($name, 'odd') !== false) {
        $weights['logic'] = 40;
        $weights['focus'] = 35;
        $weights['speed'] = 25;
    } else {
        $weights['logic'] = 35;
        $weights['focus'] = 35;
        $weights['creativity'] = 30;
    }

    return $weights;
}

function pl_ensure_learning_schema($conn) {
    $conn->query("CREATE TABLE IF NOT EXISTS game_skill_weights (
        id INT AUTO_INCREMENT PRIMARY KEY,
        game_id INT NOT NULL,
        skill_key VARCHAR(30) NOT NULL,
        weight TINYINT UNSIGNED NOT NULL DEFAULT 0,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_game_skill (game_id, skill_key)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $conn->query("CREATE TABLE IF NOT EXISTS game_age_bands (
        game_id INT NOT NULL PRIMARY KEY,
        min_age TINYINT UNSIGNED NOT NULL DEFAULT 4,
        max_age TINYINT UNSIGNED NOT NULL DEFAULT 12,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $level_columns = [
        'custom_slug' => "ALTER TABLE levels ADD COLUMN custom_slug VARCHAR(80) NULL AFTER game_url",
        'package_path' => "ALTER TABLE levels ADD COLUMN package_path VARCHAR(255) NULL AFTER custom_slug",
        'package_status' => "ALTER TABLE levels ADD COLUMN package_status ENUM('published','draft','rejected') NOT NULL DEFAULT 'published' AFTER package_path",
        'package_manifest' => "ALTER TABLE levels ADD COLUMN package_manifest TEXT NULL AFTER package_status",
    ];

    foreach ($level_columns as $column => $sql) {
        $exists = $conn->query("SHOW COLUMNS FROM levels LIKE '$column'");
        if ($exists && $exists->num_rows === 0) {
            $conn->query($sql);
        }
    }

    $conn->query("CREATE TABLE IF NOT EXISTS parent_controls (
        id INT AUTO_INCREMENT PRIMARY KEY,
        parent_id INT NOT NULL,
        child_id INT NOT NULL,
        can_clear_history TINYINT(1) NOT NULL DEFAULT 0,
        can_unlink_child TINYINT(1) NOT NULL DEFAULT 1,
        can_update_child_profile TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_parent_child (parent_id, child_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $conn->query("CREATE TABLE IF NOT EXISTS parent_learning_targets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        parent_id INT NOT NULL,
        child_id INT NOT NULL,
        skill_key VARCHAR(30) NOT NULL,
        target_value TINYINT UNSIGNED NOT NULL DEFAULT 70,
        note VARCHAR(255) DEFAULT NULL,
        due_date DATE DEFAULT NULL,
        status ENUM('active','completed','archived') NOT NULL DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_parent_child_skill (parent_id, child_id, skill_key)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $score_columns = [
        'correct_answers' => "ALTER TABLE game_scores ADD COLUMN correct_answers INT NULL AFTER level_reached",
        'total_questions' => "ALTER TABLE game_scores ADD COLUMN total_questions INT NULL AFTER correct_answers",
        'reaction_time_ms' => "ALTER TABLE game_scores ADD COLUMN reaction_time_ms INT NULL AFTER total_questions",
        'duration_seconds' => "ALTER TABLE game_scores ADD COLUMN duration_seconds INT NULL AFTER reaction_time_ms",
    ];

    foreach ($score_columns as $column => $sql) {
        $exists = $conn->query("SHOW COLUMNS FROM game_scores LIKE '$column'");
        if ($exists && $exists->num_rows === 0) {
            $conn->query($sql);
        }
    }

    $user_columns = [
        'ic_number' => "ALTER TABLE users ADD COLUMN ic_number VARCHAR(40) NULL AFTER gender",
        'ban_reason' => "ALTER TABLE users ADD COLUMN ban_reason VARCHAR(255) NULL AFTER status",
        'banned_at' => "ALTER TABLE users ADD COLUMN banned_at DATETIME NULL AFTER ban_reason",
    ];

    foreach ($user_columns as $column => $sql) {
        $exists = $conn->query("SHOW COLUMNS FROM users LIKE '$column'");
        if ($exists && $exists->num_rows === 0) {
            $conn->query($sql);
        }
    }
}

function pl_password_error($password) {
    if (strlen($password) < 8 || strlen($password) > 200) {
        return "Password must be 8 to 200 characters.";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return "Password must include at least one uppercase letter.";
    }
    if (!preg_match('/[0-9]/', $password)) {
        return "Password must include at least one number.";
    }
    return null;
}

function pl_age_in_range($birthday, $min_age, $max_age) {
    $age = pl_child_age($birthday);
    if ($age === null) {
        return false;
    }
    return $age >= $min_age && $age <= $max_age;
}

function pl_seed_game_metadata($conn) {
    pl_ensure_learning_schema($conn);
    $games = $conn->query("SELECT id, level_name, category FROM levels");
    if (!$games) {
        return;
    }

    while ($game = $games->fetch_assoc()) {
        $game_id = intval($game['id']);
        $count = $conn->query("SELECT COUNT(*) AS total FROM game_skill_weights WHERE game_id = $game_id");
        $has_weights = $count && intval($count->fetch_assoc()['total']) > 0;

        if (!$has_weights) {
            $weights = pl_default_skill_weights($game['level_name'], $game['category']);
            $stmt = $conn->prepare("INSERT INTO game_skill_weights (game_id, skill_key, weight) VALUES (?, ?, ?)");
            foreach ($weights as $skill => $weight) {
                $stmt->bind_param("isi", $game_id, $skill, $weight);
                $stmt->execute();
            }
            $stmt->close();
        }

        $age = $conn->query("SELECT game_id FROM game_age_bands WHERE game_id = $game_id");
        if ($age && $age->num_rows === 0) {
            $stmt = $conn->prepare("INSERT INTO game_age_bands (game_id, min_age, max_age) VALUES (?, 4, 12)");
            $stmt->bind_param("i", $game_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

function pl_get_game_weights($conn, $game_id, $game_name = '', $category = '') {
    pl_ensure_learning_schema($conn);
    $weights = array_fill_keys(array_keys(pl_skill_definitions()), 0);
    $game_id = intval($game_id);
    $res = $conn->query("SELECT skill_key, weight FROM game_skill_weights WHERE game_id = $game_id");

    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            if (array_key_exists($row['skill_key'], $weights)) {
                $weights[$row['skill_key']] = intval($row['weight']);
            }
        }
        return $weights;
    }

    return pl_default_skill_weights($game_name, $category);
}

function pl_save_game_metadata($conn, $game_id, $weights, $min_age, $max_age) {
    pl_ensure_learning_schema($conn);
    $game_id = intval($game_id);
    $min_age = max(4, min(12, intval($min_age)));
    $max_age = max($min_age, min(12, intval($max_age)));
    $skills = pl_skill_definitions();

    $stmt = $conn->prepare("INSERT INTO game_skill_weights (game_id, skill_key, weight) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE weight = VALUES(weight)");
    foreach ($skills as $skill => $_meta) {
        $weight = isset($weights[$skill]) ? max(0, min(100, intval($weights[$skill]))) : 0;
        $stmt->bind_param("isi", $game_id, $skill, $weight);
        $stmt->execute();
    }
    $stmt->close();

    $age_stmt = $conn->prepare("INSERT INTO game_age_bands (game_id, min_age, max_age) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE min_age = VALUES(min_age), max_age = VALUES(max_age)");
    $age_stmt->bind_param("iii", $game_id, $min_age, $max_age);
    $age_stmt->execute();
    $age_stmt->close();
}

function pl_child_age($birthday) {
    if (empty($birthday) || $birthday === '0000-00-00') {
        return null;
    }
    try {
        $birth = new DateTime($birthday);
        $today = new DateTime();
        return intval($birth->diff($today)->y);
    } catch (Exception $e) {
        return null;
    }
}

function pl_streak_days($conn, $user_id) {
    $user_id = intval($user_id);
    $res = $conn->query("SELECT DISTINCT DATE(played_at) AS play_date FROM game_scores WHERE user_id = $user_id ORDER BY play_date DESC");
    if (!$res) {
        return 0;
    }

    $dates = [];
    while ($row = $res->fetch_assoc()) {
        $dates[$row['play_date']] = true;
    }

    $streak = 0;
    $cursor = new DateTime('today');
    while (isset($dates[$cursor->format('Y-m-d')])) {
        $streak++;
        $cursor->modify('-1 day');
    }

    return $streak;
}

function pl_parent_control($conn, $parent_id, $child_id) {
    pl_ensure_learning_schema($conn);
    $parent_id = intval($parent_id);
    $child_id = intval($child_id);

    $conn->query("INSERT IGNORE INTO parent_controls (parent_id, child_id) VALUES ($parent_id, $child_id)");
    $res = $conn->query("SELECT * FROM parent_controls WHERE parent_id = $parent_id AND child_id = $child_id");
    return $res ? $res->fetch_assoc() : null;
}
?>
