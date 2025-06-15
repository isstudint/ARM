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
$conn = mysqli_connect("localhost", "root", "", "arm");

include 'sidebar.php';
?>

<main class="main-content">
    <h1 class="page-title">Tournament Standings</h1>
    
    <div class="container">
        <!-- Tournament Standings Table (LEFT) -->
        <div class="standing-cont">
            <table class="standings-table">
                <thead>
                    <tr>
                        <th>Seed</th>
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
                    
                    $seed = 1;
                    while($team = mysqli_fetch_assoc($tournament_teams)):
                        $is_qualified = $seed <= 4 && $team['games_played'] >= 3;
                    ?>
                    <tr class="team-row <?php echo $is_qualified ? 'qualified-row' : ''; ?>">
                        <td class="rank-cell"><?php echo $seed; ?></td>
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
                    $seed++;
                    endwhile; 
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Upcoming Matches (RIGHT) -->
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
                                    <img src="../<?php echo htmlspecialchars($match['team1_logo']); ?>" alt="<?php echo htmlspecialchars($match['team1']); ?>" class="upcoming-team-logo">
                                <?php else: ?>
                                    <div class="upcoming-logo-placeholder"><?php echo substr($match['team1'], 0, 2); ?></div>
                                <?php endif; ?>
                                <span class="team-name-small"><?php echo htmlspecialchars($match['team1']); ?></span>
                            </div>
                            <span class="vs-text">vs</span>
                            <div class="team-info">
                                <?php if(!empty($match['team2_logo']) && file_exists('../' . $match['team2_logo'])): ?>
                                    <img src="../<?php echo htmlspecialchars($match['team2_logo']); ?>" alt="<?php echo htmlspecialchars($match['team2']); ?>" class="upcoming-team-logo">
                                <?php else: ?>
                                    <div class="upcoming-logo-placeholder"><?php echo substr($match['team2'], 0, 2); ?></div>
                                <?php endif; ?>
                                <span class="team-name-small"><?php echo htmlspecialchars($match['team2']); ?></span>
                            </div>
                        </div>
                    </div>
                </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
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