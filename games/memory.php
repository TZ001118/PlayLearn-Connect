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
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlayLearn - 记忆配对</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/phaser@3.55.2/dist/phaser.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f4f6f9; 
            font-family: 'Nunito', 'Segoe UI', Tahoma, sans-serif;
            min-height: 100vh;
        }
        
        .header {
            width: 100%;
            max-width: 850px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 20px 10px 20px;
            box-sizing: border-box;
        }

        .back-btn {
            padding: 10px 20px;
            background-color: #ff6b6b;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            cursor: pointer;
            font-weight: 800;
            box-shadow: 0 4px 10px rgba(255, 107, 107, 0.3);
            transition: transform 0.2s;
        }
        .back-btn:hover { transform: scale(1.05); }

        .brand-title {
            font-size: 38px; 
            font-weight: 900;
            color: #2d3436;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .brand-title span { color: #0984e3; } 

        .game-wrapper {
            width: 100%;
            max-width: 800px; 
            background-color: #ffffff; 
            border-radius: 24px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.08); 
            border: 3px solid #e1e5ee; 
            overflow: hidden; 
            margin-top: 10px;
            padding: 10px 0; 
        }

        #game-container {
            width: 100%;
            text-align: center; 
            padding: 0;
            margin: 0;
        }

        #game-container canvas {
            display: block;
            margin: 0 auto !important; 
        }

        /* 现代化结算弹窗样式 */
        .overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6); display: none; justify-content: center; align-items: center; z-index: 100;
        }
        .modal {
            background: #fff; padding: 35px 25px; border-radius: 24px; text-align: center;
            max-width: 320px; width: 85%; box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            border: 4px solid #f4f6f9;
        }
        .modal h2 { font-size: 42px; margin: 0 0 5px 0; letter-spacing: 2px; }
        .modal h3 { font-size: 30px; margin: 0 0 15px 0; font-weight: 900; }
        .modal p { font-size: 18px; color: #2d3436; margin: 8px 0; font-weight: 800; }
        .modal p span { color: #d63031; }
        .modal .hint { font-size: 14px; margin-top: 12px; margin-bottom: 20px; font-weight: 700; }
        
        .modal button {
            padding: 14px 20px; border: none; border-radius: 14px;
            font-size: 18px; font-weight: 900; cursor: pointer; width: 100%; transition: 0.1s; font-family: 'Nunito';
        }
        .modal .btn-next { background: #0984e3; color: #fff; box-shadow: 0 5px 0 #074b83; margin-bottom: 12px; }
        .modal .btn-next:active { transform: translateY(5px); box-shadow: none; }
        .modal .btn-retry { background: #b2bec3; color: #2d3436; box-shadow: 0 5px 0 #636e72; }
        .modal .btn-retry:active { transform: translateY(5px); box-shadow: none; }
        
        @media (max-width: 500px) {
            .brand-title { font-size: 26px; }
            .header { padding: 10px 15px 5px 15px; }
            .back-btn { font-size: 13px; padding: 8px 14px; }
            .game-wrapper { margin-top: 0; border-radius: 16px; border-width: 2px; }
        }
    </style>
</head>
<body>

    <div class="header">
        <button class="back-btn" onclick="window.history.back()">⬅ Back</button>
        <div class="brand-title">Play<span>Learn</span></div>
        <div style="width: 80px;"></div> 
    </div>

    <div class="game-wrapper">
        <div id="game-container"></div>
    </div>

    <div id="overlay" class="overlay">
        <div class="modal">
            <h2 id="modal-stars">⭐⭐⭐</h2>
            <h3 id="modal-title" style="color: #f1c40f;">PERFECT!</h3>
            <p>Total Moves: <span id="modal-moves" style="color:#2d3436;">4</span></p>
            <p>Time Taken: <span id="modal-time" style="color:#d63031;">7s</span></p>
            <div id="modal-hint" class="hint" style="color: #00b894;">(⭐ You achieved 3 Stars! ⭐)</div>
            
            <button id="modal-btn-next" class="btn-next">NEXT LEVEL ➡</button>
            <button id="modal-btn-retry" class="btn-retry">🔄 Retry Level</button>
        </div>
    </div>

    <script>
        const levelsData = [
            { level: 1, pairs: 3, flipDelay: 800, maxMoves3Star: 6 },
            { level: 2, pairs: 4, flipDelay: 800, maxMoves3Star: 8 },
            { level: 3, pairs: 6, flipDelay: 800, maxMoves3Star: 14 },
            { level: 4, pairs: 8, flipDelay: 800, maxMoves3Star: 20 },
            { level: 5, pairs: 8, flipDelay: 400, maxMoves3Star: 18 },
            { level: 6, pairs: 10, flipDelay: 700, maxMoves3Star: 26 },
            { level: 7, pairs: 10, flipDelay: 400, maxMoves3Star: 24 },
            { level: 8, pairs: 12, flipDelay: 600, maxMoves3Star: 34 },
            { level: 9, pairs: 12, flipDelay: 350, maxMoves3Star: 30 },
            { level: 10, pairs: 15, flipDelay: 300, maxMoves3Star: 40 }
        ];

        const themePools = [
            ['🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼', '🐨', '🐯', '🦁', '🐮', '🐷', '🐸', '🐵'], 
            ['🍎', '🍌', '🍇', '🍉', '🍓', '🍒', '🍑', '🍍', '🥝', '🍋', '🥥', '🥑', '🍆', '🥕', '🌽'], 
            ['🚗', '🚓', '🚒', '🚜', '🚁', '🚀', '🛸', '⛵', '🛵', '🚲', '🛴', '🛶', '🚢', '🚆', '🛩️']
        ];

        let currentLevelIndex = 0;

        const config = {
            type: Phaser.AUTO,
            transparent: true, 
            scale: {
                mode: Phaser.Scale.FIT, 
                parent: 'game-container',
                autoCenter: Phaser.Scale.NO_CENTER, 
                width: 800,
                height: 850 
            },
            scene: { create: create }
        };

        const game = new Phaser.Game(config);

        let firstCard = null;
        let secondCard = null;
        let isProcessing = false;
        let matchedPairs = 0;
        let moves = 0;
        
        let timeTaken = 0;
        let timerEvent;
        let timeTextHUD;
        let movesTextHUD;
        let comboCount = 0;
        let isGameOver = false;

        let cardsFlippedThisTurn = 0;
        let cardsFlippedBackThisTurn = 0;

        function create() {
            this.children.removeAll();
            if (timerEvent) timerEvent.remove();

            let levelConfig = levelsData[currentLevelIndex];
            matchedPairs = 0;
            moves = 0;
            timeTaken = 0;
            comboCount = 0;
            isGameOver = false;
            
            cardsFlippedThisTurn = 0;
            cardsFlippedBackThisTurn = 0;
            firstCard = null;
            secondCard = null;
            isProcessing = false;

            this.add.text(400, 40, `LEVEL ${levelConfig.level}`, { 
                fontSize: '32px', fill: '#2d3436', fontStyle: 'bold', fontFamily: 'Nunito'
            }).setOrigin(0.5);

            timeTextHUD = this.add.text(150, 40, `⏱️ Time: 0s`, { 
                fontSize: '24px', fill: '#d63031', fontStyle: 'bold', fontFamily: 'Nunito'
            }).setOrigin(0.5);

            movesTextHUD = this.add.text(650, 40, `👟 Moves: 0`, { 
                fontSize: '24px', fill: '#0984e3', fontStyle: 'bold', fontFamily: 'Nunito'
            }).setOrigin(0.5);

            timerEvent = this.time.addEvent({
                delay: 1000,
                callback: () => {
                    if(!isGameOver) {
                        timeTaken++;
                        timeTextHUD.setText(`⏱️ Time: ${timeTaken}s`);
                    }
                },
                loop: true
            });

            let randomTheme = Phaser.Utils.Array.GetRandom(themePools);
            let shuffledTheme = Phaser.Utils.Array.Shuffle([...randomTheme]);
            let selectedEmojis = shuffledTheme.slice(0, levelConfig.pairs);
            
            let cardsData = selectedEmojis.concat(selectedEmojis); 
            Phaser.Utils.Array.Shuffle(cardsData);

            const totalCards = cardsData.length;
            let cols = Math.ceil(Math.sqrt(totalCards));
            let rows = Math.ceil(totalCards / cols);
            
            if (totalCards === 6) { cols = 3; rows = 2; }
            else if (totalCards === 8) { cols = 4; rows = 2; }
            else if (totalCards === 10) { cols = 5; rows = 2; }
            else if (totalCards === 12) { cols = 4; rows = 3; }
            else if (totalCards === 16) { cols = 4; rows = 4; }
            else if (totalCards === 20) { cols = 5; rows = 4; }
            else if (totalCards === 24) { cols = 6; rows = 4; }
            else if (totalCards === 30) { cols = 6; rows = 5; }

            let baseSpacingX = 135;
            let baseSpacingY = 165;
            
            let requiredWidth = cols * baseSpacingX;
            let requiredHeight = rows * baseSpacingY;
            
            let scaleFactor = Math.min(740 / requiredWidth, 600 / requiredHeight, 1.6);
            
            let actualGridW = (cols - 1) * baseSpacingX * scaleFactor;
            let actualGridH = (rows - 1) * baseSpacingY * scaleFactor;
            
            let startX = 400 - (actualGridW / 2);
            let startY = 460 - (actualGridH / 2); 

            for (let i = 0; i < totalCards; i++) {
                let col = i % cols;
                let row = Math.floor(i / cols);
                let x = startX + col * baseSpacingX * scaleFactor;
                let y = startY + row * baseSpacingY * scaleFactor;

                createCard(this, x, y, cardsData[i], scaleFactor);
            }
        }

        function createCard(scene, x, y, emojiStr, scaleFactor) {
            let card = scene.add.container(x, y);
            card.setScale(scaleFactor); 
            card.setSize(120, 150);
            card.setInteractive();
            card.emojiValue = emojiStr;
            card.isFlipped = false;
            card.isMatched = false;

            let back = scene.add.rectangle(0, 0, 110, 140, 0x0984e3, 1);
            back.setStrokeStyle(4, 0x74b9ff); 
            let backText = scene.add.text(0, 0, '?', { fontSize: '50px', color: '#ffffff', fontStyle: 'bold', fontFamily: 'Nunito' }).setOrigin(0.5);

            let front = scene.add.rectangle(0, 0, 110, 140, 0xffffff, 1);
            front.setStrokeStyle(4, 0x00b894); 
            let frontText = scene.add.text(0, 0, emojiStr, { fontSize: '60px' }).setOrigin(0.5);
            
            front.setVisible(false);
            frontText.setVisible(false);

            card.add([back, backText, front, frontText]);

            card.on('pointerdown', function () {
                if (isProcessing || card.isFlipped || card.isMatched || isGameOver) return;

                card.isFlipped = true;

                if (!firstCard) {
                    firstCard = card;
                } else {
                    secondCard = card;
                    isProcessing = true; 
                }

                scene.tweens.add({
                    targets: card,
                    scaleX: 0,
                    duration: 120,
                    onComplete: () => {
                        back.setVisible(false);
                        backText.setVisible(false);
                        front.setVisible(true);
                        frontText.setVisible(true);
                        
                        scene.tweens.add({ 
                            targets: card, 
                            scaleX: scaleFactor, 
                            duration: 120,
                            onComplete: () => {
                                cardsFlippedThisTurn++;
                                if (cardsFlippedThisTurn === 2) {
                                    cardsFlippedThisTurn = 0; 
                                    checkMatch(scene, scaleFactor);
                                }
                            }
                        });
                    }
                });
            });
        }

        function checkMatch(scene, scaleFactor) {
            moves++;
            movesTextHUD.setText(`👟 Moves: ${moves}`); 

            if (firstCard.emojiValue === secondCard.emojiValue) {
                firstCard.isMatched = true;
                secondCard.isMatched = true;
                comboCount++; 
                
                if(comboCount > 1) {
                    let comboText = scene.add.text(400, 120, `${comboCount}x COMBO!`, { 
                        fontSize: '40px', fill: '#fdcb6e', fontStyle: 'bold', stroke: '#d63031', strokeThickness: 6, fontFamily: 'Nunito'
                    }).setOrigin(0.5);
                    
                    scene.tweens.add({
                        targets: comboText,
                        y: 80, alpha: 0, scale: 1.5,
                        duration: 1000,
                        onComplete: () => comboText.destroy()
                    });
                }

                scene.tweens.add({ 
                    targets: [firstCard, secondCard], 
                    scaleX: scaleFactor * 1.2, scaleY: scaleFactor * 1.2, 
                    duration: 200,
                    yoyo: true,
                    onComplete: () => {
                        scene.tweens.add({
                            targets: [firstCard, secondCard],
                            scaleX: 0, scaleY: 0, alpha: 0,
                            duration: 300,
                            onComplete: () => {
                                matchedPairs++;
                                resetSelection();
                                if (matchedPairs === levelsData[currentLevelIndex].pairs) {
                                    levelComplete(scene);
                                }
                            }
                        });
                    }
                });
            } else {
                comboCount = 0; 
                scene.time.delayedCall(levelsData[currentLevelIndex].flipDelay, () => {
                    flipBack(scene, firstCard, scaleFactor);
                    flipBack(scene, secondCard, scaleFactor);
                });
            }
        }

        function flipBack(scene, card, scaleFactor) {
            scene.tweens.add({
                targets: card,
                scaleX: 0,
                duration: 120,
                onComplete: () => {
                    card.list[2].setVisible(false);
                    card.list[3].setVisible(false);
                    card.list[0].setVisible(true);
                    card.list[1].setVisible(true);
                    
                    scene.tweens.add({ 
                        targets: card, 
                        scaleX: scaleFactor, 
                        duration: 120,
                        onComplete: () => {
                            card.isFlipped = false;
                            cardsFlippedBackThisTurn++;
                            if (cardsFlippedBackThisTurn === 2) {
                                cardsFlippedBackThisTurn = 0; 
                                resetSelection();
                            }
                        }
                    });
                }
            });
        }

        function resetSelection() {
            firstCard = null;
            secondCard = null;
            isProcessing = false;
        }

        // 核心升级：与原生 HTML 模态框无缝融合
        function levelComplete(scene) {
            isGameOver = true;
            let levelConfig = levelsData[currentLevelIndex];
            
            let finalLevelReached = currentLevelIndex + 1; 
            let finalMoves = moves; 
            
            fetch('../save_score.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    game_name: 'Memory Match', 
                    score: finalMoves,
                    level_reached: finalLevelReached
                })
            }).catch(error => console.error('Error saving score:', error));

            let starDisplay = '⭐';
            let feedbackText = 'GOOD!';
            let feedbackColor = '#0984e3';
            let isPerfect = false;

            if (moves <= levelConfig.maxMoves3Star) {
                starDisplay = '⭐⭐⭐';
                feedbackText = 'PERFECT!';
                feedbackColor = '#f1c40f'; 
                isPerfect = true;
            } else if (moves <= levelConfig.maxMoves3Star + 6) {
                starDisplay = '⭐⭐';
                feedbackText = 'EXCELLENT!';
                feedbackColor = '#00b894'; 
            }

            document.getElementById('modal-stars').innerText = starDisplay;
            document.getElementById('modal-title').innerText = feedbackText;
            document.getElementById('modal-title').style.color = feedbackColor;
            document.getElementById('modal-moves').innerText = moves;
            document.getElementById('modal-time').innerText = timeTaken + 's';
            
            let hintEl = document.getElementById('modal-hint');
            if (isPerfect) {
                hintEl.innerText = '(⭐ You achieved 3 Stars! ⭐)';
                hintEl.style.color = '#00b894';
            } else {
                hintEl.innerText = `(Goal for 3 Stars: ${levelConfig.maxMoves3Star} Moves)`;
                hintEl.style.color = '#636e72';
            }

            let btnNext = document.getElementById('modal-btn-next');
            let btnRetry = document.getElementById('modal-btn-retry');

            if (currentLevelIndex < levelsData.length - 1) {
                btnNext.innerText = 'NEXT LEVEL ➡';
                btnNext.style.display = 'block';
                btnNext.onclick = function() {
                    document.getElementById('overlay').style.display = 'none';
                    currentLevelIndex++;
                    scene.scene.restart();
                };
                
                btnRetry.innerText = '🔄 Retry Level';
                btnRetry.onclick = function() {
                    document.getElementById('overlay').style.display = 'none';
                    scene.scene.restart();
                };
            } else {
                btnNext.style.display = 'none'; 
                document.getElementById('modal-title').innerText = "ALL CLEARED!";
                document.getElementById('modal-title').style.color = "#d63031";
                
                btnRetry.innerText = '🔄 Play Again';
                btnRetry.onclick = function() {
                    document.getElementById('overlay').style.display = 'none';
                    currentLevelIndex = 0;
                    scene.scene.restart();
                };
            }

            document.getElementById('overlay').style.display = 'flex';
        }
    </script>
</body>
</html>