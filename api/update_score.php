<?php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "arm");

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$match_id = isset($_POST['match_id']) ? (int)$_POST['match_id'] : 0;

if ($_POST['team'] && $_POST['score'] !== '' && $match_id) {
    $team = (int)$_POST['team'];
    $score = (int)$_POST['score'];
    
    // Check if score record exists
    $check_query = "SELECT match_id FROM scores WHERE match_id = $match_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Update existing record
        if ($team == 1) {
            $query = "UPDATE scores SET team1_score = $score WHERE match_id = $match_id";
        } else {
            $query = "UPDATE scores SET team2_score = $score WHERE match_id = $match_id";
        }
    } else {
        // Insert new record
        if ($team == 1) {
            $query = "INSERT INTO scores (match_id, team1_score, team2_score) VALUES ($match_id, $score, 0)";
        } else {
            $query = "INSERT INTO scores (match_id, team1_score, team2_score) VALUES ($match_id, 0, $score)";
        }
    }
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to update score']);
    }
} else {
    echo json_encode(['error' => 'Invalid data']);
}

mysqli_close($conn);
?>
