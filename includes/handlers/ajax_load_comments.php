<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");
require_once("../classes/Post.php");

$limit = 15; //Number of messages to be loaded per call

$message_obj = new Post($con, $_REQUEST['userLoggedIn']);
$message_obj->getComments($_REQUEST, $limit);
?>