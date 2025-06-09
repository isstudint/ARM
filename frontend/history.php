<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "arm");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


$history_query = "
    SELECT 
        m.match_id,
        m.match_date,
        t1.team_name as team1_name,
        t1.logo as team1_logo,
        t2.team_name as team2_name,
        t2.logo as team2_logo,
        s.team1_score,
        s.team2_score,
        CASE 
            WHEN s.team1_score > s.team2_score THEN t1.team_name
            WHEN s.team2_score > s.team1_score THEN t2.team_name
            ELSE 'Tie'
        END as winner_name,
        CASE 
            WHEN s.team1_score > s.team2_score THEN 'team1'
            WHEN s.team2_score > s.team1_score THEN 'team2'
            ELSE 'tie'
        END as winner
    FROM matches m
    JOIN teams t1 ON m.team1_id = t1.team_id
    JOIN teams t2 ON m.team2_id = t2.team_id
    JOIN scores s ON m.match_id = s.match_id
    WHERE s.team1_score IS NOT NULL AND s.team2_score IS NOT NULL
    ORDER BY m.match_date DESC
";

$games_result = mysqli_query($conn, $history_query);
$games = [];
while ($row = mysqli_fetch_assoc($games_result)) {
    $games[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../Css/landig.css" />
  <link rel="stylesheet" href="../Css/history1.css" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
  <title>Game History</title>

  </head>
<body>
  <?php include("sidebar.php") ?>

  <div class="header">
    <h1>Game History</h1>
  </div>

  <!-- Game History Display -->
  <div class="scoreboard">
    <?php if (empty($games)): ?>
      <div class="no-games">
        <div style="font-size: 48px; color: #ccc; margin-bottom: 20px;">🏀</div>
        <h3 style="color: #666; margin-bottom: 10px;">No Completed Games Yet</h3>
        <p  style="color: #888;">Games will appear here once matches are completed and scores are recorded.</p>
      </div>
    <?php else: ?>
      <?php foreach ($games as $game): ?>
        <div class="game-row">
          <div class="teams-info">
            <div class="team-info">
              <div class="team-logo">
                <img src="../<?php echo htmlspecialchars($game['team1_logo']); ?>" 
                     alt="<?php echo htmlspecialchars($game['team1_name']); ?>">
              </div>
              <div class="team-name <?php echo ($game['winner'] == 'team1') ? 'winner' : ''; ?>">
                <?php echo htmlspecialchars($game['team1_name']); ?>
                <?php if ($game['winner'] == 'team1'): ?>
                  <span class="winner-crown">👑</span>
                <?php endif; ?>
              </div>
            </div>
            
            <div class="team-info">
              <div class="team-logo">
                <img src="../<?php echo htmlspecialchars($game['team2_logo']); ?>" 
                     alt="<?php echo htmlspecialchars($game['team2_name']); ?>">
              </div>
              <div class="team-name <?php echo ($game['winner'] == 'team2') ? 'winner' : ''; ?>">
                <?php echo htmlspecialchars($game['team2_name']); ?>
                <?php if ($game['winner'] == 'team2'): ?>
                  <span class="winner-crown">👑</span>
                <?php endif; ?>
              </div>
            </div>
          </div>
          
          <div class="game-divider"></div>
          
          <div class="scores-section">
            <div class="team-score <?php echo ($game['winner'] == 'team1') ? 'winner' : ''; ?>">
              <?php echo $game['team1_score']; ?>
            </div>
            <div class="team-score <?php echo ($game['winner'] == 'team2') ? 'winner' : ''; ?>">
              <?php echo $game['team2_score']; ?>
            </div>
            <div class="game-date">
              <?php echo date('M j, Y', strtotime($game['match_date'])); ?>
              <small><?php echo date('g:i A', strtotime($game['match_date'])); ?></small>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <script>
   document.addEventListener("DOMContentLoaded", () => {
      const sidebarToggler = document.querySelector(".sidebar-toggler");
      const sidebar = document.querySelector(".sidebar");

      if (sidebarToggler && sidebar) {
        sidebarToggler.addEventListener("click", () => {
          sidebar.classList.toggle("collapsed");
        });
      }
    });
  </script>
</body>
</html>