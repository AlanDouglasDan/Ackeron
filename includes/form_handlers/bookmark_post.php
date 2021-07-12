<?php 
require_once '../../config/config.php';
	
	if(isset($_GET['post_id']))
        $post_id = $_GET['post_id'];
        
    if(isset($_GET['username']))
        $username = $_GET['username'];

    $date = date("Y-m-d H:i:s");

	if(isset($_POST['result'])) {
		if($_POST['result'] == 'true'){
            $f = mysqli_query($con, "SELECT * FROM bookmarks WHERE username='$username' AND post_id='$post_id'");
            if(mysqli_num_rows($f) == 0)
                $query = mysqli_query($con, "INSERT INTO bookmarks VALUES('', '$username', '$post_id', '$date')");
        }
	}
?>