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
    <title>PlayLearn - Word Wanderer</title>
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
            margin-bottom: 15px;
        }
        #level-title { font-size: 32px; font-weight: 900; color: #6c5ce7; margin: 0; }
        
        .right-status { display: flex; gap: 10px; align-items: center; }
        .help-btn {
            background-color: #0984e3; padding: 8px 12px; border-radius: 12px;
            font-weight: 900; font-size: 18px; color: white; border: none; cursor: pointer;
            box-shadow: 0 4px 0 #74b9ff; transition: 0.1s;
        }
        .help-btn:active { transform: translateY(4px); box-shadow: none; }

        .hint-box {
            width: 120px; height: 120px;
            background-color: #f1f2f6; border-radius: 20px;
            display: flex; justify-content: center; align-items: center;
            font-size: 70px; margin-bottom: 20px;
            box-shadow: inset 0 4px 10px rgba(0,0,0,0.05);
        }

        .word-display {
            display: flex; gap: 10px; margin-bottom: 25px; min-height: 50px;
        }
        .letter-slot {
            width: 45px; height: 50px;
            background-color: #dfe6e9; border-radius: 10px;
            display: flex; justify-content: center; align-items: center;
            font-size: 28px; font-weight: 900; color: #2d3436;
            border-bottom: 4px solid #b2bec3;
        }

        #game {
            display: flex; flex-direction: column; gap: 8px; 
            background-color: #b2bec3; padding: 12px; border-radius: 16px;
        }
        .span { display: flex; gap: 8px; justify-content: center; }
        
        .btn {
            width: 55px; height: 55px; margin: 0; padding: 0;
            background-color: #81ecec; border: none; border-radius: 12px;
            font-size: 30px; font-weight: 900; font-family: 'Nunito';
            color: #2d3436; cursor: pointer;
            box-shadow: 0 5px 0 #00cec9; display: flex; justify-content: center; align-items: center;
            transition: 0.1s; touch-action: manipulation;
        }
        .btn:active { transform: translateY(5px); box-shadow: none; }
        .btn.clicked {
            background-color: #dfe6e9; color: #b2bec3;
            box-shadow: none; transform: translateY(5px); pointer-events: none;
        }

        .action-btns {
            display: flex; gap: 15px; margin-top: 20px; width: 100%;
        }
        .act-btn {
            flex: 1; padding: 15px; border-radius: 12px; border: none;
            font-size: 18px; font-weight: 900; color: white; cursor: pointer;
            box-shadow: 0 4px 0 rgba(0,0,0,0.2); transition: 0.1s;
        }
        .act-btn:active { transform: translateY(4px); box-shadow: none; }
        .btn-clear { background-color: #ff7675; box-shadow: 0 4px 0 #d63031; }
        .btn-check { background-color: #00b894; box-shadow: 0 4px 0 #009477; }

        @media (max-width: 400px) {
            .btn { width: 45px; height: 45px; font-size: 24px; }
            .hint-box { width: 100px; height: 100px; font-size: 60px; }
            .letter-slot { width: 35px; height: 40px; font-size: 22px; }
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
            border: 4px solid #6c5ce7;
        }
        .tutorial-modal h2 { font-size: 28px; color: #2d3436; margin-top: 0; margin-bottom: 25px; text-align: center; font-weight: 900; }
        .tut-step { display: flex; align-items: center; margin-bottom: 20px; background: #f4f6f9; padding: 15px; border-radius: 16px; }
        .tut-icon { font-size: 36px; margin-right: 15px; background: #fff; border-radius: 12px; padding: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .tut-text { font-size: 16px; color: #636e72; line-height: 1.4; }
        .tut-text b { color: #6c5ce7; font-size: 18px; display: block; margin-bottom: 2px; }
        .tut-btn { width: 100%; padding: 15px; background: #6c5ce7; color: white; border: none; border-radius: 16px; font-size: 22px; font-weight: 900; cursor: pointer; margin-top: 10px; }
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
            </div>
        </div>

        <div class="hint-box" id="hint-emoji"></div>

        <div class="word-display" id="word-slots"></div>

        <div id="game"></div>

        <div class="action-btns">
            <button class="act-btn btn-clear" onclick="clearSlots()">❌ Clear</button>
            <button class="act-btn btn-check" onclick="checkWord()">✅ Check</button>
        </div>
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
            <h2>How to Play 🔠</h2>
            <div class="tut-step">
                <div class="tut-icon">👀</div>
                <div class="tut-text"><b>Look at the picture</b> Find out what the picture means.</div>
            </div>
            <div class="tut-step">
                <div class="tut-icon">👆</div>
                <div class="tut-text"><b>Tap the letters</b> Tap the blocks to spell the word.</div>
            </div>
            <div class="tut-step">
                <div class="tut-icon">✅</div>
                <div class="tut-text"><b>Check it!</b> Press the Check button to see if you are right.</div>
            </div>
            <button class="tut-btn" onclick="hideTutorial()">I Got It ! 🚀</button>
        </div>
    </div>

    <script>
        const levelsData = [
            { level: 1, word: "CAT", hint: "🐱", cols: 2, rows: 2 },
            { level: 2, word: "DOG", hint: "🐶", cols: 2, rows: 2 },
            { level: 3, word: "BIRD", hint: "🐦", cols: 3, rows: 2 },
            { level: 4, word: "APPLE", hint: "🍎", cols: 3, rows: 3 },
            { level: 5, word: "TIGER", hint: "🐯", cols: 3, rows: 3 }
        ];

        let hasSeenTutorial = false;
        let currentLevelIndex = 0;
        let word_target = "";
        let size_game = 0;
        let useropen1 = [];
        let grid_letters = [];
        let current_slot_index = 0;
        let attempts = 0;
        let correctAnswers = 0;
        let levelStartedAt = Date.now();

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
            word_target = lvlConfig.word;
            size_game = lvlConfig.cols * lvlConfig.rows;
            useropen1 = Array(word_target.length).fill("");
            current_slot_index = 0;
            attempts = 0;
            correctAnswers = 0;
            levelStartedAt = Date.now();

            document.getElementById('level-title').innerText = `LEVEL ${lvlConfig.level}`;
            document.getElementById('hint-emoji').innerText = lvlConfig.hint;
            document.getElementById('game').innerHTML = '';
            document.getElementById('word-slots').innerHTML = '';

            for (let i = 0; i < word_target.length; i++) {
                let slot = document.createElement('div');
                slot.className = 'letter-slot';
                slot.id = 'slot' + i;
                document.getElementById('word-slots').appendChild(slot);
            }

            grid_letters = Array(size_game).fill("");
            let letters_to_place = word_target.split("");
            const alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            
            while(letters_to_place.length < size_game) {
                let randomChar = alphabet.charAt(Math.floor(Math.random() * alphabet.length));
                letters_to_place.push(randomChar);
            }

            for (let i = letters_to_place.length - 1; i > 0; i--) {
                let j = Math.floor(Math.random() * (i + 1));
                let temp = letters_to_place[i];
                letters_to_place[i] = letters_to_place[j];
                letters_to_place[j] = temp;
            }
            grid_letters = letters_to_place;

            for(let v = 0 ; v < lvlConfig.rows ; v++){
                let content1 = document.getElementById('game');
                let span = document.createElement('div');
                span.className = 'span';
                span.id = 'v' + v;
                content1.appendChild(span);
                for(let c = 0; c < lvlConfig.cols ; c++){
                    let index = v * lvlConfig.cols + c;
                    let content2 = document.getElementById('v' + v);
                    let button = document.createElement('button');
                    let button_name = 'c' + index;
                    button.id = button_name;
                    button.className = 'btn';
                    button.innerText = grid_letters[index];
                    button.onclick = function() { x(index, button_name) };
                    content2.appendChild(button);
                }
            }

            if (currentLevelIndex === 0 && !hasSeenTutorial) {
                showTutorial();
                hasSeenTutorial = true;
            }
        }

        function x(index, btn_id) {
            if (current_slot_index < word_target.length) {
                useropen1[current_slot_index] = grid_letters[index];
                document.getElementById('slot' + current_slot_index).innerText = grid_letters[index];
                current_slot_index++;
                document.getElementById(btn_id).classList.add('clicked');
            }
        }

        function clearSlots() {
            useropen1 = Array(word_target.length).fill("");
            current_slot_index = 0;
            for (let i = 0; i < word_target.length; i++) {
                document.getElementById('slot' + i).innerText = "";
            }
            let btns = document.getElementsByClassName('btn');
            for (let i = 0; i < btns.length; i++) {
                btns[i].classList.remove('clicked');
            }
        }

        function checkWord() {
            let spelled = useropen1.join("");
            attempts++;
            if (spelled === word_target) {
                correctAnswers = 1;
                showGameOver(true);
            } else {
                showGameOver(false);
            }
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
                    game_name: 'Word Wanderer', // 名字按你后台设定的填
                    score: 100, // 过关固定给 100 分
                    level_reached: currentLevelIndex + 1,
                    correct_answers: correctAnswers,
                    total_questions: attempts,
                    duration_seconds: Math.max(1, Math.round((Date.now() - levelStartedAt) / 1000)),
                    score: isWin ? 100 : 0
                })
            }).then(function(response) {
                return response.json();
            }).then(function() {
            });

            if(isWin) {
                icon.innerText = "🎉";
                title.innerText = "You Win!";
                title.style.color = "#00b894";
                if(currentLevelIndex < levelsData.length - 1) {
                    btn.innerText = "Next Level";
                } else {
                    btn.innerText = "Play Again";
                    title.innerText = "All Cleared!";
                }
            } else {
                icon.innerText = "💥";
                title.innerText = "Wrong Word";
                title.style.color = "#d63031";
                btn.innerText = "Try Again";
            }
            overlay.style.display = "flex";
        }

        function uploadWordScore() {
            const data = {
                game_name: 'Word Wanderer',
                score: currentScore, 
                level_reached: currentLevelIndex + 1 
            };

            fetch('save_score.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(data)
            })
            .then(function(response) {
                return response.json();
            })
            .then(function() {
            });
        }

        function nextAction() {
            document.getElementById('overlay').style.display = "none";
            const title = document.getElementById('modal-title').innerText;
            if(title === "You Win!") {
                currentLevelIndex++;
                start();
            } else if(title === "All Cleared!") {
                currentLevelIndex = 0;
                start();
            } else {
                clearSlots();
            }
        }

        start();
    </script>
</body>
</html>
