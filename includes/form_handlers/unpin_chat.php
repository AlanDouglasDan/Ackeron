<?php 
    if(isset($_COOKIE['user_login']))
        $userLoggedIn = $_COOKIE['user_login'];

    require_once '../../config/config.php';
	
	if(isset($_GET['chat']))
        $chat = $_GET['chat'];

	if(isset($_POST['result'])) {
		if($_POST['result'] == 'true'){            
            $query = mysqli_query($con, "DELETE FROM pinned_chats WHERE username='$userLoggedIn' AND user_pinned='$chat'");
        }
	}

?>