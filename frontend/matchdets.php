<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Css/landig.css" />
    <link rel="stylesheet" href="../Css/landing.css" />
    <link rel="stylesheet" href="../Css/matchdets.css" />
    <title>Document</title>
</head>
<body>
    <?php include("sidebar.php") ?>

    <div class="container">
        <main class="main-content">
            <?php
            $conn = mysqli_connect("localhost", "root", "", "arm");

            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            // Get the latest live match - UPDATED to consider status
            $match_query = "
                SELECT m.*, t1.team_name as team1_name, t2.team_name as team2_name,
                       t1.logo as team1_logo, t2.logo as team2_logo,
                       COALESCE(s.team1_score, 0) as team1_score, 
                       COALESCE(s.team2_score, 0) as team2_score
                FROM matches m
                JOIN teams t1 ON m.team1_id = t1.team_id
                JOIN teams t2 ON m.team2_id = t2.team_id
                LEFT JOIN scores s ON m.match_id = s.match_id
                WHERE m.match_date >= CURDATE() 
                AND m.status IN ('Scheduled', 'Ongoing')
                ORDER BY 
                    CASE WHEN m.status = 'Ongoing' THEN 1 ELSE 2 END,
                    m.match_date ASC
                LIMIT 1
            ";
            $match_result = mysqli_query($conn, $match_query);
            
            if (!$match_result) {
                echo "Error in match query: " . mysqli_error($conn);
                $match = false;
            } else {
                $match = mysqli_fetch_assoc($match_result);
            }

            $team1_players = false;
            $team2_players = false;

            if ($match) {
                // Debug: para sa team dito pre make sure lang kung tama ang query "<!-- DEBUG: Match uses Team1 ID: {$match['team1_id']}, Team2 ID: {$match['team2_id']} -->";
                

                // Remove COALESCE from SQL and handle in PHP
                $team1_players_query = "SELECT p.player_id, p.player_name, p.position, p.jersey_num,
                                       ps.points, ps.rebounds, ps.assists
                                       FROM players p 
                                       LEFT JOIN player_stats ps ON p.player_id = ps.player_id AND ps.match_id = {$match['match_id']}
                                       WHERE p.team_id = {$match['team1_id']}
                                       ORDER BY p.player_name";
                
                /* echo "<!-- DEBUG: Team1 Query: $team1_players_query -->"; */
                $team1_players = mysqli_query($conn, $team1_players_query);
                
                if (!$team1_players) {
                    echo "<!-- ERROR in team1 players query: " . mysqli_error($conn) . " -->";
                } else {
                    echo "<!-- Team 1 players found: " . mysqli_num_rows($team1_players) . " -->";
                }
                
                // Debug: para sa team dito pre make sure lang kung tama ang query 
                $team2_players_query = "SELECT p.player_id, p.player_name, p.position, p.jersey_num,
                                       COALESCE(ps.points, 0) as points,
                                       COALESCE(ps.rebounds, 0) as rebounds,
                                       COALESCE(ps.assists, 0) as assists
                                       FROM players p 
                                       LEFT JOIN player_stats ps ON p.player_id = ps.player_id AND ps.match_id = {$match['match_id']}
                                       WHERE p.team_id = {$match['team2_id']}
                                       ORDER BY p.player_name";
                
                // echo "<!-- DEBUG: Team2 Query: $team2_players_query -->";
                $team2_players = mysqli_query($conn, $team2_players_query);
                
                if (!$team2_players) {
                    echo "<!-- ERROR in team2 players query: " . mysqli_error($conn) . " -->";
                } else {
                    echo "<!-- Team 2 players found: " . mysqli_num_rows($team2_players) . " -->";
                }
                
                // // Show all available players for debugging
                // $all_players_query = "SELECT player_id, player_name, team_id FROM players";
                // $all_players = mysqli_query($conn, $all_players_query);
                // echo "<!-- DEBUG: All players in database: -->";
                // while($p = mysqli_fetch_assoc($all_players)) {
                //     echo "<!-- Player: {$p['player_name']} (ID: {$p['player_id']}, Team: {$p['team_id']}) -->";
                // }
            }
            ?>

            <?php if ($match): ?>
            <div class="match" id="laman">
                <h1>
                    <?php 
                    if ($match['status'] == 'Ongoing') {
                        echo 'LIVE MATCH';
                    } else {
                        echo 'UPCOMING MATCH';
                    }
                    ?>
                </h1>
                <div class="teams">                   
                  <div class="team1">
                    <h3><?php echo htmlspecialchars($match['team1_name']); ?></h3>
                    <?php if(!empty($match['team1_logo']) && file_exists('../' . $match['team1_logo'])): ?>
                        <img src="../<?php echo htmlspecialchars($match['team1_logo']); ?>" alt="<?php echo htmlspecialchars($match['team1_name']); ?>" class="team-logo">
                    <?php else: ?>
                        <div class="team-logo-placeholder"><?php echo substr($match['team1_name'], 0, 2); ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="scored"><h1 id="liveTeam1Score"><?php echo $match['team1_score'] ?? 0; ?></h1></div>
                  <div class="score">
                    <h1>SCORE</h1>
                    <div class="oras"><h2 id="liveGameTime">12:00</h2></div>
                    <div class="status" id="liveGameStatus">
                        <?php 
                        switch($match['status']) {
                            case 'Ongoing': echo 'LIVE'; break;
                            case 'Scheduled': echo 'SCHEDULED'; break;
                            case 'Completed': echo 'FINAL'; break;
                            default: echo $match['status']; break;
                        }
                        ?>
                    </div>
                  </div>
                  
                  <div class="scored"><h1 id="liveTeam2Score"><?php echo $match['team2_score'] ?? 0; ?></h1></div>

                <div class="team2"> 
                  <h3><?php echo htmlspecialchars($match['team2_name']); ?></h3>
                  <?php if(!empty($match['team2_logo']) && file_exists('../' . $match['team2_logo'])): ?>
                      <img src="../<?php echo htmlspecialchars($match['team2_logo']); ?>" alt="<?php echo htmlspecialchars($match['team2_name']); ?>" class="team-logo">
                  <?php else: ?>
                      <div class="team-logo-placeholder"><?php echo substr($match['team2_name'], 0, 2); ?></div>
                  <?php endif; ?>
                </div>                
                </div>
            </div>

            <div class="below">
              <div class="left">
                <div class="player laman">
                    <h1><?php echo htmlspecialchars($match['team1_name']); ?> Players</h1>
                    <table class="player-table">
                      <thead class="thead">
                        <tr>
                          <td>Jersey #</td>
                          <td>Player name</td>
                          <td>Position</td>
                          <td>PTS</td>
                          <td>REB</td>
                          <td>AST</td>
                        </tr>
                      </thead>
                      <tbody id="team1PlayersTable">
                        <?php if ($team1_players && mysqli_num_rows($team1_players) > 0): ?>
                            <?php while($player = mysqli_fetch_assoc($team1_players)): ?>
                            <tr>
                              <td style="text-align: center;"><?php echo $player['jersey_num'] ?? 'N/A'; ?></td>
                              <td><?php echo htmlspecialchars($player['player_name']); ?></td>
                              <td><?php echo htmlspecialchars($player['position']); ?></td>
                              <td class="stat-cell" data-player="<?php echo $player['player_id']; ?>" data-stat="points">
                                  <?php echo $player['points'] ?? 0; ?>
                              </td>
                              <td class="stat-cell" data-player="<?php echo $player['player_id']; ?>" data-stat="rebounds">
                                  <?php echo $player['rebounds'] ?? 0; ?>
                              </td>
                              <td class="stat-cell" data-player="<?php echo $player['player_id']; ?>" data-stat="assists">
                                  <?php echo $player['assists'] ?? 0; ?>
                              </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6">No players found for this team</td></tr>
                        <?php endif; ?>
                      </tbody>
                    </table>  
                </div>
              </div>
              
              <div class="kanan">
                <div class="player laman">
                    <h1><?php echo htmlspecialchars($match['team2_name']); ?> Players</h1>
                    <table class="player-table">
                      <thead class="thead">
                        <tr>
                          <td>Jersey #</td>
                          <td>Player name</td>
                          <td>Position</td>
                          <td>PTS</td>
                          <td>REB</td>
                          <td>AST</td>
                        </tr>
                      </thead>
                      <tbody id="team2PlayersTable">
                        <?php if ($team2_players && mysqli_num_rows($team2_players) > 0): ?>
                            <?php while($player = mysqli_fetch_assoc($team2_players)): ?>
                            <tr>
                              <td style="text-align: center;"><?php echo $player['jersey_num'] ?? 'N/A'; ?></td>
                              <td><?php echo htmlspecialchars($player['player_name']); ?></td>
                              <td><?php echo htmlspecialchars($player['position']); ?></td>
                              <td class="stat-cell" data-player="<?php echo $player['player_id']; ?>" data-stat="points"><?php echo $player['points']; ?></td>
                              <td class="stat-cell" data-player="<?php echo $player['player_id']; ?>" data-stat="rebounds"><?php echo $player['rebounds']; ?></td>
                              <td class="stat-cell" data-player="<?php echo $player['player_id']; ?>" data-stat="assists"><?php echo $player['assists']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6">No players found for this team</td></tr>
                        <?php endif; ?>
                      </tbody>
                    </table>  
                </div>
              </div>
            </div>
            <?php else: ?>
            <div class="no-match">
                <h1>No Live Match Today</h1>
                <p>Check back later for live match updates!</p>
                <p><small>Debug: Make sure you have matches scheduled for today and that all required database tables exist.</small></p>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
    <?php if ($match): ?>
    const matchId = <?php echo $match['match_id']; ?>;
    
    function updateLiveTime(seconds) {
        let minutes = Math.floor(seconds / 60);
        let secs = seconds % 60;
        document.getElementById('liveGameTime').textContent = minutes + ':' + (secs < 10 ? '0' + secs : secs);
    }
    
    // Update live data every 2 seconds
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
                
                // Update game time and status
                if (data.game_time !== undefined) {
                    updateLiveTime(parseInt(data.game_time));
                }
                if (data.game_status !== undefined) {
                    document.getElementById('liveGameStatus').textContent = data.game_status;
                }
                
                // Update player stats
                if (data.player_stats) {
                    for (let playerId in data.player_stats) {
                        let stats = data.player_stats[playerId];
                        let elements = document.querySelectorAll('[data-player="' + playerId + '"]');
                        elements.forEach(element => {
                            let statType = element.getAttribute('data-stat');
                            if (stats[statType] !== undefined) {
                                element.textContent = stats[statType];
                            }
                        });
                    }
                }
            })
            .catch(error => console.log('Error fetching live data:', error));
    }, 2000);
    <?php endif; ?>

    // Sidebar toggle functionality
    document.addEventListener("DOMContentLoaded", () => {
        const sidebarToggler = document.querySelector(".sidebar-toggler");
        const sidebar = document.querySelector(".sidebar");

        sidebarToggler.addEventListener("click", () => {
            sidebar.classList.toggle("collapsed");
        });
    });
    </script>
</body>
</html>