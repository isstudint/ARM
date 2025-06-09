<html>

<?php 
include('sidebar.php');

include('db.php');

$match_id = isset($_GET['match_id']) ? (int)$_GET['match_id'] : null;

if ($match_id) {
    // Get match details
    $match_query = "
    SELECT m.*, t1.team_name as team1_name, t2.team_name as team2_name,
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
        die("Match not found");
    }
} else {
    // Show available matches to select
    $matches_query = "
    SELECT m.*, t1.team_name as team1_name, t2.team_name as team2_name
    FROM matches m
    JOIN teams t1 ON m.team1_id = t1.team_id
    JOIN teams t2 ON m.team2_id = t2.team_id
    ORDER BY m.match_date DESC
    LIMIT 10
    ";
    $matches = mysqli_query($conn, $matches_query);
}
?>

<style>* {
  padding: 0;
  margin: 0;
  box-sizing: border-box;
  font-family: 'Poppins', sans-serif;
}
body {
  height: 100vh;
  background: linear-gradient(135deg, #dcf0fc 0%, #e3f0ff 100%);
}
.scoreboard {
  background-color: white;
  width: min(90%, 34em);
  position: absolute;
  transform: translate(-50%, -50%);
  left: 50%;
  top: 50%;
  padding: 3em;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
  border: 1px solid #e2e8f0;
  display: grid;
  grid-template-columns: 2fr 1fr 2fr;
  align-items: center;
}
.team {
  text-align: center;
  background-color: #f8fafc;
  padding: 2em;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}
button {
  cursor: pointer;
}
#reset-btn {
  background-color: transparent;
  border: 3px solid #2d53da;
  color: #2d53da;
  height: 5em;
  width: 5em;
  margin: auto;
  border-radius: 12px;
  font-weight: 600;
  transition: all 0.3s ease;
}
#reset-btn:hover {
  background-color: #2d53da;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(45, 83, 218, 0.3);
}
.team h2 {
  color: #1a202c;
  font-weight: 600;
  margin-bottom: 1em;
  font-size: 1.5em;
}
.team p {
  color: #2d53da;
  font-size: 3.75em;
  font-weight: 700;
  margin-bottom: 0.5em;
}
.btn-container {
  width: 100%;
  display: flex;
  justify-content: space-between;
  gap: 0.5em;
}
.team button {
  background-color: #2d53da;
  border: none;
  outline: none;
  padding: 0.5em 1em;
  border-radius: 8px;
  font-weight: 600;
  font-size: 1.3em;
  color: white;
  transition: all 0.3s ease;
  flex: 1;
}
.team button:hover {
  background-color: #1e40af;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(45, 83, 218, 0.3);
}
.match-selection {
  background-color: white;
  width: min(90%, 40em);
  position: absolute;
  transform: translate(-50%, -50%);
  left: 50%;
  top: 50%;
  padding: 2em;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
  border: 1px solid #e2e8f0;
}
.match-selection h2 {
  color: #1a202c;
  font-weight: 600;
  margin-bottom: 1.5em;
  text-align: center;
}
.match-item {
  display: block;
  background-color: #f8fafc;
  padding: 1em;
  margin-bottom: 0.5em;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  text-decoration: none;
  color: #1a202c;
  transition: all 0.3s ease;
}
.match-item:hover {
  background-color: #2d53da;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(45, 83, 218, 0.3);
}
.match-teams {
  font-weight: 600;
  font-size: 1.1em;
  margin-bottom: 0.3em;
}
.match-date {
  font-size: 0.9em;
  opacity: 0.8;
}
</style>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Scoreboard</title>
    <link rel="stylesheet" href="../Css/sidebar.css">
</head>

<body>
    <?php if ($match_id && $match): ?>
    <div class="scoreboard">
      <div class="team">
        <h2><?php echo htmlspecialchars($match['team1_name']); ?></h2>
        <p id="team1Score"><?php echo $match['team1_score'] ?? 0; ?></p>
        <div class="btn-container">
          <button onclick="incrementScore(1)">+</button>
          <button onclick="decrementScore(1)">-</button>
        </div>
      </div>
      <button id="reset-btn" onclick="resetScores()">Reset</button>
      <div class="team">
        <h2><?php echo htmlspecialchars($match['team2_name']); ?></h2>
        <p id="team2Score"><?php echo $match['team2_score'] ?? 0; ?></p>
        <div class="btn-container">
          <button onclick="incrementScore(2)">+</button>
          <button onclick="decrementScore(2)">-</button>
        </div>
      </div>
    </div>
    <?php else: ?>
    <div class="match-selection">
        <h2>Select Match to Manage</h2>
        <?php while($match = mysqli_fetch_assoc($matches)): ?>
        <a href="?match_id=<?php echo $match['match_id']; ?>" class="match-item">
            <div class="match-teams">
                <?php echo htmlspecialchars($match['team1_name']) . ' vs ' . htmlspecialchars($match['team2_name']); ?>
            </div>
            <div class="match-date">
                <?php echo date('M d, Y', strtotime($match['match_date'])); ?>
            </div>
        </a>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>
</body>

<script>
let team1Score = <?php echo $match['team1_score'] ?? 0; ?>;
let team2Score = <?php echo $match['team2_score'] ?? 0; ?>;
let matchId = <?php echo $match_id ?? 'null'; ?>;

let team1ScoreValue = document.getElementById("team1Score");
let team2ScoreValue = document.getElementById("team2Score");

function incrementScore(team) {
    if (team === 1) {
        team1Score++;
        team1ScoreValue.textContent = team1Score;
        updateDatabase(1, team1Score);
    } else if (team === 2) {
        team2Score++;
        team2ScoreValue.textContent = team2Score;
        updateDatabase(2, team2Score);
    }
}

function decrementScore(team) {
    if (team === 1 && team1Score > 0) {
        team1Score--;
        team1ScoreValue.textContent = team1Score;
        updateDatabase(1, team1Score);
    } else if (team === 2 && team2Score > 0) {
        team2Score--;
        team2ScoreValue.textContent = team2Score;
        updateDatabase(2, team2Score);
    }
}

function resetScores() {
    if (confirm('Reset both team scores to 0?')) {
        team1Score = 0;
        team2Score = 0;
        team1ScoreValue.textContent = team1Score;
        team2ScoreValue.textContent = team2Score;
        updateDatabase(1, team1Score);
        updateDatabase(2, team2Score);
    }
}

function updateDatabase(team, score) {
    if (!matchId) return;
    
    fetch('../frontend/update_score.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `match_id=${matchId}&team=${team}&score=${score}`
    }).catch(error => {
        console.log('Update failed:', error);
    });
}
</script>

</html>