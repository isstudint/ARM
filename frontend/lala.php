<?php 
    if(!isset($_SESSION)) { 
        session_start(); 
    } 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Css/landing.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Support - ARM</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #2c2c2c;
            background: #fafafa;
        }
        
        .support-main {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px;
            min-height: 100vh;
        }
        
        .hero-banner {
            height: 70vh;
            background: linear-gradient(135deg, #1a1a1a 0%, #2c2c2c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            margin-bottom: 80px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-content h1 {
            font-size: 4.5rem;
            font-weight: 300;
            letter-spacing: -2px;
            margin-bottom: 20px;
            font-family: 'Montserrat', sans-serif;
        }
        
        .hero-content p {
            font-size: 1.4rem;
            font-weight: 300;
            opacity: 0.8;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .content-section {
            margin-bottom: 120px;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .section-header h2 {
            font-size: 3rem;
            font-weight: 300;
            color: #1a1a1a;
            margin-bottom: 20px;
            font-family: 'Montserrat', sans-serif;
            letter-spacing: -1px;
        }
        
        .section-header p {
            font-size: 1.2rem;
            color: #666;
            max-width: 800px;
            margin: 0 auto;
            font-weight: 300;
        }
        
        .features-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 60px;
            margin-top: 80px;
        }
        
        .feature-box {
            text-align: center;
            padding: 40px 20px;
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: #f0f0f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: #666;
            font-size: 2rem;
        }
        
        .feature-box h3 {
            font-size: 1.3rem;
            font-weight: 500;
            margin-bottom: 15px;
            color: #1a1a1a;
        }
        
        .feature-box p {
            color: #666;
            font-weight: 300;
            line-height: 1.8;
        }
        
        .access-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            margin-top: 80px;
        }
        
        .access-panel {
            background: white;
            padding: 60px 40px;
            border: 1px solid #e0e0e0;
            transition: all 0.4s ease;
        }
        
        .access-panel:hover {
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }
        
        .panel-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .panel-icon {
            width: 60px;
            height: 60px;
            background: #1a1a1a;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 1.5rem;
        }
        
        .panel-header h3 {
            font-size: 1.8rem;
            font-weight: 400;
            color: #1a1a1a;
            margin-bottom: 15px;
        }
        
        .panel-header p {
            color: #666;
            font-weight: 300;
            line-height: 1.6;
        }
        
        .form-container {
            margin-top: 30px;
        }
        
        .input-group {
            margin-bottom: 25px;
        }
        
        .input-group label {
            display: block;
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 8px;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .input-group input {
            width: 100%;
            padding: 15px 0;
            border: none;
            border-bottom: 1px solid #e0e0e0;
            background: transparent;
            font-size: 1rem;
            color: #1a1a1a;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.3s ease;
        }
        
        .input-group input:focus {
            outline: none;
            border-bottom-color: #1a1a1a;
        }
        
        .submit-button {
            width: 100%;
            padding: 18px 0;
            background: #1a1a1a;
            color: white;
            border: none;
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
            font-family: 'Poppins', sans-serif;
        }
        
        .submit-button:hover {
            background: #333;
        }
        
        .coach-panel .panel-icon {
            background: #28a745;
        }
        
        .admin-panel .panel-icon {
            background: #dc3545;
        }
        
        .status-message {
            margin-top: 20px;
            padding: 15px;
            text-align: center;
            font-size: 0.9rem;
            display: none;
        }
        
        .success {
            background: #f8f9fa;
            color: #28a745;
            border: 1px solid #28a745;
        }
        
        @media (max-width: 768px) {
            .access-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            
            .hero-content h1 {
                font-size: 3rem;
            }
            
            .section-header h2 {
                font-size: 2.2rem;
            }
            
            .support-main {
                padding: 0 20px;
            }
        }
    </style>
</head>
<body>
    <?php include("sidebar.php") ?>
    
    <div class="container">
        <main class="main-content">
            <div class="support-main">
                
                <!-- Hero Banner -->
                <section class="hero-banner">
                    <div class="hero-content">
                        <h1>ARM Support</h1>
                        <p>Athletic Records Management - Redefining basketball league administration</p>
                    </div>
                </section>
                
                <!-- What is ARM -->
                <section class="content-section">
                    <div class="section-header">
                        <h2>What is ARM?</h2>
                        <p>
                            ARM represents the pinnacle of basketball management systems. Our platform seamlessly integrates 
                            real-time statistics, comprehensive team management, and advanced analytics to create an 
                            unparalleled experience for leagues, coaches, and administrators.
                        </p>
                    </div>
                    
                    <div class="features-container">
                        <div class="feature-box">
                            <div class="feature-icon">
                                <span class="material-symbols-outlined">sports_basketball</span>
                            </div>
                            <h3>Live Statistics</h3>
                            <p>Real-time game tracking with comprehensive player and team statistics updated instantly during matches.</p>
                        </div>
                        
                        <div class="feature-box">
                            <div class="feature-icon">
                                <span class="material-symbols-outlined">groups</span>
                            </div>
                            <h3>Team Management</h3>
                            <p>Complete roster control with detailed player profiles, performance analytics, and team organization tools.</p>
                        </div>
                        
                        <div class="feature-box">
                            <div class="feature-icon">
                                <span class="material-symbols-outlined">leaderboard</span>
                            </div>
                            <h3>League Operations</h3>
                            <p>Automated standings, match scheduling, and comprehensive league administration capabilities.</p>
                        </div>
                        
                        <div class="feature-box">
                            <div class="feature-icon">
                                <span class="material-symbols-outlined">analytics</span>
                            </div>
                            <h3>Advanced Analytics</h3>
                            <p>In-depth performance metrics and historical data analysis for strategic decision making.</p>
                        </div>
                    </div>
                </section>
                
                <!-- Access Panels -->
                <section class="content-section">
                    <div class="section-header">
                        <h2>Access ARM</h2>
                        <p>Join our platform as a coach or request administrative privileges for comprehensive league management.</p>
                    </div>
                    
                    <div class="access-grid">
                        <!-- Coach Access -->
                        <div class="access-panel coach-panel">
                            <div class="panel-header">
                                <div class="panel-icon">
                                    <span class="material-symbols-outlined">sports</span>
                                </div>
                                <h3>Coach Access</h3>
                                <p>Enter your designated access key to manage your team roster and track performance metrics.</p>
                            </div>
                            
                            <form class="form-container" onsubmit="handleCoachAccess(event)">
                                <div class="input-group">
                                    <label for="coachAccessKey">Access Key</label>
                                    <input type="text" id="coachAccessKey" name="coachAccessKey" placeholder="COACH-ABC123" required>
                                </div>
                                <button type="submit" class="submit-button">Enter</button>
                                <div class="status-message success" id="coachMessage">Access verified. Redirecting...</div>
                            </form>
                        </div>
                        
                        <!-- Admin Request -->
                        <div class="access-panel admin-panel">
                            <div class="panel-header">
                                <div class="panel-icon">
                                    <span class="material-symbols-outlined">admin_panel_settings</span>
                                </div>
                                <h3>Administrator Request</h3>
                                <p>Submit your credentials for administrative access to manage leagues, teams, and system operations.</p>
                            </div>
                            
                            <form class="form-container" onsubmit="handleAdminRequest(event)">
                                <div class="input-group">
                                    <label for="fullName">Full Name</label>
                                    <input type="text" id="fullName" name="fullName" required>
                                </div>
                                <div class="input-group">
                                    <label for="emailAddress">Email Address</label>
                                    <input type="email" id="emailAddress" name="emailAddress" required>
                                </div>
                                <div class="input-group">
                                    <label for="organization">Organization</label>
                                    <input type="text" id="organization" name="organization" required>
                                </div>
                                <button type="submit" class="submit-button">Submit Request</button>
                                <div class="status-message success" id="adminMessage">Request submitted successfully.</div>
                            </form>
                        </div>
                    </div>
                </section>
                
            </div>
        </main>
    </div>
    
    <script>
        function handleCoachAccess(event) {
            event.preventDefault();
            const accessKey = document.getElementById('coachAccessKey').value;
            
            if (accessKey.startsWith('COACH-')) {
                document.getElementById('coachMessage').style.display = 'block';
                setTimeout(() => {
                    alert('Coach dashboard feature coming soon');
                }, 1500);
            } else {
                alert('Invalid access key format');
            }
        }
        
        function handleAdminRequest(event) {
            event.preventDefault();
            document.getElementById('adminMessage').style.display = 'block';
            setTimeout(() => {
                event.target.reset();
                document.getElementById('adminMessage').style.display = 'none';
            }, 3000);
        }
        
        // Sidebar toggle functionality
        document.addEventListener("DOMContentLoaded", () => {
            const sidebarToggler = document.querySelector(".sidebar-toggler");
            const sidebar = document.querySelector(".sidebar");

            if (sidebarToggler && sidebar) {
                sidebarToggler.addEventListener("click", () => {
                    sidebar.classList.toggle("collapsed");
                });
            }
        });
    </script>
</body>
</html>