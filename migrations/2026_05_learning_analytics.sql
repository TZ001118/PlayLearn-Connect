CREATE TABLE IF NOT EXISTS game_skill_weights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_id INT NOT NULL,
    skill_key VARCHAR(30) NOT NULL,
    weight TINYINT UNSIGNED NOT NULL DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_game_skill (game_id, skill_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS game_age_bands (
    game_id INT NOT NULL PRIMARY KEY,
    min_age TINYINT UNSIGNED NOT NULL DEFAULT 4,
    max_age TINYINT UNSIGNED NOT NULL DEFAULT 12,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS parent_controls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NOT NULL,
    child_id INT NOT NULL,
    can_clear_history TINYINT(1) NOT NULL DEFAULT 0,
    can_unlink_child TINYINT(1) NOT NULL DEFAULT 1,
    can_update_child_profile TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_parent_child (parent_id, child_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE game_scores ADD COLUMN IF NOT EXISTS correct_answers INT NULL AFTER level_reached;
ALTER TABLE game_scores ADD COLUMN IF NOT EXISTS total_questions INT NULL AFTER correct_answers;
ALTER TABLE game_scores ADD COLUMN IF NOT EXISTS reaction_time_ms INT NULL AFTER total_questions;
ALTER TABLE game_scores ADD COLUMN IF NOT EXISTS duration_seconds INT NULL AFTER reaction_time_ms;

CREATE TABLE IF NOT EXISTS parent_learning_targets (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE users ADD COLUMN IF NOT EXISTS ic_number VARCHAR(40) NULL AFTER gender;
ALTER TABLE users ADD COLUMN IF NOT EXISTS ban_reason VARCHAR(255) NULL AFTER status;
ALTER TABLE users ADD COLUMN IF NOT EXISTS banned_at DATETIME NULL AFTER ban_reason;

ALTER TABLE levels ADD COLUMN IF NOT EXISTS custom_slug VARCHAR(80) NULL AFTER game_url;
ALTER TABLE levels ADD COLUMN IF NOT EXISTS package_path VARCHAR(255) NULL AFTER custom_slug;
ALTER TABLE levels ADD COLUMN IF NOT EXISTS package_status ENUM('published','draft','rejected') NOT NULL DEFAULT 'published' AFTER package_path;
ALTER TABLE levels ADD COLUMN IF NOT EXISTS package_manifest TEXT NULL AFTER package_status;
