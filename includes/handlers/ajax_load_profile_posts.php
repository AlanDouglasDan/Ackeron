<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");
require_once("../classes/Post.php");

$limit = 10; //Number of posts to be loaded per call

$posts = new Post($con, $_REQUEST['userLoggedIn']);
$posts->loadProfilePosts($_REQUEST, $limit);
?>