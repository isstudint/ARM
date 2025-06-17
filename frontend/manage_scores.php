<?php 

session_start();
include('sidebar.php');

include('db.php');
include('func.php');

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
    

    $team1_players_query = "SELECT player_id, player_name, position FROM players WHERE team_id = {$match['team1_id']} ORDER BY player_name";
    $team1_players = mysqli_query($conn, $team1_players_query);
    
    $team2_players_query = "SELECT player_id, player_name, position FROM players WHERE team_id = {$match['team2_id']} ORDER BY player_name";
    $team2_players = mysqli_query($conn, $team2_players_query);
    

    // echo "<div style='background: yellow; padding: 10px; margin: 10px; z-index: 1000; position: relative;'>";
    // echo "DEBUG:  Team1 ID: {$match['team1_id']}, Team2 ID: {$match['team2_id']}<br>";
    // echo "Team1 players found: " . mysqli_num_rows($team1_players) . "<br>";
    // echo "Team2 players found: " . mysqli_num_rows($team2_players) . "<br>";
    // echo "</div>";


} else {
    $matches_query = "
    SELECT m.match_id, m.match_date, t1.team_name as team1_name, t2.team_name as team2_name,
           s.team1_score, s.team2_score
    FROM matches m
    JOIN teams t1 ON m.team1_id = t1.team_id
    JOIN teams t2 ON m.team2_id = t2.team_id
    LEFT JOIN scores s ON m.match_id = s.match_id
    ORDER BY m.match_date DESC
    LIMIT 20
    ";
    $matches = mysqli_query($conn, $matches_query);
}
?>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Scoreboard</title>
    <link rel="stylesheet" href="../Css/sidebar.css">
    
    <link rel="stylesheet" href="../Css/score.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>

    </style>
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
                    <button class="stat-btn" onclick="addPlayerStat(1, 'points', 1)">+1 PTS</button>
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
                    <div class="time-display" id="gameTime">8:00</div>
                </div>
                <div class="time-controls">
                    <input type="text" id="timeInput" placeholder="8:00" maxlength="5">
                    <button class = "set_time" onclick="setTime()">Set Time</button>
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
                        <button class = "pindot" onclick="addScore(1, 1)">+1</button>
                        <button class = "pindot" onclick="addScore(1, 2)">+2</button>
                        <button class = "pindot" onclick="addScore(1, 3)">+3</button>
                        <button class = "pindot bawas" onclick="undoLastScore(1)">Undo</button>
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
                        <button class = "pindot" onclick="addScore(2, 1)">+1</button>
                        <button class = "pindot" onclick="addScore(2, 2)">+2</button>
                        <button class = "pindot" onclick="addScore(2, 3)">+3</button>
                        <button class = "pindot bawas" onclick="undoLastScore(2)">Undo</button>
                    </div>
                </div>
            </div>


            <div class="bottom-controls">
                <button class = "pause" onclick="pauseGame()" id="pauseBtn">Pause</button>
                <button class = "reset" onclick="resetGame()">Reset All</button>
                <button class = "reset" onclick="endGame()">End Game</button>
                <button class = "correct" onclick="openCorrectionModal()">Correct Stats</button>
                
                <div class="status-controls" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; width: 100%; text-align: center;">
                    <h4>Match Status</h4>
                    <select class = "status" id="matchStatus" onchange="updateMatchStatus()" style = "width: 200px; padding: 8px; font-size: 16px;">
                        <option value="Scheduled" <?php echo ($match['status'] == 'Scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                        <option value="Ongoing" <?php echo ($match['status'] == 'Ongoing') ? 'selected' : ''; ?>>Live/Ongoing</option>
                        <option value="Completed" <?php echo ($match['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                        <option value="Cancelled" <?php echo ($match['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
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
                    <button class="stat-btn" onclick="addPlayerStat(2, 'points', 1)">+1 PTS</button>
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
                    <div class="player-name"><?php echo ($player['player_name']); ?></div>
                    <div class="player-position"><?php echo ($player['position']); ?></div>
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
    
    <!-- Correction Modal -->
    <div id="correctionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Correct Player Stats</h3>
                <span class="close" onclick="closeCorrectionModal()">&times;</span>
            </div>
            
            <div class="correction-section">
                <h4>Select Team & Player</h4>
                <select id="correctionTeam" onchange="updateCorrectionPlayers()">
                    <option value="">Select Team</option>
                    <option value="1"><?php echo htmlspecialchars($match['team1_name']); ?></option>
                    <option value="2"><?php echo htmlspecialchars($match['team2_name']); ?></option>
                </select>
                
                <select id="correctionPlayer">
                    <option value="">Select Player</option>
                </select>
            </div>
            
            <div class="correction-section">
                <h4>Remove Stats</h4>
                <div class="correction-buttons">
                    <button onclick="removePlayerStat('points', 1)">-1 PTS</button>
                    <button onclick="removePlayerStat('points', 2)">-2 PTS</button>
                    <button onclick="removePlayerStat('points', 3)">-3 PTS</button>
                </div>
                <div class="correction-buttons">
                    <button onclick="removePlayerStat('rebounds', 1)">-1 REB</button>
                    <button onclick="removePlayerStat('assists', 1)">-1 AST</button>
                </div>
            </div>
        </div>
    </div>
    
</body>

<script>



    
let team1Score = <?php echo ($match_id && $match) ? ($match['team1_score'] ?? 0) : 0; ?>;
let team2Score = <?php echo ($match_id && $match) ? ($match['team2_score'] ?? 0) : 0; ?>;
let matchId = <?php echo ($match_id && $match) ? $match_id : 'null'; ?>;

let gameRunning = false;
let gamePaused = false;
let gameTime = 480;
let currentQuarter = 1;
let gameTimer;
let syncTimer;
let playerStats = {}

// Load existing player stats and game state on page load
window.onload = function() {
    updateTime();
    if (matchId) {
        loadPlayerStats();
        loadGameState();
        startRealtimeSync();
    }
};

// Start real-time synchronization
function startRealtimeSync() {
    syncTimer = setInterval(function() {
        if (matchId) {
            syncLiveData();
        }
    }, 2000); // Sync every 2 seconds
}

function syncLiveData() {
    fetch('../api/get_live_data.php?match_id=' + matchId)
        .then(response => response.json())
        .then(data => {
            // Update scores if changed
            if (data.team1_score != team1Score) {
                team1Score = parseInt(data.team1_score);
                document.getElementById('team1Score').textContent = team1Score;
            }
            if (data.team2_score != team2Score) {
                team2Score = parseInt(data.team2_score);
                document.getElementById('team2Score').textContent = team2Score;
            }
            
            // Update player stats
            if (data.player_stats) {
                for (let playerId in data.player_stats) {
                    let stats = data.player_stats[playerId];
                    if (!playerStats[playerId]) {
                        playerStats[playerId] = {points: 0, rebounds: 0, assists: 0};
                    }
                    
                    playerStats[playerId].points = parseInt(stats.points) || 0;
                    playerStats[playerId].rebounds = parseInt(stats.rebounds) || 0;
                    playerStats[playerId].assists = parseInt(stats.assists) || 0;
                    
                    updatePlayerStatDisplay(playerId, 'pts', playerStats[playerId].points);
                    updatePlayerStatDisplay(playerId, 'reb', playerStats[playerId].rebounds);
                    updatePlayerStatDisplay(playerId, 'ast', playerStats[playerId].assists);
                }
            }
            
            // Update game state
            if (data.game_time !== undefined) {
                gameTime = parseInt(data.game_time);
                updateTime();
            }
            if (data.quarter !== undefined) {
                currentQuarter = parseInt(data.quarter);
                document.getElementById('quarter').textContent = currentQuarter;
            }
            if (data.game_status !== undefined) {
                document.getElementById('gameStatus').textContent = data.game_status;
            }
        })
        .catch(error => console.log('Sync error:', error));
}

function loadGameState() {
    fetch('../api/get_game_state.php?match_id=' + matchId)
        .then(response => response.json())
        .then(data => {
            if (data.game_time !== undefined) {
                gameTime = parseInt(data.game_time);
                updateTime();
            }
            if (data.quarter !== undefined) {
                currentQuarter = parseInt(data.quarter);
                document.getElementById('quarter').textContent = currentQuarter;
            }
            if (data.game_status !== undefined) {
                document.getElementById('gameStatus').textContent = data.game_status;
            }
        });
}

function saveGameState() {
    if (!matchId) return;
    
    let formData = new FormData();
    formData.append('match_id', matchId);
    formData.append('game_time', gameTime);
    formData.append('quarter', currentQuarter);
    formData.append('game_status', document.getElementById('gameStatus').textContent);
    
    fetch('../api/save_game_state.php', {
        method: 'POST',
        body: formData
    });
}

function loadPlayerStats() {
    if (!matchId) return;
    
    fetch('../api/get_stats.php?match_id=' + matchId)
        .then(response => response.json())
        .then(data => {
            if (data.player_stats) {
                for (let playerId in data.player_stats) {
                    let stats = data.player_stats[playerId];
                    playerStats[playerId] = {
                        points: parseInt(stats.points) || 0,
                        rebounds: parseInt(stats.rebounds) || 0,
                        assists: parseInt(stats.assists) || 0
                    };
                    
                    // Update display for both teams
                    updatePlayerStatDisplay(playerId, 'pts', playerStats[playerId].points);
                    updatePlayerStatDisplay(playerId, 'reb', playerStats[playerId].rebounds);
                    updatePlayerStatDisplay(playerId, 'ast', playerStats[playerId].assists);
                }
            }
        });
}

function updatePlayerStatDisplay(playerId, statType, value) {
    let element1 = document.getElementById(statType + '_1_' + playerId);
    let element2 = document.getElementById(statType + '_2_' + playerId);
    if (element1) element1.textContent = value;
    if (element2) element2.textContent = value;
}

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
        saveGameState();
        
        gameTimer = setInterval(function() {
            if (gameRunning && !gamePaused && gameTime > 0) {
                gameTime--;
                updateTime();
                saveGameState(); // Save every second
                
                if (gameTime === 0) {
                    gameRunning = false;
                    btn.textContent = 'Start Game';
                    status.textContent = 'Quarter Ended';
                    clearInterval(gameTimer);
                    saveGameState();
                }
            }
        }, 1000);
    } else {
        gameRunning = false;
        btn.textContent = 'Start Game';
        status.textContent = 'Stopped';
        clearInterval(gameTimer);
        saveGameState();
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
    saveGameState();
}

function setTime() {
    let input = document.getElementById('timeInput').value;
    if (input.match(/^\d{1,2}:\d{2}$/)) {
        let parts = input.split(':');
        gameTime = parseInt(parts[0]) * 60 + parseInt(parts[1]);
        updateTime();
        document.getElementById('timeInput').value = '';
        saveGameState();
    }
}

function addScore(team, points) {
    let oldScore = team === 1 ? team1Score : team2Score;
    
    if (team === 1) {
        team1Score += points;
        document.getElementById('team1Score').textContent = team1Score;
        updateDB(1, team1Score);
        
        // Log transaction
        logScoreTransaction(matchId, 1, oldScore, team1Score, points, 'add', 'Score added: +' + points);
    } else {
        team2Score += points;
        document.getElementById('team2Score').textContent = team2Score;
        updateDB(2, team2Score);
        
        // Log transaction
        logScoreTransaction(matchId, 2, oldScore, team2Score, points, 'add', 'Score added: +' + points);
    }
}

// Replace removeScore function with undoLastScore
function undoLastScore(team) {
    if (confirm('Undo the last score change for this team?')) {
        fetch(`../api/get_last_transaction.php?match_id=${matchId}&team=${team}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.transaction) {
                    let lastTransaction = data.transaction;
                    let newScore = lastTransaction.old_score;
                    
                    // Update the score to the previous value
                    if (team === 1) {
                        team1Score = newScore;
                        document.getElementById('team1Score').textContent = team1Score;
                    } else {
                        team2Score = newScore;
                        document.getElementById('team2Score').textContent = team2Score;
                    }
                    
                    updateDB(team, newScore);
                    
                    // Log the undo transaction
                    logScoreTransaction(matchId, team, lastTransaction.new_score, newScore, 
                        newScore - lastTransaction.new_score, 'undo', 
                        `Undid: ${lastTransaction.reason}`);
                        
                    console.log(`✅ Undid last score: Team ${team} | ${lastTransaction.new_score} → ${newScore}`);
                } else {
                    alert('No previous score changes found for this team');
                    console.log('❌ No transactions to undo');
                }
            })
            .catch(error => {
                console.error('Undo error:', error);
                alert('Error undoing score');
            });
    }
}

function logScoreTransaction(matchId, team, oldScore, newScore, change, actionType, reason) {
    let formData = new FormData();
    formData.append('match_id', matchId);
    formData.append('team_number', team);
    formData.append('old_score', oldScore);
    formData.append('new_score', newScore);
    formData.append('score_change', change);
    formData.append('action_type', actionType);
    formData.append('reason', reason);
    
    fetch('../api/log_score_transaction.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log(`✅ Score Transaction Logged: Team ${team} | ${oldScore} → ${newScore} (${change > 0 ? '+' : ''}${change}) | ${reason}`);
        } else {
            console.error('❌ Transaction failed:', data.error);
        }
    })
    .catch(error => console.error('Transaction error:', error));
}

function toggleTransactionLog() {
    const logPanel = document.getElementById('transactionLog');
    if (logPanel.style.display === 'none') {
        logPanel.style.display = 'block';
        loadTransactionLog();
    } else {
        logPanel.style.display = 'none';
    }
}

function loadTransactionLog() {
    fetch(`../api/get_score_transactions.php?match_id=${matchId}`)
        .then(response => response.json())
        .then(data => {
            let html = '';
            data.forEach(transaction => {
                html += `
                    <div style="margin-bottom: 10px; padding: 8px; background: white; border-radius: 4px;">
                        <strong>Team ${transaction.team_number}</strong>: ${transaction.old_score} → ${transaction.new_score} 
                        (${transaction.score_change > 0 ? '+' : ''}${transaction.score_change})
                        <br><small>${transaction.reason} - ${transaction.created_at}</small>
                    </div>
                `;
            });
            document.getElementById('transactionList').innerHTML = html || 'No transactions yet';
        });
}

function changeQuarter(direction) {
    let newQuarter = currentQuarter + direction;
    if (newQuarter >= 1 && newQuarter <= 4) {
        currentQuarter = newQuarter;
        document.getElementById('quarter').textContent = currentQuarter;
        gameTime = 480;
        updateTime();
        saveGameState();
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
    
    // Update display immediately
    let shortStat = statType.substring(0, 3);
    updatePlayerStatDisplay(playerId, shortStat, playerStats[playerId][statType]);
    
    // Save to database
    savePlayerStat(playerId, statType, playerStats[playerId][statType]);
    
    // If points, also add to team score
    if (statType === 'points') {
        addScore(team, value);
    }
}

function savePlayerStat(playerId, statType, value) {
    if (!matchId) return;
    
    let formData = new FormData();
    formData.append('match_id', matchId);
    formData.append('player_id', playerId);
    formData.append('stat_type', statType);
    formData.append('value', value);
    
    fetch('../api/save_player_stat.php', {
        method: 'POST',
        body: formData
    });
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
        
        // Reset game state
        gameTime = 480;
        currentQuarter = 1;
        gameRunning = false;
        gamePaused = false;
        document.getElementById('quarter').textContent = currentQuarter;
        document.getElementById('gameStatus').textContent = 'Ready';
        document.getElementById('gameBtn').textContent = 'Start Game';
        document.getElementById('pauseBtn').textContent = 'Pause';
        updateTime();
        
        updateDB(1, 0);
        updateDB(2, 0);
        
        // Reset player stats in database
        if (matchId) {
            let formData = new FormData();
            formData.append('match_id', matchId);
            
            fetch('../api/reset_stats.php', {
                method: 'POST',
                body: formData
            });
        }
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

// NEW: Update match status
function updateMatchStatus() {
    if (!matchId) return;
    
    let status = document.getElementById('matchStatus').value;
    let formData = new FormData();
    formData.append('match_id', matchId);
    formData.append('status', status);
    
    fetch('../api/update_match_status.php', {
        method: 'POST',
        body: formData
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              // Auto-update game controls based on status
              if (status === 'Ongoing') {
                  document.getElementById('gameStatus').textContent = 'Live';
              } else if (status === 'Completed') {
                  document.getElementById('gameStatus').textContent = 'Final';
              }
          }
      });
}

// Correction modal functions
function openCorrectionModal() {
    document.getElementById('correctionModal').style.display = 'block';
}

function closeCorrectionModal() {
    document.getElementById('correctionModal').style.display = 'none';
    document.getElementById('correctionTeam').value = '';
    document.getElementById('correctionPlayer').value = '';
}

function updateCorrectionPlayers() {
    let teamSelect = document.getElementById('correctionTeam');
    let playerSelect = document.getElementById('correctionPlayer');
    let team = teamSelect.value;
    
    playerSelect.innerHTML = '<option value="">Select Player</option>';
    
    if (team) {
        let sourceSelect = document.getElementById('team' + team + 'PlayerSelect');
        for (let i = 1; i < sourceSelect.options.length; i++) {
            let option = sourceSelect.options[i];
            playerSelect.innerHTML += `<option value="${option.value}">${option.text}</option>`;
        }
    }
}

function removePlayerStat(statType, value) {
    let team = document.getElementById('correctionTeam').value;
    let playerId = document.getElementById('correctionPlayer').value;
    
    if (!team || !playerId) {
        alert('Please select team and player first');
        return;
    }
    
    if (!playerStats[playerId]) {
        playerStats[playerId] = {points: 0, rebounds: 0, assists: 0};
    }
    
    if (playerStats[playerId][statType] < value) {
        alert('Cannot go below 0 ' + statType);
        return;
    }
    
    playerStats[playerId][statType] -= value;
    
    // Update display immediately
    let shortStat = statType.substring(0, 3);
    updatePlayerStatDisplay(playerId, shortStat, playerStats[playerId][statType]);
    
    // Save to database
    savePlayerStat(playerId, statType, playerStats[playerId][statType]);
    
    // If points, also remove from team score
    if (statType === 'points') {
        removeScore(parseInt(team), value);
    }
    
    closeCorrectionModal();
}

// Close modal when clicking outside
window.onclick = function(event) {
    let modal = document.getElementById('correctionModal');
    if (event.target == modal) {
        closeCorrectionModal();
    }
}

// Cleanup timers when page unloads
window.onbeforeunload = function() {
    if (gameTimer) clearInterval(gameTimer);
    if (syncTimer) clearInterval(syncTimer);
};


document.addEventListener("DOMContentLoaded", () => {
        const sidebarToggler = document.querySelector(".sidebar-toggler");
        const sidebar = document.querySelector(".sidebar");

        sidebarToggler.addEventListener("click", () => {
          sidebar.classList.toggle("collapsed");
        });
      });
</script>

</html>