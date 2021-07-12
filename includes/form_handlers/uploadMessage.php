<?php

require_once '../../config/config.php';
require_once("../classes/User.php");
require_once("../classes/Message.php");

$username = $_POST['username'];
$userLoggedIn = $_POST['userLoggedIn'];
// $message = $_POST['text'];

$message_obj = new Message($con, $userLoggedIn);
$date = date("Y-m-d H:i:s");

// $body = mysqli_real_escape_string($con, $_POST['text']);
$body = sanitizeString($_POST['text']); //removes html tags 
if($body != "") 
    $query = mysqli_query($con, "INSERT INTO messages VALUES('', '$username', '$userLoggedIn', '$body', '$date', 'no', 'no', 'no')");
// $message_obj->sendMessage($username, $body, $date);

$array = array();
$array['page'] = 1;
$array['profileUsername'] = $username;
echo $message_obj->getMessages($array, 30);

?>