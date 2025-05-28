<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&"
    />
    <link rel="stylesheet" href="landig.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
                    <a href=""><h1 >Players</h1></a>
                </div>
            
                <div class="coaches laman">
                    <a href=""><h1 >Standing</h1></a>
                </div>
            </div>
            <div class="team" id = "laman">
                <h1 >Teams</h1>
            </div>
        </main>

        <div class="right">
            <h1>Match history</h1>
        </div>

        
    </div>

    <div id="div2">
                <div id="container">
                    <div class="section">
                      <h1>ARM</h1>
                      <h3>Never miss a moment.  </h3>
                      <h3>ARM brings the game</h3>
                      <h3>to your screen in real-time.</h3>
                    </div>
                  
                    <div class="section">
                      <h1>ARM</h1>
                      <h3>To give every sports fan live scores  </h3>
                      <h3>and updates, anytime, anywhere.</h3>
                      <h3> Also, To be the world’s go-to platform</h3>
                      <h3>for real-time sports tracking and team insights.</h3>
                    </div>
                   
                    <div class="logo">ARM<h4>All Results Matter</h4></div>
                    
                    </div>
                    <div class="section_logo">
                      <a href="https://www.facebook.com/" target="blank">
                          <img src="fb.png"  alt="Icon" >
                            </a>
                             <a href="https://www.Instagram.com/" target="blank">
                          <img src="ig.png"  alt="Icon" >
                            </a>
                            <a href="https://www.Youtube.com/" target="blank">
                              <img src="yr.png"  alt="Icon" >
                                </a>
                                </div>
       
                
                    
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
