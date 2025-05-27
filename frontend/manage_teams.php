<?php
session_start();
include ("db.php");
include ("func.php");

// Check if user is admin
check_admin();

$conn = mysqli_connect("localhost", "root", "", "arm");

// Handle form submission for team update/creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $team_id = isset($_POST['team_id']) ? $_POST['team_id'] : '';
    $team_name = $_POST['team_name'] ?? '';
    
    // Create uploads directory if it doesn't exist
    $upload_dir = "../uploads/team_logos/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $logo_path = '';
    
    // Handle file upload
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['logo']['name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($file_ext, $allowed)) {
            // Create a unique filename
            $new_filename = uniqid('team_', true) . '.' . $file_ext;
            $destination = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $destination)) {
                $logo_path = "uploads/team_logos/" . $new_filename; // Relative path for storage
            }
        }
    }
    
    // Insert or update team in database
    if (!empty($team_id)) {
        // Update existing team
        $sql = "UPDATE teams SET team_name='$team_name'";
        
        if (!empty($logo_path)) {
            $sql .= ", logo='$logo_path'";
        }
        
        $sql .= " WHERE team_id='$team_id'";
        
        if(mysqli_query($conn, $sql)) {
            $message = "Team updated successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    } else {
        // Insert new team
        if (!empty($logo_path)) {
            $sql = "INSERT INTO teams (team_name, logo) VALUES ('$team_name', '$logo_path')";
        } else {
            $sql = "INSERT INTO teams (team_name) VALUES ('$team_name')";
        }
        
        if(mysqli_query($conn, $sql)) {
            $message = "Team added successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}

// Get team details if editing
$team = [];
if (isset($_GET['id'])) {
    $team_id = $_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM teams WHERE team_id = $team_id");
    if ($result && mysqli_num_rows($result) > 0) {
        $team = mysqli_fetch_assoc($result);
    }
}

// Get all teams for listing
$teams_result = mysqli_query($conn, "SELECT team_id, team_name, logo FROM teams ORDER BY team_name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Css/admin.css">
    <title>Manage Teams</title>
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
        }
        form button {
            background: #4285f4;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .teams-list {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .teams-list table {
            width: 100%;
            border-collapse: collapse;
        }
        .teams-list th, .teams-list td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .team-logo-preview {
            width: 50px;
            height: 50px;
            object-fit: contain;
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
    <div class="admin-container">
        <h1>Manage Teams</h1>
        
        <?php if (isset($message)): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="form-section">
            <h2><?php echo empty($team) ? 'Add New Team' : 'Edit Team'; ?></h2>
            
            <form method="post" enctype="multipart/form-data">
                <?php if (!empty($team)): ?>
                    <input type="hidden" name="team_id" value="<?php echo $team['team_id']; ?>">
                <?php endif; ?>
                
                <label for="team_name">Team Name</label>
                <input type="text" id="team_name" name="team_name" value="<?php echo $team['team_name'] ?? ''; ?>" required>
                
                <label for="logo">Team Logo</label>
                <?php if (!empty($team['logo'])): ?>
                    <div>
                        <p>Current logo:</p>
                        <img src="<?php echo htmlspecialchars($team['logo']); ?>" alt="Current Logo" style="max-width: 100px; max-height: 100px;">
                    </div>
                    <br>
                <?php endif; ?>
                <input type="file" id="logo" name="logo" accept="image/*">
                <p style="color:#777;font-size:0.9em;">Select an image file (JPG, PNG, or GIF)</p>
                
                <button type="submit">Save Team</button>
            </form>
        </div>
        
        <div class="teams-list">
            <h2>Current Teams</h2>
            <table>
                <thead>
                    <tr>
                        <th>Logo</th>
                        <th>Team Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($team_row = mysqli_fetch_assoc($teams_result)): ?>
                    <tr>
                        <td>
                            <?php if(!empty($team_row['logo']) && file_exists('../../' . $team_row['logo'])): ?>
                                <img src="../../<?php echo $team_row['logo']; ?>" alt="<?php echo $team_row['team_name']; ?> Logo" class="team-logo-preview">
                            <?php else: ?>
                                <div>No logo</div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($team_row['team_name']); ?></td>
                        <td>
                            <a href="?id=<?php echo $team_row['team_id']; ?>">Edit</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
