<?php
session_start();
include ("db.php");
include ("func.php");

if($_SERVER['REQUEST_METHOD'] == "POST"){
     $fullname =$_POST['fullname'];
     $email = $_POST['email'];
     $bday = $_POST['bday'];
     $password = $_POST['password'];

    if(strlen($password) < 8){
        echo("MUST BE 8");
    }else{
        $query = "INSERT INTO users VALUES('$fullname', '$email', '$bday', '$password')";
        mysqli_query($conn, $query);
        header("Location:login.php");
        die;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Css/output.css">
    <link rel="stylesheet" href="Css/signup.css">
    <title>Sign Up</title>
</head>



<body class="container">
    <div class="form-container">
        <h1 class="logo">ARM</h1>
        <h1 class="signup-title">Sign up</h1>
        <form action="signup.php" method="POST">
            <div class="input-group">
                <label for="fullname" class="input-label">Full Name</label>
                <input type="text" id="fullname" name="fullname" required class="input-field" placeholder="Juan Dela Cruz">
            </div>

            <div class="input-group">
                <label for="email" class="input-label">Email</label>
                <input type="email" id="email" name="email" required class="input-field" placeholder="Example@email.com">
            </div>

            <div class="input-group">
                <label for="bday" class="input-label">Birthdate</label>
                <input type="date" id="bday" name="bday" required class="input-field">
            </div>
            <div class="input-group">
                <label for="password" class="input-label">Password</label>
                <input type="password" id="password" name="password" required class="input-field">
            </div>

            <button type="submit" class="submit-button">Sign Up</button>

            <p class="form-footer">
                Already have an account? 
                <a href="login.php">Log in</a>

            </p>
             </div>
        </form>
    </div>
</body>
</html>