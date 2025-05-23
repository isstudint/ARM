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

// Get sort parameters
$sort_by = $_GET['sort'] ?? 'wins';
$order = $_GET['order'] ?? 'desc';

// Build ORDER BY clause
$order_clause = "";
switch($sort_by) {
    case 'team_name':
        $order_clause = "t.team_name " . ($order == 'asc' ? 'ASC' : 'DESC');
        break;
    case 'wins':
        $order_clause = "wins " . ($order == 'asc' ? 'ASC' : 'DESC');
        break;
    case 'losses':
        $order_clause = "losses " . ($order == 'asc' ? 'ASC' : 'DESC');
        break;
    case 'total_matches':
        $order_clause = "total_matches " . ($order == 'asc' ? 'ASC' : 'DESC');
        break;
    default:
        $order_clause = "wins DESC, losses ASC";
}

$standings_query = "
SELECT t.team_name, SUM( (t.team_id = m.team1_id AND s.team1_score > s.team2_score) OR (t.team_id = m.team2_id AND s.team2_score > s.team1_score)) AS wins,
SUM((t.team_id = m.team1_id AND s.team1_score < s.team2_score) OR  (t.team_id = m.team2_id AND s.team2_score < s.team1_score) ) AS losses ,
COUNT(m.match_id) AS total_matches
FROM teams t
LEFT JOIN matches m ON t.team_id = m.team1_id OR t.team_id = m.team2_id
LEFT JOIN scores s ON m.match_id = s.match_id
GROUP BY t.team_id, t.team_name
ORDER BY $order_clause
";  
$standings = mysqli_query($conn, $standings_query);   






include 'landing.php';
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
                        <th><a href="?sort=team_name&order=<?php echo ($sort_by == 'team_name' && $order == 'asc') ? 'desc' : 'asc'; ?>">Team <?php if($sort_by == 'team_name') echo ($order == 'asc') ? '↑' : '↓'; ?></a></th>
                        <th><a href="?sort=wins&order=<?php echo ($sort_by == 'wins' && $order == 'desc') ? 'asc' : 'desc'; ?>">W <?php if($sort_by == 'wins') echo ($order == 'asc') ? '↑' : '↓'; ?></a></th>
                        <th><a href="?sort=losses&order=<?php echo ($sort_by == 'losses' && $order == 'asc') ? 'desc' : 'asc'; ?>">L <?php if($sort_by == 'losses') echo ($order == 'asc') ? '↑' : '↓'; ?></a></th>
                        <th><a href="?sort=total_matches&order=<?php echo ($sort_by == 'total_matches' && $order == 'desc') ? 'asc' : 'desc'; ?>">GP <?php if($sort_by == 'total_matches') echo ($order == 'asc') ? '↑' : '↓'; ?></a></th>
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
                                <span class="logo-placeholder"><?php echo substr($row['team_name'], 0, 2); ?></span>
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
        <div class="">
            
        </div>
    </div>
</main>

</body>
</html>