<?php 
    if(!isset($_SESSION)) { 
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
    <link rel="stylesheet" href="../Css/sidebar.css" />
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
    <aside class="sidebar">
        <!-- Sidebar Header -->
        <div class="sidebar-header">
            <a href="landing.php" class="header-logo">
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
                    <a href="#" class="nav-link">
                        <span class="material-symbols-outlined">sports_basketball</span>
                        <span class="nav-label">Match Today</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="teams.php" class="nav-link">
                        <span class="material-symbols-outlined">groups</span>
                        <span class="nav-label">Teams</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link">
                        <span class="material-symbols-outlined">person</span>
                        <span class="nav-label">Roster</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link">
                        <span class="material-symbols-outlined">history</span>
                        <span class="nav-label">History</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="standing.php" class="nav-link">
                        <span class="material-symbols-outlined">leaderboard</span>
                        <span class="nav-label">Standing</span>
                    </a>
                </li>
                
                <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                <li class="nav-item">
                    <a href="manage_matches.php" class="nav-link">
                            <span class="material-symbols-outlined">manage_accounts</span>
                        <span class="nav-label">Manage Matches</span>    
                    </a>
                </li>
                <li class="nav-item">
                    <a href="manage_scores.php" class="nav-link">
                        <span class="material-symbols-outlined">scoreboard</span>
                        <span class="nav-label">Manage Scores</span>    
                    </a>
                </li>
                <?php endif; ?>

            
                <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 2): ?>
                <li class="nav-item">
                    <a href="manage_roster.php" class="nav-link">
                        <span class="material-symbols-outlined">patient_list</span>
                        <span class="nav-label">Manage Roster</span>    
                    </a>
                </li>
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
</aside>


</body></html>
