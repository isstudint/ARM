<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="../Css/standing.css">
   <link rel="stylesheet" href="../Css/landing.css">
   <title>Team Standings</title>
</head>
<body> 
<?php 
$conn = mysqli_connect("localhost", "root", "", "arm");

$standings_query = "
SELECT t.team_name, t.logo, SUM( (t.team_id = m.team1_id AND s.team1_score > s.team2_score) OR (t.team_id = m.team2_id AND s.team2_score > s.team1_score)) AS wins,
SUM((t.team_id = m.team1_id AND s.team1_score < s.team2_score) OR  (t.team_id = m.team2_id AND s.team2_score < s.team1_score) ) AS losses ,
COUNT(m.match_id) AS total_matches
FROM teams t
LEFT JOIN matches m ON t.team_id = m.team1_id OR t.team_id = m.team2_id
LEFT JOIN scores s ON m.match_id = s.match_id
GROUP BY t.team_id, t.team_name, t.logo
ORDER BY wins DESC, losses ASC
";  
$standings = mysqli_query($conn, $standings_query);   






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
                        <td class="stat-cell"><?php echo $row['total_matches'] ?? 0; ?></td>
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
                    SELECT m.match_date, t1.team_name AS team1, t2.team_name AS team2
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
                    <strong><?php echo date('M d', strtotime($match['match_date'])); ?></strong>: 
                    <?php echo htmlspecialchars($match['team1']); ?> vs <?php echo htmlspecialchars($match['team2']); ?>
                </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>

</main>

</body>
</html>