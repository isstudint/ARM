<?php
include('../frontend/db.php');

if (isset($_GET['match_id'])) {
    $match_id = $_GET['match_id'];
    
    $query = "SELECT * FROM score_transactions WHERE match_id = '$match_id' ORDER BY created_at DESC LIMIT 10";
    $result = mysqli_query($conn, $query);
    
    $transactions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $transactions[] = $row;
    }
    
    echo json_encode($transactions);
}
?>
