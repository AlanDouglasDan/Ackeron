<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");

sleep(0.5);

$username = $_POST['username'];
$userLoggedIn = $_POST['userLoggedIn'];

$deletes_query = mysqli_query($con, "UPDATE friend_suggestions SET ignored='yes' WHERE user_to='$userLoggedIn' AND user_from='$username'");

echo "";
?>