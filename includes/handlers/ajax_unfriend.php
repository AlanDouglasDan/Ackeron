<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");

sleep(0.5);

$username = $_POST['username'];
$userLoggedIn = $_POST['userLoggedIn'];

$user = new User($con, $userLoggedIn);
$user->removeFriend($username);

// echo "";
?>
<input onclick='addFriend("<?php echo $username; ?>", "<?php echo $userLoggedIn; ?>")' type='button' name='<?php echo $username; ?>' class='success' value='Add Friend'>
