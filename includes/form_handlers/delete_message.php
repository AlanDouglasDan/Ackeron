<?php 
	if(isset($_COOKIE['user_login']))
		$userLoggedIn = $_COOKIE['user_login'];

	require_once '../../config/config.php';
	
	if(isset($_GET['id']))
		$id = $_GET['id'];

	$date_time_now = date("Y-m-d H:i:s");

	if(isset($_POST['result'])) {
		if($_POST['result'] == 'true'){
			$check = mysqli_query($con, "SELECT date FROM messages WHERE id='$id'");
			$row = mysqli_fetch_array($check);
			$date = $row['date'];
			$start_date = new DateTime($date); //Time of post			
			$end_date = new DateTime($date_time_now); //Current time
			$interval = $start_date->diff($end_date); //Difference between dates 
			if($interval->h < 1) {
				$query = mysqli_query($con, "UPDATE messages SET deleted='yes' WHERE id='$id'");				
			}
			else{
				$black = mysqli_query($con, "SELECT * FROM blacklist WHERE username='$userLoggedIn' AND message='$id'");
                if(mysqli_num_rows($black) == 0){
                    $query3 = mysqli_query($con, "INSERT INTO blacklist VALUES(NULL, '$userLoggedIn', '', '$id')");
                }
			}
		}
	}

?>