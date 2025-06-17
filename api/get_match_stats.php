<?php
header('Content-Type: application/json');

// Database connection
$conn = mysqli_connect("localhost", "root", "", "arm");

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$match_id = isset($_GET['match_id']) ? (int)$_GET['match_id'] : 0;

if (!$match_id) {
    echo json_encode(['success' => false, 'message' => 'Match ID required']);
    exit;
}

try {
    // Get match details
    $match_query = "
        SELECT 
            m.match_id,
            m.match_date,
            m.team1_id,
            m.team2_id,
            t1.team_name as team1_name,
            t1.logo as team1_logo,
            t2.team_name as team2_name,
            t2.logo as team2_logo,
            s.team1_score,
            s.team2_score
        FROM matches m
        JOIN teams t1 ON m.team1_id = t1.team_id
        JOIN teams t2 ON m.team2_id = t2.team_id
        LEFT JOIN scores s ON m.match_id = s.match_id
        WHERE m.match_id = ?
    ";
    
    $stmt = mysqli_prepare($conn, $match_query);
    mysqli_stmt_bind_param($stmt, "i", $match_id);
    mysqli_stmt_execute($stmt);
    $match_result = mysqli_stmt_get_result($stmt);
    $match = mysqli_fetch_assoc($match_result);
    
    if (!$match) {
        echo json_encode(['success' => false, 'message' => 'Match not found']);
        exit;
    }

    // Get Team 1 player stats
    $team1_stats_query = "
         SELECT 
        p.player_name,
        p.position,
        p.image,
        COALESCE(ps.points, 0) as points,
        COALESCE(ps.rebounds, 0) as rebounds,
        COALESCE(ps.assists, 0) as assists
    FROM players p
    LEFT JOIN player_stats ps ON p.player_id = ps.player_id AND ps.match_id = ?
    WHERE p.team_id = ?
    ORDER BY COALESCE(ps.points, 0) DESC, p.player_name ASC
    ";
    
    $stmt1 = mysqli_prepare($conn, $team1_stats_query);
    mysqli_stmt_bind_param($stmt1, "ii", $match_id, $match['team1_id']);
    mysqli_stmt_execute($stmt1);
    $team1_result = mysqli_stmt_get_result($stmt1);
    
    $team1_stats = [];
    while ($row = mysqli_fetch_assoc($team1_result)) {
        $team1_stats[] = $row;
    }

    // Get Team 2 player stats
    $team2_stats_query = "
       SELECT 
        p.player_name,
        p.position,
        p.image,
        COALESCE(ps.points, 0) as points,
        COALESCE(ps.rebounds, 0) as rebounds,
        COALESCE(ps.assists, 0) as assists
    FROM players p
    LEFT JOIN player_stats ps ON p.player_id = ps.player_id AND ps.match_id = ?
    WHERE p.team_id = ?
    ORDER BY COALESCE(ps.points, 0) DESC, p.player_name ASC
    ";
    
    $stmt2 = mysqli_prepare($conn, $team2_stats_query);
    mysqli_stmt_bind_param($stmt2, "ii", $match_id, $match['team2_id']);
    mysqli_stmt_execute($stmt2);
    $team2_result = mysqli_stmt_get_result($stmt2);
    
    $team2_stats = [];
    while ($row = mysqli_fetch_assoc($team2_result)) {
        $team2_stats[] = $row;
    }

    // Return the data
    echo json_encode([
        'success' => true,
        'match' => $match,
        'team1_stats' => $team1_stats,
        'team2_stats' => $team2_stats
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching data: ' . $e->getMessage()]);
}

mysqli_close($conn);
?>