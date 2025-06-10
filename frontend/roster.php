<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../Css/roster.css" />
  <link rel="stylesheet" href="../Css/landig.css" />
  <title>Rosters</title>
</head>
<body>
  <?php include("sidebar.php") ?>

  <div class="contains">
    <!-- Nav bar with team logos -->
    <div class="teams-navbar">
      <nav>
        <ul class="team-logo-list">
          <li><a href="roster.php"><img src="../Images/nba-boston.png" alt="" class="team-logo"></a></li>
          <li><a href="roster.php"><img src="../Images/nba-haws.png" alt="" class="team-logo"></a></li>
          <li><a href="roster.php"><img src="../Images/nba-minnesota.png" alt="" class="team-logo"></a></li>   
          <li><a href="roster.php"><img src="../Images/nba-boston.png" alt="" class="team-logo"></a></li>         
        </ul>
      </nav>


      
    </div>
      <!-- Roster table -->
    <!-- Roster table -->
    <div class="roster-table">
      <h2>Roster</h2>

      <!-- Search bar -->
      <input type="text" id="searchInput" placeholder="Search for players..." class="search-bar"/>

      <table id="rosterTable">
        <thead>
          <tr>
            <th>Player Name</th>
            <th>Position</th>
            <th>Team</th>
            <th>Ppg</th>
            <th>Rpg</th>
            <th>Apg</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>John Doe</td>
            <td>Guard</td>
            <td>Boston Celtics</td>
            <td>12</td>
            <td>3</td>
            <td>5</td>
          </tr>
          <tr>
            <td>Jane Smith</td>
            <td>Forward</td>
            <td>Atlanta Hawks</td>
            <td>15</td>
            <td>6</td>
            <td>4</td> 
          </tr>
          <!-- More player rows -->
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
