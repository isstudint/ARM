<?php 

    if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 

?>
<!DOCTYPE html>
<html lang="en">
<head>
<<<<<<< Updated upstream
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&" />
    <link rel="stylesheet" href="../Css/landing.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;
    0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
=======
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
>>>>>>> Stashed changes
    <title>Document</title>
</head>
<body>
<<<<<<< HEAD
    
    <?php include("sidebar.php") ?>
=======
    <aside class="sidebar">
        <!-- Sidebar Header -->
        <div class="sidebar-header">
            <a href="#" class="header-logo">
                <h1 class="sidebar-title">ARM</h1>
            </a>
            <button class="toggler sidebar-toggler">
                <span class="material-symbols-outlined">chevron_left</span>
            </button>
        </div>

        <!-- Sidebar Primary nav -->
        <nav class="sidebar-nav">
            <ul class="nav-list primary-nav">
                <li class="nav-item">
                    <a href="teams.php" class="nav-link">
                        <span class="nav-icon material-symbols-outlined">groups_3</span>
                        <span class="nav-label">Teams</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link">
                        <span class="nav-icon material-symbols-outlined">person</span>
                        <span class="nav-label">Roster</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link">
                        <span class="nav-icon material-symbols-outlined">history_2</span>
                        <span class="nav-label">History</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="standing.php" class="nav-link">
                        <span class="nav-icon material-symbols-outlined">leaderboard</span>
                        <span class="nav-label">Standing</span>
                    </a>
<<<<<<< Updated upstream
                </li>                   <!--<span class="nav-icon material-symbols-outlined">sports_soccer</span> !--> 
                 <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>

                <li class="nav-item">
                    <a href="manage_matches.php" class="nav-link">
                            <span class="material-symbols-outlined">manage_accounts</span>
                        <span class="nav-label">Manage Matches</span>    
                    </a>
                </li>
                <?php endif; ?>



                <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 2): ?>
                <li class="nav-item">
                    <a href="manage_roster.php" class="nav-link">
                        <span class="material-symbols-outlined">patient_list</span>
                        <span class="nav-label">Manage Roster</span>    
                    </a>

                <?php endif; ?>
                
                <?php if(isset($_SESSION['is_admin']) && ($_SESSION['is_admin'] == 1 || $_SESSION['is_admin'] == 2)   ): ?>
                 <li class="nav-item">
                    <a href="manage_teams.php" class="nav-link">
                        <span class="nav-icon material-symbols-outlined">trophy</span>
                        <span class="nav-label">Manage Teams</span>    
                    </a>
                </li>
                <?php endif; ?>

            </ul>
            <!-- Sidebar Secondary nav -->
              <ul class="nav-list footer-nav">
                    <li class="nav-item">
                        <a href="" class="nav-link">
                            <span class="nav-icon material-symbols-outlined">settings</span>
                            <span class="nav-label">Settings</span>    
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="" class="nav-link">
                            <span class="nav-icon material-symbols-outlined">help</span>
                            <span class="nav-label">Help</span>    
                        </a>
                    </li>
                <?php if(isset($_SESSION['is_admin']) && ($_SESSION['is_admin'] == 1 || $_SESSION['is_admin'] == 2)  ): ?>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <span class="nav-icon material-symbols-outlined">logout</span>
                        <span class="nav-label">Logout</span>    
                    </a>
                </li>
                <?php endif; ?>
                </ul>
         </nav>
=======
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link">
                        <span class="nav-icon material-symbols-outlined">trophy</span>
                        <span class="nav-label">Coach</span>
                    </a>
                </li>
            </ul>
>>>>>>> Stashed changes

            <!-- Sidebar Secondary nav -->
            <ul class="nav-list footer-nav">
                <li class="nav-item">
                    <a href="" class="nav-link">
                        <span class="nav-icon material-symbols-outlined">settings</span>
                        <span class="nav-label">Settings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link">
                        <span class="nav-icon material-symbols-outlined">help</span>
                        <span class="nav-label">Help</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>
>>>>>>> parent of 694332d (asdsadasd)

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
                    <a href=""><h1 >Coaches</h1></a>
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
