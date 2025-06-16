<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&" />
   <link rel="stylesheet" href="sidebar.css">
   <link rel="stylesheet" href="../Css/standing.css">

   <title>Team Standings</title>
</head>
<body> 
<?php 

include('db.php');
include ('sidebar.php');
?>

<main class="main-content">
    <h1 class="page-title">Tournament Standings</h1>
    
    <div class="container">
        <!-- Tournament Standings Table (LEFT) -->
        <div class="standing-cont">
            <table class="standings-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Logo</th>
                        <th>Team</th>
                        <th>W</th>
                        <th>L</th>
                        <th>PD</th>
                        <th>GP</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $tournament_standings_query = "
                        SELECT t.team_name, t.logo,
                               COUNT(DISTINCT s.match_id) as games_played,
                               SUM((t.team_id = m.team1_id AND s.team1_score > s.team2_score) OR (t.team_id = m.team2_id AND s.team2_score > s.team1_score)) AS wins,
                               SUM((t.team_id = m.team1_id AND s.team1_score < s.team2_score) OR (t.team_id = m.team2_id AND s.team2_score < s.team1_score)) AS losses,
                               SUM(CASE WHEN t.team_id = m.team1_id THEN s.team1_score ELSE s.team2_score END) - 
                               SUM(CASE WHEN t.team_id = m.team1_id THEN s.team2_score ELSE s.team1_score END) AS point_differential
                        FROM teams t
                        LEFT JOIN matches m ON t.team_id = m.team1_id OR t.team_id = m.team2_id
                        LEFT JOIN scores s ON m.match_id = s.match_id
                        GROUP BY t.team_id, t.team_name, t.logo
                        ORDER BY wins DESC, point_differential DESC
                        LIMIT 8
                    ";
                    $tournament_teams = mysqli_query($conn, $tournament_standings_query);
                    
                    $stdings = 1;
                    while($team = mysqli_fetch_assoc($tournament_teams)):
                        $is_qualified = $stdings <= 4 && $team['games_played'] >= 3;
                        
                        // Color coding for playoff matchups
                        $row_class = '';
                        $seed_color = '';
                        if ($is_qualified) {
                            if ($stdings == 1 || $stdings == 4) {
                                $row_class = 'matchup-green';
                                $seed_color = '#28a745';
                            } else if ($stdings == 2 || $stdings == 3) {
                                $row_class = 'matchup-orange';
                                $seed_color = '#fd7e14';
                            }
                        }
                    ?>
                    <tr class="team-row <?php echo $is_qualified ? 'qualified-row ' . $row_class : 'not-qualified-row'; ?>">
                        <td class="rank-cell">
                            <strong style="color: <?php echo $seed_color ?: '#666'; ?>;"><?php echo $stdings; ?></strong>
                        </td>
                        <td class="logo-cell">
                            <div class="team-logo">
                                <?php if(!empty($team['logo']) && file_exists('../' . $team['logo'])): ?>
                                    <img src="../<?php echo $team['logo']; ?>" alt="<?php echo $team['team_name']; ?>" class="team-logo-img">
                                <?php else: ?>
                                    <div class="logo-placeholder"><?php echo substr($team['team_name'], 0, 2); ?></div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="team-cell">
                            <span class="team-name"><?php echo htmlspecialchars($team['team_name']); ?></span>
                        </td>
                        <td class="stat-cell"><?php echo $team['wins'] ?? 0; ?></td>
                        <td class="stat-cell"><?php echo $team['losses'] ?? 0; ?></td>
                        <td class="stat-cell">
                            <?php 
                            $pd = $team['point_differential'] ?? 0;
                            echo ($pd > 0 ? '+' : '') . $pd;
                            ?>
                        </td>
                        <td class="stat-cell"><?php echo $team['games_played'] ?? 0; ?>/3</td>
                        <td class="stat-cell">
                            <?php if ($is_qualified): ?>
                                <span class="qualified-badge">Qualified</span>
                            <?php else: ?>
                                <span class="active-badge">Active</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php 
                    $stdings++;
                    endwhile; 
                    ?>
                </tbody>
            </table>
        </div>

        <div class="upcoming-cont">
            <h2>Upcoming Matches</h2>
            <ul class="upcoming-matches-list">
                <?php
                $upcoming_query = "
                    SELECT m.match_date, 
                           t1.team_name AS team1, t1.logo AS team1_logo,
                           t2.team_name AS team2, t2.logo AS team2_logo
                    FROM matches m
                    JOIN teams t1 ON m.team1_id = t1.team_id
                    JOIN teams t2 ON m.team2_id = t2.team_id
                    WHERE m.match_date > NOW()
                    ORDER BY m.match_date ASC
                    LIMIT 5
                ";
                $upcoming = mysqli_query($conn, $upcoming_query);
                while($match = mysqli_fetch_assoc($upcoming)):
                ?>
                <li>
                    <div class="match-item">
                        <div class="match-date">
                            <strong><?php echo date('M d', strtotime($match['match_date'])); ?></strong>
                        </div>
                        <div class="match-teams">
                            <div class="team-info">
                                <?php if(!empty($match['team1_logo']) && file_exists('../' . $match['team1_logo'])): ?>
                                    <img src="../<?php echo ($match['team1_logo']); ?>" alt="<?php echo htmlspecialchars($match['team1']); ?>" class="upcoming-team-logo">
                                <?php else: ?>
                                    <div class="upcoming-logo-placeholder"><?php echo substr($match['team1'], 0, 2); ?></div>
                                <?php endif; ?>
                                <span class="team-name-small"><?php echo ($match['team1']); ?></span>
                            </div>
                            <span class="vs-text">vs</span>
                            <div class="team-info">
                                <?php if(!empty($match['team2_logo']) && file_exists('../' . $match['team2_logo'])): ?>
                                    <img src="../<?php echo ($match['team2_logo']); ?>" alt="<?php echo htmlspecialchars($match['team2']); ?>" class="upcoming-team-logo">
                                <?php else: ?>
                                    <div class="upcoming-logo-placeholder"><?php echo substr($match['team2'], 0, 2); ?></div>
                                <?php endif; ?>
                                <span class="team-name-small"><?php echo ($match['team2']); ?></span>
                            </div>
                        </div>
                    </div>
                </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>

    <?php
    // Get qualified teams for bracket
    $qualified_teams_query = "
        SELECT t.team_name, t.logo,
               COUNT(DISTINCT s.match_id) as games_played,
               SUM((t.team_id = m.team1_id AND s.team1_score > s.team2_score) OR (t.team_id = m.team2_id AND s.team2_score > s.team1_score)) AS wins,
               SUM((t.team_id = m.team1_id AND s.team1_score < s.team2_score) OR (t.team_id = m.team2_id AND s.team2_score < s.team1_score)) AS losses,
               SUM(CASE WHEN t.team_id = m.team1_id THEN s.team1_score ELSE s.team2_score END) - 
               SUM(CASE WHEN t.team_id = m.team1_id THEN s.team2_score ELSE s.team1_score END) AS point_differential
        FROM teams t
        LEFT JOIN matches m ON t.team_id = m.team1_id OR t.team_id = m.team2_id
        LEFT JOIN scores s ON m.match_id = s.match_id
        GROUP BY t.team_id, t.team_name, t.logo
        ORDER BY wins DESC, point_differential DESC
        LIMIT 4
    ";
    $bracket_teams = mysqli_query($conn, $qualified_teams_query);
    
    $qualified_teams = [];
    $rank = 1;
    while($team = mysqli_fetch_assoc($bracket_teams)):
        if ($team['games_played'] >= 3) {
            $qualified_teams[$rank] = $team;
        }
        $rank++;
    endwhile;

    // Get playoff bracket data including semifinals - use match_type and status
    $semifinal_query = "
        SELECT m.match_id, m.match_type, m.status,
               t1.team_name as team1_name, t2.team_name as team2_name,
               s.team1_score, s.team2_score,
               CASE 
                   WHEN s.team1_score > s.team2_score AND m.status = 'Completed' THEN t1.team_name
                   WHEN s.team2_score > s.team1_score AND m.status = 'Completed' THEN t2.team_name
                   ELSE NULL
               END as winner
        FROM matches m
        JOIN teams t1 ON m.team1_id = t1.team_id
        JOIN teams t2 ON m.team2_id = t2.team_id
        LEFT JOIN scores s ON m.match_id = s.match_id
        WHERE m.match_type = 'semifinal'
        ORDER BY m.match_id
    ";
    $semifinal_result = mysqli_query($conn, $semifinal_query);
    $semifinals = [];
    while($sf = mysqli_fetch_assoc($semifinal_result)) {
        $semifinals[] = $sf;
    }

    $final_query = "
        SELECT m.status, t1.team_name as team1_name, t2.team_name as team2_name,
               s.team1_score, s.team2_score,
               CASE 
                   WHEN s.team1_score > s.team2_score AND m.status = 'Completed' THEN t1.team_name
                   WHEN s.team2_score > s.team1_score AND m.status = 'Completed' THEN t2.team_name
                   ELSE NULL
               END as winner
        FROM matches m
        JOIN teams t1 ON m.team1_id = t1.team_id
        JOIN teams t2 ON m.team2_id = t2.team_id
        LEFT JOIN scores s ON m.match_id = s.match_id
        WHERE m.match_type = 'final'
        LIMIT 1
    ";
    $final_result = mysqli_query($conn, $final_query);
    $final_match = mysqli_fetch_assoc($final_result);
    ?>
    
    <?php if (count($qualified_teams) >= 4 || !empty($semifinals)): ?>
    <div class="playoff-bracket">
        <h2>Playoff Bracket</h2>
        <div class="bracket-container">
            <div class="semifinal-round">
   
                <div class="semifinal-match">
                    <div class="match-header">SEMIFINAL 1</div>
                    <?php if (!empty($semifinals[0])): ?>
                        <div class="team-slot">
                            <span class="seed">1</span>
                            <span class="team-name"><?php echo htmlspecialchars($semifinals[0]['team1_name']); ?></span>
                            <?php if ($semifinals[0]['team1_score'] !== null): ?>
                                <span class="score"><?php echo $semifinals[0]['team1_score']; ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="team-slot">
                            <span class="seed">4</span>
                            <span class="team-name"><?php echo htmlspecialchars($semifinals[0]['team2_name']); ?></span>
                            <?php if ($semifinals[0]['team2_score'] !== null): ?>
                                <span class="score"><?php echo $semifinals[0]['team2_score']; ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($semifinals[0]['winner']): ?>
                            <div class="winner">Winner: <?php echo htmlspecialchars($semifinals[0]['winner']); ?></div>
                        <?php elseif ($semifinals[0]['team1_score'] !== null && $semifinals[0]['team2_score'] !== null): ?>
                            <div class="winner" style="background: #ffc107; color: #333;">Game Completed - Awaiting Confirmation</div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="team-slot">
                            <span class="seed">1</span>
                            <span class="team-name"><?php echo htmlspecialchars($qualified_teams[1]['team_name'] ?? 'TBD'); ?></span>
                        </div>
                        <div class="team-slot">
                            <span class="seed">4</span>
                            <span class="team-name"><?php echo htmlspecialchars($qualified_teams[4]['team_name'] ?? 'TBD'); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="semifinal-match">
                    <div class="match-header">SEMIFINAL 2</div>
                    <?php if (!empty($semifinals[1])): ?>
                        <div class="team-slot">
                            <span class="seed">2</span>
                            <span class="team-name"><?php echo ($semifinals[1]['team1_name']); ?></span>
                            <?php if ($semifinals[1]['team1_score'] !== null): ?>
                                <span class="score"><?php echo $semifinals[1]['team1_score']; ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="team-slot">
                            <span class="seed">3</span>
                            <span class="team-name"><?php echo ($semifinals[1]['team2_name']); ?></span>
                            <?php if ($semifinals[1]['team2_score'] !== null): ?>
                                <span class="score"><?php echo $semifinals[1]['team2_score']; ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($semifinals[1]['winner']): ?>
                            <div class="winner">Winner: <?php echo ($semifinals[1]['winner']); ?></div>
                        <?php elseif ($semifinals[1]['team1_score'] !== null && $semifinals[1]['team2_score'] !== null): ?>
                            <div class="winner" style="background: #ffc107; color: #333;">Game Completed - Awaiting Confirmation</div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="team-slot">
                            <span class="seed">2</span>
                            <span class="team-name"><?php echo ($qualified_teams[2]['team_name'] ?? 'TBD'); ?></span>
                        </div>
                        <div class="team-slot">
                            <span class="seed">3</span>
                            <span class="team-name"><?php echo ($qualified_teams[3]['team_name'] ?? 'TBD'); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="bracket-connector">
                <div class="connector-line"></div>
            </div>
            
            <div class="final-round">
                <div class="final-match">
                    <div class="match-header">CHAMPIONSHIP</div>
                    <?php if ($final_match): ?>
                        <div class="team-slot">
                            <span class="team-name"><?php echo htmlspecialchars($final_match['team1_name']); ?></span>
                            <?php if ($final_match['team1_score'] !== null): ?>
                                <span class="score-cham"><?php echo $final_match['team1_score']; ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="team-slot">
                            <span class="team-name"><?php echo htmlspecialchars($final_match['team2_name']); ?></span>
                            <?php if ($final_match['team2_score'] !== null): ?>
                                <span class="score-cham"><?php echo $final_match['team2_score']; ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($final_match['winner']): ?>
                            <div class="champion">CHAMPIONS:<?php echo htmlspecialchars($final_match['winner']); ?></div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="team-slot">
                            <span class="team-name">Winner Semifinal 1</span>
                        </div>
                        <div class="team-slot">
                            <span class="team-name">Winner Semifinal 2</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
<br>
<br>
</main>

</body>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const sidebarToggler = document.querySelector(".sidebar-toggler");
        const sidebar = document.querySelector(".sidebar");

        sidebarToggler.addEventListener("click", () => {
          sidebar.classList.toggle("collapsed");
        });
      });
</script>
</html>