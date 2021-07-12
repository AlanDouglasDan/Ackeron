<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");
require_once("../classes/Message.php");

$limit = 30; //Number of messages to be loaded per call

$message_obj = new Message($con, $_REQUEST['userLoggedIn']);
$message_obj->getMessages($_REQUEST, $limit);
?>