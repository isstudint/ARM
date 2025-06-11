<?php
$games = [
    [
        'team1_name' => 'La Tri',
        'team1_logo' => '../Images/nba-boston.png',
        'team1_score' => 102,
        'team2_name' => 'Conchu',
        'team2_logo' => '../Images/nba-minnesota.png',
        'team2_score' => 122,
        'winner' => 'team2'
    ],
    [
        'team1_name' => 'GenTri',
        'team1_logo' => '../Images/nba-haws.png',
        'team1_score' => 100,
        'team2_name' => 'Fontana',
        'team2_logo' => '../Images/nba-norl.png',
        'team2_score' => 102,
        'winner' => 'team2'
    ],
    [
        'team1_name' => 'Conchu',
        'team1_logo' => '../Images/nba-haws.png',
        'team1_score' => 122,
        'team2_name' => 'Borland',
        'team2_logo' => '../Images/nba-minnesota.png',
        'team2_score' => 120,
        'winner' => 'team1'
    ],
    [
        'team1_name' => 'La Tri',
        'team1_logo' => '../Images/nba-boston.png',
        'team1_score' => 70,
        'team2_name' => 'GenTri',
        'team2_logo' => '../Images/nba-haws.png',
        'team2_score' => 100,
        'winner' => 'team2'
    ],
    [
        'team1_name' => 'Borland',
        'team1_logo' => '../Images/nba-minnesota.png',
        'team1_score' => 132,
        'team2_name' => 'Fontana',
        'team2_logo' => '../Images/nba-norl.png',
        'team2_score' => 80,
        'winner' => 'team1'
    ],
     [
        'team1_name' => 'Borland',
        'team1_logo' => '../Images/nba-minnesota.png',
        'team1_score' => 132,
        'team2_name' => 'Fontana',
        'team2_logo' => '../Images/nba-norl.png',
        'team2_score' => 85,
        'winner' => 'team1'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../Css/landing.css" />
  <link rel="stylesheet" href="../Css/landig.css" />
  <link rel="stylesheet" href="../Css/history1.css" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
  <title>History</title>
</head>
<body>
  <?php include("sidebar.php") ?>

  <div class="scoreboard">
    <?php foreach ($games as $game): ?>
      <div class="game-row">
        <div class="teams-info">
          <div class="team-info">
            <div class="team-logo">
              <img src="<?php echo $game['team1_logo']; ?>" alt="<?php echo $game['team1_name']; ?>">
            </div>
            <div class="team-name <?php echo ($game['winner'] == 'team1') ? 'winner' : ''; ?>">
              <?php echo $game['team1_name']; ?>
            </div>
          </div>
          <div class="team-info">
            <div class="team-logo">
              <img src="<?php echo $game['team2_logo']; ?>" alt="<?php echo $game['team2_name']; ?>">
            </div>
            <div class="team-name <?php echo ($game['winner'] == 'team2') ? 'winner' : ''; ?>">
              <?php echo $game['team2_name']; ?>
            </div>
          </div>
        </div>
        
        <div class="game-divider"></div>
        
        <div class="scores-section">
          <div class="team-score <?php echo ($game['winner'] == 'team1') ? 'winner' : ''; ?>">
            <?php echo $game['team1_score']; ?>
          </div>
          <div class="team-score <?php echo ($game['winner'] == 'team2') ? 'winner' : ''; ?>">
            <?php echo $game['team2_score']; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const sidebarToggler = document.querySelector(".sidebar-toggler");
      const sidebar = document.querySelector(".sidebar");

      sidebarToggler.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
      });
    });
  </script>
</body>
</html>