<?php
session_start();
require 'db_conn.php';
require_once 'includes/learning_helpers.php';
pl_ensure_learning_schema($conn);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$current_page = 'users';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PlayLearn Admin | User Analytics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="css/admin_global.css?v=<?php echo time(); ?>">
    <style>
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-active {
            background: rgba(76, 175, 80, 0.2);
            color: #4caf50;
        }

        .badge-banned {
            background: rgba(255, 77, 77, 0.2);
            color: #ff4d4d;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 140px 1fr;
            gap: 10px 16px;
        }

        .detail-grid strong {
            color: var(--text-muted, #888);
        }
        .ban-form textarea {
            width: 100%;
            min-height: 110px;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid var(--border-color, #333);
            background: var(--card-dark, #151515);
            color: var(--text-main, #fff);
            resize: vertical;
            box-sizing: border-box;
        }
        .detail-section {
            margin-top: 18px;
            padding-top: 14px;
            border-top: 1px solid var(--border-color, #333);
        }
        .detail-section h4 {
            margin: 0 0 10px;
            font-size: 14px;
        }
        .mini-list {
            display: grid;
            gap: 8px;
        }
        .mini-item {
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--border-color, #333);
            border-radius: 10px;
            padding: 10px;
            font-size: 12px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <?php include('includes/sidebar.php'); ?>

    <main class="main-content">
        <div class="header-row">
            <h2>User Analytics & Management</h2>
        </div>

        <div class="content-card">
            <span class="card-title">All Registered Users</span>
            <table id="analyticsTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Player Info</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Parent Link</th>
                        <th>Joined Date</th>
                        <th>Account Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT u.id, u.username, u.email, u.role, u.created_at, u.last_seen, u.status,
                                     u.birthday, u.gender, u.ic_number, u.ban_reason, u.banned_at,
                                     GROUP_CONCAT(DISTINCT CONCAT(p.id, ': ', p.username) ORDER BY p.username SEPARATOR ', ') AS parent_names,
                                     (
                                        SELECT GROUP_CONCAT(CONCAT(c.id, ': ', c.username) ORDER BY c.username SEPARATOR ', ')
                                        FROM account_links al_child
                                        JOIN users c ON c.id = al_child.child_id
                                        WHERE al_child.parent_id = u.id
                                     ) AS child_names
                              FROM users u
                              LEFT JOIN account_links al ON al.child_id = u.id
                              LEFT JOIN users p ON p.id = al.parent_id
                              GROUP BY u.id, u.username, u.email, u.role, u.created_at, u.last_seen, u.status,
                                       u.birthday, u.gender, u.ic_number, u.ban_reason, u.banned_at
                              ORDER BY u.id DESC";
                    $result = $conn->query($query);

                    $score_stmt = $conn->prepare("SELECT game_name, score, level_reached, played_at, correct_answers, total_questions, reaction_time_ms, duration_seconds FROM game_scores WHERE user_id = ? ORDER BY played_at DESC LIMIT 5");
                    $score_count_stmt = $conn->prepare("SELECT COUNT(*) AS total, MAX(played_at) AS last_played FROM game_scores WHERE user_id = ?");
                    $review_stmt = $conn->prepare("SELECT gr.rating_type, gr.rating_score, gr.comment_text, gr.created_at, l.level_name FROM game_reviews gr LEFT JOIN levels l ON l.id = gr.game_id WHERE gr.user_id = ? ORDER BY gr.created_at DESC LIMIT 3");
                    $review_count_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM game_reviews WHERE user_id = ?");

                    while($user = $result->fetch_assoc()):
                        $last_seen = !empty($user['last_seen']) ? strtotime($user['last_seen']) : 0;
                        $diff = $last_seen > 0 ? time() - $last_seen : PHP_INT_MAX;
                        $is_online = ($diff <= 300 && $user['status'] !== 'banned');
                        $parent_link = !empty($user['parent_names']) ? $user['parent_names'] : 'Unlinked';
                        $child_link = !empty($user['child_names']) ? $user['child_names'] : 'No linked children';
                        $user_id_int = intval($user['id']);

                        $score_count_stmt->bind_param("i", $user_id_int);
                        $score_count_stmt->execute();
                        $score_summary = $score_count_stmt->get_result()->fetch_assoc() ?: ['total' => 0, 'last_played' => null];

                        $score_stmt->bind_param("i", $user_id_int);
                        $score_stmt->execute();
                        $recent_scores = [];
                        $score_res = $score_stmt->get_result();
                        while ($score = $score_res->fetch_assoc()) {
                            $recent_scores[] = $score;
                        }

                        $review_count_stmt->bind_param("i", $user_id_int);
                        $review_count_stmt->execute();
                        $review_summary = $review_count_stmt->get_result()->fetch_assoc() ?: ['total' => 0];

                        $review_stmt->bind_param("i", $user_id_int);
                        $review_stmt->execute();
                        $recent_reviews = [];
                        $review_res = $review_stmt->get_result();
                        while ($review = $review_res->fetch_assoc()) {
                            $recent_reviews[] = $review;
                        }
                    ?>
                    <tr>
                        <td><?php echo intval($user['id']); ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($user['username']); ?></strong><br>
                            <?php if ($is_online): ?>
                                <span style="color: #00ff00; font-size: 10px;"><i class="fas fa-circle"></i> Online</span>
                            <?php else: ?>
                                <span style="color: #888; font-size: 10px;"><i class="far fa-circle"></i> Last seen: <?php echo $last_seen > 0 ? date('M d, H:i', $last_seen) : 'Never'; ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($user['role'])); ?></td>
                        <td>
                            <?php if ($user['role'] === 'player'): ?>
                                <?php if (!empty($user['parent_names'])): ?>
                                    <span class="badge badge-active"><?php echo htmlspecialchars($user['parent_names']); ?></span>
                                <?php else: ?>
                                    <span class="badge badge-banned">UNLINKED</span>
                                <?php endif; ?>
                            <?php elseif ($user['role'] === 'parent'): ?>
                                <span class="badge badge-active"><?php echo htmlspecialchars($child_link); ?></span>
                            <?php else: ?>
                                <span style="color:#888; font-size:12px;">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                        <td>
                            <span class="badge <?php echo ($user['status'] == 'active') ? 'badge-active' : 'badge-banned'; ?>">
                                <?php echo strtoupper(htmlspecialchars($user['status'])); ?>
                            </span>
                        </td>
                        <td>
                            <button
                                class="studio-btn"
                                style="background:#333; margin-right:5px;"
                                data-id="<?php echo intval($user['id']); ?>"
                                data-username="<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>"
                                data-email="<?php echo htmlspecialchars($user['email'], ENT_QUOTES); ?>"
                                data-role="<?php echo htmlspecialchars(ucfirst($user['role']), ENT_QUOTES); ?>"
                                data-parent-link="<?php echo htmlspecialchars($parent_link, ENT_QUOTES); ?>"
                                data-children="<?php echo htmlspecialchars($child_link, ENT_QUOTES); ?>"
                                data-birthday="<?php echo htmlspecialchars($user['birthday'] ?: 'Not set', ENT_QUOTES); ?>"
                                data-gender="<?php echo htmlspecialchars($user['gender'] ?: 'Not set', ENT_QUOTES); ?>"
                                data-ic="<?php echo htmlspecialchars($user['ic_number'] ?: 'Not set', ENT_QUOTES); ?>"
                                data-joined="<?php echo htmlspecialchars(date('Y-m-d', strtotime($user['created_at'])), ENT_QUOTES); ?>"
                                data-status="<?php echo htmlspecialchars(strtoupper($user['status']), ENT_QUOTES); ?>"
                                data-ban-reason="<?php echo htmlspecialchars($user['ban_reason'] ?: 'None', ENT_QUOTES); ?>"
                                data-banned-at="<?php echo htmlspecialchars($user['banned_at'] ?: 'None', ENT_QUOTES); ?>"
                                data-score-total="<?php echo intval($score_summary['total']); ?>"
                                data-last-played="<?php echo htmlspecialchars($score_summary['last_played'] ?: 'None', ENT_QUOTES); ?>"
                                data-review-total="<?php echo intval($review_summary['total']); ?>"
                                data-recent-scores="<?php echo htmlspecialchars(json_encode($recent_scores), ENT_QUOTES); ?>"
                                data-recent-reviews="<?php echo htmlspecialchars(json_encode($recent_reviews), ENT_QUOTES); ?>"
                                onclick="viewUserDetails(this)"
                            >View</button>

                            <?php if ($user['status'] == 'active'): ?>
                                <button class="studio-btn" style="background:#ff4d4d;" onclick="openBanModal(<?php echo intval($user['id']); ?>, '<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>')">Ban</button>
                            <?php else: ?>
                                <a href="process_ban.php?id=<?php echo intval($user['id']); ?>&action=unban" class="studio-btn" style="background:#4caf50; text-decoration:none;">Unban</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php
                    $score_stmt->close();
                    $score_count_stmt->close();
                    $review_stmt->close();
                    $review_count_stmt->close();
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="userModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-id-card"></i> User Detail Report</h3>
                <span class="close-btn" onclick="closeModal('userModal')">&times;</span>
            </div>
            <div class="modal-body" id="modalData"></div>
            <div class="modal-footer">
                <button class="studio-btn" style="background:#444;" onclick="closeModal('userModal')">Dismiss</button>
            </div>
        </div>
    </div>

    <div id="banModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-ban"></i> Ban Account</h3>
                <span class="close-btn" onclick="closeModal('banModal')">&times;</span>
            </div>
            <form class="modal-body ban-form" method="POST" action="process_ban.php">
                <input type="hidden" name="id" id="banUserId">
                <input type="hidden" name="action" value="ban">
                <p id="banTargetText" style="margin-top:0;"></p>
                <label for="banReason"><strong>Reason</strong></label>
                <textarea id="banReason" name="ban_reason" maxlength="255" placeholder="Explain why this account is being banned." required></textarea>
                <div class="modal-footer">
                    <button type="button" class="studio-btn" style="background:#444;" onclick="closeModal('banModal')">Cancel</button>
                    <button type="submit" class="studio-btn" style="background:#ff4d4d;">Confirm Ban</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#analyticsTable').DataTable({
                pageLength: 10,
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

        function parseJsonList(value) {
            try {
                var parsed = JSON.parse(value || '[]');
                return Array.isArray(parsed) ? parsed : [];
            } catch (error) {
                return [];
            }
        }

        function scoreMeta(item) {
            var parts = [];
            if (item.correct_answers !== null && item.total_questions !== null && Number(item.total_questions) > 0) {
                parts.push(`Accuracy ${Math.round((Number(item.correct_answers) / Number(item.total_questions)) * 100)}%`);
            }
            if (item.reaction_time_ms !== null && item.reaction_time_ms !== '') {
                parts.push(`Reaction ${item.reaction_time_ms} ms`);
            }
            if (item.duration_seconds !== null && item.duration_seconds !== '') {
                parts.push(`Duration ${item.duration_seconds} s`);
            }
            return parts.length ? parts.join(' - ') : 'No extended metrics';
        }

        function recentScoresHtml(scores) {
            if (!scores.length) {
                return '<div class="mini-item">No game records yet.</div>';
            }
            var html = '';
            for (var index = 0; index < scores.length; index += 1) {
                var item = scores[index];
                html += `
                    <div class="mini-item">
                        <strong>${esc(item.game_name)}</strong> - Score ${esc(item.score)} - Level ${esc(item.level_reached || 1)}<br>
                        <span style="color:#888;">${esc(item.played_at)} - ${esc(scoreMeta(item))}</span>
                    </div>
                `;
            }
            return html;
        }

        function recentReviewsHtml(reviews) {
            if (!reviews.length) {
                return '<div class="mini-item">No reviews submitted yet.</div>';
            }
            var html = '';
            for (var index = 0; index < reviews.length; index += 1) {
                var item = reviews[index];
                html += `
                    <div class="mini-item">
                        <strong>${esc(item.level_name || 'Unknown game')}</strong> - ${esc(item.rating_type || 'unknown')} - ${esc(item.rating_score)} stars<br>
                        <span>${esc(item.comment_text)}</span><br>
                        <span style="color:#888;">${esc(item.created_at)}</span>
                    </div>
                `;
            }
            return html;
        }

        function viewUserDetails(button) {
            var data = button.dataset;
            var scores = parseJsonList(data.recentScores);
            var reviews = parseJsonList(data.recentReviews);
            var content = `
                <div class="detail-grid">
                    <strong>User ID</strong><span>${esc(data.id)}</span>
                    <strong>Username</strong><span>${esc(data.username)}</span>
                    <strong>Email</strong><span>${esc(data.email)}</span>
                    <strong>Role</strong><span>${esc(data.role)}</span>
                    <strong>Gender</strong><span>${esc(data.gender)}</span>
                    <strong>Birthday</strong><span>${esc(data.birthday)}</span>
                    <strong>IC / ID</strong><span>${esc(data.ic)}</span>
                    <strong>Parent Link</strong><span>${esc(data.parentLink)}</span>
                    <strong>Linked Children</strong><span>${esc(data.children)}</span>
                    <strong>Joined Date</strong><span>${esc(data.joined)}</span>
                    <strong>Status</strong><span>${esc(data.status)}</span>
                    <strong>Ban Reason</strong><span>${esc(data.banReason)}</span>
                    <strong>Banned At</strong><span>${esc(data.bannedAt)}</span>
                    <strong>Game Records</strong><span>${esc(data.scoreTotal)} records - Last played: ${esc(data.lastPlayed)} - <a href="admin_scores.php?user_id=${encodeURIComponent(data.id)}" class="studio-btn" style="padding:4px 10px; text-decoration:none;">View Records</a></span>
                    <strong>Reviews</strong><span>${esc(data.reviewTotal)} reviews - <a href="admin_reviews.php?user_id=${encodeURIComponent(data.id)}" class="studio-btn" style="padding:4px 10px; text-decoration:none;">View Reviews</a></span>
                </div>
                <div class="detail-section">
                    <h4><i class="fas fa-history"></i> Recent Game Records</h4>
                    <div class="mini-list">${recentScoresHtml(scores)}</div>
                </div>
                <div class="detail-section">
                    <h4><i class="fas fa-comments"></i> Recent Reviews</h4>
                    <div class="mini-list">${recentReviewsHtml(reviews)}</div>
                </div>
            `;
            document.getElementById('modalData').innerHTML = content;
            document.getElementById('userModal').style.display = 'flex';
        }

        function openBanModal(id, username) {
            document.getElementById('banUserId').value = id;
            document.getElementById('banReason').value = '';
            document.getElementById('banTargetText').innerText = `Account: ${username}`;
            document.getElementById('banModal').style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
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
