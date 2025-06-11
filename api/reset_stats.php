<?php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "arm");

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$match_id = isset($_POST['match_id']) ? (int)$_POST['match_id'] : 0;

if ($match_id) {
    $query = "DELETE FROM player_stats WHERE match_id = $match_id";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Player stats reset successfully']);
    } else {
        echo json_encode(['error' => 'Failed to reset player stats']);
    }
} else {
    echo json_encode(['error' => 'No match ID provided']);
}

mysqli_close($conn);
?>
