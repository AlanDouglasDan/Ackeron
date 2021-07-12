<?php
require_once("config/config.php");
require_once("includes/classes/User.php");
require_once("includes/classes/Notification.php");
require_once("includes/classes/Post.php");

if(isset($_COOKIE['user_login'])){
    $userLoggedIn = $_COOKIE['user_login'];
}

$id = $_POST['fid'];
$update_query = mysqli_query($con, "UPDATE posts SET deleted='yes' WHERE id='$id'");
$post = new Post($con, $userLoggedIn);
$post->submitPost($_POST['fileb'], $_POST['filen'], '', $_POST['filel'], $_POST['filet'], '', "no");

?>