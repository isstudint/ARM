<?php 

    if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 


    include('db.php');



    $match_query = "
    SELECT m.match_id, m.status, m.match_date, m.match_type,
           t1.team_name as team1_name, t1.logo as team1_logo, m.team1_id,
           t2.team_name as team2_name, t2.logo as team2_logo, m.team2_id,
           COALESCE(s.team1_score, 0) as team1_score, 
           COALESCE(s.team2_score, 0) as team2_score
    FROM matches m
    INNER JOIN teams t1 ON m.team1_id = t1.team_id
    INNER JOIN teams t2 ON m.team2_id = t2.team_id
    LEFT JOIN scores s ON m.match_id = s.match_id
    WHERE (
        (m.match_date >= CURDATE() AND m.status IN ('Scheduled', 'Ongoing')) 
        OR 
        (m.status = 'Ongoing' AND m.match_type IN ('semifinal', 'final'))
    )
    ORDER BY 
        CASE WHEN m.status = 'Ongoing' THEN 1 ELSE 2 END,
        CASE WHEN m.match_type = 'final' THEN 1 
             WHEN m.match_type = 'semifinal' THEN 2 
             ELSE 3 END,
        m.match_date ASC
    LIMIT 1
    ";
    $match_result = mysqli_query($conn, $match_query);
    $today_match = mysqli_fetch_assoc($match_result);
    
    // Get team standings
    $standings_query = "
        SELECT t.team_id, t.team_name, t.logo,
               COUNT(m.match_id) AS total_games,
               SUM((t.team_id = m.team1_id AND s.team1_score > s.team2_score) OR 
                   (t.team_id = m.team2_id AND s.team2_score > s.team1_score)) AS wins,
               SUM((t.team_id = m.team1_id AND s.team1_score < s.team2_score) OR 
                   (t.team_id = m.team2_id AND s.team2_score < s.team1_score)) AS losses
        FROM teams t
        LEFT JOIN matches m ON t.team_id = m.team1_id OR t.team_id = m.team2_id
        LEFT JOIN scores s ON m.match_id = s.match_id
        WHERE s.team1_score IS NOT NULL AND s.team2_score IS NOT NULL
        GROUP BY t.team_id, t.team_name, t.logo
        ORDER BY wins DESC, total_games DESC
        LIMIT 4
    ";
    $standings_result = mysqli_query($conn, $standings_query);
    
    // Get all teams for team logos section
    $teams_query = "SELECT team_id, team_name, logo FROM teams ORDER BY team_name";
    $teams_result = mysqli_query($conn, $teams_query);
    
    // Get recent match history
    $history_query = "
        SELECT m.*, t1.team_name as team1_name, t2.team_name as team2_name,
               t1.logo as team1_logo, t2.logo as team2_logo,
               s.team1_score, s.team2_score
        FROM matches m
        JOIN teams t1 ON m.team1_id = t1.team_id
        JOIN teams t2 ON m.team2_id = t2.team_id
        LEFT JOIN scores s ON m.match_id = s.match_id
        WHERE s.team1_score IS NOT NULL AND s.team2_score IS NOT NULL
        AND m.status = 'Completed'
        ORDER BY m.match_date DESC
        LIMIT 8
    ";
    $history_result = mysqli_query($conn, $history_query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&"
    />
    <link rel="stylesheet" href="../Css/landig.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
</head>

<?php include("sidebar.php") ?>

    <div class="container">
        <main class="main-content">
            <div class="match" id = "laman">
                <h1>
                    <?php 
                    if ($today_match) {
                        echo $today_match['status'] == 'Ongoing' ? 'LIVE MATCH' : 'MATCH TODAY';
                    } else {
                        echo 'NO MATCH TODAY';
                    }
                    ?>
                </h1>
                
                <?php if ($today_match): ?>
                <div class="teams">                   
                  <div class="team1">
                    <h3><?php echo htmlspecialchars($today_match['team1_name']); ?></h3>
                    <?php if(!empty($today_match['team1_logo']) && file_exists('../' . $today_match['team1_logo'])): ?>
                        <img src="../<?php echo htmlspecialchars($today_match['team1_logo']); ?>" alt="<?php echo htmlspecialchars($today_match['team1_name']); ?>" class="team-logo">
                    <?php else: ?>
                        <div class="team-logo-placeholder"><?php echo substr($today_match['team1_name'], 0, 2); ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="scored"><h1 id="liveTeam1Score"><?php echo $today_match['team1_score']; ?></h1></div>
                  <div class="score">
                    <h1>SCORE</h1>
                    <div class="oras"><h2 id="liveGameTime">12:00</h2></div>
                    <div class="status" id="liveGameStatus">
                        <?php 
                        if ($today_match) {
                            switch($today_match['status']) {
                                case 'Ongoing': echo 'LIVE'; break;
                                case 'Scheduled': echo 'SCHEDULED'; break;
                                case 'Completed': echo 'FINAL'; break;
                                default: echo $today_match['status']; break;
                            }
                        }
                        ?>
                    </div>
                  </div>
                  
                  <div class="scored"><h1 id="liveTeam2Score"><?php echo $today_match['team2_score']; ?></h1></div>

                <div class="team2"> 
                  <h3><?php echo htmlspecialchars($today_match['team2_name']); ?></h3>
                  <?php if(!empty($today_match['team2_logo']) && file_exists('../' . $today_match['team2_logo'])): ?>
                      <img src="../<?php echo htmlspecialchars($today_match['team2_logo']); ?>" alt="<?php echo htmlspecialchars($today_match['team2_name']); ?>" class="team-logo">
                  <?php else: ?>
                      <div class="team-logo-placeholder"><?php echo substr($today_match['team2_name'], 0, 2); ?></div>
                  <?php endif; ?>
                </div>                
                </div>
                <?php else: ?>
                <div class="no-match-today">
                    <p>No matches scheduled for today. Check back tomorrow!</p>
                </div>
                <?php endif; ?>
            </div>

            <div class="human">
                <div class="coaches laman">
                    <a href="standing.php"><h1 >Standing</h1></a>
                    <table class="player-table">
                      <thead class="thead">
                        <tr>
                            <td>Logo</td>
                            <td>Team name</td>
                            <td>Record</td>
                            <td>Win %</td>
                        </tr>
                      <thead>
                      <tbody>
                        <?php if ($standings_result && mysqli_num_rows($standings_result) > 0): ?>
                            <?php while($team = mysqli_fetch_assoc($standings_result)): ?>
                            <tr>
                                <td class = "litrato">
                                    <?php if(!empty($team['logo']) && file_exists('../' . $team['logo'])): ?>
                                        <img src="../<?php echo $team['logo']; ?>" alt="<?php echo $team['team_name']; ?>" class="player-image">
                                    <?php else: ?>
                                        <div class="team-placeholder"><?php echo substr($team['team_name'], 0, 2); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($team['team_name']); ?></td>
                                <td><?php echo ($team['wins'] ?? 0) . ' - ' . ($team['losses'] ?? 0); ?></td>
                                <td>
                                    <?php 
                                    $total = ($team['wins'] ?? 0) + ($team['losses'] ?? 0);
                                    $win_pct = $total > 0 ? round(($team['wins'] ?? 0) / $total * 100) : 0;
                                    echo $win_pct . '%';
                                    ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4">No standings data available</td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
            <div class="team" id = "laman">
              <h1 >Teams</h1>
              <div class="scroller">
                <div class="scrolling">
                <?php if ($teams_result && mysqli_num_rows($teams_result) > 0): ?>
                    <?php while($team = mysqli_fetch_assoc($teams_result)): ?>
                        <?php if(!empty($team['logo']) && file_exists('../' . $team['logo'])): ?>
                            <img src="../<?php echo $team['logo']; ?>" alt="<?php echo $team['team_name']; ?>" class="teamlogo">
                        <?php endif; ?>
                    <?php endwhile; ?>
                    <!-- Duplicate for smooth scrolling -->
                    <?php mysqli_data_seek($teams_result, 0); ?>
                    <?php while($team = mysqli_fetch_assoc($teams_result)): ?>
                        <?php if(!empty($team['logo']) && file_exists('../' . $team['logo'])): ?>
                            <img src="../<?php echo $team['logo']; ?>" alt="<?php echo $team['team_name']; ?>" class="teamlogo">
                        <?php endif; ?>
                    <?php endwhile; ?>
                <?php endif; ?>
              </div>
            </div>
        </main>

        <div class="right">
          <h1>Match history</h1>

          <table class="match-history-table">
            <thead>
              <tr>
                <th>Match</th>
                <th class="winner">Winner</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($history_result && mysqli_num_rows($history_result) > 0): ?>
                  <?php while($match = mysqli_fetch_assoc($history_result)): ?>
                  <tr>
                    <td class="match-history">
                      <div class="teams-wrapper">
                        <div class="team-history">
                          <?php if(!empty($match['team1_logo']) && file_exists('../' . $match['team1_logo'])): ?>
                              <img src="../<?php echo $match['team1_logo']; ?>" alt="<?php echo $match['team1_name']; ?>" class="teamlogo">
                          <?php endif; ?>
                          <div class="scorees"><?php echo $match['team1_score']; ?></div>
                        </div>
                        <div class="vs">VS</div>
                        <div class="team-history">
                          <?php if(!empty($match['team2_logo']) && file_exists('../' . $match['team2_logo'])): ?>
                              <img src="../<?php echo $match['team2_logo']; ?>" alt="<?php echo $match['team2_name']; ?>" class="teamlogo">
                          <?php endif; ?>
                          <div class="scorees"><?php echo $match['team2_score']; ?></div>
                        </div>
                      </div>
                    </td>
                    <td>
                        <?php 
                        if ($match['team1_score'] > $match['team2_score']) {
                            echo htmlspecialchars($match['team1_name']);
                        } else {
                            echo htmlspecialchars($match['team2_name']);
                        }
                        ?>
                    </td>
                  </tr>
                  <?php endwhile; ?>
              <?php else: ?>
                  <tr><td colspan="2">No match history available</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
  </div>


        
</div>

    <!-- <div id="div2">
                <div id="container">
                    <div class="section">
                      <h1>ARM</h1>
                      <h3>Never miss a moment.  </h3>
                      <h3>ARM brings the game</h3>
                      <h3>to your screen in real-time.</h3>
                    </div>
                    <div class="vs">VS</div>
                    <div class="team-history">
                      <img src="../Images/nba-haws.png" alt="Hawks Logo" class="teamlogo">
                      <div class="score">98</div>
                    </div>
                  </div>
                </td>
                <td>Team 1</td>
              </tr>
            </tbody>
          </table>
</div> -->



        
    </div>

     <script>
      document.addEventListener("DOMContentLoaded", () => {
        const sidebarToggler = document.querySelector(".sidebar-toggler");
        const sidebar = document.querySelector(".sidebar");

        sidebarToggler.addEventListener("click", () => {
          sidebar.classList.toggle("collapsed");
        });
      });
      
      <?php if ($today_match): ?>
      // Real-time updates for any match (not just ongoing)
      const matchId = <?php echo $today_match['match_id']; ?>;
      
      function updateLiveTime(seconds) {
          let minutes = Math.floor(seconds / 60);
          let secs = seconds % 60;
          document.getElementById('liveGameTime').textContent = minutes + ':' + (secs < 10 ? '0' + secs : secs);
      }
      

      setInterval(function() {
          fetch('../api/get_live_data.php?match_id=' + matchId)
              .then(response => response.json())
              .then(data => {
                  // Update scores
                  if (data.team1_score !== undefined) {
                      document.getElementById('liveTeam1Score').textContent = data.team1_score;
                  }
                  if (data.team2_score !== undefined) {
                      document.getElementById('liveTeam2Score').textContent = data.team2_score;
                  }
                  
               
                  if (data.game_time !== undefined) {
                      updateLiveTime(parseInt(data.game_time));
                  }
                  if (data.game_status !== undefined) {
                      document.getElementById('liveGameStatus').textContent = data.game_status;
                  }
              })
              .catch(error => console.log('Live update error:', error));
      }, 2000); 
      <?php endif; ?>
    </script>


</body>
</html>