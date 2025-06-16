<!DOCTYPE html>
<html lang="en">
<head>
    <title>Support</title>
     <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../Css/support.css">
    <link rel="stylesheet" href="../Css/landig.css" />
</head>
<body>
    <?php include('sidebar.php') ?>

<br>
 <div class="container-ito">
        <div class="header">
            <h1>ARM</h1>
            <p>All Results Matters</p>
        </div>

        <div class="team-section">
            <div class="team-grid">
                <div class="team-card">
                    <div class="avatar">
                      <img src = "../Images/Ronz.jpg">
                    </div>
                    <h3>Ronz Arvie Remoroza</h3>
                    <div class="role">Full Stack Developer</div>
                    <div class="social-links">
                          <a href="https://www.facebook.com/" target="_blank" class="facebook">
                          <img src="../footer/fb.png" alt="Facebook Icon">
                      </a>
                      <a href="https://www.instagram.com/" target="_blank" class="instagram">
                          <img src="../footer/ig.png" alt="Instagram Icon">
                        </a>
                        <a href="https://www.youtube.com/" target="_blank" class="youtube">
                          <img src="../footer/yr.png" alt="YouTube Icon">
                        </a>
                    </div>
                </div>

                <div class="team-card">
                    <div class="avatar">
                        <img src = "../Images/rap.jpg">
                    </div>
                    <h3>Ralf Cagbay</h3>
                    <div class="role">Frontend Developer</div>  
                    <div class="social-links">
                          <a href="https://www.facebook.com/" target="_blank" class="facebook">
                          <img src="../footer/fb.png" alt="Facebook Icon">
                      </a>
                      <a href="https://www.instagram.com/" target="_blank" class="instagram">
                          <img src="../footer/ig.png" alt="Instagram Icon">
                        </a>
                        <a href="https://www.youtube.com/" target="_blank" class="youtube">
                          <img src="../footer/yr.png" alt="YouTube Icon">
                        </a>
                    </div>
                </div>

                <div class="team-card">
                    <div class="avatar">
                        <img src = "../Images/norl/bigz.png">
                    </div>
                    <h3>Marvelous Jaco Gonzales</h3>
                    <div class="role">Full Stack Developer</div>
                    <div class="social-links">
                         <a href="https://www.facebook.com/" target="_blank" class="facebook">
                          <img src="../footer/fb.png" alt="Facebook Icon">
                      </a>
                      <a href="https://www.instagram.com/" target="_blank" class="instagram">
                          <img src="../footer/ig.png" alt="Instagram Icon">
                        </a>
                        <a href="https://www.youtube.com/" target="_blank" class="youtube">
                          <img src="../footer/yr.png" alt="YouTube Icon">
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mission-section">
            <div class="mission-header">
                <h2>About Us</h2>
            </div>

            <div class="mission-grid">
                <div class="mission-card">
                    <h3>Our Mission</h3>
                    <div class="mission-caption">Connecting fans to the heartbeat of sports</div>
                    <p>At ARM, our mission is to bring fans closer to the game by delivering instant scores, live updates, and powerful insights — anytime, anywhere. We empower every sports enthusiast with the real-time thrill of every play, every match, and every moment that matters.</p>
                </div>

                <div class="mission-card">            
                    <h3>Our Vision</h3>
                    <div class="mission-caption">Building the future of sports engagement</div>
                    <p>To be the world's most trusted platform for real-time sports experiences — where every fan, anywhere, stays connected to the action and never misses the moments that define their passion for the game.</p>
                </div>
            </div>
        </div>
        
        <div class="login-section">
            <div class="login-title">Login as an Admin or Coach</div>
            <form method="POST" action="Login.php" class="login-form">
                <button type="submit" class="login-button">Login</button>
            </form>
            <div class="email-text">Send us a message if you want to be Admin or Coach</div>
            <input type="email" class="email-input" id="email-inp" placeholder="Message us">
        </div>
    </div>
  <br>
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

      const input = document.getElementById("email-inp");

    input.addEventListener("keydown", function(event) {
      if (event.key === "Enter") {
        event.preventDefault(); // Optional: prevent form submit behavior
        input.value = ""; // Clear input field
      }
    });
 
  </script>
</body>
</html>
