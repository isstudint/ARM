<?php
session_start();
include ("db.php");
include ("func.php");


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARM - Log In</title>
    <link rel="stylesheet" href="Css/login.css">
</head>

<body class="container">
    <div class="form-container">
        <h1 class="logo">ARM</h1>
        <h2 class="login-title">Log in</h2>
        <form action="login.php" method="POST">
            <div class="input-group">
                <label for="fullname" class="input-label">Full Name</label>
                <input type="text" id="fullname" name="fullname" required class="input-field" placeholder="Juan Dela Cruz">
            </div>

            <div class="input-group">
                <label for="password" class="input-label">Password</label>
                <input type="password" id="password" name="password" required class="input-field">
            </div>

            <button type="submit" class="submit-button">Log In</button>

            <p class="form-footer">
                Don't have an account? <a href="signup.php">Sign Up</a>
            </p>
        </form>
    </div>
</body>
</html>