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
            <tr onclick="showPlayerStats(<?php echo $player['player_id']; ?>)" style="cursor: pointer;">
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

  <!-- Player Stats Modal -->
  <div id="playerStatsModal" class="player-modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="modalTitle">Player Statistics</h2>
        <button class="close-btn" onclick="closePlayerStatsModal()">&times;</button>
      </div>
      <div class="modal-body">
        <div class="player-info" id="playerInfo">
          <!-- Player info will be populated here -->
        </div>
        <div class="stats-container" id="playerStatsContainer">
 
        </div>
      </div>
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


    function showPlayerStats(playerId) {
      const modal = document.getElementById('playerStatsModal');
      const statsContainer = document.getElementById('playerStatsContainer');
      
      // Show modal
      modal.style.display = 'block';
      
      // Show loading
      statsContainer.innerHTML = '<div class="loading">Loading player statistics...</div>';
      
      // Fetch player stats and game history
      fetch(`../api/get_player_stats.php?player_id=${playerId}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            displayPlayerStats(data);
          } else {
            statsContainer.innerHTML = '<div class="no-games">No statistics available for this player.</div>';
          }
        })
        .catch(error => {
          console.error('Error fetching player stats:', error);
          statsContainer.innerHTML = '<div class="no-games">Error loading statistics. Please try again.</div>';
        });
    }

    function displayPlayerStats(data) {
      const playerInfo = document.getElementById('playerInfo');
      const statsContainer = document.getElementById('playerStatsContainer');
      const modalTitle = document.getElementById('modalTitle');
      
      // Update modal title
      modalTitle.textContent = `${data.player.player_name} - Career Statistics`;
      
      // Generate player image or placeholder
      const playerImageHtml = data.player.image ? 
        `<img src="../${data.player.image}" alt="${data.player.player_name}" class="player-avatar-large" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
         <div class="player-avatar-placeholder" style="display:none;">${getInitials(data.player.player_name)}</div>` :
        `<div class="player-avatar-placeholder">${getInitials(data.player.player_name)}</div>`;
      
      // Update player info
      playerInfo.innerHTML = `
        <div>
          ${playerImageHtml}
        </div>
        <div class="player-details">
          <h3>${data.player.player_name}</h3>
          <p><strong>Team:</strong> ${data.player.team_name}</p>
          <p><strong>Position:</strong> ${data.player.position}</p>
          <p><strong>Jersey #:</strong> ${data.player.jersey_num || 'N/A'}</p>
          <p><strong>Age:</strong> ${data.player.age}</p>
          
          <div class="career-stats">
            <div class="career-stat">
              <div class="stat-value">${data.career_stats.avg_points}</div>
              <div class="stat-label">PPG</div>
            </div>
            <div class="career-stat">
              <div class="stat-value">${data.career_stats.avg_rebounds}</div>
              <div class="stat-label">RPG</div>
            </div>
            <div class="career-stat">
              <div class="stat-value">${data.career_stats.avg_assists}</div>
              <div class="stat-label">APG</div>
            </div>
            <div class="career-stat">
              <div class="stat-value">${data.career_stats.games_played}</div>
              <div class="stat-label">GP</div>
            </div>
          </div>
        </div>
      `;
      
      // Update games history
      statsContainer.innerHTML = `
        <div class="games-history">
          <h4>Game History (${data.games.length} games)</h4>
          ${generateGameHistory(data.games)}
        </div>
      `;
    }

    function generateGameHistory(games) {
      if (!games || games.length === 0) {
        return '<div class="no-games">No game history available for this player.</div>';
      }
      
      return games.map(game => {
        return `
          <div class="game-card">
            <div class="game-header">
              <div class="game-matchup">${game.team1_name} vs ${game.team2_name}</div>
              <div class="game-date">${formatDate(game.match_date)}</div>
            </div>
            <div class="game-stats">
              <div class="game-stat">
                <div class="value">${game.points || 0}</div>
                <div class="label">PTS</div>
              </div>
              <div class="game-stat">
                <div class="value">${game.rebounds || 0}</div>
                <div class="label">REB</div>
              </div>
              <div class="game-stat">
                <div class="value">${game.assists || 0}</div>
                <div class="label">AST</div>
              </div>
            </div>
          </div>
        `;
      }).join('');
    }

    function formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric'
      });
    }

    function getInitials(name) {
      return name.split(' ').map(word => word.charAt(0)).join('').toUpperCase().substring(0, 2);
    }

    function closePlayerStatsModal() {
      document.getElementById('playerStatsModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
      const modal = document.getElementById('playerStatsModal');
      if (event.target === modal) {
        closePlayerStatsModal();
      }
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        closePlayerStatsModal();
      }
    });
  </script>
</body>
</html>
