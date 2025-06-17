<?php
include('../frontend/db.php');

if (isset($_GET['match_id']) && isset($_GET['team'])) {
    $match_id = (int)$_GET['match_id'];
    $team = (int)$_GET['team'];
    
    // Get the most recent non-undo transaction for this team
    $query = "SELECT * FROM score_transactions 
              WHERE match_id = $match_id 
              AND team_number = $team 
              AND action_type != 'undo'
              ORDER BY created_at DESC 
              LIMIT 1";
    
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $transaction = mysqli_fetch_assoc($result);
        echo json_encode(['success' => true, 'transaction' => $transaction]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No transactions found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
}
?>
