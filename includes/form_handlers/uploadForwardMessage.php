<?php

require_once '../../config/config.php';
require_once("../classes/User.php");
require_once("../classes/Message.php");

$usernames = $_POST['usernames'];
$userLoggedIn = $_POST['userLoggedIn'];
$post_id = $_POST['id'];
// $message = $_POST['text'];

$message_obj = new Message($con, $userLoggedIn);
$date = date("Y-m-d H:i:s");

// $body = sanitizeString($_POST['text']); //removes html tags 
$body = "post.php?id=$post_id";
if($usernames != ""){
    $usernames = substr($usernames, 0, -1);
    $usernames = explode(",", $usernames);
    foreach($usernames as $username){
        $query = mysqli_query($con, "INSERT INTO messages VALUES('', '$username', '$userLoggedIn', '$body', '$date', 'no', 'no', 'no')");
    }
}

?>