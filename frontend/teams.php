<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Css/teams.css">
    <title>Teams</title>
 
</head>
<?php 
$conn = mysqli_connect("localhost", "root", "", "arm");

$teams_query = "
SELECT t.team_id, t.team_name, t.logo,
COUNT(m.match_id) AS total_matches,
SUM((t.team_id = m.team1_id AND s.team1_score > s.team2_score) OR (t.team_id = m.team2_id AND s.team2_score > s.team1_score)) AS wins,
SUM((t.team_id = m.team1_id AND s.team1_score < s.team2_score) OR (t.team_id = m.team2_id AND s.team2_score < s.team1_score)) AS losses
FROM teams t
LEFT JOIN matches m ON t.team_id = m.team1_id OR t.team_id = m.team2_id
LEFT JOIN scores s ON m.match_id = s.match_id
GROUP BY t.team_id, t.team_name, t.logo
ORDER BY t.team_name ASC
";
$teams = mysqli_query($conn, $teams_query);   

if (!$teams) {
    die("Query failed: " . mysqli_error($conn));
}


?>
<body>
    <?php include("sidebar.php") ?>
    <div class="main-content">
        <div class="teams-grid">
            <?php while($team = mysqli_fetch_assoc($teams)): ?>
            <a href="players.php?team_id=<?php echo $team['team_id']; ?>" class="team-card">
                <div class="team-header">
                    <div class="team-logo">
                        <?php if(!empty($team['logo']) && file_exists('../' . $team['logo'])): ?>
                            <img src="../<?php echo htmlspecialchars($team['logo']); ?>" alt="<?php echo htmlspecialchars($team['team_name']); ?>">
                        <?php else: ?>
                            <div class="no-logo">Logo</div>
                        <?php endif; ?>
                    </div>
                    <div class="team-name"><?php echo htmlspecialchars($team['team_name']); ?></div>
                </div>
                <div class="team-record">
                    W - <?php echo $team['wins'] ?? 0; ?>&nbsp;&nbsp;&nbsp;L - <?php echo $team['losses'] ?? 0; ?>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>