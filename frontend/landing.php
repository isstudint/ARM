<?php 

    if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&"
    />
    <link rel="stylesheet" href="../Css/landig.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
</head>

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
                  <div class="scored"><h1>999</h1></div>

                  <div class="score"><h2>SCORE</h2></div>
                    <div class="scored"><h1>999</h1></div>

                <div class="team2"> 
                  <h3>Team name</h3> 
                  <h7>(standing)</h7>
                  <img src= "../Images/nba-haws.png" alt="Team 1 Logo" class="team-logo"> 
                </div>                
                </div>
            </div>

            <div class="human">
                <div class="player laman" >
                    <a href="roster.php"><h1>Players</h1></a>
                    <table class="player-table">
                      <thead class="thead">
                        <tr>
                            <td>Logo</td>
                            <td>Player name</td>
                            <td>Position</td>
                            <td>Team</td>
                        </tr>
                      <thead>
                      <tbody>
                        <tr>
                            <td class = "litrato"><img src="../Images/nba-boston.png" alt="Player Image" class="player-image"></td>
                            <td>Player Name</td>
                            <td>Position</td>
                            <td>Team Name</td>
                        </tr>
                        
                        <tr>
                            <td class = "litrato"><img src="../Images/nba-boston.png" alt="Player Image" class="player-image"></td>
                            <td>Player Name</td>
                            <td>Position</td>
                            <td>Team Name</td>
                        </tr>
                      

                    </table>
                    
                </div>
            
                <div class="coaches laman">
                    <a href=""><h1 >Standing</h1></a>
                    <table class="player-table">
                      <thead class="thead">
                        <tr>
                            <td>Logo</td>
                            <td>Team name</td>
                            <td>Standing</td>
                            <td>Win %</td>
                        </tr>
                      <thead>
                      <tbody>
                        <tr>
                            <td class = "litrato"><img src="../Images/nba-boston.png" alt="Player Image" class="player-image"></td>
                            <td>Boston Celtics</td>
                            <td>12 -  1</td>
                            <td>99%</td>
                        </tr>
                        
                        <tr>
                            <td class = "litrato"><img src="../Images/nba-boston.png" alt="Player Image" class="player-image"></td>
                            <td>Boston Celtics</td>
                            <td>12 -  1</td>
                            <td>99%</td>
                        </tr>
                      

                    </table>
                </div>
            </div>
            <div class="team" id = "laman">
              <h1 >Teams</h1>
              <div class="scroller">
                <div class="scrolling">
                <img src="../Images/nba-boston.png" alt="Player Image" class="teamlogo">
                <img src="../Images/nba-haws.png" alt="Player Image" class="teamlogo">
                <img src="../Images/nba-minnesota.png" alt="Player Image" class="teamlogo">
                <img src="../Images/nba-norl.png" alt="Player Image" class="teamlogo">
            
           
              </div>
            </div>
        </main>

        <div class="right">
          <h1>Match history</h1>

          <table class="match-history-table">
            <thead>
              <tr>
                <th>Match</th>
                <th class="winner">Winner</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="match-history">
                  <div class="teams-wrapper">
                    <div class="team-history">
                      <img src="../Images/nba-boston.png" alt="Boston Logo" class="teamlogo">
                      <div class="score">102</div>
                    </div>
                    <div class="vs">VS</div>
                    <div class="team-history">
                      <img src="../Images/nba-haws.png" alt="Hawks Logo" class="teamlogo">
                      <div class="score">98</div>
                    </div>
                  </div>
                </td>
                <td>Team 1</td>
              </tr>

              <tr>
                <td class="match-history">
                  <div class="teams-wrapper">
                    <div class="team-history">
                      <img src="../Images/nba-boston.png" alt="Boston Logo" class="teamlogo">
                      <div class="score">102</div>
                    </div>
                    <div class="vs">VS</div>
                    <div class="team-history">
                      <img src="../Images/nba-haws.png" alt="Hawks Logo" class="teamlogo">
                      <div class="score">98</div>
                    </div>
                  </div>
                </td>
                <td>Team 1</td>
              </tr>

              <tr>
                <td class="match-history">
                  <div class="teams-wrapper">
                    <div class="team-history">
                      <img src="../Images/nba-boston.png" alt="Boston Logo" class="teamlogo">
                      <div class="score">102</div>
                    </div>
                    <div class="vs">VS</div>
                    <div class="team-history">
                      <img src="../Images/nba-haws.png" alt="Hawks Logo" class="teamlogo">
                      <div class="score">98</div>
                    </div>
                  </div>
                </td>
                <td>Team 1</td>
              </tr>

              <tr>
                <td class="match-history">
                  <div class="teams-wrapper">
                    <div class="team-history">
                      <img src="../Images/nba-boston.png" alt="Boston Logo" class="teamlogo">
                      <div class="score">102</div>
                    </div>
                    <div class="vs">VS</div>
                    <div class="team-history">
                      <img src="../Images/nba-haws.png" alt="Hawks Logo" class="teamlogo">
                      <div class="score">98</div>
                    </div>
                  </div>
                </td>
                <td>Team 1</td>
              </tr>
            </tbody>
          </table>
  </div>


        
</div>

    <!-- <div id="div2">
                <div id="container">
                    <div class="section">
                      <h1>ARM</h1>
                      <h3>Never miss a moment.  </h3>
                      <h3>ARM brings the game</h3>
                      <h3>to your screen in real-time.</h3>
                    </div>
                    <div class="vs">VS</div>
                    <div class="team-history">
                      <img src="../Images/nba-haws.png" alt="Hawks Logo" class="teamlogo">
                      <div class="score">98</div>
                    </div>
                  </div>
                </td>
                <td>Team 1</td>
              </tr>
            </tbody>
          </table>
</div> -->



        
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