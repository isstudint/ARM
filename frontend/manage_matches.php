<?php
session_start();
include ("db.php");
include ("func.php");

check_admin();




if (isset($_GET['delete']) && isset($_GET['match_id'])) {
    $match_id = intval($_GET['match_id']);
    

    $score_check = "SELECT COUNT(*) as score_count FROM scores WHERE match_id = $match_id";
    $score_result = mysqli_query($conn, $score_check);
    $score_row = mysqli_fetch_assoc($score_result);
    
    if ($score_row['score_count'] > 0) {
        $error = "Cannot delete match with recorded scores.";
    } else {
        $delete_query = "DELETE FROM matches WHERE match_id = $match_id";
        if (mysqli_query($conn, $delete_query)) {
            $message = "Match deleted successfully!";
        } else {
            $error = "Error deleting match: " . mysqli_error($conn);
        }
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $team1_id = $_POST['team1_id'];
    $team2_id = $_POST['team2_id'];
    $match_date = $_POST['match_date'];

    // Validate that match date is not in the past
    $current_datetime = date('Y-m-d H:i:s');
    if ($match_date < $current_datetime) {
        $error = "Cannot schedule a match in the past. Please select a future date and time.";
    } else if ($team1_id == $team2_id) {
        $error = "Please select two different teams.";
    } else {
        // Check if either team already has a match scheduled on the same day
        $date_only = date('Y-m-d', strtotime($match_date));
        $check_query = "
            SELECT COUNT(*) as match_count 
            FROM matches 
            WHERE DATE(match_date) = '$date_only' 
            AND (team1_id = $team1_id OR team2_id = $team1_id OR team1_id = $team2_id OR team2_id = $team2_id)
        ";
        
        $check_result = mysqli_query($conn, $check_query);
        $check_row = mysqli_fetch_assoc($check_result);
        
        if ($check_row['match_count'] > 0) {

            $team1_query = "SELECT team_name FROM teams WHERE team_id = $team1_id";
            $team2_query = "SELECT team_name FROM teams WHERE team_id = $team2_id";
            $team1_result = mysqli_query($conn, $team1_query);
            $team2_result = mysqli_query($conn, $team2_query);
            $team1_name = mysqli_fetch_assoc($team1_result)['team_name'];
            $team2_name = mysqli_fetch_assoc($team2_result)['team_name'];
            
            $error = "Cannot schedule match. One or both teams ($team1_name, $team2_name) already have a match scheduled on " . date('M j, Y', strtotime($match_date)) . ".";
        } else {
            $insert_query = "INSERT INTO matches (team1_id, team2_id, match_date) VALUES ($team1_id, $team2_id, '$match_date')";
            if (mysqli_query($conn, $insert_query)) {
                $message = "Match scheduled successfully!";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}

// Get all teams for dropdown
$teams_query = "SELECT team_id, team_name FROM teams ORDER BY team_name";
$teams = mysqli_query($conn, $teams_query);

// Get all matches
$matches_query = "
SELECT m.match_id, m.match_date, m.status, t1.team_name AS team1_name, t2.team_name AS team2_name,
       (SELECT COUNT(*) FROM scores s WHERE s.match_id = m.match_id) as has_scores
FROM matches m
JOIN teams t1 ON m.team1_id = t1.team_id
JOIN teams t2 ON m.team2_id = t2.team_id
ORDER BY m.match_date DESC
";
$matches = mysqli_query($conn, $matches_query);


$standings_query = "
SELECT t.team_id, t.team_name, t.logo,
COUNT(m.match_id) AS total_scheduled,
SUM((t.team_id = m.team1_id AND s.team1_score > s.team2_score) OR (t.team_id = m.team2_id AND s.team2_score > s.team1_score)) AS wins,
SUM((t.team_id = m.team1_id AND s.team1_score < s.team2_score) OR (t.team_id = m.team2_id AND s.team2_score < s.team1_score)) AS losses
FROM teams t
LEFT JOIN matches m ON t.team_id = m.team1_id OR t.team_id = m.team2_id
LEFT JOIN scores s ON m.match_id = s.match_id
GROUP BY t.team_id, t.team_name, t.logo
ORDER BY total_scheduled DESC, wins DESC
";
$standings = mysqli_query($conn, $standings_query);




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&" />
    <title>Manage Matches</title>
    <style>
        .main-content {
            margin-left: 302px;
            padding: 20px;
            display: flex;
            gap: 20px;
        }
        
        .left-section {
            flex: 2;
        }
        
        .right-section {
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
        }
        
        .create-button {
            background: #2d53da;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
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
            background:rgb(48, 50, 163);
            color:white
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
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: contain;
        }
        
        .logo-placeholder {
            width: 40px;
            height: 40px;
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

        .table-header {
            background:rgb(37, 13, 145) !important;
        }
        
        .delete-btn {
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
        }
        
        .delete-btn:hover {
            background: #c82333;
        }
        
        .delete-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        
        .actions-cell {
            text-align: center;
        }
        
        .confirm-delete {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        .confirm-delete a {
            color: #721c24;
            text-decoration: none;
            font-weight: bold;
        }
        
        .min-datetime {
            min: <?php echo date('Y-m-d\TH:i'); ?>;
        }
    </style>
    <script>
        function confirmDelete(matchId, team1, team2, date) {
            if (confirm(`Are you sure you want to delete the match between ${team1} and ${team2} on ${date}?`)) {
                window.location.href = `?delete=1&match_id=${matchId}`;
            }
        }
    </script>
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
                <h2>Create New Match</h2>
                <form method="post">
                    <div class="form-group">
                        <label>Team 1:</label>
                        <select name="team1_id" required>
                            <option value="">Select Team 1</option>
                            <?php 
                            mysqli_data_seek($teams, 0);
                            while($team = mysqli_fetch_assoc($teams)): 
                            ?>
                            <option value="<?php echo $team['team_id']; ?>">
                                <?php echo htmlspecialchars($team['team_name']); ?>
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
                            ?>
                            <option value="<?php echo $team['team_id']; ?>">
                                <?php echo ($team['team_name']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Match Date:</label>
                        <input type="datetime-local" name="match_date" required min="<?php echo date('Y-m-d\TH:i'); ?>">
                        <small style="color: #666; font-size: 12px;">Note: Cannot schedule matches in the past</small>
                    </div>
                    
                    <button class="create-button" type="submit">Create Match</button>
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
                    <tr>
                        <td><?php echo htmlspecialchars($match['team1_name']); ?></td>
                        <td><?php echo htmlspecialchars($match['team2_name']); ?></td>
                        <td><?php echo date('M j, Y - g:i A', strtotime($match['match_date'])); ?></td>
                        <td><?php echo htmlspecialchars($match['status']); ?></td>
                        <td class="actions-cell">
                            <?php if ($match['has_scores'] == 0): ?>
                                <button type="button" class="delete-btn" 
                                        onclick="confirmDelete(<?php echo $match['match_id']; ?>, 
                                               '<?php echo addslashes($match['team1_name']); ?>', 
                                               '<?php echo addslashes($match['team2_name']); ?>', 
                                               '<?php echo date('M j, Y', strtotime($match['match_date'])); ?>')">
                                    Delete
                                </button>
                            <?php else: ?>
                                <button type="button" class="delete-btn" disabled title="Cannot delete match with recorded scores">
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
            <div class="standings-title">Team Standings</div>
            <?php while($team = mysqli_fetch_assoc($standings)): ?>
            <div class="team-item">
                <?php if(!empty($team['logo']) && file_exists('../' . $team['logo'])): ?>
                    <img src="../<?php echo $team['logo']; ?>" alt="<?php echo $team['team_name']; ?>" class="team-logo">
                <?php else: ?>
                    <div class="logo-placeholder"><?php echo substr($team['team_name'], 0, 2); ?></div>
                <?php endif; ?>
                
                <div class="team-info">
                    <div class="team-name"><?php echo $team['team_name']; ?></div>
                    <div class="team-stats">
                        Games: <?php echo $team['total_scheduled'] ?? 0; ?> | 
                        W: <?php echo $team['wins'] ?? 0; ?> | 
                        L: <?php echo $team['losses'] ?? 0; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
