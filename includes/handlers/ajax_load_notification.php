<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");
require_once("../classes/Notification.php");

$limit = 20; //Number of posts to be loaded per call

$notification = new Notification($con, $_REQUEST['userLoggedIn']);
$notification->getNotification($_REQUEST, $limit);
?>