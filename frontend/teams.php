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


$total_teams = mysqli_num_rows($teams);

$total_matches_query = "SELECT COUNT(*) as total FROM matches";
$total_matches_result = mysqli_query($conn, $total_matches_query);
$total_matches = mysqli_fetch_assoc($total_matches_result)['total'];

$total_players_query = "SELECT COUNT(*) as total FROM players";
$total_players_result = mysqli_query($conn, $total_players_query);
$total_players = mysqli_fetch_assoc($total_players_result)['total'];

// Get upcoming scheduled games
$scheduled_games_query = "
SELECT m.*, t1.team_name as team1_name, t2.team_name as team2_name
FROM matches m
JOIN teams t1 ON m.team1_id = t1.team_id
JOIN teams t2 ON m.team2_id = t2.team_id
WHERE m.match_date >= CURDATE()
ORDER BY m.match_date ASC
LIMIT 6
";
$scheduled_games = mysqli_query($conn, $scheduled_games_query);

// Get team rankings (top teams by wins)
$rankings_query = "
SELECT t.team_id, t.team_name, t.logo,
COUNT(m.match_id) AS total_matches,
SUM((t.team_id = m.team1_id AND s.team1_score > s.team2_score) OR (t.team_id = m.team2_id AND s.team2_score > s.team1_score)) AS wins,
SUM((t.team_id = m.team1_id AND s.team1_score < s.team2_score) OR (t.team_id = m.team2_id AND s.team2_score < s.team1_score)) AS losses
FROM teams t
LEFT JOIN matches m ON t.team_id = m.team1_id OR t.team_id = m.team2_id
LEFT JOIN scores s ON m.match_id = s.match_id
GROUP BY t.team_id, t.team_name, t.logo
ORDER BY wins DESC, losses ASC
LIMIT 5
";
$rankings = mysqli_query($conn, $rankings_query);
?>

<body>
    <?php include("sidebar.php") ?>
    <div class="main-content">
        <div class="teams-section">
            <div class="teams-grid">
                <?php while($team = mysqli_fetch_assoc($teams)): ?>
                <a href="roster.php?team_id=<?php echo $team['team_id']; ?>" class="team-card">
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

        <div class="right-sidebar">

            <div class="sidebar-card">
                <h1>Top Teams</h1>
                <?php if (mysqli_num_rows($rankings) > 0): ?>
                    <?php $position = 1; ?>
                    <?php while($rank = mysqli_fetch_assoc($rankings)): ?>
                    <div class="ranking-item">
                        <div class="ranking-position"><?php echo $position; ?></div>
                        <div class="ranking-team">
                            <div class="ranking-team-name"><?php echo htmlspecialchars($rank['team_name']); ?></div>
                            <div class="ranking-record"><?php echo ($rank['wins'] ?? 0) . '-' . ($rank['losses'] ?? 0); ?></div>
                        </div>
                        <div class="ranking-wins"><?php echo $rank['wins'] ?? 0; ?>W</div>
                    </div>
                    <?php $position++; ?>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 20px; color: #64748b;">
                        <p>No rankings available</p>
                    </div>
                <?php endif; ?>
            </div>


            <div class="sidebar-card">
                <h3>Upcoming Games</h3>
                <?php if (mysqli_num_rows($scheduled_games) > 0): ?>
                    <?php while($game = mysqli_fetch_assoc($scheduled_games)): ?>
                    <div class="schedule-item">
                        <div class="schedule-teams">
                            <?php echo ($game['team1_name']) . ' vs ' . ($game['team2_name']); ?>
                        </div>
                        <div class="schedule-time">
                            <?php echo date('M d', strtotime($game['match_date'])); ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 20px; color: #64748b;">
                        <p>No upcoming games</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>