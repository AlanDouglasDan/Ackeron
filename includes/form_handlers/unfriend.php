<?php 
require_once '../../config/config.php';
require_once '../../includes/classes/User.php';
        
    if(isset($_COOKIE['user_login'])){
        $userLoggedIn = $_COOKIE['user_login'];
    }

    if(isset($_GET['name']))
        $username = $_GET['name'];

	if(isset($_POST['result'])) {
		if($_POST['result'] == 'true'){
            $user = new User($con, $userLoggedIn);
            $user->removeFriend($username);
        }
	}
?>