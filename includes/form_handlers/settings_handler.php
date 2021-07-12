<?php  
if(isset($_POST['update_details'])) {

	$first_name = strip_tags($_POST['first_name']);
	$last_name = strip_tags($_POST['last_name']);
	$email = strip_tags($_POST['email']);
	$username = strip_tags($_POST['username']);

	$email_check = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");
	$row = mysqli_fetch_array($email_check);
	$matched_user = $row['username'];

	if($matched_user == "" || $matched_user == $userLoggedIn) {
		$message = "Details updated!<br><br>";
		$qe = mysqli_query($con, "SELECT * FROM users WHERE username='$username' AND username!='$userLoggedIn'");
		if(mysqli_num_rows($qe) == 0){
			$query = mysqli_query($con, "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email', username='$username' WHERE username='$userLoggedIn'");
			setcookie("user_login", $username, time() + (30*24*60*60));
			$_SESSION['username'] = $username;
			// $a = mysqli_query($con, "UPDATE friend_suggestions SET user_to='$username' WHERE user_to='$userLoggedIn'");
			// $b = mysqli_query($con, "UPDATE friend_suggestions SET user_from='$username' WHERE user_from='$userLoggedIn'");
			// $a = mysqli_query($con, "UPDATE friend_requests SET user_to='$username' WHERE user_to='$userLoggedIn'");
			// $b = mysqli_query($con, "UPDATE friend_requests SET user_from='$username' WHERE user_from='$userLoggedIn'");
			// $b = mysqli_query($con, "UPDATE backgrounds SET username='$username' WHERE username='$userLoggedIn'");
			// $b = mysqli_query($con, "UPDATE blacklist SET username='$username' WHERE username='$userLoggedIn'");
			// $b = mysqli_query($con, "UPDATE bookmarks SET username='$username' WHERE username='$userLoggedIn'");
			// $b = mysqli_query($con, "UPDATE comments SET posted_by='$username' WHERE posted_by='$userLoggedIn'");
			// $b = mysqli_query($con, "UPDATE comments SET posted_to='$username' WHERE posted_to='$userLoggedIn'");
			// $b = mysqli_query($con, "UPDATE comment_comments SET posted_by='$username' WHERE posted_by='$userLoggedIn'");
			// $b = mysqli_query($con, "UPDATE comment_comments SET posted_to='$username' WHERE posted_to='$userLoggedIn'");
			// $b = mysqli_query($con, "UPDATE comment_likes SET username='$username' WHERE username='$userLoggedIn'");
			// $b = mysqli_query($con, "UPDATE group_chats SET creator='$username' WHERE username='$userLoggedIn'");
			// $b = mysqli_query($con, "UPDATE group_chats SET creator='$username' WHERE username='$userLoggedIn'");
			// $b = mysqli_query($con, "UPDATE group_chats SET creator='$username' WHERE username='$userLoggedIn'");
		}
		else{
			$query = mysqli_query($con, "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email' WHERE username='$userLoggedIn'");
		}
	}
	else
		$message = "That email is already in use!<br><br>";

	// header("Location: settings.php");
}
else 
	$message = "";

//password update
if(isset($_POST['update_password'])) {

	$old_password = strip_tags($_POST['old_password']);
	$new_password_1 = strip_tags($_POST['new_password_1']);
	$new_password_2 = strip_tags($_POST['new_password_2']);

	$password_query = mysqli_query($con, "SELECT password FROM users WHERE username='$userLoggedIn'");
	$row = mysqli_fetch_array($password_query);
	$db_password = $row['password'];

	if(password_verify($old_password, $db_password)){

		if($new_password_1 == $new_password_2) {


			if(strlen($new_password_1) <= 6) {
				$password_message = "Sorry, your password must be greater than 7 characters<br><br>";
			}	
			else {
				$new_password_hash = password_hash($new_password_1, PASSWORD_DEFAULT);
				$password_query = mysqli_query($con, "UPDATE users SET password='$new_password_hash' WHERE username='$userLoggedIn'");
				$password_message = "Password has been changed!<br><br>";
			}


		}
		else {
			$password_message = "Your two new passwords need to match!<br><br>";
		}

	}
	else {
			$password_message = "The old password is incorrect! <br><br>";
	}

}
else {
	$password_message = "";
}

//close account?
if(isset($_POST['close_account'])) {
	header("Location: close_account.php");
}


?>