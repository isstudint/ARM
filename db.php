<?php 
$conn = mysqli_connect("localhost","root","","arm");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

