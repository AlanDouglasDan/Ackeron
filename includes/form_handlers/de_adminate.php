<?php 
require_once '../../config/config.php';
	
	if(isset($_GET['name']))
        $participant = $_GET['name'];
    
    if(isset($_GET['group']))
        $group = $_GET['group'];

	if(isset($_POST['result'])) {
		if($_POST['result'] == 'true'){
            $query = mysqli_query($con, "SELECT admins FROM group_chats WHERE group_info='$group'");
            $row = mysqli_fetch_array($query);
            $admins = $row['admins'];
            $new = str_replace($participant . ",", "", $admins);
            $query2 = mysqli_query($con, "UPDATE group_chats SET admins='$new' WHERE group_info='$group'");
        }
	}
?>