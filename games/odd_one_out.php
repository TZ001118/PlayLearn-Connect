<?php
session_start();
require '../db_conn.php';
include('../maintenance_check.php');
// 1. 先检查是否登录，没登录直接踢回登录页
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user_id'];

// 2. 实时检查该用户的状态是否被封禁
// 使用 prepare 语句更安全，防止 SQL 注入
$stmt = $conn->prepare("SELECT status FROM users WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$res = $stmt->get_result();
$u = $res->fetch_assoc();

if ($u && $u['status'] === 'banned') {
    session_destroy(); // 销毁所有登录信息
    header("Location: ../login.php?error=banned"); // 跳回登录页并带上错误提示
    exit();
}
// 更新玩家活跃时间
$conn->query("UPDATE users SET last_seen = NOW() WHERE id = " . $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>PlayLearn - Odd One Out</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0; padding: 0;
            display: flex; flex-direction: column; align-items: center;
            background-color: #f4f6f9; 
            font-family: 'Nunito', 'Segoe UI', Tahoma, sans-serif;
            min-height: 100vh;
            touch-action: manipulation; 
        }

        .header {
            width: 100%; max-width: 850px;
            display: flex; justify-content: space-between; align-items: center;
            padding: 20px 20px 10px 20px; box-sizing: border-box;
        }

        .back-btn {
            padding: 10px 20px; background-color: #ff6b6b; color: white;
            border: none; border-radius: 12px; font-size: 15px; cursor: pointer;
            font-weight: 800; box-shadow: 0 4px 10px rgba(255, 107, 107, 0.3); transition: 0.2s;
        }
        .back-btn:hover { transform: scale(1.05); }

        .brand-title { font-size: 38px; font-weight: 900; color: #2d3436; text-transform: uppercase; }
        .brand-title span { color: #0984e3; }

        .game-wrapper {
            width: 100%; max-width: 450px; 
            background-color: #ffffff; border-radius: 24px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.08); border: 3px solid #e1e5ee; 
            margin-top: 10px; padding: 20px; box-sizing: border-box;
            display: flex; flex-direction: column; align-items: center;
        }

        .status-bar {
            width: 100%; display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 25px;
        }
        #level-title { font-size: 32px; font-weight: 900; color: #00cec9; margin: 0; }
        
        .right-status { display: flex; gap: 10px; align-items: center; }
        .score-count {
            background-color: #ffeaa7; padding: 8px 15px; border-radius: 12px;
            font-weight: 800; font-size: 18px; color: #d63031;
        }
        .help-btn {
            background-color: #0984e3; padding: 8px 12px; border-radius: 12px;
            font-weight: 900; font-size: 18px; color: white; border: none; cursor: pointer;
            box-shadow: 0 4px 0 #74b9ff; transition: 0.1s;
        }
        .help-btn:active { transform: translateY(4px); box-shadow: none; }

        #game-board {
            display: grid;
            gap: 10px;
            width: 100%;
            aspect-ratio: 1 / 1;
            background-color: #e1e5ee;
            padding: 10px;
            border-radius: 20px;
            box-sizing: border-box;
        }

        .grid-btn {
            background-color: #ffffff;
            border: none;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 45px;
            cursor: pointer;
            box-shadow: 0 4px 0 #b2bec3;
            transition: transform 0.1s, box-shadow 0.1s;
            touch-action: manipulation;
        }
        .grid-btn:active { transform: translateY(4px); box-shadow: none; }
        .grid-btn.correct-anim { background-color: #00b894; box-shadow: 0 4px 0 #009477; }
        .grid-btn.wrong-anim { background-color: #ff7675; box-shadow: 0 4px 0 #d63031; }

        @media (max-width: 400px) {
            .grid-btn { font-size: 35px; border-radius: 8px; }
            #game-board { gap: 6px; padding: 6px; border-radius: 16px; }
        }

        .overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6); display: none; justify-content: center; align-items: center; z-index: 100;
        }
        .modal {
            background: #fff; padding: 40px; border-radius: 24px; text-align: center;
            max-width: 350px; width: 85%;
        }
        .modal h2 { font-size: 36px; margin: 0 0 10px 0; }
        
        .modal-buttons { display: flex; gap: 15px; justify-content: center; margin-top: 25px; }
        .modal-buttons button {
            padding: 12px 20px; border: none; border-radius: 12px;
            font-size: 16px; font-weight: bold; cursor: pointer; flex: 1;
        }
        .btn-main { background: #00b894; color: #fff; }
        .btn-exit { background: #ff7675; color: #fff; }

        .tutorial-modal {
            background: #ffffff; padding: 35px; border-radius: 24px; 
            max-width: 400px; width: 85%; text-align: left;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            border: 4px solid #00cec9;
        }
        .tutorial-modal h2 { font-size: 28px; color: #2d3436; margin-top: 0; margin-bottom: 25px; text-align: center; font-weight: 900; }
        .tut-step { display: flex; align-items: center; margin-bottom: 20px; background: #f4f6f9; padding: 15px; border-radius: 16px; }
        .tut-icon { font-size: 36px; margin-right: 15px; background: #fff; border-radius: 12px; padding: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .tut-text { font-size: 16px; color: #636e72; line-height: 1.4; }
        .tut-text b { color: #00cec9; font-size: 18px; display: block; margin-bottom: 2px; }
        .tut-btn { width: 100%; padding: 15px; background: #00cec9; color: white; border: none; border-radius: 16px; font-size: 22px; font-weight: 900; cursor: pointer; margin-top: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <button class="back-btn" onclick="window.history.back()">⬅ Back</button>
        <div class="brand-title">Play<span>Learn</span></div>
        <div style="width: 80px;"></div> 
    </div>

    <div class="game-wrapper">
        <div class="status-bar">
            <h2 id="level-title">LEVEL 1</h2>
            <div class="right-status">
                <button class="help-btn" onclick="showTutorial()">❓</button>
                <div class="score-count">🔍 <span id="score-display">0 / 5</span></div>
            </div>
        </div>

        <div id="game-board"></div>
    </div>

    <div id="overlay" class="overlay">
        <div class="modal">
            <h2 id="modal-icon">🎉</h2>
            <h3 id="modal-title">You Win!</h3>
            <div class="modal-buttons">
                <button onclick="exitGame()" class="btn-exit">Exit</button>
                <button onclick="nextAction()" id="modal-btn" class="btn-main">Next Level</button>
            </div>
        </div>
    </div>

    <div id="tutorial-overlay" class="overlay">
        <div class="tutorial-modal">
            <h2>How to Play 🔍</h2>
            <div class="tut-step">
                <div class="tut-icon">👀</div>
                <div class="tut-text"><b>Look Closely</b> All pictures look the same, but one is hiding!</div>
            </div>
            <div class="tut-step">
                <div class="tut-icon">🤔</div>
                <div class="tut-text"><b>Find the Different One</b> Spot the picture that doesn't belong.</div>
            </div>
            <div class="tut-step">
                <div class="tut-icon">👆</div>
                <div class="tut-text"><b>Tap It Fast</b> Tap the different picture to win a point.</div>
            </div>
            <button class="tut-btn" onclick="hideTutorial()">I Got It ! 🚀</button>
        </div>
    </div>

    <script>
        const levelsData = [
            { level: 1, size: 2, target: 3 },
            { level: 2, size: 3, target: 4 },
            { level: 3, size: 4, target: 4 },
            { level: 4, size: 5, target: 5 },
            { level: 5, size: 6, target: 5 }
        ];

        const emojiPairs = [
            ['😀', '😃'], ['🍎', '🍅'], ['🚗', '🚕'], ['🐶', '🐺'], 
            ['☀️', '🌤️'], ['🏀', '🏐'], ['🍉', '🍓'], ['🐢', '🦖'], 
            ['🌲', '🌳'], ['🍔', '🥪'], ['🐱', '🐯'], ['🌻', '🌼'],
            ['🌍', '🌎'], ['⚽', '⚾'], ['📙', '📘'], ['⏳', '⌛']
        ];

        let hasSeenTutorial = false;
        let currentLevelIndex = 0;
        let currentScore = 0;
        let targetScore = 0;
        let isProcessing = false;

        function showTutorial() {
            document.getElementById('tutorial-overlay').style.display = 'flex';
        }

        function hideTutorial() {
            document.getElementById('tutorial-overlay').style.display = 'none';
        }

        function exitGame() {
            window.location.href = '../homepage.php';
        }

        function start() {
            let lvlConfig = levelsData[currentLevelIndex];
            currentScore = 0;
            targetScore = lvlConfig.target;
            isProcessing = false;

            document.getElementById('level-title').innerText = `LEVEL ${lvlConfig.level}`;
            updateScore();
            generateGrid();

            if (currentLevelIndex === 0 && !hasSeenTutorial) {
                showTutorial();
                hasSeenTutorial = true;
            }
        }

        function generateGrid() {
            let lvlConfig = levelsData[currentLevelIndex];
            let size = lvlConfig.size;
            let total = size * size;
            let board = document.getElementById('game-board');
            
            board.style.gridTemplateColumns = `repeat(${size}, 1fr)`;
            board.innerHTML = '';

            let pair = emojiPairs[Math.floor(Math.random() * emojiPairs.length)];
            let normalEmoji = pair[0];
            let oddEmoji = pair[1];

            if (Math.random() > 0.5) {
                normalEmoji = pair[1];
                oddEmoji = pair[0];
            }

            let oddIndex = Math.floor(Math.random() * total);

            for (let i = 0; i < total; i++) {
                let btn = document.createElement('button');
                btn.className = 'grid-btn';
                btn.innerText = (i === oddIndex) ? oddEmoji : normalEmoji;
                let isOdd = (i === oddIndex);
                
                let dynamicFontSize = 45;
                if (size === 4) dynamicFontSize = 35;
                if (size === 5) dynamicFontSize = 28;
                if (size === 6) dynamicFontSize = 22;
                btn.style.fontSize = dynamicFontSize + 'px';

                btn.onclick = function() { handleTap(isOdd, this); };
                board.appendChild(btn);
            }
        }

        function handleTap(isOdd, btnElement) {
            if (isProcessing) return;
            isProcessing = true;

            if (isOdd) {
                btnElement.classList.add('correct-anim');
                currentScore++;
                updateScore();
                
                setTimeout(() => {
                    if (currentScore >= targetScore) {
                        showGameOver(true);
                    } else {
                        isProcessing = false;
                        generateGrid();
                    }
                }, 300);
            } else {
                btnElement.classList.add('wrong-anim');
                setTimeout(() => {
                    showGameOver(false);
                }, 400);
            }
        }

        function updateScore() {
            document.getElementById('score-display').innerText = `${currentScore} / ${targetScore}`;
        }

        function showGameOver(isWin) {
            const overlay = document.getElementById('overlay');
            const icon = document.getElementById('modal-icon');
            const title = document.getElementById('modal-title');
            const btn = document.getElementById('modal-btn');

            fetch('../save_score.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    game_name: 'Odd One Out', 
                    score: currentScore, 
                    level_reached: currentLevelIndex + 1 
                })
            }).then(r=>r.json()).then(d=>console.log(d));
            
            if (isWin) {
                icon.innerText = "🎉";
                title.innerText = "You Win!";
                title.style.color = "#00b894";
                if (currentLevelIndex < levelsData.length - 1) {
                    btn.innerText = "Next Level";
                } else {
                    btn.innerText = "Play Again";
                    title.innerText = "All Cleared!";
                }
            } else {
                icon.innerText = "💥";
                title.innerText = "Game Over";
                title.style.color = "#d63031";
                btn.innerText = "Try Again";
            }
            overlay.style.display = "flex";
        }

        function nextAction() {
            document.getElementById('overlay').style.display = "none";
            const title = document.getElementById('modal-title').innerText;
            if (title === "You Win!") {
                currentLevelIndex++;
            } else if (title === "All Cleared!") {
                currentLevelIndex = 0;
            }
            start();
        }

        start();
    </script>
</body>
</html>