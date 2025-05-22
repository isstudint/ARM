<?php
$conn = mysqli_connect("localhost", "root", "", "arm");

$check = mysqli_query($conn, "SELECT * FROM teams LIMIT 1");
if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "INSERT INTO teams (team_name, coach_name) VALUES ('Borland', 'John')");
    mysqli_query($conn, "INSERT INTO teams (team_name, coach_name) VALUES ('Gentri', 'Mary')");
    mysqli_query($conn, "INSERT INTO matches (team1_id, team2_id, match_date) VALUES (1, 2, '2025-05-25')");
    mysqli_query($conn, "INSERT INTO scores (match_id, team1_score, team2_score) VALUES (1, 0, 0)");
}

if (isset($_GET['action'])) {
    $match_id = 1;
    $result = mysqli_query($conn, "SELECT team1_score, team2_score FROM scores WHERE match_id = $match_id");
    $row = mysqli_fetch_assoc($result);
    $team1_score = $row['team1_score'];
    $team2_score = $row['team2_score'];

    if ($_GET['action'] == 'team1_plus') $team1_score += 1;
    elseif ($_GET['action'] == 'team1_minus' && $team1_score > 0) $team1_score -= 1;
    elseif ($_GET['action'] == 'team2_plus') $team2_score += 1;
    elseif ($_GET['action'] == 'team2_minus' && $team2_score > 0) $team2_score -= 1;

    mysqli_query($conn, "UPDATE scores SET team1_score = $team1_score, team2_score = $team2_score WHERE match_id = $match_id");
    header("Location: index.php");
    exit;
}




$query = "SELECT m.match_id, t1.team_name AS team1_name, t2.team_name AS team2_name, 
          s.team1_score, s.team2_score 
          FROM matches m
          JOIN teams t1 ON m.team1_id = t1.team_id
          JOIN teams t2 ON m.team2_id = t2.team_id
          JOIN scores s ON m.match_id = s.match_id
          LIMIT 1";
$result = mysqli_query($conn, $query);
$match = mysqli_fetch_assoc($result);
?>

<html>
<head>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <style>
        body { font-family: Arial; text-align: center; margin-top: 50px; }
        .score { font-size: 48px; font-weight: bold; margin: 20px; }
        .team { font-size: 24px; }
        a { margin: 0 10px; text-decoration: none; display: inline-block; 
            padding: 10px 15px; background: #eee; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Match Score</h1>
    
    <div class="team"><?php echo $match['team1_name']; ?></div>
    <div class="score"><?php echo $match['team1_score']; ?></div>
    <div>
        <a href="?action=team1_plus">+2 Point</a>
        <a href="?action=team1_minus">-1 Point</a>
    </div>
    
    <hr>
    
    <div class="team"><?php echo $match['team2_name']; ?></div>
    <div class="score"><?php echo $match['team2_score']; ?></div>
    <div>
        <a href="?action=team2_plus">+2 Point</a>
        <a href="?action=team2_minus">-1 Point</a>
    </div>
</body>
</html>

