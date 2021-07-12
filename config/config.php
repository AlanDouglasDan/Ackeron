<?php
    ob_start();
    session_start();

    $timezone = date_default_timezone_set("Africa/Lagos");

    $con = mysqli_connect("localhost", "Alan", "alan", "ackeron");

    if(mysqli_connect_errno()){
        echo "failed to connect".mysqli_connect_errno();
    }

    function sanitizeString($var)
    {
        global $con;
        $var = strip_tags($var);
        $var = htmlentities($var);
        $var = trim($var);
        if (get_magic_quotes_gpc())
            $var = stripslashes($var);
        return $con->real_escape_string($var);
    }
?>