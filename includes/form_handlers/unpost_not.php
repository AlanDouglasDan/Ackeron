<?php 
require_once '../../config/config.php';
	
	if(isset($_GET['person']))
        $person = $_GET['person'];
        
    if(isset($_GET['username']))
        $username = $_GET['username'];

    $date = date("Y-m-d H:i:s");

	if(isset($_POST['result'])) {
		if($_POST['result'] == 'true'){
            $query = mysqli_query($con, "DELETE FROM post_notifications WHERE username='$username' AND person='$person'");
        }
	}
?>