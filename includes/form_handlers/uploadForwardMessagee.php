<?php

require_once '../../config/config.php';
require_once("../classes/User.php");
require_once("../classes/Message.php");

$usernames = $_POST['usernames'];
$userLoggedIn = $_POST['userLoggedIn'];
$post_id = $_POST['id'];
// $message = $_POST['text'];

// $message_obj = new Message($con, $userLoggedIn);
// $date = date("Y-m-d H:i:s");

// $body = "post.php?id=$post_id";
// if($usernames != ""){
//     $usernames = substr($usernames, 0, -1);
//     $usernames = explode(",", $usernames);
//     foreach($usernames as $username){
//         $query = mysqli_query($con, "INSERT INTO messages VALUES('', '$username', '$userLoggedIn', '$body', '$date', 'no', 'no', 'no')");
//     }
// }

$query = mysqli_query($con, "SELECT body FROM messages WHERE id='$post_id'");
if(mysqli_num_rows($query)){
    $row = mysqli_fetch_array($query);
    $body = $row['body'];    
    $body = mysqli_real_escape_string($con, $body);
    $body_array = preg_split("/\s+/", $body);
    foreach($body_array as $key => $value) {
        if(strpos($value, "reply.php.??.id=") !== false) {
            $value = "";
            $body_array[$key] = $value;
        }
        if(strpos($value, "status.php.??.id=") !== false) {
            $value = "";
            $body_array[$key] = $value;
        }
    }
    $body = implode(" ", $body_array);
    $date = date("Y-m-d H:i:s");
    $date = date("Y-m-d H:i:s");
    if($usernames){
        $usernames = substr($usernames, 0, -1);
        $usernames = explode(",", $usernames);
        foreach($usernames as $key => $pes){
            if(is_numeric($pes))
                $pes = "ACK_GROUP..??.$pes";
            // if($key == 0)
            //     $nek = $pes;
            $queery = mysqli_query($con, "INSERT INTO messages VALUES('', '$pes', '$userLoggedIn', '$body', '$date', 'no', 'no', 'no')");
        }
        // header("Location: messages.php?u=" .$nek);
    }
}

?>