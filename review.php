<?php
session_start();
require 'db_conn.php';

// 1. Validate arguments from URL
$game_id = isset($_GET['game_id']) ? intval($_GET['game_id']) : 0;
$initial_role = isset($_GET['role']) ? $_GET['role'] : 'student';
$initial_role = ($initial_role === 'parent') ? 'parent' : 'student';

if ($game_id === 0) {
    die("Invalid Game Session.");
}

// 2. Fetch current game details
$game_stmt = $conn->prepare("SELECT id, level_name, image_url, level_description FROM levels WHERE id = ?");
$game_stmt->bind_param("i", $game_id);
$game_stmt->execute();
$game_details = $game_stmt->get_result()->fetch_assoc();

if (!$game_details) {
    die("Game not found in database.");
}

// 3. Handle Form Submission (Save Review to Database)
$submit_success = false;
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic security guard: Ensure user is logged in
    if (!isset($_SESSION['user_id'])) {
        $error_msg = "Please log in first to submit a review.";
    } else {
        $user_id = intval($_SESSION['user_id']);
        $username = $_SESSION['username'];
        $form_role = $_POST['form_role'] ?? 'student';
        $rating = floatval($_POST['rating']);
        $comment = trim($_POST['comment']);

        // Double check permissions to prevent hacking
        if (!in_array($form_role, ['student', 'parent'], true)) {
            $error_msg = "Invalid review type.";
        } elseif (($form_role === 'student' && $_SESSION['role'] !== 'player') || 
            ($form_role === 'parent' && $_SESSION['role'] !== 'parent')) {
            $error_msg = "Unauthorized submission: Action does not match your account type.";
        } elseif ($rating < 1 || $rating > 5 || empty($comment)) {
            $error_msg = "Please provide both a valid rating and a comment.";
        } else {
            // Check if user has already reviewed this game under this category
            $check_stmt = $conn->prepare("SELECT id FROM game_reviews WHERE game_id = ? AND user_id = ? AND rating_type = ?");
            $check_stmt->bind_param("iis", $game_id, $user_id, $form_role);
            $check_stmt->execute();
            
            if ($check_stmt->get_result()->num_rows > 0) {
                // Update existing review
                $update_stmt = $conn->prepare("UPDATE game_reviews SET rating_score = ?, comment_text = ?, created_at = NOW() WHERE game_id = ? AND user_id = ? AND rating_type = ?");
                $update_stmt->bind_param("dsiis", $rating, $comment, $game_id, $user_id, $form_role);
                $update_stmt->execute();
            } else {
                // Insert brand new review
                $insert_stmt = $conn->prepare("INSERT INTO game_reviews (game_id, user_id, username, rating_type, rating_score, comment_text) VALUES (?, ?, ?, ?, ?, ?)");
                $insert_stmt->bind_param("iissds", $game_id, $user_id, $username, $form_role, $rating, $comment);
                $insert_stmt->execute();
            }
            $submit_success = true;
            // Set the active view to the role they just submitted
            $initial_role = $form_role;
        }
    }
}

// 4. Fetch all existing reviews from the database for this game
$student_reviews = [];
$parent_reviews = [];

$review_stmt = $conn->prepare("SELECT username, rating_type, rating_score, comment_text, created_at FROM game_reviews WHERE game_id = ? ORDER BY created_at DESC");
$review_stmt->bind_param("i", $game_id);
$review_stmt->execute();
$review_res = $review_stmt->get_result();

while($row = $review_res->fetch_assoc()) {
    if ($row['rating_type'] === 'student') {
        $student_reviews[] = $row;
    } elseif ($row['rating_type'] === 'parent') {
        $parent_reviews[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlayLearn | Game Hub Reviews</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Nunito', 'sans-serif'] },
                    colors: { primary: '#0f172a', blobPurple: '#6C3FF5', blobOrange: '#FF9B6B' }
                }
            }
        }
    </script>
    <style>
        .fade-in-up { animation: fadeInUp 0.5s cubic-bezier(0.165, 0.84, 0.44, 1) forwards; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        
        /* Star feedback selection style matrix */
        .star-rating { display: flex; flex-direction: row-reverse; justify-content: center; gap: 8px; }
        .star-rating input { display: none; }
        .star-rating label { font-size: 32px; color: #cbd5e1; cursor: pointer; transition: all 0.2s; }
        .star-rating label:hover, .star-rating label:hover ~ label, .star-rating input:checked ~ label { color: #f59e0b; transform: scale(1.1); }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased min-h-screen pt-20 pb-12">

    <header class="bg-white/80 backdrop-blur-md fixed w-full top-0 z-40 border-b border-slate-200 h-16 flex items-center px-6 justify-between">
        <a href="homepage.php" class="text-2xl font-black tracking-tight text-primary">Play<span class="text-blobOrange">Learn</span> Hub</a>
        <a href="homepage.php" class="text-slate-500 font-bold hover:text-primary transition-colors flex items-center gap-2"><i class="fas fa-chevron-left"></i> Back to Hub</a>
    </header>

    <div class="max-w-4xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 sticky top-24 text-center fade-in-up">
                <img src="<?php echo htmlspecialchars($game_details['image_url']); ?>" class="w-32 h-32 rounded-2xl object-cover mx-auto shadow-md mb-4">
                <h2 class="text-xl font-black text-primary mb-2"><?php echo htmlspecialchars($game_details['level_name']); ?></h2>
                <p class="text-xs text-slate-500 font-semibold leading-relaxed mb-4"><?php echo htmlspecialchars($game_details['level_description']); ?></p>
                
                <div class="flex justify-center gap-4 pt-4 border-t border-slate-100">
                    <div class="text-center">
                        <span class="text-xs font-bold text-slate-400 block">Kids Fun</span>
                        <span class="text-sm font-black text-blobOrange"><i class="fas fa-gamepad"></i> <?php echo count($student_reviews); ?> reviews</span>
                    </div>
                    <div class="text-center border-l border-slate-100 pl-4">
                        <span class="text-xs font-bold text-slate-400 block">Edu Value</span>
                        <span class="text-sm font-black text-blobPurple"><i class="fas fa-brain"></i> <?php echo count($parent_reviews); ?> reviews</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 flex flex-col gap-6 fade-in-up" style="animation-delay: 0.1s;">
            
            <?php if($submit_success): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl font-bold text-sm"><i class="fas fa-check-circle mr-2"></i> Your evaluation has been published successfully!</div>
            <?php  endif; if(!empty($error_msg)): ?>
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-2xl font-bold text-sm"><i class="fas fa-exclamation-circle mr-2"></i> <?php echo $error_msg; ?></div>
            <?php endif; ?>

            <div class="bg-white p-1.5 rounded-2xl shadow-sm border border-slate-100 flex gap-2">
                <button onclick="switchTab('student')" id="tabBtn-student" class="flex-1 py-3 rounded-xl font-black text-sm flex items-center justify-center gap-2 transition-all">
                    <i class="fas fa-gamepad"></i> Kids' Corner
                </button>
                <button onclick="switchTab('parent')" id="tabBtn-parent" class="flex-1 py-3 rounded-xl font-black text-sm flex items-center justify-center gap-2 transition-all">
                    <i class="fas fa-brain"></i> Parents' Corner
                </button>
            </div>

            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex-grow">
                
                <div id="panel-student" class="tab-panel hidden flex flex-col gap-4">
                    <h3 class="font-black text-lg text-primary mb-2">👦 Kids' Gameplay Experiences</h3>
                    <?php if(count($student_reviews) == 0): ?>
                        <p class="text-slate-400 text-sm font-bold py-6 text-center">No kids have shared their thoughts yet. Be the first!</p>
                    <?php else: foreach($student_reviews as $r): ?>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-black text-sm text-primary"><?php echo htmlspecialchars($r['username']); ?></span>
                                <span class="text-amber-500 text-xs font-black"><i class="fas fa-star"></i> <?php echo $r['rating_score']; ?></span>
                            </div>
                            <p class="text-slate-600 text-sm font-semibold leading-relaxed"><?php echo htmlspecialchars($r['comment_text']); ?></p>
                        </div>
                    <?php endforeach; endif; ?>
                </div>

                <div id="panel-parent" class="tab-panel hidden flex flex-col gap-4">
                    <h3 class="font-black text-lg text-primary mb-2">🧠 Parents' Educational Insights</h3>
                    <?php if(count($parent_reviews) == 0): ?>
                        <p class="text-slate-400 text-sm font-bold py-6 text-center">No educational reviews from parents yet.</p>
                    <?php else: foreach($parent_reviews as $r): ?>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-black text-sm text-primary"><?php echo htmlspecialchars($r['username']); ?></span>
                                <span class="text-amber-500 text-xs font-black"><i class="fas fa-star"></i> <?php echo $r['rating_score']; ?></span>
                            </div>
                            <p class="text-slate-600 text-sm font-semibold leading-relaxed"><?php echo htmlspecialchars($r['comment_text']); ?></p>
                        </div>
                    <?php endforeach; endif; ?>
                </div>

            </div>

            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <?php 
                $user_role = $_SESSION['role'] ?? '';
                ?>
                <div id="form-student-allowed" class="form-guard hidden">
                    <h4 class="font-black text-base text-primary mb-3">Share your gameplay experience!</h4>
                    <form action="review.php?game_id=<?php echo $game_id; ?>" method="POST" class="space-y-4">
                        <input type="hidden" name="form_role" value="student">
                        <div class="star-rating"><input type="radio" id="s5" name="rating" value="5" required/><label for="s5"><i class="fas fa-star"></i></label><input type="radio" id="s4" name="rating" value="4"/><label for="s4"><i class="fas fa-star"></i></label><input type="radio" id="s3" name="rating" value="3"/><label for="s3"><i class="fas fa-star"></i></label><input type="radio" id="s2" name="rating" value="2"/><label for="s2"><i class="fas fa-star"></i></label><input type="radio" id="s1" name="rating" value="1"/><label for="s1"><i class="fas fa-star"></i></label></div>
                        <textarea name="comment" rows="3" required placeholder="What did you love about this game?..." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm font-semibold outline-none focus:border-blobOrange focus:bg-white transition-colors resize-none"></textarea>
                        <button type="submit" class="w-full bg-blobOrange text-white font-black py-3 rounded-full shadow-lg shadow-blobOrange/20 transition-transform hover:-translate-y-0.5">Submit Fun Rating</button>
                    </form>
                </div>

                <div id="form-parent-allowed" class="form-guard hidden">
                    <h4 class="font-black text-base text-primary mb-3">Evaluate the educational value of this game</h4>
                    <form action="review.php?game_id=<?php echo $game_id; ?>" method="POST" class="space-y-4">
                        <input type="hidden" name="form_role" value="parent">
                        <div class="star-rating"><input type="radio" id="p5" name="rating" value="5" required/><label for="p5"><i class="fas fa-star"></i></label><input type="radio" id="p4" name="rating" value="4"/><label for="p4"><i class="fas fa-star"></i></label><input type="radio" id="p3" name="rating" value="3"/><label for="p3"><i class="fas fa-star"></i></label><input type="radio" id="p2" name="rating" value="2"/><label for="p2"><i class="fas fa-star"></i></label><input type="radio" id="p1" name="rating" value="1"/><label for="p1"><i class="fas fa-star"></i></label></div>
                        <textarea name="comment" rows="3" required placeholder="Describe what educational impact or skills this game reinforces..." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm font-semibold outline-none focus:border-blobPurple focus:bg-white transition-colors resize-none"></textarea>
                        <button type="submit" class="w-full bg-blobPurple text-white font-black py-3 rounded-full shadow-lg shadow-blobPurple/20 transition-transform hover:-translate-y-0.5">Submit Educational Report</button>
                    </form>
                </div>

                <div id="msg-student-blocked" class="form-guard hidden text-center py-2">
                    <p class="text-sm font-bold text-slate-400"><i class="fas fa-lock mr-1"></i> You are browsing the Kids' paradise. Parents can review under the Parents' Corner tab.</p>
                </div>
                <div id="msg-parent-blocked" class="form-guard hidden text-center py-2">
                    <p class="text-sm font-bold text-slate-400"><i class="fas fa-lock mr-1"></i> Only parents can post evaluations regarding industrial educational value parameters.</p>
                </div>
            </div>

        </div>
    </div>

    <script>
        const userRole = "<?php echo $user_role; ?>"; // 'player' or 'parent' or 'admin' or empty

        function switchTab(targetRole) {
            // 1. Reset all panels and buttons state
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
            document.querySelectorAll('.form-guard').forEach(f => f.classList.add('hidden'));
            
            const studentBtn = document.getElementById('tabBtn-student');
            const parentBtn = document.getElementById('tabBtn-parent');
            
            studentBtn.className = "flex-1 py-3 rounded-xl font-black text-sm flex items-center justify-center gap-2 transition-all bg-slate-100 text-slate-500";
            parentBtn.className = "flex-1 py-3 rounded-xl font-black text-sm flex items-center justify-center gap-2 transition-all bg-slate-100 text-slate-500";

            // 2. Active specified panel state
            document.getElementById('panel-' + targetRole).classList.remove('hidden');
            
            if (targetRole === 'student') {
                studentBtn.className = "flex-1 py-3 rounded-xl font-black text-sm flex items-center justify-center gap-2 transition-all bg-blobOrange text-white shadow-md shadow-blobOrange/20";
                
                // Form Guard Injection Matrix for Kids' corner
                if (userRole === 'player') {
                    document.getElementById('form-student-allowed').classList.remove('hidden');
                } else {
                    document.getElementById('msg-student-blocked').classList.remove('hidden');
                }
            } else {
                parentBtn.className = "flex-1 py-3 rounded-xl font-black text-sm flex items-center justify-center gap-2 transition-all bg-blobPurple text-white shadow-md shadow-blobPurple/20";
                
                // Form Guard Injection Matrix for Parents' corner
                if (userRole === 'parent') {
                    document.getElementById('form-parent-allowed').classList.remove('hidden');
                } else {
                    document.getElementById('msg-parent-blocked').classList.remove('hidden');
                }
            }
        }

        // Initialize view based on URL context smoothly
        document.addEventListener('DOMContentLoaded', () => {
            switchTab("<?php echo $initial_role; ?>");
        });
    </script>
</body>
</html>
