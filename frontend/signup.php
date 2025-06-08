<?php
session_start();
include ("db.php");
include ("func.php");

$admin_keys = array("ARM2023admin", "RapRap2023", "Ronz2023", "Marvs2023","Fein");
$coach_keys = array("Respect", "MG","DNA");
$error_msg = "";
$success_msg = "";

if($_SERVER['REQUEST_METHOD'] == "POST"){
     $fullname = $_POST['fullname'];
     $email = $_POST['email'];
     $accesskey = $_POST['accesskey'];
     $password = $_POST['password'];

    if(strlen($password) < 8){
        $error_msg = "Password must be at least 8 characters long!";
    } 
    else {

        
        if(in_array($accesskey, $admin_keys)){
            $is_admin = 1;
        } 
        elseif(in_array($accesskey, $coach_keys)){
            $is_admin = 2;
        } 
        else {
            $error_msg = "Invalid admin/coach key! Please check your access key.";
        }
        
        if(empty($error_msg)){
            $query = "INSERT INTO users (fullname, email, password, is_admin) VALUES('$fullname', '$email', '$password', '$is_admin')";
            if(mysqli_query($conn, $query)){
                $success_msg = "Account created successfully! Redirecting to login...";
                header("location: login.php");
            } else {
                $error_msg = "Error creating account: " . mysqli_error($conn);
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Css/output.css">
    <link rel="stylesheet" href="../Css/signup.css">
    <title>Sign Up</title>
</head>

<body class="container">
    <div class="form-container">
        <h1 class="logo">ARM</h1>
        <h1 class="signup-title">Sign up</h1>
        <?php if($error_msg): ?>
            <div style="color: red; text-align: center; margin-bottom: 1rem;"><?php echo $error_msg; ?></div>
        <?php endif; ?>
        <?php if($success_msg): ?>
            <div style="color: green; text-align: center; margin-bottom: 1rem;"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        <form action="signup.php" method="POST">
            <div class="input-group">
                <label for="fullname" class="input-label">Full Name</label>
                <input type="text" id="fullname" name="fullname" required class="input-field" placeholder="Juan Dela Cruz" value="<?php echo isset($fullname) ? $fullname : ''; ?>">
            </div>

            <div class="input-group">
                <label for="email" class="input-label">Email</label>

                <input type="email" id="email" name="email" required class="input-field" placeholder="Example@email.com" value="<?php echo isset($email) ? $email : ''; ?>">
            </div>

            <div class="input-group">
                <label for="accesskey" class="input-label">Access Key</label>
                <input type="text" id="accesskey" name="accesskey" required class="input-field" placeholder="Enter admin/coach key (Required)" value="<?php echo isset($accesskey) ? $accesskey : ''; ?>">
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
        </form>
    </div>
</body>
</html>