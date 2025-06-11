<?php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "arm");

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$match_id = isset($_POST['match_id']) ? (int)$_POST['match_id'] : 0;
$player_id = isset($_POST['player_id']) ? (int)$_POST['player_id'] : 0;
$stat_type = isset($_POST['stat_type']) ? mysqli_real_escape_string($conn, $_POST['stat_type']) : '';
$value = isset($_POST['value']) ? (int)$_POST['value'] : 0;

if ($match_id && $player_id && $stat_type) {
    // Check if player stat record exists
    $check_query = "SELECT player_id FROM player_stats WHERE match_id = $match_id AND player_id = $player_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Update existing
        $query = "UPDATE player_stats SET $stat_type = $value WHERE match_id = $match_id AND player_id = $player_id";
    } else {
        // Insert new
        $query = "INSERT INTO player_stats (match_id, player_id, $stat_type) VALUES ($match_id, $player_id, $value)";
    }
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to save player stat']);
    }
} else {
    echo json_encode(['error' => 'Invalid data']);
}

mysqli_close($conn);
?>
