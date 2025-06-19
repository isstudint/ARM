<?php
session_start();
include ("db.php");
include ("func.php");

check_admin();

// Handle delete request
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $player_id = intval($_GET['id']);
    
    if (isset($_GET['force']) && $_GET['force'] == '1') {
        // Get player image to delete file
        $player_query = "SELECT image FROM players WHERE player_id = $player_id";
        $player_result = mysqli_query($conn, $player_query);
        $player_data = mysqli_fetch_assoc($player_result);
        
        // Delete player stats and player
        mysqli_query($conn, "DELETE FROM player_stats WHERE player_id = $player_id");
        $delete_query = "DELETE FROM players WHERE player_id = $player_id";
        
        if (mysqli_query($conn, $delete_query)) {
            if (!empty($player_data['image']) && file_exists('../' . $player_data['image'])) {
                unlink('../' . $player_data['image']);
            }
            $message = "Player and all statistics deleted successfully!";
        } else {
            $error = "Error deleting player: " . mysqli_error($conn);
        }
    } else {
        // Check if player has stats
        $stats_check = "SELECT COUNT(*) as stat_count FROM player_stats WHERE player_id = $player_id";
        $stats_result = mysqli_query($conn, $stats_check);
        $stats_row = mysqli_fetch_assoc($stats_result);
        
        if ($stats_row['stat_count'] > 0) {
            $player_query = "SELECT player_name FROM players WHERE player_id = $player_id";
            $player_result = mysqli_query($conn, $player_query);
            $player_data = mysqli_fetch_assoc($player_result);
            
            $confirm_delete = [
                'player_id' => $player_id,
                'player_name' => $player_data['player_name'],
                'stat_count' => $stats_row['stat_count']
            ];
        } else {
            // Delete player without stats
            $player_query = "SELECT image FROM players WHERE player_id = $player_id";
            $player_result = mysqli_query($conn, $player_query);
            $player_data = mysqli_fetch_assoc($player_result);
            
            $delete_query = "DELETE FROM players WHERE player_id = $player_id";
            if (mysqli_query($conn, $delete_query)) {
                if (!empty($player_data['image']) && file_exists('../' . $player_data['image'])) {
                    unlink('../' . $player_data['image']);
                }
                $message = "Player deleted successfully!";
            } 
        }
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $player_id = $_POST['player_id'] ?? '';
    $player_name = trim($_POST['player_name'] ?? '');
    $team_id = $_POST['team_id'] ?? '';
    $position = $_POST['position'] ?? '';
    $age = $_POST['age'] ?? '';
    $jersey_num = $_POST['jersey_num'] ?? '';
    
    // Validation: Check for duplicate player name in the same team
    $duplicate_check = "SELECT player_id FROM players WHERE player_name = '$player_name' AND team_id = '$team_id'";
    if (!empty($player_id)) {
        $duplicate_check .= " AND player_id != '$player_id'";
    }
    $duplicate_result = mysqli_query($conn, $duplicate_check);
    
    if (mysqli_num_rows($duplicate_result) > 0) {
        $error = "A player with the name '$player_name' already exists in this team!";
    } else {
        // Validation: Check if player exists in another team
        $existing_player_check = "SELECT p.player_id, t.team_name FROM players p 
                                 JOIN teams t ON p.team_id = t.team_id 
                                 WHERE p.player_name = '$player_name'";
        if (!empty($player_id)) {
            $existing_player_check .= " AND p.player_id != '$player_id'";
        }
        $existing_result = mysqli_query($conn, $existing_player_check);
        
        if (mysqli_num_rows($existing_result) > 0) {
            $existing_player = mysqli_fetch_assoc($existing_result);
            $error = "Player '$player_name' already exists in team: " . $existing_player['team_name'] . "!";
        } else {
            // Validation: Check for duplicate jersey number in the same team
            $jersey_check = "SELECT player_id FROM players WHERE jersey_num = '$jersey_num' AND team_id = '$team_id'";
            if (!empty($player_id)) {
                $jersey_check .= " AND player_id != '$player_id'";
            }
            $jersey_result = mysqli_query($conn, $jersey_check);
            
            if (mysqli_num_rows($jersey_result) > 0) {
                $error = "Jersey number $jersey_num is already taken in this team!";
            } else {
                // Proceed with file upload and database operations
                $upload_dir = "../uploads/player_images/";
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $image_path = '';
                
                // Handle file upload
                if (isset($_FILES['player_image']) && $_FILES['player_image']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['player_image']['name'];
                    $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($file_ext, $allowed)) {
                        $new_filename = uniqid('player_', true) . '.' . $file_ext;
                        $destination = $upload_dir . $new_filename;
                        
                        if (move_uploaded_file($_FILES['player_image']['tmp_name'], $destination)) {
                            $image_path = "uploads/player_images/" . $new_filename;
                        }
                    } else {
                        $error = "Please upload a valid image file (JPG, JPEG, PNG, or GIF)!";
                    }
                }
                
                // Only proceed if no file upload errors
                if (!isset($error)) {
                    if (!empty($player_id)) {
                        // Update player
                        $sql = "UPDATE players SET player_name='$player_name', team_id='$team_id', position='$position', age='$age', jersey_num='$jersey_num'";
                        if (!empty($image_path)) {
                            // Delete old image if exists
                            $old_image_query = "SELECT image FROM players WHERE player_id = '$player_id'";
                            $old_image_result = mysqli_query($conn, $old_image_query);
                            $old_image_data = mysqli_fetch_assoc($old_image_result);
                            if (!empty($old_image_data['image']) && file_exists('../' . $old_image_data['image'])) {
                                unlink('../' . $old_image_data['image']);
                            }
                            $sql .= ", image='$image_path'";
                        }
                        $sql .= " WHERE player_id='$player_id'";
                        
                        if(mysqli_query($conn, $sql)) {
                            $message = "Player updated successfully!";
                        } else {
                            $error = "Error updating player: " . mysqli_error($conn);
                        }
                    } else {
                        // Create new player
                        if (!empty($image_path)) {
                            $sql = "INSERT INTO players (player_name, team_id, position, age, jersey_num, image) VALUES ('$player_name', '$team_id', '$position', '$age', '$jersey_num', '$image_path')";
                        } else {
                            $sql = "INSERT INTO players (player_name, team_id, position, age, jersey_num) VALUES ('$player_name', '$team_id', '$position', '$age', '$jersey_num')";
                        }
                        
                        if(mysqli_query($conn, $sql)) {
                            $message = "Player added successfully!";
                        } else {
                            $error = "Error adding player: " . mysqli_error($conn);
                        }
                    }
                }
            }
        }
    }
}

// Get player for editing
$player = [];
if (isset($_GET['id'])) {
    $player_id = $_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM players WHERE player_id = $player_id");
    if ($result && mysqli_num_rows($result) > 0) {
        $player = mysqli_fetch_assoc($result);
    }
}

// Get teams and players
$teams_result = mysqli_query($conn, "SELECT team_id, team_name FROM teams ORDER BY team_name");
$players_result = mysqli_query($conn, "
    SELECT p.player_id, p.player_name, p.position, p.jersey_num ,p.age, p.image, t.team_name 
    FROM players p 
    LEFT JOIN teams t ON p.team_id = t.team_id 
    ORDER BY t.team_name, p.player_name
");

include("sidebar.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Css/manage_r.css">
    <title>Manage Players</title>
    <style>

    </style>
</head>
<body>
    <div class="main-content">
        <div class="admin-container">
            <h1>Manage Players</h1>
            
            <?php if (isset($message)): ?>
                <div class="message success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <!-- Confirmation Modal -->
            <?php if (isset($confirm_delete)): ?>
            <div id="deleteModal" class="modal" style="display: block;">
                <div class="modal-content">
                    <span class="close" onclick="closeModal()">&times;</span>
                    <h3>⚠️ Delete Player with Statistics</h3>
                    <p><strong><?php echo htmlspecialchars($confirm_delete['player_name']); ?></strong> has <strong><?php echo $confirm_delete['stat_count']; ?></strong> recorded statistics.</p>
                    <p>Deleting this player will also <strong>permanently remove all their game statistics</strong>.</p>
                    <p><strong>This action cannot be undone!</strong></p>
                    
                    <div class="modal-buttons">
                        <button class="btn-cancel" onclick="closeModal()">Cancel</button>
                        <button class="btn-delete" onclick="confirmDelete()">Delete Player & Stats</button>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-section">
                <h2><?php echo empty($player) ? 'Add New Player' : 'Edit Player'; ?></h2>
                
                <form method="post" enctype="multipart/form-data">
                    <?php if (!empty($player)): ?>
                        <input type="hidden" name="player_id" value="<?php echo $player['player_id']; ?>">
                    <?php endif; ?>
                    
                    <label for="player_name">Player Name</label>
                    <input type="text" id="player_name" name="player_name" value="<?php echo htmlspecialchars($player['player_name'] ?? ''); ?>" required>
                    <small style="color:#777;font-size:0.9em;">Player's full name</small>
                    
                    <label for="team_id">Team</label>
                    <select id="team_id" name="team_id" required>
                        <option value="">Select Team</option>
                        <?php 
                        mysqli_data_seek($teams_result, 0);
                        while($team = mysqli_fetch_assoc($teams_result)): 
                        ?>
                            <option value="<?php echo $team['team_id']; ?>" 
                                <?php echo (isset($player['team_id']) && $player['team_id'] == $team['team_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($team['team_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    
                    <label for="position">Position</label>
                    <select id="position" name="position" required>
                        <option value="">Select Position</option>
                        <option value="Point Guard" <?php echo (isset($player['position']) && $player['position'] == 'Point Guard') ? 'selected' : ''; ?>>Point Guard</option>
                        <option value="Shooting Guard" <?php echo (isset($player['position']) && $player['position'] == 'Shooting Guard') ? 'selected' : ''; ?>>Shooting Guard</option>
                        <option value="Small Forward" <?php echo (isset($player['position']) && $player['position'] == 'Small Forward') ? 'selected' : ''; ?>>Small Forward</option>
                        <option value="Power Forward" <?php echo (isset($player['position']) && $player['position'] == 'Power Forward') ? 'selected' : ''; ?>>Power Forward</option>
                        <option value="Center" <?php echo (isset($player['position']) && $player['position'] == 'Center') ? 'selected' : ''; ?>>Center</option>
                    </select>
                    
                    <label for="age">Age</label>
                    <input type="number" id="age" name="age" min="15" max="50" value="<?php echo $player['age'] ?? ''; ?>" required>
                    
                    <label for="jersey_num">Jersey Number</label>
                    <input type="number" id="jersey_num" name="jersey_num" min="0" max="99" value="<?php echo $player['jersey_num'] ?? ''; ?>" required>
                    <small style="color:#777;font-size:0.9em;">Uniform number worn by the player</small>

                    <?php if (!empty($player['image'])): ?>
                        <div>
                            <p>Current image:</p>
                            <img src="../<?php echo $player['image']; ?>" alt="Current Image" style="max-width: 100px; max-height: 100px;">
                        </div>
                        <br>
                    <?php endif; ?>
                    
                    <label for="player_image">Player Image</label>
                    <input type="file" id="player_image" name="player_image" accept="image/*">
                    <p style="color:#777;font-size:0.9em;">Select an image file (JPG, PNG, or GIF)</p>
                    
                    <button type="submit">Save Player</button>
                </form>
            </div>
            
            <div class="players-list">
                <h2>Current Players</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Player Name</th>
                            <th>Team</th>
                            <th>Position</th>
                            <th>Jersey Number</th>
                            <th>Age</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($player_row = mysqli_fetch_assoc($players_result)): ?>
                        <tr>
                            <td>
                                <?php if(!empty($player_row['image']) && file_exists('../' . $player_row['image'])): ?>
                                    <img src="../<?php echo htmlspecialchars($player_row['image']); ?>" alt="<?php echo htmlspecialchars($player_row['player_name']); ?>" class="player-image-preview">
                                <?php else: ?>
                                    <div>No image</div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($player_row['player_name']); ?></td>
                            <td><?php echo htmlspecialchars($player_row['team_name']); ?></td>
                            <td><?php echo htmlspecialchars($player_row['position']); ?></td>
                            <td><?php echo htmlspecialchars($player_row['jersey_num']); ?></td>
                            <td><?php echo htmlspecialchars($player_row['age']); ?></td>
                            <td>
                                <a href="?id=<?php echo $player_row['player_id']; ?>" class="edit">Edit</a>
                                <a href="javascript:void(0)" class="delete" onclick="confirmDelete(<?php echo $player_row['player_id']; ?>, '<?php echo addslashes($player_row['player_name']); ?>')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const sidebarToggler = document.querySelector(".sidebar-toggler");
            const sidebar = document.querySelector(".sidebar");

            sidebarToggler.addEventListener("click", () => {
                sidebar.classList.toggle("collapsed");
            });
        });
        
        function confirmDelete(playerId, playerName) {
            if (confirm('Are you sure you want to delete player "' + playerName + '"? This action cannot be undone.')) {
                window.location.href = '?delete=1&id=' + playerId;
            }
        }
        
        <?php if (isset($confirm_delete)): ?>
        function closeModal() {
            window.location.href = 'manage_roster.php';
        }
        
        function confirmDelete() {
            window.location.href = '?delete=1&id=<?php echo $confirm_delete['player_id']; ?>&force=1';
        }
        
        window.onclick = function(event) {
            let modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                closeModal();
            }
        }
        <?php endif; ?>
    </script>
</body>
</html>