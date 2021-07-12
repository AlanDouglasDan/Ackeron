<?php
// require_once("includes/header.php"); 
require_once("config/config.php"); 
require_once("includes/classes/User.php"); 
$userLoggedIn = $_SESSION['username'];

// if(isset($_POST['add_friend'])) {
// 	$user = new User($con, $userLoggedIn);
// 	$name = $_POST['username'];
// 	$user->sendRequest($name);
// 	$deletes_query = mysqli_query($con, "DELETE FROM friend_suggestions WHERE user_to='$userLoggedIn' AND user_from='$name'");
// }

// if(isset($_POST['remove_friend'])){
// 	$name = $_POST['username'];
// 	$deletes_query = mysqli_query($con, "UPDATE friend_suggestions SET ignored='yes' WHERE user_to='$userLoggedIn' AND user_from='$name'");
// }

?>

<script>
	var sound = new Audio();
	sound.src = "button_click.mp3";
</script>

<div class="main_column column center-block" id="main_column" style="padding: 0;">

	<h4 class="special_name center-block">Friend Requests</h4>

	<?php  

	$query = mysqli_query($con, "SELECT * FROM friend_requests WHERE user_to='$userLoggedIn'");
	if(mysqli_num_rows($query) == 0)
		echo "You have no friend requests at this time!";
	else{
		while($row = mysqli_fetch_array($query)){
			$user_from = $row['user_from'];
			$user_from_obj = new User($con, $user_from);
			$user_from_friend_array = $user_from_obj->getFriendArray();

			// if(isset($_POST['accept_request' . $user_from ])) {
			// 	$add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array, '$user_from,') WHERE username='$userLoggedIn'");
			// 	$add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array, '$userLoggedIn,') WHERE username='$user_from'");

			// 	$delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
			// 	$notification = new Notification($con, $userLoggedIn);
			// 	$notification->insertNotification("0", $user_from, "friend_request");
				
			// 	echo "You are now friends!";
			// 	header("Location: requests.php");
			// }

			// if(isset($_POST['ignore_request' . $user_from ])) {
			// 	$delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
			// 	echo "Request ignored!";
			// 	header("Location: requests.php");
			// }

			// echo "<img class='friend_img' src=" .$user_from_obj->getProfilePic() .">";
			// echo "<div class='pull-left wid'><a href=". $user_from ."><p class='bot marginless'> ". $user_from_obj->getFirstAndLastName()." sent you a friend request!</p></a></div>";

			echo "<div class='request$user_from'><div class='row'><div class='col-sm-6 req_shmuck'><img class='friend_img' src=" .$user_from_obj->getProfilePic() .">";
			echo "<a href=". $user_from ." style='text-align: left; word-break: break-work;'>". $user_from_obj->getFirstAndLastName()." sent you a friend request!</a></div>";

			?>			
				<form action="requests.php" method="POST" class='col-sm-6 req_shmuck'>
					<div class='col-xs-6' onclick="sound.play()"><input type="button" onclick="acceptRequest('<?php echo $user_from; ?>', '<?php echo $userLoggedIn; ?>', 'no')"class="accept_button" value="Accept"></div>
					<div class='col-xs-6' onclick="sound.play()"><input type="button" onclick="ignoreRequest('<?php echo $user_from; ?>', '<?php echo $userLoggedIn; ?>')"class="ignore_button" value="Ignore"></div>
				</form>
			</div>
			<hr class="marginless">
			</div>
			<?php
		}
	}

	?>

	<br><br><h4 class="special_name">Friend Suggestions</h4>

	<?php
		$Query = mysqli_query($con, "SELECT * FROM users WHERE username = '$userLoggedIn' AND user_closed='no'");
		$row = mysqli_fetch_array($Query);
		$user_obj = new User($con, $userLoggedIn);
		$query = mysqli_query($con, "SELECT friend_array FROM users WHERE username='$userLoggedIn'");
		$row = mysqli_fetch_array($query);
		$friends = $row['friend_array'];
		if(strlen($friends) <= 2)
			echo "You currently have no friend suggestions";
		else{
			$friends = substr($friends, 1, -1);
			$friends = explode(',', $friends);
			$list = "";
			foreach ($friends as $friend) {
				$friend_obj = new User($con, $friend);
				$other_friends = $friend_obj->getFriendArray();
				$other_friends = substr($other_friends, 1, -1);
				$other_friends = explode(',', $other_friends);
				foreach($other_friends as $other_friend){
					if(!$user_obj->isFriend($other_friend))
						if($userLoggedIn != $other_friend)
							$list .= $other_friend. ",";
				}
			}
			$list = substr($list, 0, -1);
			$list = explode(',', $list);
			$elim = "";
			$list = array_unique($list);
			if($list){
				foreach ($list as $person) {
					$check_friend_requests = mysqli_query($con, "SELECT * FROM friend_requests WHERE user_to = '$userLoggedIn' OR user_from ='$userLoggedIn'");
					if(mysqli_num_rows($check_friend_requests)){
						while($slot = mysqli_fetch_array($check_friend_requests)){
							$elim .= $slot['user_from']. ",";
							$elim .= $slot['user_to']. ",";
						}
					}
				}
				$elim = substr($elim, 0, -1);
				$elim = explode(',', $elim);
				$elim = array_unique($elim);
				
				if($elim)
					$total = array_diff($list, $elim);
				// echo count($total);
				if(count($total) < 1)
					echo "You currently have no friend suggestions";
				else{					
					$suggestion_query = mysqli_query($con, "SELECT user_from FROM friend_suggestions WHERE user_to='$userLoggedIn' AND ignored='no'");
					if(mysqli_num_rows($suggestion_query)){
						while ($person = mysqli_fetch_array($suggestion_query)) {
							$suggestion = $person['user_from'];
							if($user_obj->isFriend($suggestion))
								$b_query = mysqli_query($con, "DELETE FROM friend_suggestions WHERE user_to='$userLoggedIn' AND user_from='$suggestion'");
						}
					}
					foreach ($elim as $key) {
						$a_query = mysqli_query($con, "DELETE FROM friend_suggestions WHERE user_to='$userLoggedIn' AND user_from='$key'");
					}
					foreach($total as $insert){
						if($insert){
							$another_query = mysqli_query($con, "SELECT * FROM friend_suggestions WHERE user_to='$userLoggedIn' AND user_from='$insert'");
							if(mysqli_num_rows($another_query) == 0)
								$insert_query = mysqli_query($con, "INSERT INTO friend_suggestions VALUES('', '$userLoggedIn', '$insert', 'no')");							
						}
					}
					$suggestion_query = mysqli_query($con, "SELECT user_from FROM friend_suggestions WHERE user_to='$userLoggedIn' AND ignored='no'");
					if(mysqli_num_rows($suggestion_query)){
						while ($person = mysqli_fetch_array($suggestion_query)) {
							$suggestion = $person['user_from'];							
							$suggested_obj = new User($con, $suggestion);

							echo "<div class='suggestion$suggestion'><div class='row'><div class='col-sm-6 req_shmuck'><img class='friend_img' src=" .$suggested_obj->getProfilePic() .">";
							echo "<a href=". $suggestion ." style='text-align: left; word-break: break-work;'>Add ". $suggested_obj->getFirstAndLastName()." as a friend?</a></div>";
							?>
							<form action="requests.php" method="POST" class='col-sm-6 req_shmuck'>
								<input type="hidden" value="<?php echo $suggestion; ?>" name="username">
								<div class='col-xs-6' onclick="sound.play()"><input onclick="sendRequest('<?php echo $suggestion; ?>', '<?php echo $userLoggedIn; ?>')" type="button"  class="accept_button" value="Add Friend"></div>
								<div class='col-xs-6' onclick="sound.play()"><input onclick="declineRequest('<?php echo $suggestion; ?>', '<?php echo $userLoggedIn; ?>')" type="button" class="ignore_button" value="Ignore"></div>
							</form>
						</div>
						<hr class="marginless">
						</div>
						<?php
						}					
					}
					else
						echo "You currently have no friend suggestions";
				}
					
			}
		}						
		
		
	?>

</div>