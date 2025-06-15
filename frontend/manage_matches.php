<?php
session_start();
include ("db.php");
include ("func.php");

check_admin();

// Simple functions
function getTeamGamesCount($conn, $team_id) {
    $query = "SELECT COUNT(*) as games_count FROM matches WHERE (team1_id = $team_id OR team2_id = $team_id) AND status != 'Cancelled'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['games_count'];
}

function teamsAlreadyPlayed($conn, $team1_id, $team2_id) {
    $query = "SELECT COUNT(*) as match_count FROM matches WHERE ((team1_id = $team1_id AND team2_id = $team2_id) OR (team1_id = $team2_id AND team2_id = $team1_id)) AND status != 'Cancelled'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['match_count'] > 0;
}

// Handle match deletion
if (isset($_GET['delete']) && isset($_GET['match_id'])) {
    $match_id = intval($_GET['match_id']);
    

    mysqli_query($conn, "DELETE FROM player_stats WHERE match_id = $match_id");
    mysqli_query($conn, "DELETE FROM scores WHERE match_id = $match_id");
    
    // Delete match
    $delete_query = "DELETE FROM matches WHERE match_id = $match_id";
    if (mysqli_query($conn, $delete_query)) {
        $message = "Match deleted successfully!";
    } else {
        $error = "Error deleting match: " . mysqli_error($conn);
    }
}

// Function to check if playoffs should be scheduled when regular season is complete
function checkAndSchedulePlayoffs($conn) {
    $qualified_query = "
        SELECT t.team_id, t.team_name,
               COUNT(DISTINCT s.match_id) as games_played,
               SUM((t.team_id = m.team1_id AND s.team1_score > s.team2_score) OR (t.team_id = m.team2_id AND s.team2_score > s.team1_score)) AS wins,
               SUM(CASE WHEN t.team_id = m.team1_id THEN s.team1_score ELSE s.team2_score END) - 
               SUM(CASE WHEN t.team_id = m.team1_id THEN s.team2_score ELSE s.team1_score END) AS point_differential
        FROM teams t
        LEFT JOIN matches m ON t.team_id = m.team1_id OR t.team_id = m.team2_id
        LEFT JOIN scores s ON m.match_id = s.match_id
        WHERE s.team1_score IS NOT NULL AND s.team2_score IS NOT NULL
        GROUP BY t.team_id, t.team_name
        HAVING games_played >= 3
        ORDER BY wins DESC, point_differential DESC
        LIMIT 4
    ";
    
    $qualified_result = mysqli_query($conn, $qualified_query);
    $qualified_teams = [];
    
    while($team = mysqli_fetch_assoc($qualified_result)) {
        $qualified_teams[] = $team;
    }
    
    if (count($qualified_teams) >= 4) {
        $existing_playoffs = mysqli_query($conn, "SELECT COUNT(*) as count FROM matches WHERE status = 'Playoff'");
        $playoff_count = mysqli_fetch_assoc($existing_playoffs)['count'];
        
        if ($playoff_count == 0) {
            $semifinal1_date = date('Y-m-d H:i:s', strtotime('+1 day 14:00'));
            $semifinal2_date = date('Y-m-d H:i:s', strtotime('+1 day 16:00'));
            $final_date = date('Y-m-d H:i:s', strtotime('+2 days 15:00'));
            
            // Semifinal 1: #1 vs #4
            mysqli_query($conn, "INSERT INTO matches (team1_id, team2_id, match_date, status) VALUES ({$qualified_teams[0]['team_id']}, {$qualified_teams[3]['team_id']}, '$semifinal1_date', 'Playoff')");
            
            // Semifinal 2: #2 vs #3  
            mysqli_query($conn, "INSERT INTO matches (team1_id, team2_id, match_date, status) VALUES ({$qualified_teams[1]['team_id']}, {$qualified_teams[2]['team_id']}, '$semifinal2_date', 'Playoff')");
            
            // Championship Final - Use first qualified team as placeholder, will be updated when semifinals complete
            mysqli_query($conn, "INSERT INTO matches (team1_id, team2_id, match_date, status) VALUES ({$qualified_teams[0]['team_id']}, {$qualified_teams[1]['team_id']}, '$final_date', 'Final')");
        }
    }
    

    checkAndUpdateFinal($conn);
}

// Function to update final match when semifinals are complete
function checkAndUpdateFinal($conn) {
    $semifinals_query = "
        SELECT m.match_id, m.team1_id, m.team2_id, s.team1_score, s.team2_score,
               CASE 
                   WHEN s.team1_score > s.team2_score THEN m.team1_id
                   WHEN s.team2_score > s.team1_score THEN m.team2_id
                   ELSE NULL
               END as winner_id
        FROM matches m
        LEFT JOIN scores s ON m.match_id = s.match_id
        WHERE m.status = 'Playoff'
        AND s.team1_score IS NOT NULL AND s.team2_score IS NOT NULL
    ";
    
    $semifinals_result = mysqli_query($conn, $semifinals_query);
    $winners = [];
    
    while($semifinal = mysqli_fetch_assoc($semifinals_result)) {
        if ($semifinal['winner_id']) {
            $winners[] = $semifinal['winner_id'];
        }
    }
    
    // If both semifinals are complete, update the final
    if (count($winners) == 2) {
        $final_check = mysqli_query($conn, "SELECT match_id FROM matches WHERE status = 'Final' LIMIT 1");
        if ($final_row = mysqli_fetch_assoc($final_check)) {
            $update_final = "UPDATE matches SET team1_id = {$winners[0]}, team2_id = {$winners[1]} WHERE match_id = {$final_row['match_id']}";
            mysqli_query($conn, $update_final);
        }
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $team1_id = $_POST['team1_id'];
    $team2_id = $_POST['team2_id'];
    $match_date = $_POST['match_date'];

    if ($team1_id == $team2_id) {
        $error = "Please select two different teams.";
    } else if (getTeamGamesCount($conn, $team1_id) >= 3) {
        $error = "Team 1 has already played 3 games.";
    } else if (getTeamGamesCount($conn, $team2_id) >= 3) {
        $error = "Team 2 has already played 3 games.";
    } else if (teamsAlreadyPlayed($conn, $team1_id, $team2_id)) {
        $error = "These teams have already played each other.";
    } else {
        $insert_query = "INSERT INTO matches (team1_id, team2_id, match_date) VALUES ($team1_id, $team2_id, '$match_date')";
        if (mysqli_query($conn, $insert_query)) {
            $message = "Match scheduled successfully!";
            checkAndSchedulePlayoffs($conn);
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}

// Get all teams
$teams_query = "SELECT team_id, team_name FROM teams ORDER BY team_name";
$teams = mysqli_query($conn, $teams_query);

// Get all matches
$matches_query = "
SELECT m.match_id, m.match_date, m.status, t1.team_name AS team1_name, t2.team_name AS team2_name
FROM matches m
JOIN teams t1 ON m.team1_id = t1.team_id
JOIN teams t2 ON m.team2_id = t2.team_id
ORDER BY m.match_date DESC
";
$matches = mysqli_query($conn, $matches_query);

// Get tournament standings for right sidebar
$standings_query = "
SELECT t.team_id, t.team_name, t.logo,
       COUNT(m.match_id) AS games_played,
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
$standings = mysqli_query($conn, $standings_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Matches</title>
    <style>
        .sidebar.collapsed .sidebar-header .toggler{
            transform: translate(-50px, 40px);
        }

        .sidebar.collapsed .sidebar-title {
            margin-left: 0;
            transform: translateX(-6px); 
            transition: transform 0.3s ease;
        }

        
        .sidebar.collapsed ~ .main-content {
            margin-left: 105px;
        }   


        .main-content {
            margin-left: 302px;
            padding: 20px;
            display: flex;
            gap: 20px;
            transition: margin-left 0.5s ease   ;
        }
        
        .left-section {
            flex: 2;
        }
        
        .right-section {
            margin-top: 50px;
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            height: fit-content;
        }
        
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        select, input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        .create-button {
            background: #2d53da;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        
        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background: #2d53da;
            color: white;
        }
        
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            background: #d4edda;
            color: #155724;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
        }
        
        .delete-btn {
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .warning {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #495057;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .team-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            margin-bottom: 8px;
            border: 1px solid #eee;
            border-radius: 6px;
        }
        
        .team-logo {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: contain;
        }
        
        .logo-placeholder {
            width: 30px;
            height: 30px;
            background: #f0f0f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            color: #666;
        }
        
        .team-info {
            flex: 1;
        }
        
        .team-name {
            font-weight: 600;
            margin-bottom: 2px;
        }
        
        .team-stats {
            font-size: 12px;
            color: #666;
        }
        
        .standings-title {
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <?php include("sidebar.php"); ?>
    
    <div class="main-content">
        <div class="left-section">
            <h1>Manage Matches</h1>
            
            <?php if (isset($message)): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="form-container">
                <h2>Schedule New Match</h2>
                
                <div class="warning">
                    <strong>Rules:</strong> Each team can play max 3 games. Teams cannot play each other twice.
                </div>
                
                <form method="post">
                    <div class="form-group">
                        <label>Team 1:</label>
                        <select name="team1_id" required>
                            <option value="">Select Team 1</option>
                            <?php 
                            mysqli_data_seek($teams, 0);
                            while($team = mysqli_fetch_assoc($teams)): 
                                $games_count = getTeamGamesCount($conn, $team['team_id']);
                            ?>
                            <option value="<?php echo $team['team_id']; ?>">
                                <?php echo htmlspecialchars($team['team_name']); ?> (<?php echo $games_count; ?>/3 games)
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Team 2:</label>
                        <select name="team2_id" required>
                            <option value="">Select Team 2</option>
                            <?php 
                            mysqli_data_seek($teams, 0);
                            while($team = mysqli_fetch_assoc($teams)): 
                                $games_count = getTeamGamesCount($conn, $team['team_id']);
                            ?>
                            <option value="<?php echo $team['team_id']; ?>">
                                <?php echo htmlspecialchars($team['team_name']); ?> (<?php echo $games_count; ?>/3 games)
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Match Date:</label>
                        <input type="datetime-local" name="match_date" required>
                    </div>
                    
                    <button class="create-button" type="submit">Schedule Match</button>
                </form>
            </div>
            
            <h2>All Matches</h2>
            <table>
                <thead>
                    <tr>
                        <th>Team 1</th>
                        <th>Team 2</th>
                        <th>Match Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($match = mysqli_fetch_assoc($matches)): ?>
                    <tr style="<?php echo $match['status'] == 'Playoff' ? 'background-color: #e8f5e8;' : ($match['status'] == 'Final' ? 'background-color: #ffd700;' : ''); ?>">
                        <td><?php echo htmlspecialchars($match['team1_name']); ?></td>
                        <td><?php echo htmlspecialchars($match['team2_name']); ?></td>
                        <td><?php echo date('M j, Y - g:i A', strtotime($match['match_date'])); ?></td>
                        <td>
                            <?php 
                            if ($match['status'] == 'Playoff') {
                                echo '<strong style="color: #28a745;">SEMIFINAL</strong>';
                            } else if ($match['status'] == 'Final') {
                                echo '<strong style="color: #ffd700;">CHAMPIONSHIP</strong>';
                            } else {
                                echo htmlspecialchars($match['status']); 
                            }
                            ?>
                        </td>
                        <td>
                            <?php if ($match['status'] != 'Playoff' && $match['status'] != 'Final'): ?>
                            <button type="button" class="delete-btn" 
                                    onclick="if(confirm('Delete this match?')) window.location.href='?delete=1&match_id=<?php echo $match['match_id']; ?>'">
                                Delete
                            </button>
                            <?php else: ?>
                                <span style="color: #666; font-size: 12px;">Playoff Match</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <div class="right-section">
            <div class="standings-title">Tournament Standings</div>
            <div style="font-size: 12px; color: #666; margin-bottom: 15px;">
                Regular Season Rankings (Top 4 qualify)
            </div>
            
            <?php 
            $rank = 1;
            while($team = mysqli_fetch_assoc($standings)): 
                $is_qualified = $rank <= 4 && $team['games_played'] >= 3;
            ?>
            <div class="team-item" style="<?php echo $is_qualified ? 'border-left: 4px solid #4caf50;' : ''; ?>">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="font-weight: bold; color: <?php echo $is_qualified ? '#4caf50' : '#333'; ?>;">
                        #<?php echo $rank; ?>
                    </div>
                    
                    <?php if(!empty($team['logo']) && file_exists('../' . $team['logo'])): ?>
                        <img src="../<?php echo $team['logo']; ?>" alt="<?php echo $team['team_name']; ?>" class="team-logo">
                    <?php else: ?>
                        <div class="logo-placeholder"><?php echo substr($team['team_name'], 0, 2); ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="team-info">
                    <div class="team-name">
                        <?php echo htmlspecialchars($team['team_name']); ?>
                        <?php if ($is_qualified): ?>
                            <span style="color: #4caf50; font-size: 12px;">QUALIFIED</span>
                        <?php endif; ?>
                    </div>
                    <div class="team-stats">
                        <?php echo $team['games_played']; ?>/3 Games | 
                        <?php echo $team['wins'] ?? 0; ?>W-<?php echo $team['losses'] ?? 0; ?>L | 
                        PD: <?php echo ($team['point_differential'] ?? 0) > 0 ? '+' : ''; ?><?php echo $team['point_differential'] ?? 0; ?>
                    </div>
                </div>
            </div>
            <?php 
            $rank++;
            endwhile; 
            ?>
        </div>
    </div>
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
