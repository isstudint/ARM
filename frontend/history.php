<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../Css/landing.css" />
  <link rel="stylesheet" href="../Css/landig.css" />
  <link rel="stylesheet" href="../Css/history.css" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

   

  <title>History</title>
</head>
<body>
  <?php include("sidebar.php") ?>

  <div class="history-container">
    <main class="main-content">
    <div class="header-row">
        <h1>Match History</h1>
        <input type="date" class="date-selector" />
    </div>
        <p>See detailed score breakdowns below.</p>


      <div class="teams-grid">
        <!-- Match 1 -->
        <div class="match-card">
          <div class="match-title"><img src="../Images/nba-haws.png" alt="Hawks Logo" class="teamlogo-history"> vs <img src="../Images/nba-boston.png" alt="Boston Logo" class="teamlogo-history"></div>
          <table class="score-table">
            <tr>
              <th>Team</th>
              <th>1st</th>
              <th>2nd</th>
              <th>3rd</th>
              <th>4th</th>
              <th>Final</th>
            </tr>
            <tr>
                <td class="team-name-cell">
                    <div class="logo-with-crown">
                    <span class="material-symbols-outlined crown-icon">crown</span>
                    <img src="../Images/nba-haws.png" alt="Hawks Logo" class="teamlogo-history" />
                    </div>
                </td>
                <td>20</td>
                <td>18</td>
                <td>25</td>
                <td>21</td>
                <td><strong>84</strong></td>
                </tr>
                <tr>
                <td class="team-name-cell">
                    <img src="../Images/nba-boston.png" alt="Boston Logo" class="teamlogo-history" />
                </td>
                <td>15</td>
                <td>22</td>
                <td>19</td>
                <td>21</td>
                <td><strong>77</strong></td>
            </tr>

          </table>
        </div>
        <div class="match-card">
          <div class="match-title"><img src="../Images/nba-haws.png" alt="Hawks Logo" class="teamlogo-history"> vs <img src="../Images/nba-boston.png" alt="Boston Logo" class="teamlogo-history"></div>
          <table class="score-table">
            <tr>
              <th>Team</th>
              <th>1st</th>
              <th>2nd</th>
              <th>3rd</th>
              <th>4th</th>
              <th>Final</th>
            </tr>
            <tr>
                <td class="team-name-cell">
                    <div class="logo-with-crown">
                    <span class="material-symbols-outlined crown-icon">crown</span>
                    <img src="../Images/nba-haws.png" alt="Hawks Logo" class="teamlogo-history" />
                    </div>
                </td>
                <td>20</td>
                <td>18</td>
                <td>25</td>
                <td>21</td>
                <td><strong>84</strong></td>
                </tr>
                <tr>
                <td class="team-name-cell">
                    <img src="../Images/nba-boston.png" alt="Boston Logo" class="teamlogo-history" />
                </td>
                <td>15</td>
                <td>22</td>
                <td>19</td>
                <td>21</td>
                <td><strong>77</strong></td>
            </tr>

          </table>
        </div>
          </table>
        </div>
      </div>
    </main>
  </div>
</body>

  <script>
      document.addEventListener("DOMContentLoaded", () => {
        const sidebarToggler = document.querySelector(".sidebar-toggler");
        const sidebar = document.querySelector(".sidebar");

        sidebarToggler.addEventListener("click", () => {
          sidebar.classList.toggle("collapsed");
        });
      });
    </script>



</html>
