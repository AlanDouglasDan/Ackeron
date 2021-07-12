<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");

sleep(0.5);

$username = $_POST['username'];
$userLoggedIn = $_POST['userLoggedIn'];

$user = new User($con, $userLoggedIn);
$user->sendRequest($username);
$deletes_query = mysqli_query($con, "DELETE FROM friend_suggestions WHERE user_to='$userLoggedIn' AND user_from='$username'");

echo "";
?>