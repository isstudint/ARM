<?php 

session_start();
include('sidebar.php');

$conn = mysqli_connect("localhost", "root", "", "arm");

$match_id = isset($_GET['match_id']) ? (int)$_GET['match_id'] : null;

if ($match_id) {
    $match_query = "
    SELECT m.*, t1.team_name as team1_name, t2.team_name as team2_name,
           t1.logo as team1_logo, t2.logo as team2_logo,
           s.team1_score, s.team2_score
    FROM matches m
    JOIN teams t1 ON m.team1_id = t1.team_id
    JOIN teams t2 ON m.team2_id = t2.team_id
    LEFT JOIN scores s ON m.match_id = s.match_id
    WHERE m.match_id = $match_id
    ";
    $match_result = mysqli_query($conn, $match_query);
    $match = mysqli_fetch_assoc($match_result);
    
    if (!$match) {
        echo "<script>alert('Match not found'); window.location.href='manage_scores.php';</script>";
        exit;
    }
    
    // Get players for both teams
    $team1_players_query = "SELECT player_id, player_name, position FROM players WHERE team_id = {$match['team1_id']} ORDER BY player_name";
    $team1_players = mysqli_query($conn, $team1_players_query);
    
    $team2_players_query = "SELECT player_id, player_name, position FROM players WHERE team_id = {$match['team2_id']} ORDER BY player_name";
    $team2_players = mysqli_query($conn, $team2_players_query);
    
} else {
    $matches_query = "
    SELECT m.match_id, m.match_date, t1.team_name as team1_name, t2.team_name as team2_name,
           s.team1_score, s.team2_score
    FROM matches m
    JOIN teams t1 ON m.team1_id = t1.team_id
    JOIN teams t2 ON m.team2_id = t2.team_id
    LEFT JOIN scores s ON m.match_id = s.match_id
    ORDER BY m.match_date DESC
    LIMIT 10
    ";
    $matches = mysqli_query($conn, $matches_query);
}
?>


<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Scoreboard</title>
    <link rel="stylesheet" href="../Css/sidebar.css">
    <link rel="stylesheet" href="../Css/score.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <?php if ($match_id && $match): ?>
    <div class="game-container">
        <!-- Team 1 Roster -->
        <div class="team-roster">
            <div class="roster-header">
                <div class="roster-logo">
                    <?php if(!empty($match['team1_logo']) && file_exists('../' . $match['team1_logo'])): ?>
                        <img src="../<?php echo htmlspecialchars($match['team1_logo']); ?>" alt="<?php echo htmlspecialchars($match['team1_name']); ?>">
                    <?php else: ?>
                        <div class="roster-logo-placeholder"><?php echo strtoupper(substr($match['team1_name'], 0, 2)); ?></div>
                    <?php endif; ?>
                </div>
                <div class="roster-team-name"><?php echo htmlspecialchars($match['team1_name']); ?></div>
            </div>
            
            <div class="stats-section">
                <h4>Record Stats</h4>
                <select id="team1PlayerSelect" class="player-select">
                    <option value="">Select Player</option>
                    <?php mysqli_data_seek($team1_players, 0); ?>
                    <?php while($player = mysqli_fetch_assoc($team1_players)): ?>
                    <option value="<?php echo $player['player_id']; ?>">
                        <?php echo htmlspecialchars($player['player_name']); ?>
                    </option>
                    <?php endwhile; ?>
                </select>
                
                <div class="stat-buttons">
                    <button class="stat-btn" onclick="addPlayerStat(1, 'points', 2)">+2 PTS</button>
                    <button class="stat-btn" onclick="addPlayerStat(1, 'points', 3)">+3 PTS</button>
                    <button class="stat-btn" onclick="addPlayerStat(1, 'rebounds', 1)">+1 REB</button>
                    <button class="stat-btn" onclick="addPlayerStat(1, 'assists', 1)">+1 AST</button>
                </div>
            </div>
            
            <div class="players-list">
                <?php mysqli_data_seek($team1_players, 0); ?>
                <?php while($player = mysqli_fetch_assoc($team1_players)): ?>
                <div class="player-item">
                    <div class="player-name"><?php echo htmlspecialchars($player['player_name']); ?></div>
                    <div class="player-position"><?php echo htmlspecialchars($player['position']); ?></div>
                    <div class="player-stats">
                        <span>PTS: <span id="pts_1_<?php echo $player['player_id']; ?>">0</span></span>
                        <span>REB: <span id="reb_1_<?php echo $player['player_id']; ?>">0</span></span>
                        <span>AST: <span id="ast_1_<?php echo $player['player_id']; ?>">0</span></span>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>


        <div class="scoreboard">

            <div class="game-info">
                <div class="game-status" id="gameStatus">Ready</div>
                <div class="quarter-time">
                    <div>Quarter: <span id="quarter">1</span></div>
                    <div class="time-display" id="gameTime">12:00</div>
                </div>
                <div class="time-controls">
                    <input type="text" id="timeInput" placeholder="12:00" maxlength="5">
                    <button onclick="setTime()">Set Time</button>
                </div>
            </div>

            <!-- Teams and Scores -->
            <div class="teams-section">
                <div class="team">
                    <div class="team-header">
                        <div class="team-logo">
                            <?php if(!empty($match['team1_logo']) && file_exists('../' . $match['team1_logo'])): ?>
                                <img src="../<?php echo htmlspecialchars($match['team1_logo']); ?>" alt="<?php echo htmlspecialchars($match['team1_name']); ?>">
                            <?php else: ?>
                                <div class="team-logo-placeholder"><?php echo strtoupper(substr($match['team1_name'], 0, 2)); ?></div>
                            <?php endif; ?>
                        </div>
                        <h2><?php echo htmlspecialchars($match['team1_name']); ?></h2>
                    </div>
                    <div class="team-score" id="team1Score"><?php echo $match['team1_score'] ?? 0; ?></div>
                    <div class="score-buttons">
                        <button onclick="addScore(1, 1)">+1</button>
                        <button onclick="addScore(1, 2)">+2</button>
                        <button onclick="addScore(1, 3)">+3</button>
                        <button onclick="removeScore(1, 1)">-1</button>
                    </div>
                </div>

                <div class="game-controls">
                    <button class="start-btn" onclick="startGame()" id="gameBtn">Start Game</button>
                    <div class="quarter-controls">
                        <button onclick="changeQuarter(-1)" id="prevQ">← Prev</button>
                        <button onclick="changeQuarter(1)" id="nextQ">Next →</button>
                    </div>
                </div>

                <div class="team">
                    <div class="team-header">
                        <div class="team-logo">
                            <?php if(!empty($match['team2_logo']) && file_exists('../' . $match['team2_logo'])): ?>
                                <img src="../<?php echo htmlspecialchars($match['team2_logo']); ?>" alt="<?php echo htmlspecialchars($match['team2_name']); ?>">
                            <?php else: ?>
                                <div class="team-logo-placeholder"><?php echo strtoupper(substr($match['team2_name'], 0, 2)); ?></div>
                            <?php endif; ?>
                        </div>
                        <h2><?php echo htmlspecialchars($match['team2_name']); ?></h2>
                    </div>
                    <div class="team-score" id="team2Score"><?php echo $match['team2_score'] ?? 0; ?></div>
                    <div class="score-buttons">
                        <button onclick="addScore(2, 1)">+1</button>
                        <button onclick="addScore(2, 2)">+2</button>
                        <button onclick="addScore(2, 3)">+3</button>
                        <button onclick="removeScore(2, 1)">-1</button>
                    </div>
                </div>
            </div>

            <!-- Bottom Controls -->
            <div class="bottom-controls">
                <button onclick="pauseGame()" id="pauseBtn">Pause</button>
                <button onclick="resetGame()">Reset All</button>
                <button onclick="endGame()">End Game</button>
            </div>
        </div>

        <!-- Team 2 Roster -->
        <div class="team-roster">
            <div class="roster-header">
                <div class="roster-logo">
                    <?php if(!empty($match['team2_logo']) && file_exists('../' . $match['team2_logo'])): ?>
                        <img src="../<?php echo htmlspecialchars($match['team2_logo']); ?>" alt="<?php echo htmlspecialchars($match['team2_name']); ?>">
                    <?php else: ?>
                        <div class="roster-logo-placeholder"><?php echo strtoupper(substr($match['team2_name'], 0, 2)); ?></div>
                    <?php endif; ?>
                </div>
                <div class="roster-team-name"><?php echo htmlspecialchars($match['team2_name']); ?></div>
            </div>
            
            <div class="stats-section">
                <h4>Record Stats</h4>
                <select id="team2PlayerSelect" class="player-select">
                    <option value="">Select Player</option>
                    <?php mysqli_data_seek($team2_players, 0); ?>
                    <?php while($player = mysqli_fetch_assoc($team2_players)): ?>
                    <option value="<?php echo $player['player_id']; ?>">
                        <?php echo htmlspecialchars($player['player_name']); ?>
                    </option>
                    <?php endwhile; ?>
                </select>
                
                <div class="stat-buttons">
                    <button class="stat-btn" onclick="addPlayerStat(2, 'points', 2)">+2 PTS</button>
                    <button class="stat-btn" onclick="addPlayerStat(2, 'points', 3)">+3 PTS</button>
                    <button class="stat-btn" onclick="addPlayerStat(2, 'rebounds', 1)">+1 REB</button>
                    <button class="stat-btn" onclick="addPlayerStat(2, 'assists', 1)">+1 AST</button>
                </div>
            </div>
            
            <div class="players-list">
                <?php mysqli_data_seek($team2_players, 0); ?>
                <?php while($player = mysqli_fetch_assoc($team2_players)): ?>
                <div class="player-item">
                    <div class="player-name"><?php echo htmlspecialchars($player['player_name']); ?></div>
                    <div class="player-position"><?php echo htmlspecialchars($player['position']); ?></div>
                    <div class="player-stats">
                        <span>PTS: <span id="pts_2_<?php echo $player['player_id']; ?>">0</span></span>
                        <span>REB: <span id="reb_2_<?php echo $player['player_id']; ?>">0</span></span>
                        <span>AST: <span id="ast_2_<?php echo $player['player_id']; ?>">0</span></span>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="match-selection">
        <h2>Select Match to Manage</h2>
        <?php if (mysqli_num_rows($matches) > 0): ?>
            <?php while($match = mysqli_fetch_assoc($matches)): ?>
            <a href="?match_id=<?php echo $match['match_id']; ?>" class="match-item">
                <div class="match-teams">
                    <?php echo htmlspecialchars($match['team1_name']) . ' vs ' . htmlspecialchars($match['team2_name']); ?>
                </div>
                <div class="match-info">
                    <div class="match-date">
                        <?php echo date('M d, Y - g:i A', strtotime($match['match_date'])); ?>
                    </div>
                    <div class="current-score">
                        Current: <?php echo ($match['team1_score'] ?? 0); ?> - <?php echo ($match['team2_score'] ?? 0); ?>
                    </div>
                </div>
            </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No matches available. <a href="manage_matches.php">Create a match first</a></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</body>

<script>
let team1Score = <?php echo $match['team1_score'] ?? 0; ?>;
let team2Score = <?php echo $match['team2_score'] ?? 0; ?>;
let matchId = <?php echo $match_id ?? 'null'; ?>;

let gameRunning = false;
let gamePaused = false;
let gameTime = 720; // 12 minutes
let currentQuarter = 1;
let gameTimer;

// Player stats
let playerStats = {};

function updateTime() {
    let minutes = Math.floor(gameTime / 60);
    let seconds = gameTime % 60;
    document.getElementById('gameTime').textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
}

function startGame() {
    let btn = document.getElementById('gameBtn');
    let status = document.getElementById('gameStatus');
    
    if (!gameRunning) {
        gameRunning = true;
        btn.textContent = 'Stop Game';
        status.textContent = 'Live';
        
        gameTimer = setInterval(function() {
            if (gameRunning && !gamePaused && gameTime > 0) {
                gameTime--;
                updateTime();
                
                if (gameTime === 0) {
                    gameRunning = false;
                    btn.textContent = 'Start Game';
                    status.textContent = 'Quarter Ended';
                    clearInterval(gameTimer);
                }
            }
        }, 1000);
    } else {
        gameRunning = false;
        btn.textContent = 'Start Game';
        status.textContent = 'Stopped';
        clearInterval(gameTimer);
    }
}

function pauseGame() {
    gamePaused = !gamePaused;
    let btn = document.getElementById('pauseBtn');
    let status = document.getElementById('gameStatus');
    
    if (gamePaused) {
        btn.textContent = 'Resume';
        status.textContent = 'Paused';
    } else {
        btn.textContent = 'Pause';
        status.textContent = 'Live';
    }
}

function setTime() {
    let input = document.getElementById('timeInput').value;
    if (input.includes(':')) {
        let parts = input.split(':');
        gameTime = parseInt(parts[0]) * 60 + parseInt(parts[1]);
        updateTime();
    }
}

function addScore(team, points) {
    if (team === 1) {
        team1Score += points;
        document.getElementById('team1Score').textContent = team1Score;
        updateDB(1, team1Score);
    } else {
        team2Score += points;
        document.getElementById('team2Score').textContent = team2Score;
        updateDB(2, team2Score);
    }
}

function removeScore(team, points) {
    if (confirm('Remove ' + points + ' point(s)?')) {
        if (team === 1 && team1Score >= points) {
            team1Score -= points;
            document.getElementById('team1Score').textContent = team1Score;
            updateDB(1, team1Score);
        } else if (team === 2 && team2Score >= points) {
            team2Score -= points;
            document.getElementById('team2Score').textContent = team2Score;
            updateDB(2, team2Score);
        }
    }
}

function changeQuarter(direction) {
    let newQuarter = currentQuarter + direction;
    if (newQuarter >= 1 && newQuarter <= 4) {
        currentQuarter = newQuarter;
        document.getElementById('quarter').textContent = currentQuarter;
        gameTime = 720; // Reset time
        updateTime();
    }
}

function addPlayerStat(team, statType, value) {
    let select = document.getElementById('team' + team + 'PlayerSelect');
    let playerId = select.value;
    
    if (!playerId) {
        alert('Select a player first');
        return;
    }
    
    if (!playerStats[playerId]) {
        playerStats[playerId] = {points: 0, rebounds: 0, assists: 0};
    }
    
    playerStats[playerId][statType] += value;
    
    // Update display
    let shortStat = statType.substring(0, 3);
    let element = document.getElementById(shortStat + '_' + team + '_' + playerId);
    if (element) {
        element.textContent = playerStats[playerId][statType];
    }
    
    // If points, also add to team score
    if (statType === 'points') {
        addScore(team, value);
    }
}

function resetGame() {
    if (confirm('Reset everything? This will clear all scores and stats.')) {
        team1Score = 0;
        team2Score = 0;
        document.getElementById('team1Score').textContent = 0;
        document.getElementById('team2Score').textContent = 0;
        
        // Reset player stats
        playerStats = {};
        let statElements = document.querySelectorAll('[id^="pts_"], [id^="reb_"], [id^="ast_"]');
        statElements.forEach(element => element.textContent = '0');
        
        updateDB(1, 0);
        updateDB(2, 0);
    }
}

function endGame() {
    if (confirm('End the game?')) {
        gameRunning = false;
        clearInterval(gameTimer);
        document.getElementById('gameStatus').textContent = 'Game Ended';
        document.getElementById('gameBtn').textContent = 'Game Ended';
        document.getElementById('gameBtn').disabled = true;
    }
}

function updateDB(team, score) {
    if (!matchId) return;
    
    let formData = new FormData();
    formData.append('match_id', matchId);
    formData.append('team', team);
    formData.append('score', score);
    
    fetch('../api/update_score.php', {
        method: 'POST',
        body: formData
    });
}

updateTime();
</script>
</html>