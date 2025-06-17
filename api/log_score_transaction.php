<?php
include('../frontend/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $match_id = $_POST['match_id'];
    $team_number = $_POST['team_number'];
    $old_score = $_POST['old_score'];
    $new_score = $_POST['new_score'];
    $score_change = $_POST['score_change'];
    $action_type = $_POST['action_type'];
    $reason = $_POST['reason'];
    
    $query = "INSERT INTO score_transactions (match_id, team_number, old_score, new_score, score_change, action_type, reason) 
              VALUES ('$match_id', '$team_number', '$old_score', '$new_score', '$score_change', '$action_type', '$reason')";
    
    if (mysqli_query($conn, $query)) {

            mysqli_query($conn, "DELETE FROM score_transactions WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 DAY)"); // Clean up old transactions

        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
}
?>
