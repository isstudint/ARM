<?php 
    if(!isset($_SESSION)) { 
        session_start(); 
    } 
?>
<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "arm");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$history_query = "
    SELECT 
        m.match_id,
        m.match_date,
        t1.team_name as team1_name,
        t1.logo as team1_logo,
        t2.team_name as team2_name,
        t2.logo as team2_logo,
        s.team1_score,
        s.team2_score,
        CASE 
            WHEN s.team1_score > s.team2_score THEN t1.team_name
            WHEN s.team2_score > s.team1_score THEN t2.team_name
        END as winner_name,
        CASE 
            WHEN s.team1_score > s.team2_score THEN 'team1'
            WHEN s.team2_score > s.team1_score THEN 'team2'
        END as winner
    FROM matches m
    JOIN teams t1 ON m.team1_id = t1.team_id
    JOIN teams t2 ON m.team2_id = t2.team_id
    JOIN scores s ON m.match_id = s.match_id
    WHERE s.team1_score IS NOT NULL AND s.team2_score IS NOT NULL
    ORDER BY m.match_date DESC
";

$games_result = mysqli_query($conn, $history_query);
$games = [];
while ($row = mysqli_fetch_assoc($games_result)) {
    $games[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../Css/landig.css" />
  <link rel="stylesheet" href="../Css/history1.css" />
  <title>Game History</title>
</head>
<body>
  <?php include("sidebar.php") ?>
  <br>
  <div class="header">
    <h1>Game History</h1>
  </div>

  <!-- Game History Display -->
  <div class="scoreboard">
    <?php if (empty($games)): ?>
      <div class="no-games">
        <div style="font-size: 48px; color: #ccc; margin-bottom: 20px;">🏀</div>
        <h3 style="color: #666; margin-bottom: 10px;">No Completed Games Yet</h3>
        <p style="color: #888;">Games will appear here once matches are completed and scores are recorded.</p>
      </div>
    <?php else: ?>
      <?php foreach ($games as $game): ?>
        <div class="game-row" onclick="showMatchStats(<?php echo $game['match_id']; ?>)">
          <div class="teams-info">
            <div class="team-info">
              <div class="team-logo">
                <img src="../<?php echo htmlspecialchars($game['team1_logo']); ?>" 
                     alt="<?php echo htmlspecialchars($game['team1_name']); ?>">
              </div>
              <div class="team-name <?php echo ($game['winner'] == 'team1') ? 'winner' : ''; ?>">
                <?php echo htmlspecialchars($game['team1_name']); ?>
              </div>
            </div>
            
            <div class="team-info">
              <div class="team-logo">
                <img src="../<?php echo htmlspecialchars($game['team2_logo']); ?>" 
                     alt="<?php echo htmlspecialchars($game['team2_name']); ?>">
              </div>
              <div class="team-name <?php echo ($game['winner'] == 'team2') ? 'winner' : ''; ?>">
                <?php echo htmlspecialchars($game['team2_name']); ?>
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
            <div class="game-date">
              <?php echo date('M j, Y', strtotime($game['match_date'])); ?>
              <small><?php echo date('g:i A', strtotime($game['match_date'])); ?></small>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- Player Stats Modal -->
  <div id="statsModal" class="stats-modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="modalTitle">Match Statistics</h2>
        <button class="close-btn" onclick="closeStatsModal()">&times;</button>
      </div>
      <div class="modal-body">
        <div class="match-info" id="matchInfo">
          <!-- Match info will be populated here -->
        </div>
        <div class="stats-container" id="statsContainer">
          <!-- Player stats will be populated here -->
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const sidebarToggler = document.querySelector(".sidebar-toggler");
      const sidebar = document.querySelector(".sidebar");

      if (sidebarToggler && sidebar) {
        sidebarToggler.addEventListener("click", () => {
          sidebar.classList.toggle("collapsed");
        });
      }
    });

    function showMatchStats(matchId) {
      const modal = document.getElementById('statsModal');
      const statsContainer = document.getElementById('statsContainer');
      
      // Show modal
      modal.style.display = 'block';
      
      // Show loading
      statsContainer.innerHTML = '<div class="loading">Loading player statistics...</div>';
      
      // Fetch match and player stats
      fetch(`../api/get_match_stats.php?match_id=${matchId}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            displayMatchStats(data);
          } else {
            statsContainer.innerHTML = '<div class="no-stats">No statistics available for this match.</div>';
          }
        })
        .catch(error => {
          console.error('Error fetching stats:', error);
          statsContainer.innerHTML = '<div class="no-stats">Error loading statistics. Please try again.</div>';
        });
    }

    function displayMatchStats(data) {
      const matchInfo = document.getElementById('matchInfo');
      const statsContainer = document.getElementById('statsContainer');
      const modalTitle = document.getElementById('modalTitle');
      
      // Update modal title
      modalTitle.textContent = `${data.match.team1_name} vs ${data.match.team2_name} - Statistics`;
      
      // Update match info
      matchInfo.innerHTML = `
        <div class="match-teams">
          <div class="team-display">
            <img src="../${data.match.team1_logo}" alt="${data.match.team1_name}" class="team-logo-small">
            <span class="team-name-large">${data.match.team1_name}</span>
          </div>
          <div class="final-score">${data.match.team1_score} - ${data.match.team2_score}</div>
          <div class="team-display">
            <img src="../${data.match.team2_logo}" alt="${data.match.team2_name}" class="team-logo-small">
            <span class="team-name-large">${data.match.team2_name}</span>
          </div>
        </div>
        <div class="game-date">
          ${new Date(data.match.match_date).toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit'
          })}
        </div>
      `;
      
      // Update stats container
      statsContainer.innerHTML = `
        <div class="team-stats">
          <div class="team-stats-header">
            <img src="../${data.match.team1_logo}" alt="${data.match.team1_name}" class="team-logo-large">
            <span class="team-name-large">${data.match.team1_name}</span>
          </div>
          <div class="stats-header">
            <span>Player</span>
            <div style="display: flex; gap: 20px;">
              <span>PTS</span>
              <span>REB</span>
              <span>AST</span>
            </div>
          </div>
          <div class="players-stats">
            ${generatePlayerStats(data.team1_stats)}
          </div>
        </div>
        
        <div class="team-stats">
          <div class="team-stats-header">
            <img src="../${data.match.team2_logo}" alt="${data.match.team2_name}" class="team-logo-large">
            <span class="team-name-large">${data.match.team2_name}</span>
          </div>
          <div class="stats-header">
            <span>Player</span>
            <div style="display: flex; gap: 20px;">
              <span>PTS</span>
              <span>REB</span>
              <span>AST</span>
            </div>
          </div>
          <div class="players-stats">
            ${generatePlayerStats(data.team2_stats)}
          </div>
        </div>
      `;
    }

    function generatePlayerStats(playerStats) {
      if (!playerStats || playerStats.length === 0) {
        return '<div class="no-stats">No player statistics recorded for this team.</div>';
      }
      
      return playerStats.map(player => {
        // Function to get player initials for placeholder
        const getInitials = (name) => {
          return name.split(' ').map(word => word.charAt(0)).join('').toUpperCase().substring(0, 2);
        };
        
        // Generate player image or placeholder
        const playerImageHtml = player.image ? 
          `<img src="../${player.image}" alt="${player.player_name}" class="player-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
           <div class="player-image-placeholder" style="display:none;">${getInitials(player.player_name)}</div>` :
          `<div class="player-image-placeholder">${getInitials(player.player_name)}</div>`;
        
        return `
          <div class="player-stat-row">
            <div class="player-info">
              ${playerImageHtml}
              <div class="player-name">${player.player_name}</div>
            </div>
            <div class="player-stats">
              <div class="stat-item">
                <div class="stat-value">${player.points || 0}</div>
                <div class="stat-label">PTS</div>
              </div>
              <div class="stat-item">
                <div class="stat-value">${player.rebounds || 0}</div>
                <div class="stat-label">REB</div>
              </div>
              <div class="stat-item">
                <div class="stat-value">${player.assists || 0}</div>
                <div class="stat-label">AST</div>
              </div>
            </div>
          </div>
        `;
      }).join('');
    }

    function closeStatsModal() {
      document.getElementById('statsModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
      const modal = document.getElementById('statsModal');
      if (event.target === modal) {
        closeStatsModal();
      }
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        closeStatsModal();
      }
    });
  </script>
</body>
</html>