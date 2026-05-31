<?php
session_start();
require 'db_conn.php';
require_once 'includes/learning_helpers.php';
pl_ensure_learning_schema($conn);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

function fail_package($message) {
    die("Package upload failed: " . htmlspecialchars($message));
}

function package_safe_path($path) {
    $path = str_replace('\\', '/', trim($path));
    $path = preg_replace('#/+#', '/', $path);
    $path = ltrim($path, '/');
    if ($path === '' || strpos($path, '..') !== false || preg_match('/^[a-zA-Z]:/', $path)) {
        return '';
    }
    return $path;
}

function strip_common_root_folder($paths) {
    $roots = [];
    foreach ($paths as $path) {
        $parts = explode('/', $path);
        if (count($parts) < 2) {
            return $paths;
        }
        $roots[] = $parts[0];
    }
    if (count(array_unique($roots)) !== 1) {
        return $paths;
    }
    return array_map(function ($path) {
        $parts = explode('/', $path);
        array_shift($parts);
        return implode('/', $parts);
    }, $paths);
}

function ensure_supported_package_file($path) {
    if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'md') {
        return false;
    }
    $allowed_ext = ['html', 'css', 'js', 'json', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'mp3', 'wav', 'ogg', 'txt'];
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext, true)) {
        fail_package("Unsupported file type: " . $ext);
    }
    return true;
}

function create_clean_package_dir($slug) {
    $base_dir = __DIR__ . '/games/custom';
    $target_dir = $base_dir . '/' . $slug;
    if (!is_dir($base_dir)) {
        mkdir($base_dir, 0755, true);
    }
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    return $target_dir;
}

function save_game_record($conn, $manifest, $slug, $target_dir) {
    $title = trim($manifest['title'] ?? '');
    $description = trim($manifest['description'] ?? '');
    $category = trim($manifest['category'] ?? 'Logic');
    $difficulty = trim($manifest['difficulty'] ?? 'Normal');
    $entry = package_safe_path($manifest['entry'] ?? 'index.html');
    $cover = package_safe_path($manifest['cover'] ?? '');
    $age = is_array($manifest['age'] ?? null) ? $manifest['age'] : [];
    $min_age = max(4, min(12, intval($age['min'] ?? 4)));
    $max_age = max($min_age, min(12, intval($age['max'] ?? 12)));
    $skills = is_array($manifest['skills'] ?? null) ? $manifest['skills'] : [];

    if ($title === '') {
        fail_package("title is required.");
    }
    if ($entry === '' || strtolower(pathinfo($entry, PATHINFO_EXTENSION)) !== 'html') {
        fail_package("entry must be a safe .html file path.");
    }
    if (!file_exists($target_dir . '/' . $entry)) {
        fail_package("entry file was not found in the package.");
    }
    if (!in_array($difficulty, ['Easy', 'Normal', 'Hard'], true)) {
        $difficulty = 'Normal';
    }

    $cover_path = '';
    if ($cover !== '' && file_exists($target_dir . '/' . $cover)) {
        $cover_path = 'games/custom/' . $slug . '/' . $cover;
    }

    $game_url = 'game_player.php?id=__PENDING__';
    $status = 'published';
    $manifest_json = json_encode($manifest, JSON_UNESCAPED_SLASHES);
    $package_path = 'games/custom/' . $slug;

    $stmt = $conn->prepare("INSERT INTO levels (level_name, category, level_description, difficulty, target_score, image_url, game_url, custom_slug, package_path, package_status, package_manifest) VALUES (?, ?, ?, ?, 0, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $title, $category, $description, $difficulty, $cover_path, $game_url, $slug, $package_path, $status, $manifest_json);
    if (!$stmt->execute()) {
        fail_package("Could not create game record.");
    }

    $game_id = intval($conn->insert_id);
    $stmt->close();

    $final_url = 'game_player.php?id=' . $game_id;
    $update = $conn->prepare("UPDATE levels SET game_url = ? WHERE id = ?");
    $update->bind_param("si", $final_url, $game_id);
    $update->execute();
    $update->close();

    pl_save_game_metadata($conn, $game_id, $skills, $min_age, $max_age);
}

function validate_unique_slug($conn, $slug) {
    $slug_check = $conn->prepare("SELECT id FROM levels WHERE custom_slug = ? LIMIT 1");
    $slug_check->bind_param("s", $slug);
    $slug_check->execute();
    if ($slug_check->get_result()->num_rows > 0) {
        $slug_check->close();
        fail_package("slug already exists.");
    }
    $slug_check->close();
}

function install_from_zip($conn, $zip_path) {
    if (!class_exists('ZipArchive')) {
        fail_package("Zip upload requires ZipArchive. Use unpacked file upload instead.");
    }

    $zip = new ZipArchive();
    if ($zip->open($zip_path) !== true) {
        fail_package("Could not open zip package.");
    }

    $manifest_raw = $zip->getFromName('manifest.json');
    if ($manifest_raw === false) {
        $zip->close();
        fail_package("manifest.json is required at the root of the package.");
    }

    $manifest = json_decode($manifest_raw, true);
    if (!is_array($manifest)) {
        $zip->close();
        fail_package("manifest.json is not valid JSON.");
    }

    $slug = pl_normalize_slug($manifest['slug'] ?? ($manifest['title'] ?? ''));
    validate_unique_slug($conn, $slug);
    $target_dir = create_clean_package_dir($slug);

    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);
        $normalized = package_safe_path($name);
        if ($normalized === '' || substr($normalized, -1) === '/') {
            continue;
        }
        if (!ensure_supported_package_file($normalized)) {
            continue;
        }
        $destination = $target_dir . '/' . $normalized;
        $destination_dir = dirname($destination);
        if (!is_dir($destination_dir)) {
            mkdir($destination_dir, 0755, true);
        }
        copy("zip://" . $zip_path . "#" . $name, $destination);
    }
    $zip->close();

    save_game_record($conn, $manifest, $slug, $target_dir);
}

function install_from_uploaded_files($conn, $files) {
    $items = [];
    $count = count($files['name'] ?? []);
    for ($i = 0; $i < $count; $i++) {
        if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            continue;
        }
        $raw_path = $files['full_path'][$i] ?? $files['name'][$i];
        $safe_path = package_safe_path($raw_path);
        if ($safe_path === '') {
            fail_package("Unsafe file path found.");
        }
        $items[] = [
            'path' => $safe_path,
            'tmp_name' => $files['tmp_name'][$i]
        ];
    }

    if (empty($items)) {
        fail_package("No unpacked package files uploaded.");
    }

    $paths = strip_common_root_folder(array_column($items, 'path'));
    foreach ($items as $index => $item) {
        $items[$index]['path'] = $paths[$index];
        if (!ensure_supported_package_file($items[$index]['path'])) {
            unset($items[$index]);
        }
    }
    $items = array_values($items);

    $manifest_item = null;
    foreach ($items as $item) {
        if ($item['path'] === 'manifest.json') {
            $manifest_item = $item;
            break;
        }
    }
    if (!$manifest_item) {
        fail_package("manifest.json is required in the selected files.");
    }

    $manifest_raw = file_get_contents($manifest_item['tmp_name']);
    $manifest = json_decode($manifest_raw, true);
    if (!is_array($manifest)) {
        fail_package("manifest.json is not valid JSON.");
    }

    $slug = pl_normalize_slug($manifest['slug'] ?? ($manifest['title'] ?? ''));
    validate_unique_slug($conn, $slug);
    $target_dir = create_clean_package_dir($slug);

    foreach ($items as $item) {
        $destination = $target_dir . '/' . $item['path'];
        $destination_dir = dirname($destination);
        if (!is_dir($destination_dir)) {
            mkdir($destination_dir, 0755, true);
        }
        if (!move_uploaded_file($item['tmp_name'], $destination)) {
            fail_package("Could not save uploaded file: " . $item['path']);
        }
    }

    save_game_record($conn, $manifest, $slug, $target_dir);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    fail_package("Invalid request method.");
}

if (!empty($_FILES['game_package']['tmp_name'])) {
    install_from_zip($conn, $_FILES['game_package']['tmp_name']);
} elseif (!empty($_FILES['game_files']['name'][0])) {
    install_from_uploaded_files($conn, $_FILES['game_files']);
} else {
    fail_package("Upload a zip package or unpacked package files.");
}

header("Location: admin_games.php?msg=package_uploaded");
exit();
?>
