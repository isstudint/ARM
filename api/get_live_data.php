<?php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "arm");

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$match_id = isset($_GET['match_id']) ? (int)$_GET['match_id'] : 0;

if ($match_id) {
    // Get current scores
    $score_query = "SELECT team1_score, team2_score FROM scores WHERE match_id = $match_id";
    $score_result = mysqli_query($conn, $score_query);
    $scores = mysqli_fetch_assoc($score_result);
    
    // Get player stats
    $stats_query = "SELECT player_id, points, rebounds, assists FROM player_stats WHERE match_id = $match_id";
    $stats_result = mysqli_query($conn, $stats_query);
    
    $player_stats = [];
    if ($stats_result) {
        while ($row = mysqli_fetch_assoc($stats_result)) {
            $player_stats[$row['player_id']] = [
                'points' => $row['points'] ?: 0,
                'rebounds' => $row['rebounds'] ?: 0,
                'assists' => $row['assists'] ?: 0
            ];
        }
    }
    
    // Get game state
    $state_query = "SELECT game_time, quarter, game_status FROM game_state WHERE match_id = $match_id";
    $state_result = mysqli_query($conn, $state_query);
    $game_state = mysqli_fetch_assoc($state_result);
    
    echo json_encode([
        'team1_score' => $scores['team1_score'] ?? 0,
        'team2_score' => $scores['team2_score'] ?? 0,
        'player_stats' => $player_stats,
        'game_time' => $game_state['game_time'] ?? 720,
        'quarter' => $game_state['quarter'] ?? 1,
        'game_status' => $game_state['game_status'] ?? 'Ready'
    ]);
} else {
    echo json_encode(['error' => 'No match ID provided']);
}

mysqli_close($conn);
?>
