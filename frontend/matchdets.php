<?php
// Move database connection to top and add error handling
include('db.php');

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}


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
$match = $match_result ? mysqli_fetch_assoc($match_result) : false;

$team1_players = $team2_players = false;

if ($match) {
    // Updated player queries to include player image/photo
    $team1_players_query = "
        SELECT p.player_id, p.player_name, p.position, p.jersey_num, p.image,
               COALESCE(ps.points, 0) as points,
               COALESCE(ps.rebounds, 0) as rebounds,
               COALESCE(ps.assists, 0) as assists
        FROM players p 
        LEFT JOIN player_stats ps ON p.player_id = ps.player_id AND ps.match_id = ?
        WHERE p.team_id = ?
        ORDER BY p.jersey_num, p.player_name
    ";
    
    $team2_players_query = "
        SELECT p.player_id, p.player_name, p.position, p.jersey_num, p.image,
               COALESCE(ps.points, 0) as points,
               COALESCE(ps.rebounds, 0) as rebounds,
               COALESCE(ps.assists, 0) as assists
        FROM players p 
        LEFT JOIN player_stats ps ON p.player_id = ps.player_id AND ps.match_id = ?
        WHERE p.team_id = ?
        ORDER BY p.jersey_num, p.player_name
    ";
    
    // Use prepared statements for better security
    if ($stmt1 = mysqli_prepare($conn, $team1_players_query)) {
        mysqli_stmt_bind_param($stmt1, "ii", $match['match_id'], $match['team1_id']);
        mysqli_stmt_execute($stmt1);
        $team1_players = mysqli_stmt_get_result($stmt1);
    }
    
    if ($stmt2 = mysqli_prepare($conn, $team2_players_query)) {
        mysqli_stmt_bind_param($stmt2, "ii", $match['match_id'], $match['team2_id']);
        mysqli_stmt_execute($stmt2);
        $team2_players = mysqli_stmt_get_result($stmt2);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Css/landig.css" />
    <link rel="stylesheet" href="../Css/landing.css" />
    <link rel="stylesheet" href="../Css/matchdets.css" />
    <title>Match Details - ARM</title>
    <style>
        /* Additional CSS for player images */
        .player-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ccc;
        }
        
        .player-image-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            color: #666;
            border: 2px solid #ccc;
        }
        
        .player-table td {
            text-align: center;
            vertical-align: middle;
            padding: 8px;
        }
    </style>
</head>
<body>
    <?php include("sidebar.php") ?>

    <div class="container">
        <main class="main-content">
            <?php if ($match): ?>
            <div class="match" id="laman">
                <h1>
                    <?php 
                    if ($match['status'] == 'Ongoing') {
                        if ($match['match_type'] == 'final') {
                            echo 'LIVE CHAMPIONSHIP';
                        } else if ($match['match_type'] == 'semifinal') {
                            echo 'LIVE SEMIFINAL';
                        } else {
                            echo 'LIVE MATCH';
                        }
                    } else {
                        if ($match['match_type'] == 'final') {
                            echo 'CHAMPIONSHIP TODAY';
                        } else if ($match['match_type'] == 'semifinal') {
                            echo 'SEMIFINAL TODAY';
                        } else {
                            echo 'MATCH TODAY';
                        }
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
                  <div class="scored"><h1 id="liveTeam1Score"><?php echo $match['team1_score']; ?></h1></div>
                  <div class="score">
                    <h1>SCORE</h1>
                    <div class="oras"><h2 id="liveGameTime">12:00</h2></div>
                    <div class="status" id="liveGameStatus">
                        <?php 
                        $statusLabels = [
                            'Ongoing' => 'LIVE',
                            'Scheduled' => 'SCHEDULED', 
                            'Completed' => 'FINAL'
                        ];
                        echo $statusLabels[$match['status']] ?? $match['status'];
                        ?>
                    </div>
                  </div>
                  <div class="scored"><h1 id="liveTeam2Score"><?php echo $match['team2_score']; ?></h1></div>
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
                          <td>Image</td>
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
                              <td>
                                <?php if(!empty($player['image']) && file_exists('../' . $player['image'])): ?>
                                    <img src="../<?php echo htmlspecialchars($player['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($player['player_name']); ?>" 
                                         class="player-image">
                                <?php else: ?>
                                    <div class="player-image-placeholder">
                                        <?php echo strtoupper(substr($player['player_name'], 0, 2)); ?>
                                    </div>
                                <?php endif; ?>
                              </td>
                              <td><?php echo $player['jersey_num'] ?: 'N/A'; ?></td>
                              <td><?php echo htmlspecialchars($player['player_name']); ?></td>
                              <td><?php echo htmlspecialchars($player['position']); ?></td>
                              <td class="stat-cell" data-player="<?php echo $player['player_id']; ?>" data-stat="points">
                                  <?php echo $player['points']; ?>
                              </td>
                              <td class="stat-cell" data-player="<?php echo $player['player_id']; ?>" data-stat="rebounds">
                                  <?php echo $player['rebounds']; ?>
                              </td>
                              <td class="stat-cell" data-player="<?php echo $player['player_id']; ?>" data-stat="assists">
                                  <?php echo $player['assists']; ?>
                              </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7">No players found for this team</td></tr>
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
                          <td>Image</td>
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
                              <td>
                                <?php if(!empty($player['image']) && file_exists('../' . $player['image'])): ?>
                                    <img src="../<?php echo htmlspecialchars($player['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($player['player_name']); ?>" 
                                         class="player-image">
                                <?php else: ?>
                                    <div class="player-image-placeholder">
                                        <?php echo strtoupper(substr($player['player_name'], 0, 2)); ?>
                                    </div>
                                <?php endif; ?>
                              </td>
                              <td><?php echo $player['jersey_num'] ?: 'N/A'; ?></td>
                              <td><?php echo htmlspecialchars($player['player_name']); ?></td>
                              <td><?php echo htmlspecialchars($player['position']); ?></td>
                              <td class="stat-cell" data-player="<?php echo $player['player_id']; ?>" data-stat="points"><?php echo $player['points']; ?></td>
                              <td class="stat-cell" data-player="<?php echo $player['player_id']; ?>" data-stat="rebounds"><?php echo $player['rebounds']; ?></td>
                              <td class="stat-cell" data-player="<?php echo $player['player_id']; ?>" data-stat="assists"><?php echo $player['assists']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7">No players found for this team</td></tr>
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
            </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
    <?php if ($match): ?>
    const matchId = <?php echo $match['match_id']; ?>;
    
    function updateLiveTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const secs = seconds % 60;
        document.getElementById('liveGameTime').textContent = `${minutes}:${secs.toString().padStart(2, '0')}`;
    }
    
    // Optimized live data updates
    function fetchLiveData() {
        fetch(`../api/get_live_data.php?match_id=${matchId}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
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
                    Object.entries(data.player_stats).forEach(([playerId, stats]) => {
                        ['points', 'rebounds', 'assists'].forEach(statType => {
                            const element = document.querySelector(`[data-player="${playerId}"][data-stat="${statType}"]`);
                            if (element && stats[statType] !== undefined) {
                                element.textContent = stats[statType];
                            }
                        });
                    });
                }
            })
            .catch(error => console.error('Live data fetch error:', error));
    }
    
    // Start live updates
    setInterval(fetchLiveData, 2000);
    fetchLiveData(); // Initial load
    <?php endif; ?>

    // Sidebar toggle functionality
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