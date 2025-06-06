<?php
session_start();
include ("db.php");
include ("func.php");

check_admin();
$conn = mysqli_connect("localhost", "root", "", "arm");

// Handle form submission for player creation/update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $player_id = isset($_POST['player_id']) ? $_POST['player_id'] : '';
    $player_name = $_POST['player_name'] ?? '';
    $team_id = $_POST['team_id'] ?? '';
    $position = $_POST['position'] ?? '';
    $age = $_POST['age'] ?? '';
    
    // Create uploads directory if it doesn't exist
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
        }
    }
    
    // Insert or update player in database
    if (!empty($player_id)) {
        // Update existing player
        $sql = "UPDATE players SET player_name='$player_name', team_id='$team_id', position='$position', age='$age'";
        
        if (!empty($image_path)) {
            $sql .= ", image='$image_path'";
        }
        
        $sql .= " WHERE player_id='$player_id'";
        
        if(mysqli_query($conn, $sql)) {
            $message = "Player updated successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    } else {
        // Insert new player
        if (!empty($image_path)) {
            $sql = "INSERT INTO players (player_name, team_id, position, age, image) VALUES ('$player_name', '$team_id', '$position', '$age', '$image_path')";
        } else {
            $sql = "INSERT INTO players (player_name, team_id, position, age) VALUES ('$player_name', '$team_id', '$position', '$age')";
        }
        
        if(mysqli_query($conn, $sql)) {
            $message = "Player added successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}

// Get player details if editing
$player = [];
if (isset($_GET['id'])) {
    $player_id = $_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM players WHERE player_id = $player_id");
    if ($result && mysqli_num_rows($result) > 0) {
        $player = mysqli_fetch_assoc($result);
    }
}

// Get all teams for dropdown
$teams_result = mysqli_query($conn, "SELECT team_id, team_name FROM teams ORDER BY team_name");

// Get all players for listing
$players_result = mysqli_query($conn, "
    SELECT p.player_id, p.player_name, p.position, p.age, p.image, t.team_name 
    FROM players p 
    LEFT JOIN teams t ON p.team_id = t.team_id 
    ORDER BY t.team_name, p.player_name
");

// Check if query was successful
if (!$players_result) {
    die("Query failed: " . mysqli_error($conn));
}

include("sidebar.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Css/landing.css">
    <title>Manage Players</title>
    <style>

        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-section {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        form input, form select {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        form button {
            background: #4285f4;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .players-list {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .players-list table {
            width: 100%;
            border-collapse: collapse;
        }
        .players-list th, .players-list td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .player-image-preview {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
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
            
            <div class="form-section">
                <h2><?php echo empty($player) ? 'Add New Player' : 'Edit Player'; ?></h2>
                
                <form method="post" enctype="multipart/form-data">
                    <?php if (!empty($player)): ?>
                        <input type="hidden" name="player_id" value="<?php echo $player['player_id']; ?>">
                    <?php endif; ?>
                    
                    <label for="player_name">Player Name</label>
                    <input type="text" id="player_name" name="player_name" value="<?php echo $player['player_name'] ?? ''; ?>" required>
                    
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
                    
                    <label for="player_image">Player Image</label>
                    <?php if (!empty($player['image'])): ?>
                        <div>
                            <p>Current image:</p>
                            <img src="../<?php echo htmlspecialchars($player['image']); ?>" alt="Current Image" style="max-width: 100px; max-height: 100px;">
                        </div>
                        <br>
                    <?php endif; ?>
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
                            <th>Age</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($player_row = mysqli_fetch_assoc($players_result)): ?>
                        <tr>
                            <td>
                                <?php if(!empty($player_row['image']) && file_exists('../' . $player_row['image'])): ?>
                                    <img src="../<?php echo $player_row['image']; ?>" alt="<?php echo $player_row['player_name']; ?>" class="player-image-preview">
                                <?php else: ?>
                                    <div>No image</div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($player_row['player_name']); ?></td>
                            <td><?php echo htmlspecialchars($player_row['team_name']); ?></td>
                            <td><?php echo htmlspecialchars($player_row['position']); ?></td>
                            <td><?php echo htmlspecialchars($player_row['age']); ?></td>
                            <td>
                                <a href="?id=<?php echo $player_row['player_id']; ?>">Edit</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>