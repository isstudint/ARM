<?php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "arm");

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$match_id = isset($_GET['match_id']) ? (int)$_GET['match_id'] : 0;

if ($match_id) {
    $query = "SELECT game_time, quarter, game_status FROM game_state WHERE match_id = $match_id";
    $result = mysqli_query($conn, $query);
    $state = mysqli_fetch_assoc($result);
    
    if ($state) {
        echo json_encode($state);
    } else {
        echo json_encode([
            'game_time' => 720,
            'quarter' => 1,
            'game_status' => 'Ready'
        ]);
    }
} else {
    echo json_encode(['error' => 'No match ID provided']);
}

mysqli_close($conn);
?>
