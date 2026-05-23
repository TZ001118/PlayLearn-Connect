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
$conn->query("UPDATE users SET last_seen = NOW() WHERE id = " . $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>PlayLearn - Logic Sweeper</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --btn-size: 45px; 
            --gap-size: 4px;
        }
        body {
            margin: 0; padding: 0;
            display: flex; flex-direction: column; align-items: center;
            background-color: #f4f6f9; 
            font-family: 'Nunito', 'Segoe UI', Tahoma, sans-serif;
            min-height: 100vh;
            overflow-x: hidden; 
        }
        .header {
            width: 100%; max-width: 850px;
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 20px 5px 20px; box-sizing: border-box;
        }
        .back-btn {
            padding: 8px 16px; background-color: #ff6b6b; color: white;
            border: none; border-radius: 10px; font-size: 15px; cursor: pointer;
            font-weight: 800; box-shadow: 0 4px 10px rgba(255, 107, 107, 0.3); transition: 0.2s;
        }
        .back-btn:hover { transform: scale(1.05); }
        .brand-title { font-size: 32px; font-weight: 900; color: #2d3436; text-transform: uppercase; }
        .brand-title span { color: #0984e3; }
        .game-wrapper {
            width: 100%; max-width: 550px; 
            background-color: #ffffff; border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 3px solid #e1e5ee; 
            margin-top: 5px; padding: 15px 20px; box-sizing: border-box;
            display: flex; flex-direction: column; align-items: center;
        }
        .status-bar {
            width: 100%; display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 15px;
        }
        #level-title { font-size: 28px; font-weight: 900; color: #4A90E2; margin: 0; }
        .right-status { display: flex; gap: 8px; align-items: center; }
        .mine-count {
            background-color: #ffeaa7; padding: 6px 12px; border-radius: 10px;
            font-weight: 800; font-size: 16px; color: #d63031;
        }
        .help-btn {
            background-color: #00cec9; padding: 6px 10px; border-radius: 10px;
            font-weight: 900; font-size: 16px; color: white; border: none; cursor: pointer;
            box-shadow: 0 4px 0 #00b894; transition: 0.1s;
        }
        .help-btn:active { transform: translateY(4px); box-shadow: none; }
        .controls {
            display: flex; gap: 10px; margin-bottom: 15px; width: 100%;
        }
        .mode-btn {
            flex: 1; padding: 10px; border-radius: 12px; border: 3px solid #e1e5ee;
            font-size: 18px; font-weight: 900; cursor: pointer; background-color: #fff;
            transition: all 0.2s; color: #b2bec3; box-shadow: 0 4px 0 #e1e5ee;
        }
        .mode-btn.active-dig {
            border-color: #00cec9; background-color: #e0fbfc; color: #00cec9;
            box-shadow: 0 4px 0 #00cec9; transform: translateY(2px);
        }
        .mode-btn.active-flag {
            border-color: #ff7675; background-color: #ffeaea; color: #ff7675;
            box-shadow: 0 4px 0 #ff7675; transform: translateY(2px);
        }
        #game {
            display: flex; flex-direction: column; gap: var(--gap-size); 
            background-color: #b2bec3; padding: var(--gap-size); border-radius: 10px;
        }
        .span { display: flex; gap: var(--gap-size); justify-content: center; }
        .btn {
            width: var(--btn-size); height: var(--btn-size); margin: 0; padding: 0;
            background-color: #81ecec; border: none; border-radius: 6px;
            font-size: calc(var(--btn-size) * 0.55); font-weight: 900; font-family: 'Nunito';
            color: #2d3436; cursor: pointer;
            box-shadow: 0 3px 0 #00cec9; display: flex; justify-content: center; align-items: center;
            transition: transform 0.1s, background-color 0.2s;
            touch-action: manipulation;
        }
        .btn:active { transform: translateY(3px); box-shadow: none; }
        .action-bar {
            display: flex; gap: 8px; width: 100%; margin-top: 15px;
        }
        .action-btn {
            flex: 1; padding: 10px 4px; border-radius: 10px; border: none;
            font-weight: 900; font-size: 14px; cursor: pointer; color: white;
            transition: 0.1s; font-family: 'Nunito';
        }
        .btn-restart { background-color: #fdcb6e; box-shadow: 0 4px 0 #e1b12c; color: #2d3436; }
        .btn-reset { background-color: #ff7675; box-shadow: 0 4px 0 #d63031; }
        .action-btn:active { transform: translateY(4px); box-shadow: none; }

        @media (max-width: 500px) {
            .header { padding: 10px 15px 5px 15px; }
            .brand-title { font-size: 24px; }
            .back-btn { font-size: 13px; padding: 6px 12px; }
            .game-wrapper { padding: 12px; margin-top: 0; border-radius: 16px; border-width: 2px; }
            .status-bar { margin-bottom: 10px; }
            #level-title { font-size: 22px; }
            .mine-count { font-size: 14px; padding: 4px 8px; }
            .help-btn { font-size: 14px; padding: 4px 8px; }
            .controls { margin-bottom: 10px; gap: 8px; }
            .mode-btn { padding: 8px; font-size: 16px; border-width: 2px; box-shadow: 0 3px 0 #e1e5ee; }
            .action-bar { margin-top: 10px; gap: 6px; }
            .action-btn { padding: 10px 4px; font-size: 14px; }
            .tutorial-modal { width: 95% !important; padding: 20px !important; }
        }

        .overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6); display: none; justify-content: center; align-items: center; z-index: 100;
        }
        .modal {
            background: #fff; padding: 30px; border-radius: 20px; text-align: center;
            max-width: 300px; width: 80%;
        }
        .modal h2 { font-size: 32px; margin: 0 0 10px 0; }
        .modal button {
            margin-top: 15px; padding: 10px 20px; border: none; border-radius: 10px;
            background: #4A90E2; color: #fff; font-size: 16px; font-weight: bold; cursor: pointer;
        }
        .tutorial-modal {
            background: #ffffff; padding: 30px; border-radius: 20px; 
            max-width: 400px; width: 85%; text-align: left;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2); border: 4px solid #0984e3;
        }
        .tutorial-modal h2 {
            font-size: 24px; color: #2d3436; margin-top: 0; margin-bottom: 20px;
            text-align: center; font-weight: 900;
        }
        .tut-step {
            display: flex; align-items: center; margin-bottom: 15px;
            background: #f4f6f9; padding: 12px; border-radius: 12px;
        }
        .tut-icon {
            font-size: 28px; margin-right: 12px;
            background: #fff; border-radius: 10px; padding: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .tut-text { font-size: 14px; color: #636e72; line-height: 1.4; }
        .tut-text b { color: #0984e3; font-size: 16px; display: block; margin-bottom: 2px; }
        .tut-btn {
            width: 100%; padding: 12px; background: #00b894; color: white;
            border: none; border-radius: 12px; font-size: 18px; font-weight: 900;
            cursor: pointer; box-shadow: 0 4px 0 #009477; transition: 0.1s; margin-top: 5px;
        }
        .tut-btn:active { transform: translateY(4px); box-shadow: none; }
        #up { display: none; }
    </style>
</head>
<body>

    <div class="header">
        <button class="back-btn" onclick="window.history.back()">⬅ Back</button>
        <div class="brand-title">Play<span>Learn</span></div>
        <div style="width: 70px;"></div> 
    </div>

    <div class="game-wrapper">
        <div class="status-bar">
            <h2 id="level-title">LEVEL 1</h2>
            <div class="right-status">
                <button class="help-btn" onclick="showTutorial()">❓</button>
                <div class="mine-count">💣 <span id="mine-display">0</span></div>
            </div>
        </div>

        <div class="controls">
            <button class="mode-btn active-dig" id="btn-dig" onclick="setMode('open')">⛏️ Dig</button>
            <button class="mode-btn" id="btn-flag" onclick="setMode('tag')">🚩 Flag</button>
        </div>

        <div id="up"></div>
        <div id="game"></div>

        <div class="action-bar">
            <button class="action-btn btn-restart" onclick="restartCurrentLevel()">🔄 Replay</button>
            <button class="action-btn btn-reset" onclick="resetToLevel1()">⏪ Level 1</button>
        </div>
    </div>

    <div id="overlay" class="overlay">
        <div class="modal">
            <h2 id="modal-icon">🎉</h2>
            <h3 id="modal-title">You Win!</h3>
            <button onclick="nextAction()" id="modal-btn">Next Level</button>
        </div>
    </div>

    <div id="tutorial-overlay" class="overlay">
        <div class="tutorial-modal">
            <h2>How to Play 🧩</h2>
            <div class="tut-step">
                <div class="tut-icon">⛏️</div>
                <div class="tut-text"><b>Dig Safe Spots</b> Tap blocks to find empty spaces. Don't dig the bombs!</div>
            </div>
            <div class="tut-step">
                <div class="tut-icon">1️⃣2️⃣</div>
                <div class="tut-text"><b>Read the Numbers</b> A number tells you exactly how many 💣 bombs are touching it.</div>
            </div>
            <div class="tut-step">
                <div class="tut-icon">🚩</div>
                <div class="tut-text"><b>Flag the Bombs</b> Think there's a bomb? Switch to Flag mode and mark it!</div>
            </div>
            <button class="tut-btn" onclick="hideTutorial()">I Got It ! 🚀</button>
        </div>
    </div>

    <script>
        let hasSeenTutorial = false; 

        function showTutorial() { document.getElementById('tutorial-overlay').style.display = 'flex'; }
        function hideTutorial() { document.getElementById('tutorial-overlay').style.display = 'none'; }
        
        let currentLevelIndex = 1;
        let currentMode = 'open';

        function setMode(mode) {
            currentMode = mode;
            document.getElementById('btn-dig').classList.remove('active-dig');
            document.getElementById('btn-flag').classList.remove('active-flag');
            if (mode === 'open') document.getElementById('btn-dig').classList.add('active-dig');
            else document.getElementById('btn-flag').classList.add('active-flag');
        }

        let size_game = 10;
        
        function calculateGridSize() {
            let isMobile = window.innerWidth <= 500;
            let uiHeight = isMobile ? 220 : 280; 
            let availableHeight = window.innerHeight - uiHeight;
            let availableWidth = isMobile ? window.innerWidth - 40 : 510; 
            let maxGridPx = Math.min(availableWidth, availableHeight);
            let gap = isMobile ? 2 : 4; 
            let btnSize = Math.floor(maxGridPx / size_game) - gap;
            
            if (btnSize > 50) btnSize = 50;
            if (btnSize < 20) btnSize = 20;

            document.documentElement.style.setProperty('--btn-size', btnSize + 'px');
            document.documentElement.style.setProperty('--gap-size', gap + 'px');
        }

        window.addEventListener('resize', calculateGridSize);

        let size_s = [];
        let landmine_location;
        let landmine_number;
        let location_user;
        let useropen1 = [];
        let clicktime = 0;
        let size_coucolet = [];
        let size_coucolet_copy = [];
        let retryCount = 0;

        function start() {
            calculateGridSize();

            document.getElementById('level-title').innerText = `LEVEL ${currentLevelIndex}`;
            document.getElementById('game').innerHTML = ''; 

            landmine_number = 4 + currentLevelIndex;
            if (landmine_number >= size_game * size_game) {
                landmine_number = (size_game * size_game) - 1;
            }

            for(let v = 0 ; v < size_game ; v++){
                let content1 = document.getElementById('game');
                let span = document.createElement('span');
                let span_id = 'v' + v;
                span.id = span_id;
                span.className = 'span';
                content1.appendChild(span);
                for(let c = 0; c < size_game ; c++){
                    let content2 = document.getElementById(span_id);
                    let button = document.createElement('button');
                    let button_name = 'c' + (v * size_game + c);
                    button.id = button_name;
                    button.onclick = function() {x(button_name)};
                    button.className = 'btn';
                    content2.appendChild(button);
                }
            }

            size_s = Array(size_game * size_game).fill(0);
            useropen1 = Array(size_game * size_game).fill(0);
            clicktime = 0;
            retryCount = 0;
            
            document.getElementById('mine-display').innerText = landmine_number; 

            for(let v = 0; v < landmine_number ; v++){
                let landmine_location = Math.random() * (size_game * size_game + 0.99);
                let landmine_location_f = Math.floor(landmine_location);
                if(size_s[landmine_location_f] == 0){
                    size_s[landmine_location_f] = 1;
                }else{
                    v--;
                }
            }
            collor();
            coucolet();
            useropen();

            if(currentLevelIndex === 1 && !hasSeenTutorial) {
                showTutorial();
                hasSeenTutorial = true; 
            }
        }

        function restartCurrentLevel() { start(); }
        function resetToLevel1() { currentLevelIndex = 1; start(); }

        function x(z){
            collor();
            coucolet();
            useropen();
            location_user = z;
            
            if(currentMode === 'open') {
                if(clicktime == 0){ firstclick(z); return; }
                eopen(); 
            } else {
                tag();
            }
        }

        function collor() {
            for(let v = 0; v < size_game * size_game ; v++){
                if(size_s[v] == 1){
                    document.getElementById('c' + v).textContent = "💣";
                    document.getElementById('c' + v).style.color = "#d63031";
                }else{
                    document.getElementById('c' + v).style.backgroundColor = "#ffffff" ;
                }
            }
        }

        function coucolet(){
            size_coucolet = Array(size_game * size_game).fill(0);
            for(let v = 0; v < size_game * size_game ; v++){
                let number = 0;
                let run = true;
                if(size_s[v] == 1){
                    run = false;
                }else if(v == 0){
                    number += (size_s[v + 1]);
                    number += (size_s[v + size_game]);
                    number += (size_s[v + size_game + 1]);
                }else if(v == size_game - 1){
                    number += (size_s[v - 1]);
                    number += (size_s[v + size_game]);
                    number += (size_s[v + size_game - 1]);
                }else if(v == (size_game * size_game - size_game)){
                    number += (size_s[v + 1]);
                    number += (size_s[v - size_game]);
                    number += (size_s[v - size_game + 1]);
                }else if(v == (size_game * size_game - 1)){
                    number += (size_s[v - 1]);
                    number += (size_s[v - size_game]);
                    number += (size_s[v - size_game - 1]);
                }else if(v < size_game){
                    number += (size_s[v + 1]);
                    number += (size_s[v - 1]);
                    number += (size_s[v + size_game]);
                    number += (size_s[v + size_game + 1]);
                    number += (size_s[v + size_game - 1]);
                }else if (v % size_game == 0){
                    number += (size_s[v + 1]);
                    number += (size_s[v + size_game]);
                    number += (size_s[v + size_game + 1]);
                    number += (size_s[v - size_game]);
                    number += (size_s[v - size_game + 1]);
                }else if ((v % size_game) == (size_game - 1)){
                    number += (size_s[v - 1]);
                    number += (size_s[v + size_game]);
                    number += (size_s[v + size_game - 1]);
                    number += (size_s[v - size_game]);
                    number += (size_s[v - size_game - 1]);
                }else if (v > (size_game * size_game - size_game)){
                    number += (size_s[v + 1]);
                    number += (size_s[v - 1]);
                    number += (size_s[v - size_game]);
                    number += (size_s[v - size_game + 1]);
                    number += (size_s[v - size_game - 1]);
                }else{
                    number += (size_s[v + 1]);
                    number += (size_s[v - 1]);
                    number += (size_s[v - size_game]);
                    number += (size_s[v - size_game + 1]);
                    number += (size_s[v - size_game - 1]);
                    number += (size_s[v + size_game]);
                    number += (size_s[v + size_game + 1]);
                    number += (size_s[v + size_game - 1]);
                }
                if(run){ size_coucolet[v] = number; }
            }
            for(let v = 0; v < size_game * size_game ; v++){
                if(size_coucolet[v] > 0){
                    document.getElementById('c' + v).textContent = size_coucolet[v];
                    const colors = ['#0984e3', '#00b894', '#d63031', '#6c5ce7', '#e17055'];
                    document.getElementById('c' + v).style.color = colors[size_coucolet[v]-1] || '#2d3436';
                }
            }
        }

        function useropen(){
            for(let v = 0 ; v < size_game * size_game ; v++){
                if(useropen1[v] == 0){
                    document.getElementById('c' + v).textContent = '';
                    document.getElementById('c' + v).style.backgroundColor = "#81ecec" ;
                    document.getElementById('c' + v).style.boxShadow = "0 3px 0 #00cec9"; 
                    document.getElementById('c' + v).style.transform = "translateY(0)";
                }else if(useropen1[v] == 2){
                    document.getElementById('c' + v).textContent = '🚩'; 
                    document.getElementById('c' + v).style.backgroundColor = "#ffeaa7" ;
                    document.getElementById('c' + v).style.boxShadow = "0 3px 0 #fdcb6e";
                    document.getElementById('c' + v).style.transform = "translateY(0)";
                }else{
                    document.getElementById('c' + v).style.backgroundColor = "#dfe6e9" ; 
                    document.getElementById('c' + v).style.boxShadow = "none"; 
                    document.getElementById('c' + v).style.transform = "translateY(3px)"; 
                }
            }
        }

        function firstclick(df){
            let df2 = parseInt(df.slice(1), 10);
            
            if (size_s[df2] > 0) {
                start();
                firstclick(df);
                return;
            }
            
            if (size_coucolet[df2] > 0 && retryCount < 15 && landmine_number < (size_game * size_game / 3)) {
                retryCount++;
                start();
                firstclick(df);
                return;
            }

            retryCount = 0;
            clicktime = 1;
            size_coucolet_copy = Array(size_coucolet.length).fill(0);
            for(let b = 0; b < size_coucolet.length ; b++){
                size_coucolet_copy[b] = size_coucolet[b];
            }
            coucolet_while_for_firstclick(df);
            
            eopen();
        }

        function eopen() {
            let open_number = location_user.slice(1,location_user.length);
            let numberc_ = parseInt(open_number,10);
            useropen1[numberc_] = 1;
            let z1 = location_user.slice(1,location_user.length);
            let z2 = parseInt(z1,10);
            if((size_s[z2] == 0) && (size_coucolet[z2] == 0)){
                coucolet_while_for_firstclick(location_user);
            }
            let df1 = location_user.slice(1,location_user.length);
            let df2 = parseInt(df1,10);
            let c_up = 0;
            let ewhile = (size_game * size_game) - landmine_number;
            for(let d = 0; d < useropen1.length; d++){
                if(useropen1[d] == 1){ c_up++; }
            }
            
            if(size_s[df2] == 1){
                setTimeout(() => showGameOver(false), 100);
            }else if(c_up >= ewhile){
                setTimeout(() => showGameOver(true), 100);
            }
            collor(); coucolet(); useropen();
        }

        function tag() {
            let tag1 = location_user.slice(1,location_user.length);
            let tag2 = parseInt(tag1,10);
            if(useropen1[tag2] == 0){ useropen1[tag2] = 2; }
            else if(useropen1[tag2] == 2){ useropen1[tag2] = 0; }
            collor(); coucolet(); useropen();
        }

        function coucolet_while_for_firstclick(x){
            let xnumber = x.slice(1,x.length);
            let numberc = parseInt(xnumber,10);
            useropen1[numberc] = 1;
            if(size_coucolet_copy[numberc] == 0){
                size_coucolet_copy[numberc] = 1;
                if(numberc == 0){
                    coucolet_while_for_firstclick('c' + (numberc + 1));
                    coucolet_while_for_firstclick('c' + (numberc + size_game));
                    coucolet_while_for_firstclick('c' + (numberc + size_game + 1));
                }else if(numberc == size_game - 1){
                    coucolet_while_for_firstclick('c' + (numberc - 1));
                    coucolet_while_for_firstclick('c' + (numberc + size_game));
                    coucolet_while_for_firstclick('c' + (numberc + size_game - 1));
                }else if(numberc == (size_game * size_game - size_game)){
                    coucolet_while_for_firstclick('c' + (numberc + 1));
                    coucolet_while_for_firstclick('c' + (numberc - size_game));
                    coucolet_while_for_firstclick('c' + (numberc - size_game + 1));
                }else if(numberc == (size_game * size_game - 1)){
                    coucolet_while_for_firstclick('c' + (numberc - 1));
                    coucolet_while_for_firstclick('c' + (numberc - size_game));
                    coucolet_while_for_firstclick('c' + (numberc - size_game - 1));
                }else if(numberc < size_game){
                    coucolet_while_for_firstclick('c' + (numberc + 1));
                    coucolet_while_for_firstclick('c' + (numberc - 1));
                    coucolet_while_for_firstclick('c' + (numberc + size_game));
                    coucolet_while_for_firstclick('c' + (numberc + size_game + 1));
                    coucolet_while_for_firstclick('c' + (numberc + size_game - 1));
                }else if ((numberc % size_game) == 0){
                    coucolet_while_for_firstclick('c' + (numberc + 1));
                    coucolet_while_for_firstclick('c' + (numberc + size_game));
                    coucolet_while_for_firstclick('c' + (numberc + size_game + 1));
                    coucolet_while_for_firstclick('c' + (numberc - size_game));
                    coucolet_while_for_firstclick('c' + (numberc - size_game + 1));
                }else if (numberc % size_game == size_game - 1){
                    coucolet_while_for_firstclick('c' + (numberc - 1));
                    coucolet_while_for_firstclick('c' + (numberc + size_game));
                    coucolet_while_for_firstclick('c' + (numberc + size_game - 1));
                    coucolet_while_for_firstclick('c' + (numberc - size_game));
                    coucolet_while_for_firstclick('c' + (numberc - size_game - 1));
                }else if (numberc > (size_game * size_game - size_game)){
                    coucolet_while_for_firstclick('c' + (numberc + 1));
                    coucolet_while_for_firstclick('c' + (numberc - 1));
                    coucolet_while_for_firstclick('c' + (numberc - size_game));
                    coucolet_while_for_firstclick('c' + (numberc - size_game + 1));
                    coucolet_while_for_firstclick('c' + (numberc - size_game - 1));
                }else{
                    coucolet_while_for_firstclick('c' + (numberc + 1));
                    coucolet_while_for_firstclick('c' + (numberc - 1));
                    coucolet_while_for_firstclick('c' + (numberc - size_game));
                    coucolet_while_for_firstclick('c' + (numberc - size_game + 1));
                    coucolet_while_for_firstclick('c' + (numberc - size_game - 1));
                    coucolet_while_for_firstclick('c' + (numberc + size_game));
                    coucolet_while_for_firstclick('c' + (numberc + size_game + 1));
                    coucolet_while_for_firstclick('c' + (numberc + size_game - 1));
                }
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
                    game_name: 'Mine Sweeper', 
                    score: 100, 
                    level_reached: currentLevelIndex
                })
            }).catch(e => console.error("Save failed:", e));

            if(isWin) {
                icon.innerText = "🎉";
                title.innerText = "You Win!";
                title.style.color = "#00b894";
                btn.innerText = "Next Level";
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
            }
            start();
        }

        start();
    </script>
</body>
</html>