<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Css/landing.css">
    <link rel="stylesheet" href="../Css/landig.css">
    <link rel="stylesheet" href="../Css/history.css">
    
    <title>Document</title>
</head>
<body>
    <?php include("sidebar.php") ?>
    <div class="history-container">
        <main class="main-content">
            <h1>History</h1>
            <p>This page will display the history of matches played.</p>
            <p>Content coming soon...</p>
        </main>
    
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