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


function checkAndSchedulePlayoffs($conn) {
    $existing_playoffs = mysqli_query($conn, "SELECT COUNT(*) as count FROM matches WHERE match_type IN ('semifinal', 'final')");
    $playoff_count = mysqli_fetch_assoc($existing_playoffs)['count'];
    
    if ($playoff_count > 0) {
        checkAndUpdateFinal($conn);
        return false;
    }
    
    $regular_matches_query = "
        SELECT COUNT(*) as total_matches, COUNT(s.match_id) as completed_matches
        FROM matches m
        LEFT JOIN scores s ON m.match_id = s.match_id
        WHERE m.match_type = 'regular'
    ";
    $regular_result = mysqli_query($conn, $regular_matches_query);
    $regular_data = mysqli_fetch_assoc($regular_result);
    
    if ($regular_data['total_matches'] != $regular_data['completed_matches']) {
        return false;
    }
    
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
          AND m.match_type = 'regular'
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

    // Schedule ONLY SEMIFINALS if we have exactly 4 qualified teams
    if (count($qualified_teams) == 4) {
        // Get the date of the last completed regular season match
        $last_match_query = "
            SELECT MAX(m.match_date) as last_date
            FROM matches m
            JOIN scores s ON m.match_id = s.match_id
            WHERE m.match_type = 'regular'
        ";
        $last_match_result = mysqli_query($conn, $last_match_query);
        $last_match_data = mysqli_fetch_assoc($last_match_result);
        
        // Schedule semifinals for the day after the last regular season match
        $last_date = $last_match_data['last_date'] ? date('Y-m-d', strtotime($last_match_data['last_date'])) : date('Y-m-d');
        $semifinal_date = date('Y-m-d', strtotime($last_date . ' +1 day'));
        
        $semifinal1_datetime = $semifinal_date . ' 14:00:00'; // 2:00 PM
        $semifinal2_datetime = $semifinal_date . ' 16:00:00'; // 4:00 PM
        
        // Insert ONLY Semifinals - NO FINAL YET
        mysqli_query($conn, "INSERT INTO matches (team1_id, team2_id, match_date, match_type, status) VALUES ({$qualified_teams[0]['team_id']}, {$qualified_teams[3]['team_id']}, '$semifinal1_datetime', 'semifinal', 'Scheduled')");
        
        mysqli_query($conn, "INSERT INTO matches (team1_id, team2_id, match_date, match_type, status) VALUES ({$qualified_teams[1]['team_id']}, {$qualified_teams[2]['team_id']}, '$semifinal2_datetime', 'semifinal', 'Scheduled')");
        
        // DON'T schedule final yet - wait for semifinals to complete
        
        return true; 
    }
    
    checkAndUpdateFinal($conn);
    return false;
}

function checkAndUpdateFinal($conn) {
    // Check if final already exists
    $final_exists = mysqli_query($conn, "SELECT COUNT(*) as count FROM matches WHERE match_type = 'final'");
    $final_count = mysqli_fetch_assoc($final_exists)['count'];
    
    if ($final_count > 0) {
        return; // Final already scheduled
    }
    
    // Only check for COMPLETED semifinals (not just scored)
    $semifinals_query = "
        SELECT m.match_id, m.team1_id, m.team2_id, m.status, s.team1_score, s.team2_score,
               CASE 
                   WHEN s.team1_score > s.team2_score THEN m.team1_id
                   WHEN s.team2_score > s.team1_score THEN m.team2_id
                   ELSE NULL
               END as winner_id
        FROM matches m
        LEFT JOIN scores s ON m.match_id = s.match_id
        WHERE m.match_type = 'semifinal'
        AND m.status = 'Completed'
        AND s.team1_score IS NOT NULL AND s.team2_score IS NOT NULL
    ";
    
    $semifinals_result = mysqli_query($conn, $semifinals_query);
    $winners = [];
    
    while($semifinal = mysqli_fetch_assoc($semifinals_result)) {
        if ($semifinal['winner_id']) {
            $winners[] = $semifinal['winner_id'];
        }
    }
    
    // ONLY create final when BOTH semifinals are COMPLETED
    if (count($winners) == 2) {
        // Get the date after the last semifinal
        $last_semifinal_query = "
            SELECT MAX(m.match_date) as last_date
            FROM matches m
            WHERE m.match_type = 'semifinal'
        ";
        $last_semifinal_result = mysqli_query($conn, $last_semifinal_query);
        $last_semifinal_data = mysqli_fetch_assoc($last_semifinal_result);
        
        $final_date = date('Y-m-d', strtotime($last_semifinal_data['last_date'] . ' +1 day'));
        $final_datetime = $final_date . ' 15:00:00'; // 3:00 PM
        
        // Create the final match with the two winners
        mysqli_query($conn, "INSERT INTO matches (team1_id, team2_id, match_date, match_type, status) VALUES ({$winners[0]}, {$winners[1]}, '$final_datetime', 'final', 'Scheduled')");
    }
}

// Add this function to be called whenever scores are updated
function triggerPlayoffCheck($conn) {
    checkAndSchedulePlayoffs($conn);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $team1_id = $_POST['team1_id'];
    $team2_id = $_POST['team2_id'];
    $match_date = $_POST['match_date'];

 
    $current_datetime = date('Y-m-d H:i:s');
    if ($match_date < $current_datetime) {
        $error = "Cannot schedule";
    } else if ($team1_id == $team2_id) {
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
            // Check if this completes regular season and triggers playoffs
            $playoff_scheduled = checkAndSchedulePlayoffs($conn);
            if ($playoff_scheduled) {
                $message .= " Playoffs have been automatically scheduled!";
            }
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}

// Add manual trigger for semifinals
if (isset($_GET['schedule_playoffs']) && $_GET['schedule_playoffs'] == '1') {
    $result = checkAndSchedulePlayoffs($conn);
    if ($result) {
        $message = "Semifinals scheduled successfully! Finals will be scheduled automatically when semifinals are completed.";
    } else {
        $error = "Could not schedule playoffs. Make sure all 12 regular season matches have scores and 4 teams have completed 3 games each.";
    }
}

// Add manual trigger to check for final scheduling
if (isset($_GET['check_final']) && $_GET['check_final'] == '1') {
    checkAndUpdateFinal($conn);
    $message = "Checked for final scheduling. If both semifinals are complete, final has been scheduled.";
}

// Get all teams
$teams_query = "SELECT team_id, team_name FROM teams ORDER BY team_name";
$teams = mysqli_query($conn, $teams_query);

// Get all matches - include match_type in query
$matches_query = "
SELECT m.match_id, m.match_date, m.status, m.match_type, t1.team_name AS team1_name, t2.team_name AS team2_name
FROM matches m
JOIN teams t1 ON m.team1_id = t1.team_id
JOIN teams t2 ON m.team2_id = t2.team_id
ORDER BY m.match_date DESC
";
$matches = mysqli_query($conn, $matches_query);

// Get tournament standings for right sidebar (single query)
$standings_query = "
SELECT t.team_id, t.team_name, t.logo,
       COUNT(DISTINCT s.match_id) AS games_played,
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
    <link rel="stylesheet" href="../Css/manage_m.css">
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
                    <br><small>Playoffs will automatically schedule when all regular season matches are completed.</small>
                    <br><br>
                    <?php
                    $playoff_check = mysqli_query($conn, "SELECT COUNT(*) as count FROM matches WHERE match_type IN ('semifinal', 'final')");
                    $playoff_exists = mysqli_fetch_assoc($playoff_check)['count'];
                    
                    if ($playoff_exists == 0) {
                        $regular_check = mysqli_query($conn, "
                            SELECT COUNT(*) as total_matches, COUNT(s.match_id) as completed_matches
                            FROM matches m
                            LEFT JOIN scores s ON m.match_id = s.match_id
                            WHERE m.match_type = 'regular'
                        ");
                        $regular_data = mysqli_fetch_assoc($regular_check);
                        
                        if ($regular_data['total_matches'] == $regular_data['completed_matches'] && $regular_data['total_matches'] >= 12) {
                            echo '<a href="?schedule_playoffs=1" style="background: #28a745; color: white; padding: 11px; border-radius: 4px; text-decoration: none; font-size: 14px;">
                                    Schedule Semifinals
                                  </a>';
                        } else {
                            echo '<span style="color: #dc3545; font-size: 12px;">Complete all regular season matches to enable playoff scheduling</span>';
                            
                        }
                    } else {
                        echo '<span style="color: #28a745; font-size: 12px;">Playoffs already scheduled</span>';
                        
                        $final_check = mysqli_query($conn, "SELECT COUNT(*) as count FROM matches WHERE match_type = 'final'");
                        $final_exists = mysqli_fetch_assoc($final_check)['count'];
                        
                        if ($final_exists == 0) {
                            echo '<br><a href="?check_final=1" style="background: #ffd700; color: #333; padding: 8px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; margin-top: 5px; display: inline-block;">
                                    Check Final Scheduling
                                  </a>';
                        }
                    }
                    ?>
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
                                <?php echo ($team['team_name']); ?> (<?php echo $games_count; ?>/3 games)
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Match Date:</label>
                        <input type="datetime-local" name="match_date" required min="<?php echo date('Y-m-d\TH:i'); ?>">
                        <small style="color: #666; font-size: 12px;">Match cannot be scheduled past day</small>
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
                    <tr style="<?php echo $match['match_type'] == 'semifinal' ? 'background-color: #e8f5e8;' : ($match['match_type'] == 'final' ? 'background-color: #ffd700;' : ''); ?>">
                        <td><?php echo ($match['team1_name']); ?></td>
                        <td><?php echo ($match['team2_name']); ?></td>
                        <td><?php echo date('M j, Y - g:i A', strtotime($match['match_date'])); ?></td>
                        <td>
                            <?php 
                            if ($match['match_type'] == 'semifinal') {
                                echo '<strong style="color: #28a745;">SEMIFINAL</strong>';
                            } else if ($match['match_type'] == 'final') {
                                echo '<strong style="color: #ffd700;">CHAMPIONSHIP</strong>';
                            } else {
                                echo ($match['status']); 
                            }
                            ?>
                        </td>
                        <td>
                            <?php if ($match['match_type'] == 'regular'): ?>
                            <button type="button" class="delete-btn" 
                                    onclick="if(confirm('Delete this match?')) window.location.href='?delete=1&match_id=<?php echo $match['match_id']; ?>'">
                                Delete
                            </button>
                            <?php elseif ($match['match_type'] == 'semifinal'): ?>
                                <button type="button" class="delete-btn" 
                                        onclick="if(confirm('Delete this semifinal match? This will also delete the final if it exists.')) window.location.href='?delete=1&match_id=<?php echo $match['match_id']; ?>'">
                                    Delete
                                </button>
                            <?php elseif ($match['match_type'] == 'final'): ?>
                                <button type="button" class="delete-btn" 
                                        onclick="if(confirm('Delete the championship final?')) window.location.href='?delete=1&match_id=<?php echo $match['match_id']; ?>'">
                                    Delete
                                </button>
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
