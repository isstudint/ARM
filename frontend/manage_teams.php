<?php
session_start();
include ("db.php");
include ("func.php");

// Check if user is admin
check_admin();

$conn = mysqli_connect("localhost", "root", "", "arm");


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['delete_team'])) {
        $team_id = $_POST['team_id'];
        $force_delete = isset($_POST['force_delete']);
        

        $data_check = "SELECT 
            (SELECT COUNT(*) FROM players WHERE team_id = $team_id) as players,
            (SELECT COUNT(*) FROM matches WHERE team1_id = $team_id OR team2_id = $team_id) as matches
        ";
        $check_result = mysqli_query($conn, $data_check);
        $data = mysqli_fetch_assoc($check_result);
        
        if (($data['players'] > 0 || $data['matches'] > 0) && !$force_delete) {
            // Show warning
            $team_name_query = "SELECT team_name FROM teams WHERE team_id = $team_id";
            $team_result = mysqli_query($conn, $team_name_query);
            $team_data = mysqli_fetch_assoc($team_result);
            
            $warning = "Team '{$team_data['team_name']}' has {$data['players']} players and {$data['matches']} matches. Delete anyway?";
            $pending_delete_id = $team_id;
        } else {
            // Just delete everything
            if ($force_delete) {
                // Delete related data first
                mysqli_query($conn, "DELETE FROM scores WHERE match_id IN (SELECT match_id FROM matches WHERE team1_id = $team_id OR team2_id = $team_id)");
                mysqli_query($conn, "DELETE FROM matches WHERE team1_id = $team_id OR team2_id = $team_id");
                mysqli_query($conn, "DELETE FROM players WHERE team_id = $team_id");
            }
            
            // Delete team
            if (mysqli_query($conn, "DELETE FROM teams WHERE team_id = $team_id")) {
                $message = "Team deleted successfully!";
            } else {
                $error = "Error deleting team.";
            }
        }
    } else {
        $team_id = isset($_POST['team_id']) ? $_POST['team_id'] : '';
        $team_name = trim($_POST['team_name'] ?? '');
        $coach_name = trim($_POST['coach_name'] ?? '');
        
        // Check for duplicate team name
        if (!empty($team_id)) {
      
            $duplicate_check = "SELECT team_id FROM teams WHERE team_name = '$team_name' AND team_id != $team_id";
        } else {
      
            $duplicate_check = "SELECT team_id FROM teams WHERE team_name = '$team_name'";
        }
        
        $duplicate_result = mysqli_query($conn, $duplicate_check);
        
        if (mysqli_num_rows($duplicate_result) > 0) {
            $error = "Team name '$team_name' already exists. Please choose a different name.";
        } else {
            // Check team count limit before adding new team
            if (empty($team_id)) { // Only check for new teams, not updates
                $team_count_query = "SELECT COUNT(*) as team_count FROM teams";
                $count_result = mysqli_query($conn, $team_count_query);
                $count_data = mysqli_fetch_assoc($count_result);
                
                if ($count_data['team_count'] >= 8) {
                    $error = "Tournament is full! Maximum 8 teams allowed. Please delete a team first to add a new one.";
                } else {


                    $upload_dir = "../uploads/team_logos/";
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $logo_path = '';
                    
    
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
                            } else {
                                $error = "Failed to upload logo file.";
                            }
                        } else {
                            $error = "Invalid file type. Please upload JPG, PNG, or GIF files only.";
                        }
                    }
                    
                    // Insert new team if no errors
                    if (!isset($error)) {
                        if (!empty($logo_path)) {
                            $sql = "INSERT INTO teams (team_name, coach_name, logo) VALUES ('$team_name', '$coach_name', '$logo_path')";
                        } else {
                            $sql = "INSERT INTO teams (team_name, coach_name) VALUES ('$team_name', '$coach_name')";
                        }
                        
                        if(mysqli_query($conn, $sql)) {
                            $message = "Team added successfully!";
                        } else {
                            $error = "Error: " . mysqli_error($conn);
                        }
                    }
                }
            } else {
                // Update existing team code
                // Create uploads directory if it doesn't exist
                $upload_dir = "../uploads/team_logos/";
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $logo_path = '';
                
                // Handle file upload
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif','jfif'];
                    $filename = $_FILES['logo']['name'];
                    $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($file_ext, $allowed)) {

                        $new_filename = uniqid('team_', true) . '.' . $file_ext;
                        $destination = $upload_dir . $new_filename;
                        
                        if (move_uploaded_file($_FILES['logo']['tmp_name'], $destination)) {
                            $logo_path = "uploads/team_logos/" . $new_filename; 
                        } 
                    } else {
                        $error = "Invalid file type. Please upload JPG, PNG, or GIF files only.";
                    }
                }
                
                // Update existing team
                if (!isset($error)) {
                    $sql = "UPDATE teams SET team_name='$team_name', coach_name='$coach_name'";
                    
                    if (!empty($logo_path)) {
                        $sql .= ", logo='$logo_path'";
                    }
                    
                    $sql .= " WHERE team_id='$team_id'";
                    
                    if(mysqli_query($conn, $sql)) {
                        $message = "Team updated successfully!";
                    } else {
                        $error = "Error: " . mysqli_error($conn);
                    }
                }



            }

        }
    }
}


$team_count_query = "SELECT COUNT(*) as team_count FROM teams";
$count_result = mysqli_query($conn, $team_count_query);
$total_teams = mysqli_fetch_assoc($count_result)['team_count'];


$team = [];
if (isset($_GET['id'])) {
    $team_id = $_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM teams WHERE team_id = $team_id");
    if ($result && mysqli_num_rows($result) > 0) {
        $team = mysqli_fetch_assoc($result);
    }
}


$teams_result = mysqli_query($conn, "SELECT team_id, team_name, coach_name, logo FROM teams ORDER BY team_name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&" />
    <link rel="stylesheet" href="../Css/manage_t.css">
    <title>Manage Teams</title>
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
            transition: margin-left 0.3s ease;
        }
        
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
            background: #2d53da;
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
        
        .current-logo {
            max-width: 100px;
            max-height: 100px;
            border-radius: 50%;
            object-fit: contain;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        
        .modal-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-header .material-symbols-outlined {
            color: #dc3545;
            font-size: 24px;
            margin-right: 10px;
        }
        
        .modal-title {
            margin: 0;
            color: #dc3545;
            font-size: 18px;
        }
        
        .modal-body {
            margin-bottom: 30px;
            line-height: 1.5;
            color: #333;
        }
        
        .modal-footer {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .warning .material-symbols-outlined {
            color: #856404;
            vertical-align: middle;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <?php include("sidebar.php"); ?>
    
    <div class="main-content">
        <div class="admin-container">
            <h1>Manage Teams</h1>
            
            <?php if (isset($message)): ?>
                <div class="message success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (isset($warning)): ?>
                <div class="warning">
                    <span class="material-symbols-outlined">warning</span>
                    <?php echo $warning; ?>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="team_id" value="<?php echo $pending_delete_id; ?>">
                        <input type="hidden" name="delete_team" value="1">
                        <input type="hidden" name="force_delete" value="1">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you absolutely sure? This will permanently delete the team and ALL associated data including matches and scores. This action CANNOT be undone!');">
                            Yes, Delete Everything
                        </button>
                    </form>
                    <button type="button" class="btn btn-secondary" onclick="window.location.reload();" style="margin-left: 10px;">
                        Cancel
                    </button>
                </div>
            <?php endif; ?>
            
            <div class="form-section">
                <h2><?php echo empty($team) ? 'Add New Team' : 'Edit Team'; ?></h2>
                
                <?php if (empty($team) && $total_teams >= 8): ?>
                    <div class="warning">
                        <span class="material-symbols-outlined">info</span>
                        Tournament Teams Full!. The Limit of 8 teams has been reached.
                    </div>
                <?php else: ?>
                    <div style="background: #e3f2fd; border: 1px solid #2196f3; color: #1565c0; padding: 12px; border-radius: 4px; margin-bottom: 15px;">
                        <span class="material-symbols-outlined" style="vertical-align: middle; margin-right: 8px;">info</span>
                        Tournament Teams: <?php echo $total_teams; ?>/8
                        <?php if ($total_teams < 8): ?>
                            (<?php echo 8 - $total_teams; ?> spots remaining)
                        <?php else: ?>
                            (Tournament Full!)
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" enctype="multipart/form-data" <?php echo (empty($team) && $total_teams >= 8) ? 'style="display:none;"' : ''; ?>>
                    <?php if (!empty($team)): ?>
                        <input type="hidden" name="team_id" value="<?php echo $team['team_id']; ?>">
                    <?php endif; ?>
                    
                    <label for="team_name">Team Name</label>
                    <input type="text" id="team_name" name="team_name" value="<?php echo $team['team_name'] ?? ''; ?>" required>
                    
                    <label for="coach_name">Coach Name</label>
                    <input type="text" id="coach_name" name="coach_name" value="<?php echo $team['coach_name'] ?? ''; ?>" placeholder="Enter coach name">
                    
                    <label for="logo">Team Logo</label>
                    <?php if (!empty($team['logo'])): ?>
                        <div>
                            <p>Current logo:</p>
                            <img src="../<?php echo htmlspecialchars($team['logo']); ?>" alt="Current Logo" class="current-logo">
                        </div>
                        <br>
                    <?php endif; ?>
                    <input type="file" id="logo" name="logo" accept="image/*">
                    <p style="color:#777;font-size:0.9em;">Select an image file (JPG, PNG, or GIF)</p>
                    
                    <button type="submit"><?php echo empty($team) ? 'Add Team' : 'Update Team'; ?></button>
                </form>
            </div>
            
            <div class="teams-list">
                <h2>Current Teams (<?php echo $total_teams; ?>/8)</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Team Name</th>
                            <th>Coach</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($team_row = mysqli_fetch_assoc($teams_result)): ?>
                        <tr>
                            <td>
                                <?php if(!empty($team_row['logo']) && file_exists('../' . $team_row['logo'])): ?>
                                    <img src="../<?php echo htmlspecialchars($team_row['logo']); ?>" alt="<?php echo htmlspecialchars($team_row['team_name']); ?> Logo" class="team-logo-preview">
                                <?php else: ?>
                                    <div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #666;">No Logo</div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($team_row['team_name']); ?></td>
                            <td><?php echo htmlspecialchars($team_row['coach_name'] ?: 'Not assigned'); ?></td>
                            <td>
                                <a href="?id=<?php echo $team_row['team_id']; ?>" style="color: #2d53da; text-decoration: none; margin-right: 10px;">Edit</a>
                                <button type="button" onclick="confirmDelete(<?php echo $team_row['team_id']; ?>, '<?php echo addslashes($team_row['team_name']); ?>')" 
                                        style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; font-size: 12px;">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="material-symbols-outlined">warning</span> <!-- sa google lang -->
                <h3 class="modal-title">Confirm Team Deletion</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the team "<span id="teamNameSpan"></span>"?</p>
                <p><strong>Note:</strong> If this team has matches or scores, you will be prompted with additional options you also forfeiting this tournament.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="submitDelete()">Delete Team</button>
            </div>
        </div>
    </div>

    <form id="deleteForm" method="post" style="display: none;">
        <input type="hidden" name="team_id" id="deleteTeamId">
        <input type="hidden" name="delete_team" value="1">
    </form>

    <script>

        document.addEventListener("DOMContentLoaded", () => {
        const sidebarToggler = document.querySelector(".sidebar-toggler");
        const sidebar = document.querySelector(".sidebar");

        sidebarToggler.addEventListener("click", () => {
          sidebar.classList.toggle("collapsed");
        });
      });
        let currentDeleteId = null;
        
        function confirmDelete(teamId, teamName) {
            currentDeleteId = teamId;
            document.getElementById('teamNameSpan').textContent = teamName;
            document.getElementById('deleteModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
            currentDeleteId = null;
        }
        
        function submitDelete() {
            if (currentDeleteId) {
                document.getElementById('deleteTeamId').value = currentDeleteId;
                document.getElementById('deleteForm').submit();
            }
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
