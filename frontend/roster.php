<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../Css/roster.css" />
  <link rel="stylesheet" href="../Css/landig.css" />
  
  <title>Rosters</title>
  <style>

  </style>
</head>
<body>
  <?php include("sidebar.php") ?>

  <?php
  $conn = mysqli_connect("localhost", "root", "", "arm");
  

  $teams_query = "SELECT team_id, team_name, logo FROM teams ORDER BY team_name";
  $teams = mysqli_query($conn, $teams_query);
  



  // Get selected team or show all
  $selected_team = isset($_GET['team_id']) ? (int)$_GET['team_id'] : null;

  // Get players
if ($selected_team) {
  $players_query = "
    SELECT 
      p.*, 
      t.team_name,
      ROUND(AVG(s.points), 2) AS ppg,
      ROUND(AVG(s.assists), 2) AS apg,
      ROUND(AVG(s.rebounds), 2) AS rpg
    FROM players p
    JOIN teams t ON p.team_id = t.team_id
    LEFT JOIN player_stats s ON p.player_id = s.player_id
    WHERE p.team_id = $selected_team
    GROUP BY p.player_id
    ORDER BY p.player_name";
} else {
  $players_query = "
    SELECT 
      p.*, 
      t.team_name,
      ROUND(AVG(s.points), 2) AS ppg,
      ROUND(AVG(s.assists), 2) AS apg,
      ROUND(AVG(s.rebounds), 2) AS rpg
    FROM players p
    JOIN teams t ON p.team_id = t.team_id
    LEFT JOIN player_stats s ON p.player_id = s.player_id
    GROUP BY p.player_id
    ORDER BY t.team_name, p.player_name";
}

  $players = mysqli_query($conn, $players_query);
  ?>

  <div class="contains">
    <!-- Nav bar with team logos -->
    <div class="teams-navbar">
      <nav>
        <ul class="team-logo-list">
          <li><a href="roster.php" class="<?php echo !$selected_team ? 'active' : ''; ?>" style= "text-decoration: none"><button class = "all_teams" style="text-decoration: none; background:rgb(0, 233, 221); color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; font-size: 12px;">All Teams</button></a></li>
          <?php while($team = mysqli_fetch_assoc($teams)): ?>
          <li>
            <a href="roster.php?team_id=<?php echo $team['team_id']; ?>" 
               class="<?php echo $selected_team == $team['team_id'] ? 'active' : ''; ?>">
              <?php if(!empty($team['logo']) && file_exists('../' . $team['logo'])): ?>
                <img src="../<?php echo htmlspecialchars($team['logo']); ?>" alt="<?php echo htmlspecialchars($team['team_name']); ?>" class="team-logo">
              <?php else: ?>
                <div class="team-logo-text"><?php echo substr($team['team_name'], 0, 3); ?></div>
              <?php endif; ?>
            </a>
          </li>
          <?php endwhile; ?>
        </ul>
      </nav>
    </div>
      
    <!-- Roster table -->
    <div class="roster-table">
      <h2>
        <?php if ($selected_team): ?>
          <?php
          mysqli_data_seek($teams, 0);
          while($team = mysqli_fetch_assoc($teams)) {
            if ($team['team_id'] == $selected_team) {
              echo htmlspecialchars($team['team_name']) . " Roster";
              break;
            }
          }
          ?>
        <?php else: ?>
          All Players
        <?php endif; ?>
      </h2>

      <!-- Search bar -->
      <input type="text" id="searchInput" placeholder="Search for players..." class="search-bar"/>

      <table id="rosterTable">
        <thead>
          <tr>
            <th>Img</th>
            <th>Jersey #</th>
            <th>Player Name</th>
            <th>Position</th>
            <th>Team</th>
            <th>Age</th>
            <th>Ppg</th>
            <th>Apg</th>
            <th>Rpg</th>
          </tr>
        </thead>
        <tbody>
          <?php while($player = mysqli_fetch_assoc($players)): ?>
            <tr>
              <td>
                  <?php if(!empty($player['image']) && file_exists('../' . $player['image'])): ?>
                      <img src="../<?php echo htmlspecialchars($player['image']); ?>" alt="<?php echo htmlspecialchars($player['player_name']); ?>" class="player-image-preview">
                  <?php else: ?>
                      <div class="no-image-placeholder">No Image</div>
                  <?php endif; ?>
              </td>
              <td><?php echo htmlspecialchars($player['jersey_num']); ?></td>
              <td><?php echo htmlspecialchars($player['player_name']); ?></td>
              <td><?php echo htmlspecialchars($player['position']); ?></td>
              <td><?php echo htmlspecialchars($player['team_name']); ?></td>
              <td><?php echo htmlspecialchars($player['age']); ?></td>
              <td><?php echo htmlspecialchars($player['ppg']); ?></td>
              <td><?php echo htmlspecialchars($player['apg']); ?></td> 
              <td><?php echo htmlspecialchars($player['rpg']); ?></td>
            </tr>
          <?php endwhile; ?>
      </tbody>

      </table>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const sidebarToggler = document.querySelector(".sidebar-toggler");
      const sidebar = document.querySelector(".sidebar");

      sidebarToggler?.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
      });
    });



    // Search functionality for the roster table
    
    document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("searchInput");
    const table = document.getElementById("rosterTable");
    const rows = table.getElementsByTagName("tr");

      searchInput.addEventListener("input", () => {
      const filter = searchInput.value.toLowerCase();
      for (let i = 1; i < rows.length; i++) {
        const rowText = rows[i].innerText.toLowerCase();
        rows[i].style.display = rowText.includes(filter) ? "" : "none";
      }
    });
  });


  </script>
</body>
</html>
</html>
