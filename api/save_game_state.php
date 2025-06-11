<?php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "arm");

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$match_id = isset($_POST['match_id']) ? (int)$_POST['match_id'] : 0;
$game_time = isset($_POST['game_time']) ? (int)$_POST['game_time'] : 720;
$quarter = isset($_POST['quarter']) ? (int)$_POST['quarter'] : 1;
$game_status = isset($_POST['game_status']) ? mysqli_real_escape_string($conn, $_POST['game_status']) : 'Ready';

if ($match_id) {
    // Check if game state exists
    $check_query = "SELECT match_id FROM game_state WHERE match_id = $match_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Update existing
        $query = "UPDATE game_state SET game_time = $game_time, quarter = $quarter, game_status = '$game_status' WHERE match_id = $match_id";
    } else {
        // Insert new
        $query = "INSERT INTO game_state (match_id, game_time, quarter, game_status) VALUES ($match_id, $game_time, $quarter, '$game_status')";
    }
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to save game state']);
    }
} else {
    echo json_encode(['error' => 'Invalid match ID']);
}

mysqli_close($conn);
?>
