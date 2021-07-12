<?php
    $usernam = $_REQUEST['username'];
    $_SESSION['username'] = $usernam;
    header("Location: ../../index.php");
?>