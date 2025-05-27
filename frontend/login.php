<?php
session_start();
include ("db.php");
include ("func.php");

$error_msg = "";

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $admin_key = isset($_POST['accesskey']) ? $_POST['accesskey'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if(!empty($admin_key) && !empty($password)){
        $query = "SELECT * FROM users WHERE is_admin >= 1 LIMIT 1";
        $result = mysqli_query($conn, $query);
        
        if($result && mysqli_num_rows($result) > 0){
            $user_data = mysqli_fetch_assoc($result);
            
            $valid_admin_keys = array("ARM2023admin", "RapRap2023", "Ronz2023", "Marvs2023","Fein");
            $valid_coach_keys = array("Respect", "MG","DNA");

            if(in_array($admin_key, $valid_admin_keys) && $user_data['password'] === $password){
                $_SESSION['id'] = $user_data['id'];
                $_SESSION['fullname'] = $user_data['fullname'];
                $_SESSION['is_admin'] = 1;
                
                header("Location: teams.php");
                die;
            } 
            else if(in_array($admin_key, $valid_coach_keys) && $user_data['password'] === $password){
                $_SESSION['id'] = $user_data['id'];
                $_SESSION['fullname'] = $user_data['fullname'];
                $_SESSION['is_admin'] = 2;
                
                header("Location: teams.php");
                die;
            }
            else {
                $error_msg = "Invalid access key or password!";
            }
        } else {
            $error_msg = "No user account found!";
        }
    } else {
        $error_msg = "Please enter both access key and password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARM - Log In</title>
    <link rel="stylesheet" href="../Css/login.css">
</head>

<body class="container">
    <div class="form-container">
        <h1 class="logo">ARM</h1>
        <h2 class="login-title">Log in</h2>
        <?php if($error_msg): ?>
            <div style="color: red; text-align: center; margin-bottom: 1rem;"><?php echo $error_msg; ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="input-group">
                <label for="accesskey" class="input-label">Admin Key</label>
                <input type="text" id="accesskey" name="accesskey" required class="input-field" placeholder="Enter admin key">
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