<?php
include('../frontend/db.php');

if (isset($_GET['player_id'])) {
    $player_id = intval($_GET['player_id']);
    
    try {
        // Get player basic info
        $player_query = "
            SELECT p.*, t.team_name 
            FROM players p 
            JOIN teams t ON p.team_id = t.team_id 
            WHERE p.player_id = ?
        ";
        $stmt = mysqli_prepare($conn, $player_query);
        mysqli_stmt_bind_param($stmt, "i", $player_id);
        mysqli_stmt_execute($stmt);
        $player_result = mysqli_stmt_get_result($stmt);
        $player = mysqli_fetch_assoc($player_result);
        
        if (!$player) {
            echo json_encode(['success' => false, 'error' => 'Player not found']);
            exit;
        }
        
        // Get career statistics
        $career_query = "
            SELECT 
                COUNT(*) as games_played,
                ROUND(AVG(points), 1) as avg_points,
                ROUND(AVG(rebounds), 1) as avg_rebounds,
                ROUND(AVG(assists), 1) as avg_assists,
                SUM(points) as total_points,
                SUM(rebounds) as total_rebounds,
                SUM(assists) as total_assists
            FROM player_stats 
            WHERE player_id = ?
        ";
        $stmt2 = mysqli_prepare($conn, $career_query);
        mysqli_stmt_bind_param($stmt2, "i", $player_id);
        mysqli_stmt_execute($stmt2);
        $career_result = mysqli_stmt_get_result($stmt2);
        $career_stats = mysqli_fetch_assoc($career_result);
        
        // Get game-by-game history
        $games_query = "
            SELECT 
                ps.points, ps.rebounds, ps.assists,
                m.match_date, m.match_id,
                t1.team_name as team1_name,
                t2.team_name as team2_name,
                s.team1_score, s.team2_score
            FROM player_stats ps
            JOIN matches m ON ps.match_id = m.match_id
            JOIN teams t1 ON m.team1_id = t1.team_id
            JOIN teams t2 ON m.team2_id = t2.team_id
            LEFT JOIN scores s ON m.match_id = s.match_id
            WHERE ps.player_id = ?
            ORDER BY m.match_date DESC
        ";
        $stmt3 = mysqli_prepare($conn, $games_query);
        mysqli_stmt_bind_param($stmt3, "i", $player_id);
        mysqli_stmt_execute($stmt3);
        $games_result = mysqli_stmt_get_result($stmt3);
        
        $games = [];
        while ($game = mysqli_fetch_assoc($games_result)) {
            $games[] = $game;
        }
        
        // Clean up career stats (handle null values)
        $career_stats['games_played'] = $career_stats['games_played'] ?: 0;
        $career_stats['avg_points'] = $career_stats['avg_points'] ?: '0.0';
        $career_stats['avg_rebounds'] = $career_stats['avg_rebounds'] ?: '0.0';
        $career_stats['avg_assists'] = $career_stats['avg_assists'] ?: '0.0';
        
        echo json_encode([
            'success' => true,
            'player' => $player,
            'career_stats' => $career_stats,
            'games' => $games
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No player ID provided']);
}
?>
