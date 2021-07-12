<?php 
require_once("includes/header.php");
require_once("includes/form_handlers/settings_handler.php");

// echo $_SERVER['REMOTE_ADDR'] ."<br>";
// echo $_SERVER['HTTP_USER_AGENT'];
// if(isset($_COOKIE['user_login'])){
// 	$a = $_COOKIE['user_login'];
// 	echo $a;
// }
// else{
// 	echo "Nop";
// }
?>

<style>
    .settings_input{
        height: 25px;
        outline: none;
    }
</style>
<?php 
	// echo $_COOKIE['user_login'] . "<br>";
	// echo $userLoggedIn;
?>
<div class="main_column column" id="setings" style="height: 93vh;">
	<div class="row">
		<div class="col-sm-8">
			<h4>Account Settings</h4>
			<a href="upload.php">
			<?php
			echo "<img src='" . $user['profile_pic'] ."' class='small_profile_pic'>";
			?>
			<br>
			Upload new profile picture</a> <br><br><br>

			Modify the values and click 'Update Details'

			<?php
			$user_data_query = mysqli_query($con, "SELECT first_name, last_name, email, username FROM users WHERE username='$userLoggedIn'");
			$row = mysqli_fetch_array($user_data_query);

			$first_name = $row['first_name'];
			$last_name = $row['last_name'];
			$email = $row['email'];
			$username = $row['username'];
			?>

			<form action="settings.php" method="POST">
				First Name: <input type="text" name="first_name" value="<?php echo $first_name; ?>" class="settings_input"><br>
				Last Name: <input type="text" name="last_name" value="<?php echo $last_name; ?>" class="settings_input"><br>
				Email: <input type="email" name="email" value="<?php echo $email; ?>" class="settings_input"><br>
				Username: <input type="text" name="username" value="<?php echo $username; ?>" class="settings_input"><br>

				<?php echo $message; ?>

				<input onclick="sound.play()" type="submit" name="update_details" value="Update Details" class="info settings_submit"><br>
			</form>

			<h4>Change Password</h4>
			<form action="settings.php" method="POST">
				Old Password: <input type="password" name="old_password" class="settings_input"><br>
				New Password: <input type="password" name="new_password_1" class="settings_input"><br>
				New Password Again: <input type="password" name="new_password_2" class="settings_input"><br>

				<?php echo $password_message; ?>

				<input onclick="sound.play()" type="submit" name="update_password" value="Update Password" class="info settings_submit"><br>
			</form>

			<h4>Close Account</h4>
			<form action="settings.php" method="POST">
				<input onclick="sound.play()" type="submit" name="close_account" id="close_account" value="Close Account" class="danger settings_submit">
			</form><br><br>
			<a onclick="sound.play()" id="starred_link" class="btn btn-success" role="button">Starred Messages</a>

			<div class='to_hide' style="margin-top:25;">
				<input type="checkbox" class="checkbox" id="chk">
				<label onclick="sound.play()" class="label" for="chk">
					<i class="fa fa-moon-o"></i>
					<i class="fa fa-sun-o"></i>
					<div class="ball"></div>
				</label>
			</div>
		</div>
		<center class="col-sm-offset-1 col-sm-3 margin to_hide">
			<a onclick="sound.play()" href="about.php" role="button" class="btn btn-info">About Us</a><br><br>
			<a onclick="bookmarks()" id="bookmark_btn" class="btn btn-primary" role="button">Saved Posts</a><br><br>
		</center>
	</div>
</div>

<div id="starred_msgs">
	<div class="card-header fix">
		<span id="bring_back" class="fa fa-remove"></span>
		<span class="center-block">Starred Messages</span>
	</div>
	<?php
	$message_obj = new Message($con, $userLoggedIn);
	$star_ids= array();
	$star_check = mysqli_query($con, "SELECT msg_id FROM starred_messages WHERE username='$userLoggedIn'");
	if(mysqli_num_rows($star_check)){
		$counter = 0;
		while($sd = mysqli_fetch_array($star_check)){
			$isd = $sd['msg_id'];
			array_push($star_ids, $isd);
		}
		rsort($star_ids, SORT_NUMERIC);
		foreach ($star_ids as $f) {
			$counter++;
			echo $message_obj->getSingleMessage($f, $counter, "show") . "<hr>";
		}
	}
	?>
</div>

<script>
	$(document).ready(function(){
		$('#starred_link').on('click',function(){
			document.getElementById("setings").style.display="none";
			document.getElementById("starred_msgs").style.display="block";
		});
		$('#bring_back').on('click',function(){
			document.getElementById("setings").style.display="block";
			document.getElementById("starred_msgs").style.display="none";
		});
	});
	var sound = new Audio();
	sound.src = "button_clicked.mp3";

	chk.addEventListener('change', () => {
		darkMode = localStorage.getItem('darkMode');
		
		if(darkMode !== 'enabled'){
			enableDarkMode();
		}
		else{
			disableDarkMode();
		}
	});
	// console.log(darkMode);
</script>