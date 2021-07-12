<?php 
    if(isset($_COOKIE['user_login']))
        $userLoggedIn = $_COOKIE['user_login'];

    require_once '../../config/config.php';
	
	if(isset($_GET['chat']))
        $chat = $_GET['chat'];

	if(isset($_POST['result'])) {
		if($_POST['result'] == 'true'){            
            if(strpos($chat, "ACK_GROUP..??.") === false){
                $query = mysqli_query($con, "SELECT * FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$chat') OR (user_to='$chat' AND user_from='$userLoggedIn')");                                
            }
            else{
                $query = mysqli_query($con, "SELECT * FROM messages WHERE user_to='$chat'");
            }
            while ($row = mysqli_fetch_array($query)) {
                $id = $row['id'];
                $black = mysqli_query($con, "SELECT * FROM blacklist WHERE username='$userLoggedIn' AND message='$id'");
                if(mysqli_num_rows($black) == 0){
                    $query3 = mysqli_query($con, "INSERT INTO blacklist VALUES(NULL, '$userLoggedIn', '', '$id')");
                }
            }
        }
	}

?>