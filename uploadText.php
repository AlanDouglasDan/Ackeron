<?php
require_once("config/config.php");
require_once("includes/classes/User.php");
require_once("includes/classes/Notification.php");
require_once("includes/classes/Post.php");

if(isset($_COOKIE['user_login'])){
    $userLoggedIn = $_COOKIE['user_login'];
}

$post = new Post($con, $userLoggedIn);
$post->submitPost($_POST['fileb'], $_POST['filen'], '', $_POST['filel'], $_POST['filet'], '', "no");

?>