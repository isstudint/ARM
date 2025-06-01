<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GSAP Animation Demo - ARM</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&" />
    <link rel="stylesheet" href="../Css/gsap_demo.css">
    <script src="../FRAMEWORKS/gsap-public/umd/gsap.js"></script>
    <script src="../FRAMEWORKS/gsap-public/umd/TextPlugin.js"></script>
    <script src="../FRAMEWORKS/gsap-public/umd/ScrollTrigger.js"></script>
    <script src="../FRAMEWORKS/gsap-public/umd/CustomBounce.js"></script>
    <script src="../FRAMEWORKS/gsap-public/umd/EasePack.js"></script>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-content">
            <h1 class="hero-title">
                <span class="word">ARM</span>
                <span class="word">BASKETBALL</span>
                <span class="word">LEAGUE</span>
            </h1>
            <p class="hero-subtitle">Experience the power of GSAP animations</p>
            <button class="cta-button">
                <span>START EXPERIENCE</span>
                <div class="button-bg"></div>
            </button>
        </div>
        
        <!-- Floating Basketball -->
        <div class="basketball-container">
            <div class="basketball">🏀</div>
        </div>
        
        <!-- Particles -->
        <div class="particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>
    </section>

    <!-- Interactive Cards Section -->
    <section class="cards-section">
        <h2 class="section-title">Interactive Team Cards</h2>
        <div class="cards-container">
            <div class="team-card" data-team="lakers">
                <div class="card-bg"></div>
                <div class="card-content">
                    <div class="team-logo">🟣</div>
                    <h3>Lakers</h3>
                    <div class="stats">
                        <span class="wins">15</span>
                        <span class="losses">8</span>
                    </div>
                </div>
                <div class="card-overlay"></div>
            </div>
            
            <div class="team-card" data-team="warriors">
                <div class="card-bg"></div>
                <div class="card-content">
                    <div class="team-logo">🔵</div>
                    <h3>Warriors</h3>
                    <div class="stats">
                        <span class="wins">18</span>
                        <span class="losses">5</span>
                    </div>
                </div>
                <div class="card-overlay"></div>
            </div>
            
            <div class="team-card" data-team="celtics">
                <div class="card-bg"></div>
                <div class="card-content">
                    <div class="team-logo">🟢</div>
                    <h3>Celtics</h3>
                    <div class="stats">
                        <span class="wins">20</span>
                        <span class="losses">3</span>
                    </div>
                </div>
                <div class="card-overlay"></div>
            </div>
        </div>
    </section>

    <!-- Scoreboard Animation -->
    <section class="scoreboard-section">
        <h2 class="section-title">Live Score Animation</h2>
        <div class="scoreboard">
            <div class="team-score">
                <div class="team-name">TEAM A</div>
                <div class="score" id="scoreA">0</div>
            </div>
            <div class="vs">VS</div>
            <div class="team-score">
                <div class="team-name">TEAM B</div>
                <div class="score" id="scoreB">0</div>
            </div>
        </div>
        <button class="animate-score-btn">Animate Score!</button>
    </section>

    <!-- Text Animation Section -->
    <section class="text-section">
        <div class="typewriter">
            <span class="typing-text"></span>
            <span class="cursor">|</span>
        </div>
    </section>

    <!-- Morphing Shapes -->
    <section class="shapes-section">
        <h2 class="section-title">Morphing Basketball Court</h2>
        <div class="court-container">
            <svg class="basketball-court" viewBox="0 0 400 200">
                <rect class="court-bg" x="0" y="0" width="400" height="200" fill="#8B4513"/>
                <circle class="center-circle" cx="200" cy="100" r="30" fill="none" stroke="#fff" stroke-width="2"/>
                <rect class="three-point-line" x="50" y="50" width="300" height="100" fill="none" stroke="#fff" stroke-width="2" rx="50"/>
                <rect class="key" x="0" y="75" width="50" height="50" fill="none" stroke="#fff" stroke-width="2"/>
                <rect class="key" x="350" y="75" width="50" height="50" fill="none" stroke="#fff" stroke-width="2"/>
            </svg>
        </div>
        <button class="morph-btn">Transform Court!</button>
    </section>

    <!-- Physics Demo -->
    <section class="physics-section">
        <h2 class="section-title">Basketball Physics</h2>
        <div class="basketball-game">
            <div class="hoop">
                <div class="rim"></div>
                <div class="net"></div>
            </div>
            <div class="ball" id="physicsball">🏀</div>
        </div>
        <button class="shoot-btn">Shoot!</button>
    </section>

    <!-- Control Panel -->
    <div class="control-panel">
        <button class="control-btn" data-action="replay">Replay All</button>
        <button class="control-btn" data-action="pause">Pause</button>
        <button class="control-btn" data-action="resume">Resume</button>
    </div>

    <script src="../js/gsap_demo.js"></script>
</body>
</html>
