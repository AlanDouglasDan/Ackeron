<?php
    require_once("includes/header.php");

    // setcookie("user_login", $username, time() - (30*24*60*60));
    
    $time = date("Y-m-d H:i:s");
    $clock_out_query = mysqli_query($con, "UPDATE logins SET logout='$time' WHERE username='$userLoggedIn' AND logout='0000-00-00 00:00:00'");
    $updatez = mysqli_query($con, "UPDATE typing SET typing='no' WHERE username='$userLoggedIn'");

    session_start();
    session_destroy();
    header("Location: register.php")
?>