<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlayLearns - Challenge Your Mind</title>
    <style>
        :root {
            --primary: #4A90E2;
            --accent: #FF6B6B;
            --text: #2D3436;
            --bg: #FFFFFF;
            --hero-bg: #1A3C6B; 
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: var(--bg); color: var(--text); }

        header {
            background: #fff;
            padding: 1rem 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f0f0f0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .logo { font-size: 1.6rem; font-weight: 800; color: var(--primary); text-decoration: none; }
        .logo span { color: var(--accent); }
        nav a { margin-left: 20px; text-decoration: none; color: var(--text); font-weight: 500; font-size: 0.9rem; }
        .login-btn { background: var(--primary); color: white !important; padding: 6px 18px; border-radius: 8px; }

        .hero {
            height: 30vh;
            background-color: var(--hero-bg);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .hero h1 { font-size: 3rem; font-weight: 800; margin-bottom: 10px; }
        .hero p { opacity: 0.9; font-size: 1.1rem; }

        .game-section { padding: 4rem 8%; }
        .game-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 2.5rem;
        }

        .game-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
            aspect-ratio: 1 / 1; 
            cursor: pointer;
        }
        .game-card:hover { transform: translateY(-8px); box-shadow: 0 12px 24px rgba(0,0,0,0.15); }

        .game-thumb {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .game-card:hover .game-thumb { transform: scale(1.05); }

        .game-info {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.98);
            padding: 1rem 1.5rem;
            z-index: 2;
            height: 60px; 
            transition: height 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            display: flex;
            flex-direction: column;
        }

        .game-card:hover .game-info {
            height: 140px; 
        }

        .game-info h3 {
            font-size: 1.2rem;
            color: #000;
            margin-bottom: 15px; 
            white-space: nowrap;
        }

        .game-info p {
            font-size: 0.9rem;
            color: #666;
            line-height: 1.4;
            opacity: 0; 
            transition: opacity 0.3s ease;
        }

        .game-card:hover .game-info p {
            opacity: 1; 
        }

        @media (max-width: 768px) {
            .hero h1 { font-size: 2rem; }
            nav { display: none; }
        }
    </style>
</head>
<body>

    <header>
        <a href="#" class="logo">Play<span>Learns</span></a>
        <nav>
            <a href="homepage.php">Home</a>
            <a href="#">Games</a>
            <a href="#">Leaderboard</a>
            <a href="login.php" class="login-btn">Login</a> 
        </nav>
    </header>

    <section class="hero">
        <h1>Fun Way to Learn</h1>
        <p>Ready to level up your brain?</p>
    </section>

    <main class="game-section">
        <div class="game-grid">
            
            <div class="game-card" onclick="window.location.href='game2048.html'">
                <img src="img/2048.png" alt="2048" class="game-thumb">
                <div class="game-info">
                    <h3>2048 Puzzle</h3>
                    <p>Merge the numbers and get to the 2048 tile! A great way to practice powers of 2.</p>
                </div>
            </div>

            <div class="game-card" onclick="window.location.href='minesweeper.html'">
                <img src="img/扫雷.png" alt="Minesweeper" class="game-thumb">
                <div class="game-info">
                    <h3>Minesweeper</h3>
                    <p>Use logic to clear the grid without detonating any mines. Classic brain training.</p>
                </div>
            </div>

            <div class="game-card" onclick="window.location.href='snake.html'">
                <img src="img/贪吃蛇.png" alt="Snake" class="game-thumb">
                <div class="game-info">
                    <h3>Classic Snake</h3>
                    <p>Eat the food, grow longer, and don't hit the walls! Improves reaction and focus.</p>
                </div>
            </div>

            <div class="game-card" onclick="window.location.href='word_wanderer.html'">
                <img src="img/Word Wanderer.png" alt="Word Wanderer" class="game-thumb">
                <div class="game-info">
                    <h3>Word Wanderer</h3>
                    <p>Connect letters to discover hidden words. Perfect for expanding your vocabulary.</p>
                </div>
            </div>

            <div class="game-card" onclick="window.location.href='memory.html'">
                <img src="img/match.jpeg" alt="Emoji Memory Match" class="game-thumb">
                <div class="game-info">
                    <h3>Emoji Memory Match</h3>
                    <p>Train your brain by finding matching pairs of fun emojis! Great for boosting short-term memory and daily focus.</p>
                </div>
            </div>

            <div class="game-card" onclick="window.location.href='math_pop.html'">
                <img src="img/Math Pop.png" alt="Math Pop" class="game-thumb">
                <div class="game-info">
                    <h3>Math Pop</h3>
                    <p>Solve the math puzzles by popping the correct bubbles. Great for practicing quick calculations.</p>
                </div>
            </div>

            <div class="game-card" onclick="window.location.href='odd_one_out.html'">
                <img src="https://api.dicebear.com/7.x/shapes/svg?seed=OddOneOut&backgroundColor=00cec9" alt="Odd One Out" class="game-thumb">
                <div class="game-info">
                    <h3>Odd One Out</h3>
                    <p>Spot the difference! Find the emoji that doesn't belong to train your observation skills and focus.</p>
                </div>
            </div>
        </div>
    </main>

</body>
</html>