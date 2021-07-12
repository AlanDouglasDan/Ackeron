<?php 
require_once '../../config/config.php';
	
	if(isset($_GET['post_id']))
        $post_id = $_GET['post_id'];
        
    if(isset($_GET['username']))
        $username = $_GET['username'];

	if(isset($_POST['result'])) {
		if($_POST['result'] == 'true')
			$query = mysqli_query($con, "INSERT INTO spam VALUES('', '$username', '$post_id')");
	}
?>