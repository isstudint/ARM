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

$standings_query = "
SELECT t.team_name, t.logo, 
SUM( (t.team_id = m.team1_id AND s.team1_score > s.team2_score) OR (t.team_id = m.team2_id AND s.team2_score > s.team1_score)) AS wins,
SUM((t.team_id = m.team1_id AND s.team1_score < s.team2_score) OR  (t.team_id = m.team2_id AND s.team2_score < s.team1_score) ) AS losses,

COUNT(DISTINCT s.match_id) AS games_played
FROM teams t
LEFT JOIN matches m ON t.team_id = m.team1_id OR t.team_id = m.team2_id
LEFT JOIN scores s ON m.match_id = s.match_id
GROUP BY t.team_id, t.team_name, t.logo
ORDER BY wins DESC, losses ASC
";  
$standings = mysqli_query($conn, $standings_query);   



// COUNT(DISTINCT m.match_id) AS total_matches, if di gumana yung query


include 'sidebar.php';
?>

<main class="main-content">

        <h1 class="page-title">Team Standings</h1>
    <div class="container">
        <div class="standing-cont">
            <table class="standings-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Logo</th>
                        <th>Team</th>
                        <th>W</th>
                        <th>L</th>
                        <th>GP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rank = 1;
                    while($row = mysqli_fetch_assoc($standings)): 
                    ?>
                    <tr class="team-row" data-rank="<?php echo $rank; ?>">
                        <td class="rank-cell"><?php echo $rank; ?></td>
                        <td class="logo-cell">
                            <div class="team-logo">
                                <?php if(!empty($row['logo']) && file_exists('../' . $row['logo'])): ?>
                                    <img src="../<?php echo htmlspecialchars($row['logo']); ?>" alt="<?php echo htmlspecialchars($row['team_name']); ?>" class="team-logo-img">
                                <?php else: ?>
                                    <div class="logo-placeholder"><?php echo substr($row['team_name'], 0, 2); ?></div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="team-cell">
                            <span class="team-name"><?php echo htmlspecialchars($row['team_name']); ?></span>
                        </td>
                        <td class="stat-cell"><?php echo $row['wins'] ?? 0; ?></td>
                        <td class="stat-cell"><?php echo $row['losses'] ?? 0; ?></td>
                        <td class="stat-cell"><?php echo $row['games_played'] ?? 0; ?></td>
                    </tr>
                    <?php 
                    $rank++;
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
</html>