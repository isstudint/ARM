<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Css/sidebar.css">
    <link rel="stylesheet" href="../Css/roster.css">
    <title>Team Roster</title>
</head>

<?php 
$conn = mysqli_connect("localhost", "root", "", "arm");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$team_id = isset($_GET['team_id']) ? (int)$_GET['team_id'] : 1;

// Get team information
$team_query = "SELECT * FROM teams WHERE team_id = $team_id";
$team_result = mysqli_query($conn, $team_query);
$team = mysqli_fetch_assoc($team_result);

// Get players
$players_query = "SELECT * FROM players WHERE team_id = $team_id ORDER BY player_id ASC";
$players_result = mysqli_query($conn, $players_query);
?>

<body>
    <?php include("sidebar.php") ?>
    <div class="main-content">
        <div class="page-header">
            <div class="team-info">
                <div class="team-logo">
                    <?php if(!empty($team['logo']) && file_exists('../' . $team['logo'])): ?>
                        <img src="../<?php echo htmlspecialchars($team['logo']); ?>" alt="<?php echo htmlspecialchars($team['team_name']); ?>">
                    <?php else: ?>
                        <div class="logo-placeholder">
                            <?php echo strtoupper(substr($team['team_name'], 0, 2)); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="team-details">
                    <h1><?php echo htmlspecialchars($team['team_name']); ?></h1>
                    <p>Team Roster</p>
                </div>
            </div>
        </div>

        <div class="roster-section">
            <div class="section-header">
                <h2>Players</h2>
                <span class="player-count"><?php echo mysqli_num_rows($players_result); ?> Players</span>
            </div>

            <div class="players-grid">
                <?php if (mysqli_num_rows($players_result) > 0): ?>
                    <?php while($player = mysqli_fetch_assoc($players_result)): ?>
                    <div class="player-card">
                        <div class="player-avatar">
                            <?php if(!empty($player['image']) && file_exists('../' . $player['image'])): ?>
                                <img src="../<?php echo htmlspecialchars($player['image']); ?>" alt="<?php echo htmlspecialchars($player['player_name']); ?>">
                            <?php else: ?>
                                <div class="avatar-placeholder">
                                    <?php echo strtoupper(substr($player['player_name'], 0, 2)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="player-info">
                            <h3><?php echo htmlspecialchars($player['player_name']); ?></h3>
                            <p class="position"><?php echo htmlspecialchars($player['position']); ?></p>
                            <p class="age">Age: <?php echo htmlspecialchars($player['age']); ?></p>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <h3>No Players Found</h3>
                        <p>This team doesn't have any players yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>