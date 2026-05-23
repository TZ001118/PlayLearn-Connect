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
    <title>PlayLearn - Math Pop</title>
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
        #level-title { font-size: 32px; font-weight: 900; color: #e84393; margin: 0; }
        
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

        .equation-box {
            width: 100%; height: 140px;
            background-color: #e0fbfc; border-radius: 20px;
            display: flex; justify-content: center; align-items: center;
            font-size: 55px; font-weight: 900; color: #2d3436; margin-bottom: 30px;
            border: 4px solid #00cec9; box-shadow: inset 0 4px 10px rgba(0,0,0,0.05);
        }

        #choices {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            width: 100%;
        }
        
        .choice-btn {
            height: 90px; margin: 0; padding: 0;
            background-color: #81ecec; border: none; border-radius: 20px;
            font-size: 40px; font-weight: 900; font-family: 'Nunito';
            color: #2d3436; cursor: pointer;
            box-shadow: 0 6px 0 #00cec9; display: flex; justify-content: center; align-items: center;
            transition: 0.1s; touch-action: manipulation;
        }
        .choice-btn:active { transform: translateY(6px); box-shadow: none; }
        .choice-btn.wrong { background-color: #ff7675; box-shadow: 0 6px 0 #d63031; color: white; }
        .choice-btn.correct { background-color: #00b894; box-shadow: 0 6px 0 #009477; color: white; }

        @media (max-width: 400px) {
            .equation-box { height: 110px; font-size: 45px; }
            .choice-btn { height: 75px; font-size: 32px; }
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
            border: 4px solid #e84393;
        }
        .tutorial-modal h2 { font-size: 28px; color: #2d3436; margin-top: 0; margin-bottom: 25px; text-align: center; font-weight: 900; }
        .tut-step { display: flex; align-items: center; margin-bottom: 20px; background: #f4f6f9; padding: 15px; border-radius: 16px; }
        .tut-icon { font-size: 36px; margin-right: 15px; background: #fff; border-radius: 12px; padding: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .tut-text { font-size: 16px; color: #636e72; line-height: 1.4; }
        .tut-text b { color: #e84393; font-size: 18px; display: block; margin-bottom: 2px; }
        .tut-btn { width: 100%; padding: 15px; background: #e84393; color: white; border: none; border-radius: 16px; font-size: 22px; font-weight: 900; cursor: pointer; margin-top: 10px; }
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
                <div class="score-count">⭐ <span id="score-display">0 / 5</span></div>
            </div>
        </div>

        <div class="equation-box" id="equation"></div>

        <div id="choices"></div>

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
            <h2>How to Play 🧮</h2>
            <div class="tut-step">
                <div class="tut-icon">👀</div>
                <div class="tut-text"><b>Read the Math</b> Look at the math problem inside the big box.</div>
            </div>
            <div class="tut-step">
                <div class="tut-icon">🤔</div>
                <div class="tut-text"><b>Find the Answer</b> Think about what the correct number is.</div>
            </div>
            <div class="tut-step">
                <div class="tut-icon">👆</div>
                <div class="tut-text"><b>Pop It!</b> Tap the correct bubble to score a point.</div>
            </div>
            <button class="tut-btn" onclick="hideTutorial()">I Got It ! 🚀</button>
        </div>
    </div>

    <script>
        const levelsData = [
            { level: 1, op: ['+'], max: 10, winScore: 5 },
            { level: 2, op: ['+'], max: 20, winScore: 5 },
            { level: 3, op: ['+', '-'], max: 20, winScore: 7 },
            { level: 4, op: ['+', '-'], max: 50, winScore: 8 },
            { level: 5, op: ['+', '-'], max: 100, winScore: 10 }
        ];

        let hasSeenTutorial = false;
        let currentLevelIndex = 0;
        let currentScore = 0;
        let targetScore = 0;
        let correctAnswer = 0;
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
            targetScore = lvlConfig.winScore;
            isProcessing = false;

            document.getElementById('level-title').innerText = `LEVEL ${lvlConfig.level}`;
            updateScore();
            generateQuestion();

            if (currentLevelIndex === 0 && !hasSeenTutorial) {
                showTutorial();
                hasSeenTutorial = true;
            }
        }

        function generateQuestion() {
            let lvlConfig = levelsData[currentLevelIndex];
            let op = lvlConfig.op[Math.floor(Math.random() * lvlConfig.op.length)];
            let num1, num2;

            if (op === '+') {
                num1 = Math.floor(Math.random() * (lvlConfig.max / 2)) + 1;
                num2 = Math.floor(Math.random() * (lvlConfig.max / 2)) + 1;
                correctAnswer = num1 + num2;
            } else {
                num1 = Math.floor(Math.random() * lvlConfig.max) + 5;
                num2 = Math.floor(Math.random() * (num1 - 1)) + 1;
                correctAnswer = num1 - num2;
            }

            document.getElementById('equation').innerText = `${num1} ${op} ${num2} = ?`;

            let answers = [correctAnswer];
            while(answers.length < 4) {
                let offset = Math.floor(Math.random() * 11) - 5;
                if (offset === 0) offset = 1;
                let wrongAnswer = correctAnswer + offset;
                if (wrongAnswer > 0 && !answers.includes(wrongAnswer)) {
                    answers.push(wrongAnswer);
                }
            }

            for (let i = answers.length - 1; i > 0; i--) {
                let j = Math.floor(Math.random() * (i + 1));
                let temp = answers[i];
                answers[i] = answers[j];
                answers[j] = temp;
            }

            const choicesDiv = document.getElementById('choices');
            choicesDiv.innerHTML = '';
            
            for (let i = 0; i < 4; i++) {
                let btn = document.createElement('button');
                btn.className = 'choice-btn';
                btn.innerText = answers[i];
                btn.onclick = function() { checkAnswer(answers[i], this); };
                choicesDiv.appendChild(btn);
            }
        }

        function checkAnswer(selectedVal, btnElement) {
            if (isProcessing) return;
            isProcessing = true;

            if (selectedVal === correctAnswer) {
                btnElement.classList.add('correct');
                currentScore++;
                updateScore();
                
                setTimeout(() => {
                    if (currentScore >= targetScore) {
                        showGameOver(true);
                    } else {
                        isProcessing = false;
                        generateQuestion();
                    }
                }, 400);
            } else {
                btnElement.classList.add('wrong');
                setTimeout(() => {
                    showGameOver(false);
                }, 500);
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
                    game_name: 'Math Pop', 
                    score: currentScore,
                    level_reached: currentLevelIndex + 1
                })
            }).then(r => r.json()).then(d => console.log(d));

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
                title.innerText = "Game Over";
                title.style.color = "#d63031";
                btn.innerText = "Try Again";
            }
            overlay.style.display = "flex";
        }

        function nextAction() {
            document.getElementById('overlay').style.display = "none";
            const title = document.getElementById('modal-title').innerText;
            if(title === "You Win!") {
                currentLevelIndex++;
            } else if(title === "All Cleared!") {
                currentLevelIndex = 0;
            }
            start();
        }

        start();
    </script>
</body>
</html>