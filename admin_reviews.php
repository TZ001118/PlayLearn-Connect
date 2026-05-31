<?php
session_start();
require 'db_conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$current_page = 'reviews';
$filter_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$filter_game_id = isset($_GET['game_id']) ? intval($_GET['game_id']) : 0;
$filter_label = '';

$where = [];
$types = '';
$params = [];
if ($filter_user_id > 0) {
    $where[] = 'gr.user_id = ?';
    $types .= 'i';
    $params[] = $filter_user_id;
}
if ($filter_game_id > 0) {
    $where[] = 'gr.game_id = ?';
    $types .= 'i';
    $params[] = $filter_game_id;
}
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$stats = [
    'total_reviews' => 0,
    'student_reviews' => 0,
    'parent_reviews' => 0,
    'invalid_reviews' => 0,
    'average_rating' => null,
];
$stats_res = $conn->query("SELECT 
    COUNT(*) AS total_reviews,
    SUM(rating_type = 'student') AS student_reviews,
    SUM(rating_type = 'parent') AS parent_reviews,
    SUM(rating_type NOT IN ('student','parent') OR rating_type IS NULL OR rating_type = '') AS invalid_reviews,
    AVG(rating_score) AS average_rating
    FROM game_reviews");
if ($stats_res) {
    $stats = array_merge($stats, $stats_res->fetch_assoc());
}

$sql = "SELECT gr.*, u.email, u.role, l.level_name
        FROM game_reviews gr
        LEFT JOIN users u ON u.id = gr.user_id
        LEFT JOIN levels l ON l.id = gr.game_id
        $where_sql
        ORDER BY gr.created_at DESC, gr.id DESC";

if ($params) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $reviews = $stmt->get_result();
} else {
    $reviews = $conn->query($sql);
}

if ($filter_user_id > 0) {
    $label_stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $label_stmt->bind_param("i", $filter_user_id);
    $label_stmt->execute();
    $label_user = $label_stmt->get_result()->fetch_assoc();
    $filter_label = 'User: ' . ($label_user['username'] ?? ('#' . $filter_user_id));
    $label_stmt->close();
} elseif ($filter_game_id > 0) {
    $label_stmt = $conn->prepare("SELECT level_name FROM levels WHERE id = ?");
    $label_stmt->bind_param("i", $filter_game_id);
    $label_stmt->execute();
    $label_game = $label_stmt->get_result()->fetch_assoc();
    $filter_label = 'Game: ' . ($label_game['level_name'] ?? ('#' . $filter_game_id));
    $label_stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PlayLearn Admin | Reviews</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="css/admin_global.css?v=<?php echo time(); ?>">
    <style>
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .stat-card {
            background: var(--card-dark);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 16px;
        }

        .stat-card span {
            color: var(--text-gray);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .stat-card strong {
            display: block;
            margin-top: 8px;
            font-size: 24px;
        }

        .type-pill {
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
        }

        .type-student {
            background: rgba(255, 155, 107, 0.18);
            color: #ff9b6b;
        }

        .type-parent {
            background: rgba(108, 63, 245, 0.18);
            color: #8f72ff;
        }

        .type-invalid {
            background: rgba(255, 77, 77, 0.18);
            color: #ff7675;
        }
    </style>
</head>
<body>
    <?php include('includes/sidebar.php'); ?>

    <main class="main-content">
        <div class="header-row">
            <h2>Review System Monitor</h2>
            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'review_deleted'): ?>
                <div style="background: rgba(76, 175, 80, 0.2); color: #4caf50; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
                    <i class="fas fa-check-circle"></i> Review deleted successfully.
                </div>
            <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'delete_failed'): ?>
                <div style="background: rgba(255, 77, 77, 0.2); color: #ff7675; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
                    <i class="fas fa-exclamation-circle"></i> Review deletion failed.
                </div>
            <?php endif; ?>
            <?php if ($filter_label): ?>
                <div style="background: rgba(9, 132, 227, 0.18); color: #74b9ff; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
                    <i class="fas fa-filter"></i> Showing reviews for <?php echo htmlspecialchars($filter_label); ?>.
                    <a href="admin_reviews.php" style="color:#fff; margin-left:10px; font-weight:800;">Show all</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="stat-grid">
            <div class="stat-card"><span>Total Reviews</span><strong><?php echo intval($stats['total_reviews']); ?></strong></div>
            <div class="stat-card"><span>Student Reviews</span><strong><?php echo intval($stats['student_reviews']); ?></strong></div>
            <div class="stat-card"><span>Parent Reviews</span><strong><?php echo intval($stats['parent_reviews']); ?></strong></div>
            <div class="stat-card"><span>Invalid Type Rows</span><strong><?php echo intval($stats['invalid_reviews']); ?></strong></div>
            <div class="stat-card"><span>Average Rating</span><strong><?php echo $stats['average_rating'] !== null ? number_format(floatval($stats['average_rating']), 1) : 'N/A'; ?></strong></div>
        </div>

        <div class="content-card">
            <span class="card-title">All Reviews</span>
            <table id="reviewsTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Game</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $reviews->fetch_assoc()): ?>
                        <?php
                        $type = $row['rating_type'];
                        $type_class = in_array($type, ['student', 'parent'], true) ? ('type-' . $type) : 'type-invalid';
                        $type_label = in_array($type, ['student', 'parent'], true) ? ucfirst($type) : 'Invalid';
                        ?>
                        <tr>
                            <td>#<?php echo intval($row['id']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($row['level_name'] ?: ('Game #' . $row['game_id'])); ?><br>
                                <a href="admin_reviews.php?game_id=<?php echo intval($row['game_id']); ?>" style="font-size:11px;">Filter game</a>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($row['username']); ?><br>
                                <span style="color:#888; font-size:11px;"><?php echo htmlspecialchars($row['role'] ?: 'Unknown role'); ?></span>
                            </td>
                            <td><span class="type-pill <?php echo $type_class; ?>"><?php echo $type_label; ?></span></td>
                            <td><?php echo htmlspecialchars($row['rating_score']); ?></td>
                            <td><?php echo htmlspecialchars($row['comment_text']); ?></td>
                            <td><?php echo date('M d, Y - H:i', strtotime($row['created_at'])); ?></td>
                            <td>
                                <button
                                    type="button"
                                    class="studio-btn"
                                    style="background:#e74c3c;"
                                    data-review-id="<?php echo intval($row['id']); ?>"
                                    data-review-label="#<?php echo intval($row['id']); ?>"
                                    onclick="openDeleteReviewModal(this)">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="deleteReviewModal" class="modal-overlay">
        <div class="modal-content" style="width: min(420px, 92vw);">
            <div class="modal-header">
                <h3><i class="fas fa-trash-alt"></i> Delete Review</h3>
                <span class="close-btn" onclick="closeDeleteReviewModal()">&times;</span>
            </div>
            <form method="POST" action="process_delete_review.php">
                <input type="hidden" name="review_id" id="deleteReviewId">
                <div class="modal-body">
                    <p style="margin-top:0;">This review will be removed from the admin monitor.</p>
                    <p style="color:#ff7675; font-weight:800;" id="deleteReviewLabel"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeDeleteReviewModal()">Cancel</button>
                    <button type="submit" class="studio-btn" style="background:#e74c3c;">Delete</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#reviewsTable').DataTable({
                pageLength: 15,
                order: [[0, 'desc']],
                responsive: true
            });
        });

        function openDeleteReviewModal(button) {
            document.getElementById('deleteReviewId').value = button.getAttribute('data-review-id');
            document.getElementById('deleteReviewLabel').textContent = 'Review ' + button.getAttribute('data-review-label');
            document.getElementById('deleteReviewModal').style.display = 'flex';
        }

        function closeDeleteReviewModal() {
            document.getElementById('deleteReviewModal').style.display = 'none';
        }
    </script>
</body>
</html>
