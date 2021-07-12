<?php

    setcookie("user_login", $username, time() - (30*24*60*60));

    require_once("../../includes/header.php");

    $time = date("Y-m-d H:i:s");
    $clock_out_query = mysqli_query($con, "UPDATE logins SET logout='$time' WHERE username='$userLoggedIn' AND logout='0000-00-00 00:00:00'");

    session_start();
    session_destroy();
    header("Location: ../../register.php")
?>