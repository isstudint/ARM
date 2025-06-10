<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Css/landig.css" />
    <link rel="stylesheet" href="../Css/landing.css" />
    <link rel="stylesheet" href="../Css/matchdets.css" />
    <title>Document</title>
</head>
<body>
    <?php include("sidebar.php") ?>

    <div class="container">
        <main class="main-content">
            <div class="match" id = "laman">
                <h1>MATCH TODAY</h1>
                <div class="teams">                   
                  <div class="team1">
                    <h3>Team name</h3>
                    <h7>(standing)</h7>    
                    <img src= "../Images/nba-boston.png" alt="Team 1 Logo" class="team-logo">
                  </div>
                  <div class="scored"><h1>84</h1></div>
                  <div class="score">
                    <h1>SCORE</h1>
                    <div class="oras"><h2>10:00</h2></div>
                  </div>
                  
                  <div class="scored"><h1>72</h1></div>

                <div class="team2"> 
                  <h3>Team name</h3> 
                  <h7>(standing)</h7>
                  <img src= "../Images/nba-haws.png" alt="Team 1 Logo" class="team-logo"> 
                </div>                
                </div>
            </div>

            <div class="below">
              <div class="left">
                <div class="player laman" >
                    <a href="roster.php"><h1>Players</h1></a>
                    <table class="player-table">
                      <thead class="thead">
                        <tr>
                          <td>Logo</td>
                          <td>Player name</td>
                          <td>Jersey #</td>
                          <td>Position</td>
                          <td>Team</td>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="litrato">
                            <img src="../Images/nba-boston.png" alt="Player Image" class="player-image">
                          </td>
                          <td>Player Name</td>
                          <td style="text-align: center;">18</td>
                          <td>Position</td>
                          <td>Team Name</td>
                        </tr>
                        <tr>
                          <td class="litrato">
                            <img src="../Images/nba-boston.png" alt="Player Image" class="player-image">
                          </td>
                          <td>Player Name</td>
                          <td style="text-align: center;">18</td>
                          <td>Position</td>
                          <td>Team Name</td>
                        </tr>
                      </tbody>
                    </table>  
                </div>

              </div>
              <div class="kanan">
                <div class="player laman" >
                    <a href="roster.php"><h1>Players</h1></a>
                    <table class="player-table">
                      <thead class="thead">
                        <tr>
                          <td>Logo</td>
                          <td>Player name</td>
                          <td>Jersey #</td>
                          <td>Position</td>
                          <td>Team</td>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="litrato">
                            <img src="../Images/nba-boston.png" alt="Player Image" class="player-image">
                          </td>
                          <td>Player Name</td>
                          <td style="text-align: center;">18</td>
                          <td>Position</td>
                          <td>Team Name</td>
                        </tr>
                        <tr>
                          <td class="litrato">
                            <img src="../Images/nba-boston.png" alt="Player Image" class="player-image">
                          </td>
                          <td>Player Name</td>
                          <td style="text-align: center;">18</td>
                          <td>Position</td>
                          <td>Team Name</td>
                        </tr>
                      </tbody>
                    </table>  
                </div>

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