<?php
session_start();
require 'db_conn.php';
require_once 'includes/learning_helpers.php';
pl_ensure_learning_schema($conn);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$filter_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$filter_username = '';
if ($filter_user_id > 0) {
    $filter_stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $filter_stmt->bind_param("i", $filter_user_id);
    $filter_stmt->execute();
    $filter_user = $filter_stmt->get_result()->fetch_assoc();
    $filter_username = $filter_user['username'] ?? '';
    $filter_stmt->close();
}

$current_page = 'scores';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PlayLearn Admin | Player Scores</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="css/admin_global.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include('includes/sidebar.php'); ?>

    <main class="main-content">
        <div class="header-row">
            <h2>Player Game Records</h2>
            <?php if($filter_user_id > 0): ?>
                <div style="background: rgba(9, 132, 227, 0.18); color: #74b9ff; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
                    <i class="fas fa-filter"></i> Showing records for <?php echo htmlspecialchars($filter_username ?: ('User #' . $filter_user_id)); ?>.
                    <a href="admin_scores.php" style="color:#fff; margin-left:10px; font-weight:800;">Show all</a>
                </div>
            <?php endif; ?>
            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'cleared'): ?>
                <div style="background: rgba(76, 175, 80, 0.2); color: #4caf50; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
                    <i class="fas fa-check-circle"></i> All game records have been cleared successfully.
                </div>
            <?php endif; ?>
            <button class="studio-btn" style="background:#ff4d4d;" onclick="openClearRecordsModal()">
                <i class="fas fa-trash"></i> Clear Old Records
            </button>
        </div>

        <div id="scoreModal" class="modal-overlay">
            <div class="modal-content" style="width: 480px;">
                <div class="modal-header">
                    <h3><i class="fas fa-search-plus"></i> Record Inspection</h3>
                    <span class="close-btn" onclick="closeScoreModal()">&times;</span>
                </div>
                <div class="modal-body" id="scoreDetails" style="line-height: 2;"></div>
                <div class="modal-footer">
                    <button class="studio-btn" style="background:#444;" onclick="closeScoreModal()">Close</button>
                </div>
            </div>
        </div>

        <div id="clearRecordsModal" class="modal-overlay">
            <div class="modal-content" style="width: 440px;">
                <div class="modal-header">
                    <h3><i class="fas fa-trash"></i> Clear Game Records</h3>
                    <span class="close-btn" onclick="closeClearRecordsModal()">&times;</span>
                </div>
                <div class="modal-body">
                    <p style="margin-top:0;">This will permanently delete all player game records.</p>
                    <p style="color:#ff7675; font-weight:800;">Please continue only when old demo data is no longer needed.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeClearRecordsModal()">Cancel</button>
                    <a class="studio-btn" style="background:#ff4d4d; text-decoration:none;" href="process_clear_scores.php">Clear Records</a>
                </div>
            </div>
        </div>

        <div class="content-card">
            <span class="card-title">All Game Scores</span>
            <table id="scoresTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Record ID</th>
                        <th>Player Name</th>
                        <th>Game Played</th>
                        <th>Level</th>
                        <th>Score / Moves</th>
                        <th>Accuracy</th>
                        <th>Reaction</th>
                        <th>Played At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($filter_user_id > 0) {
                        $stmt = $conn->prepare("SELECT s.*, u.username FROM game_scores s JOIN users u ON s.user_id = u.id WHERE s.user_id = ? ORDER BY s.id DESC");
                        $stmt->bind_param("i", $filter_user_id);
                        $stmt->execute();
                        $res = $stmt->get_result();
                    } else {
                        $res = $conn->query("SELECT s.*, u.username FROM game_scores s JOIN users u ON s.user_id = u.id ORDER BY s.id DESC");
                    }
                    while($row = $res->fetch_assoc()):
                        $accuracy = null;
                        if (isset($row['correct_answers'], $row['total_questions']) && intval($row['total_questions']) > 0) {
                            $accuracy = round((intval($row['correct_answers']) / intval($row['total_questions'])) * 100);
                        }
                    ?>
                    <tr>
                        <td>#<?php echo intval($row['id']); ?></td>
                        <td><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['game_name']); ?></td>
                        <td>
                            <span style="background: #333; padding: 2px 8px; border-radius: 4px; color: #aaa; font-size: 12px;">
                                Lvl <?php echo intval($row['level_reached'] ?? 1); ?>
                            </span>
                        </td>
                        <td><strong><?php echo intval($row['score']); ?></strong></td>
                        <td><?php echo $accuracy !== null ? ($accuracy . '%') : '<span style="color:#888;">N/A</span>'; ?></td>
                        <td><?php echo !empty($row['reaction_time_ms']) ? (intval($row['reaction_time_ms']) . ' ms') : '<span style="color:#888;">N/A</span>'; ?></td>
                        <td><?php echo date('M d, Y - H:i', strtotime($row['played_at'])); ?></td>
                        <td>
                            <button class="studio-btn" style="padding: 2px 8px; font-size: 11px; background: #444; border: none; color: #eee; cursor: pointer;" onclick="inspectRecord(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                Inspect
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#scoresTable').DataTable({
                pageLength: 15,
                order: [[0, 'desc']],
                responsive: true
            });
        });

        function esc(value) {
            return String(value || '').replace(/[&<>"']/g, function(char) {
                var entities = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };

                return entities[char];
            });
        }

        function metricText(rowData) {
            const parts = [];
            const correct = Number(rowData.correct_answers);
            const total = Number(rowData.total_questions);
            if (total > 0) {
                parts.push(`Accuracy: ${Math.round((correct / total) * 100)}% (${correct}/${total})`);
            }
            if (rowData.reaction_time_ms) {
                parts.push(`Reaction: ${rowData.reaction_time_ms} ms`);
            }
            if (rowData.duration_seconds) {
                parts.push(`Duration: ${rowData.duration_seconds} s`);
            }
            return parts.length ? parts.join('<br>') : 'No extended metrics recorded.';
        }

        function inspectRecord(rowData) {
            const time = new Date(rowData.played_at).toLocaleString();
            const details = `
                <div style="text-align: center; margin-bottom: 20px;">
                    <i class="fas fa-trophy" style="font-size: 40px; color: #f1c40f;"></i>
                </div>
                <p><strong>Record ID:</strong> <span style="color:#888;">#${esc(rowData.id)}</span></p>
                <p><strong>Player:</strong> <span style="color:#4A90E2; font-weight:bold;">${esc(rowData.username)}</span></p>
                <p><strong>Game:</strong> ${esc(rowData.game_name)}</p>
                <p><strong>Level Reached:</strong> <span style="color:#aaa;">Lvl ${esc(rowData.level_reached || 1)}</span></p>
                <p><strong>Final Result:</strong> <span style="font-size: 20px; color: #4caf50; font-weight:bold;">${esc(rowData.score)}</span></p>
                <p><strong>Learning Metrics:</strong><br><span style="font-size: 13px;">${metricText(rowData)}</span></p>
                <p><strong>Timestamp:</strong> <span style="font-size: 13px;">${esc(time)}</span></p>
                <hr style="border:0; border-top:1px solid #333; margin: 15px 0;">
                <p style="font-size: 11px; color: #666; font-style: italic;">Verified by PlayLearn Security System</p>
            `;
            document.getElementById('scoreDetails').innerHTML = details;
            document.getElementById('scoreModal').style.display = 'flex';
        }

        function closeScoreModal() {
            document.getElementById('scoreModal').style.display = 'none';
        }

        function openClearRecordsModal() {
            document.getElementById('clearRecordsModal').style.display = 'flex';
        }

        function closeClearRecordsModal() {
            document.getElementById('clearRecordsModal').style.display = 'none';
        }

        $(document).on('draw.dt', function(e, settings) {
            var api = new $.fn.dataTable.Api(settings);
            var $paginate = $(settings.nTableWrapper).find('.dataTables_paginate');
            var info = api.page.info();

            if ($paginate.find('.custom-page-jump').length === 0) {
                var jumpHtml = '<span class="custom-page-jump" style="margin-right: 15px; font-weight: 600; color: var(--text-main);">' +
                               'Page <input type="number" min="1" max="' + info.pages + '" class="page-jump-input" ' +
                               'style="width: 55px; padding: 3px 6px; margin: 0 5px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--card-dark); color: var(--text-main); text-align: center; outline: none; transition: 0.2s;">' +
                               '</span>';

                $paginate.prepend(jumpHtml);
                $paginate.find('.page-jump-input').on('keyup change', function(e) {
                    if (e.type === 'keyup' && e.which !== 13) return;
                    var page = parseInt($(this).val(), 10);
                    var currentInfo = api.page.info();
                    if (page > 0 && page <= currentInfo.pages) {
                        api.page(page - 1).draw('page');
                    } else if ($(this).val() !== '') {
                        $(this).val(currentInfo.page + 1);
                    }
                });
            }

            $paginate.find('.page-jump-input').val(info.page + 1).attr('max', info.pages);
        });
    </script>
</body>
</html>
