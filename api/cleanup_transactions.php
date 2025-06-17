<?php
include('../frontend/db.php');

// Clean up transactions older than 30 days
$cleanup_query = "DELETE FROM score_transactions WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)";

if (mysqli_query($conn, $cleanup_query)) {
    $deleted_rows = mysqli_affected_rows($conn);
    echo json_encode([
        'success' => true, 
        'message' => "Cleaned up $deleted_rows old transaction records",
        'deleted_count' => $deleted_rows
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'error' => mysqli_error($conn)
    ]);
}
?>
