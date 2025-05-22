<?php
$conn = mysqli_connect("localhost", "root", "", "arm");

$standings_query = "
SELECT t.team_name, SUM( (t.team_id = m.team1_id AND s.team1_score > s.team2_score) OR (t.team_id = m.team2_id AND s.team2_score > s.team1_score)) AS wins,
SUM((t.team_id = m.team1_id AND s.team1_score < s.team2_score) OR  (t.team_id = m.team2_id AND s.team2_score < s.team1_score) ) AS losses ,
COUNT(m.match_id) AS total_matches
FROM teams t
LEFT JOIN matches m ON t.team_id = m.team1_id OR t.team_id = m.team2_id
LEFT JOIN scores s ON m.match_id = s.match_id
GROUP BY t.team_id, t.team_name
ORDER BY wins DESC, losses ASC
";  // Query sa pagkuha ng standings 
$standings = mysqli_query($conn, $standings_query);



$total_teams = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM teams"))[0];
$total_matches = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM matches"))[0];
$total_players = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM players"))[0];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sports Tournament Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
    <div class="sidebar">
        <h2></h2>
        <a href="#" class="active">Dashboard</a>
        <a href="#">Teams</a>
        <a href="#">Matches</a>
        <a href="#">Players</a>
        <a href="#">Standings</a>
    </div>
    <div >
        <h1>Admin VIEW</h1>
        <div >
            <div class="bento-box">
                <h3>Total Teams</h3>
                <div class="stat"><?php echo $total_teams; ?></div>
            </div>
            <div >
                <h3>Total Matches</h3>
                <div class="stat"><?php echo $total_matches; ?></div>
            </div>
            <div >
                <h3>Total Players</h3>
                <div class="stat"><?php echo $total_players; ?></div>
            </div>
        </div>
        <div class="standings-box">
            <h3>Standings</h3>
            <table class="standings-table">
                <tr>
                    <th>Team</th>
                    <th>W</th>
                    <th>L</th>
                    <th>GP</th>
                </tr>
                <?php while($row = mysqli_fetch_assoc($standings)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['team_name']); ?></td>
                    <td><?php echo $row['wins']; ?></td>
                    <td><?php echo $row['losses']; ?></td>
                    <td><?php echo $row['total_matches']; ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>