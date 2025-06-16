<?php
session_start();
include ("db.php");
include ("func.php");

$error_msg = "";

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $access_key = isset($_POST['accesskey']) ? $_POST['accesskey'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if(!empty($access_key) && !empty($password)){
        // Query to find user with the provided access key
        $query = "SELECT * FROM users WHERE access_key = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $access_key);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result && $result->num_rows > 0){
            $user_data = $result->fetch_assoc();
            
            // Verify the password (plain text comparison)
            if($password === $user_data['password']){
                // Set session variables
                $_SESSION['id'] = $user_data['id'];
                $_SESSION['fullname'] = $user_data['fullname'];
                $_SESSION['is_admin'] = $user_data['is_admin'];
                
                header("Location: teams.php");
                exit();
            } else {
                $error_msg = "Invalid access key or password!";
            }
        } else {
            $error_msg = "Access key not registered!";
        }
    } else {
        
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARM - Log In</title>
    <link rel ="stylesheet" href="../Css/login.css">
</head>

<body class="container">
    <div class="form-container">
        <h1 class="logo">ARM</h1>
        <h2 class="login-title">Log in</h2>
        <?php if($error_msg): ?>
            <div style="color: red; text-align: center; margin-bottom: 1rem;"><?php echo $error_msg; ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST" id="form">
            <div class="input-group">
                <label for="accesskey" class="input-label">Access Key</label>
                <input type="text" id="accesskey" name="accesskey" required class="input-field" placeholder="Enter access key">
                <div class="error"></div>
            </div>
            
            <div class="input-group">
                <label for="password" class="input-label">Password</label>
                <input type="password" id="password" name="password" required class="input-field">
                <div class="error"></div>
                 <input type="checkbox" id="showPassword"> <label for="showPassword">Show Password</label>
            </div>
            
            <button type="submit" class="submit-button">Log In</button>
            
            <p class="form-footer">
                Don't have an account? <a href="signup.php">Sign Up</a>
            </p>
        </form>
    </div>
   
</body>
 <script>
    document.getElementById("showPassword").addEventListener("change", function () {
        const passwordInput = document.getElementById("password");
        passwordInput.type = this.checked ? "text" : "password";
    });
</script>
</html>