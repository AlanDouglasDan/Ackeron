<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");

sleep(0.5);

$username = $_POST['username'];
$userLoggedIn = $_POST['userLoggedIn'];

$delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$username'");
echo "";
?>