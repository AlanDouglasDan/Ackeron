<?php

require_once("includes/header.php");

if(isset($_GET['q'])) {
	$query = $_GET['q'];
}
else {
	$query = "";
}

if(isset($_GET['type'])) {
	$type = $_GET['type'];
}
else {
	$type = "name";
}
?>

<div class="main_column column" id="main_column">

	<?php 
	if($query == "")
		echo "You must enter something in the search box.";
	else {
		//If query contains an underscore, assume user is searching for usernames
		if($type == "username") 
			$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' ORDER BY first_name");
		//If there are two words, assume they are first and last names respectively
		else {
			$names = explode(" ", $query);

			if(count($names) == 3)
				$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[2]%') AND user_closed='no' ORDER BY first_name");
			//If query has one word only, search first names or last names 
			else if(count($names) == 2)
				$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed='no' ORDER BY first_name");
			else 
				$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed='no' ORDER BY first_name");
		}

		//Check if results were found 
		if(mysqli_num_rows($usersReturnedQuery) == 0)
			echo "We can't find anyone with a " . $type . " : " .$query;
		else 
			echo mysqli_num_rows($usersReturnedQuery) . " results found: <br> <br>";

		echo "<p id='grey'>Try searching for:</p>";
		echo "<a href='search.php?q=" . $query ."&type=name'>Names</a>, <a href='search.php?q=" . $query ."&type=username'>Usernames</a><br><br>";

		$counter = 0;
		while($row = mysqli_fetch_array($usersReturnedQuery)) {
			$counter++;
			$user_obj = new User($con, $user['username']);

			$button = "";
			$mutual_friends = "";
			$use = $row['username'];

			if($user['username'] != $row['username']) {

				//Generate button depending on friendship status 
				if($user_obj->isFriend($row['username']))
					$button = "<input onclick='sound.play()' type='button' name='" . $row['username'] . "' class='danger' value='Remove Friend'>";
				else if($user_obj->didReceiveRequest($row['username']))
					$button = "<input type='button' name='" . $row['username'] . "' class='warning acceptreq$use' value='Accept request'>";
				else if($user_obj->didSendRequest($row['username']))
					$button = "<input onclick='sound.play()' type='button' class='default' value='Request Sent'>";
				else 
					$button = "<input onclick='sound.play()' type='button' name='" . $row['username'] . "' class='success' value='Add Friend'>";

				$mutual_friends = $user_obj->getMutualFriends($row['username']) . " friends in common";

				//Button forms
				// if(isset($_POST[$row['username']])) {

				// 	if($user_obj->isFriend($row['username'])) {
				// 		$user_obj->removeFriend($row['username']);
				// 		header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
				// 	}
				// 	// else if($user_obj->didReceiveRequest($row['username'])) {
				// 	// 	header("Location: requests.php");
				// 	// }
				// 	else if($user_obj->didSendRequest($row['username'])) {
				// 	}
				// 	else {
				// 		$user_obj->sendRequest($row['username']);
				// 		header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
				// 	}

				// }
			}
			?>

			<div class='request<?php echo $use; ?>'>
				<div class='search_result'>
					<div class='searchPageFriendButtons'>
						<?php
							if($user_obj->isFriend($row['username'])){
								?>
								<span class='btn<?php echo $use; ?>'>
									<input onclick='unfriend("<?php echo $use; ?>", "<?php echo $userLoggedIn; ?>")' type='button' name='<?php echo $row['username']; ?>' class='danger' value='Remove Friend'>
								</span>
							<?php
							}
							else if($user_obj->didReceiveRequest($row['username'])){
								?> 
								<span class='btn<?php echo $use; ?>'>
									<input type='button' onclick="acceptRequest('<?php echo $use; ?>', '<?php echo $userLoggedIn; ?>', 'yes')" name='<?php echo $row['username']; ?>' class='warning' value='Accept request'>
								</span>
							<?php
							}
							else if($user_obj->didSendRequest($row['username'])){
								?>
								<input onclick='sound.play()' type='button' class='default' value='Request Sent'>
							<?php
							}
							else{
								?>
								<span class='btn<?php echo $use; ?>'>
									<input onclick='addFriend("<?php echo $use; ?>", "<?php echo $userLoggedIn; ?>")' type='button' name='<?php echo $row['username']; ?>' class='success' value='Add Friend'>
								</span>
							<?php
							}
							?>
					</div>

					<div class='result_profile_pic'>
						<img src='<?php echo $row['profile_pic']; ?>' style='height: 100px;'>
					</div>

					<a href='<?php echo $row['username']; ?>'><p id='grey'> <?php echo $row['username']; ?></p>
                        
						<?php echo $mutual_friends; ?></a><br>

				</div>
				<hr id='search_hr'>
			</div>

			<?php

		} //End while
	}

	?>

</div>

<script>
	var sound = new Audio();
	sound.src = "button_click.mp3";
</script>