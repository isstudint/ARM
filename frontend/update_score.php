<?php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "arm");

if ($_POST) {
    $match_id = (int)$_POST['match_id'];
    $team = (int)$_POST['team'];
    $score = (int)$_POST['score'];
    
    $column = $team === 1 ? 'team1_score' : 'team2_score';
    

    $check_query = "SELECT * FROM scores WHERE match_id = $match_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Update existing score
        $update_query = "UPDATE scores SET $column = $score WHERE match_id = $match_id";
    } else {
        // Insert new score record
        if ($team === 1) {
            $update_query = "INSERT INTO scores (match_id, team1_score, team2_score) VALUES ($match_id, $score, 0)";
        } else {
            $update_query = "INSERT INTO scores (match_id, team1_score, team2_score) VALUES ($match_id, 0, $score)";
        }
    }
    
    $result = mysqli_query($conn, $update_query);
    
    echo json_encode(['success' => $result]);
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
