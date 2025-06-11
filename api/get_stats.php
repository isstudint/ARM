<?php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "arm");

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$match_id = isset($_GET['match_id']) ? (int)$_GET['match_id'] : 0;

if ($match_id) {
    $stats_query = "SELECT player_id, points, rebounds, assists FROM player_stats WHERE match_id = $match_id";
    $stats_result = mysqli_query($conn, $stats_query);
    
    $player_stats = [];
    while ($row = mysqli_fetch_assoc($stats_result)) {
        $player_stats[$row['player_id']] = [
            'points' => $row['points'] ?: 0,
            'rebounds' => $row['rebounds'] ?: 0,
            'assists' => $row['assists'] ?: 0
        ];
    }
    
    echo json_encode(['player_stats' => $player_stats]);
} else {
    echo json_encode(['error' => 'No match ID provided']);
}

mysqli_close($conn);
?>
