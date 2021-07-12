<?php 
require_once '../../config/config.php';
	
	if(isset($_GET['name']))
        $participant = $_GET['name'];
    
    if(isset($_GET['group']))
        $group = $_GET['group'];

	if(isset($_POST['result'])) {
		if($_POST['result'] == 'true'){
            $query = mysqli_query($con, "SELECT users FROM group_chats WHERE group_info='$group'");
            $row = mysqli_fetch_array($query);
            $admins = $row['users'];
            $new = str_replace($participant . ",", "", $admins);
            $date_time_now = date("Y-m-d H:i:s");
            $query2 = mysqli_query($con, "UPDATE group_chats SET users='$new' WHERE group_info='$group'");            
            $black = mysqli_query($con, "SELECT * FROM blacklist WHERE username='$participant' AND chat='$group'");
            if(mysqli_num_rows($black) == 0)
                $query3 = mysqli_query($con, "INSERT INTO blacklist VALUES(NULL, '$participant', '$group', '')");                
            $query4 = mysqli_query($con, "INSERT INTO messages VALUES(NULL, '$group', '$participant', 'ACK_GR_MESSAGE..??..', '$date_time_now', 'no', 'no', 'no')");
            header("Location: messages.php");
        }
	}
?>