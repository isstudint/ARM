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
        
        if(empty($error_msg)) {
            // Check if access key has already been used
            $stmt = $conn->prepare("SELECT id FROM users WHERE access_key = ?");
            $stmt->bind_param("s", $accesskey);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error_msg = "This access key has already been used for registration.";
            } else {
                // Check if email is already registered
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $error_msg = "Email already registered.";
                } else {
                    // All checks passed, create the account
                    $query = "INSERT INTO users (fullname, email, password, is_admin, access_key) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("sssis", $fullname, $email, $password, $is_admin, $accesskey);

                    if ($stmt->execute()) {
                        $success_msg = "Account created successfully!";
                        header("Location: login.php");
                        exit;
                    } else {
                        $error_msg = "Error: " . $stmt->error;
                    }
                }
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
        <form action="signup.php" method="POST" id = "form" >
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
                 <div class = "error"></div>
            </div>
            <div class="input-group">
                <label for="password" class="input-label">Password</label>
               <input type="password" id="password" name="password" required class="input-field" value="<?php echo isset($password) ? htmlspecialchars($password) : ''; ?>">
                 <div class = "error"></div><br>
                 <input type="checkbox" id="showPassword"> <label for="showPassword">Show Password</label>

            </div>

            <button type="submit" class="submit-button">Sign Up</button>

            <p class="form-footer">
                Already have an account? 
                <a href="login.php">Log in</a>
            </p>
        </form>
    </div>

    <script>
    document.getElementById("showPassword").addEventListener("change", function () {
        const passwordInput = document.getElementById("password");
        passwordInput.type = this.checked ? "text" : "password";
    });
</script>
</body>
</html>