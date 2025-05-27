<?php
function check_login($conn) {
    if(isset($_SESSION['id'])){
        $id = $_SESSION['id'];
        $query = "SELECT * FROM users WHERE id = '$id' LIMIT 1";
        $result = mysqli_query($conn,$query);
        if($result && mysqli_num_rows($result) > 0){
            $user_data = mysqli_fetch_assoc($result);
            return $user_data;
        }
    }

    header("Location:login.php");
    die;
}

function check_admin() {
    if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1 && $_SESSION['is_admin'] !== 2) {
        echo "Access denied. Admin only.";
        die;
    }
}

?>

