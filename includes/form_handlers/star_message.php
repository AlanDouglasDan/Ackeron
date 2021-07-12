<?php 
require_once '../../config/config.php';
	
	if(isset($_GET['id']))
        $id = $_GET['id'];        
    
    if(isset($_GET['username']))
        $username = $_GET['username'];        

	if(isset($_POST['result'])) {
		if($_POST['result'] == 'true')
			$query = mysqli_query($con, "INSERT INTO starred_messages VALUES(NULL, '$username', '$id')");
	}

?>