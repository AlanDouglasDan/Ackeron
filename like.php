<?php
    require_once 'config/config.php';
    require_once("includes/classes/User.php");
    require_once("includes/classes/Post.php");
    require_once("includes/classes/Notification.php");

    if (isset($_SESSION['username'])) {
        $userLoggedIn = $_SESSION['username'];
        $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
        $user = mysqli_fetch_array($user_details_query);
    }
    else {
        header("Location: register.php");
    }

    if(isset($_GET['post_id'])) {
		$post_id = $_GET['post_id'];
	}
	
    $get_likes = mysqli_query($con, "SELECT likes, added_by FROM posts WHERE id='$post_id'");
	$row = mysqli_fetch_array($get_likes);
	$total_likes = $row['likes']; 
    $user_liked = $row['added_by'];
    
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$user_liked'");
	$row = mysqli_fetch_array($user_details_query);
    $total_user_likes = $row['num_likes'];

    if(isset($_POST['like_button'])) {
		$total_likes++;
		$query = mysqli_query($con, "UPDATE posts SET likes='$total_likes' WHERE id='$post_id'");
		$total_user_likes++;
		$user_likes = mysqli_query($con, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_liked'");
		$insert_user = mysqli_query($con, "INSERT INTO likes VALUES('', '$userLoggedIn', '$post_id')");
		
		if($user_liked != $userLoggedIn) {
			$notification = new Notification($con, $userLoggedIn);
			$notification->insertNotification($post_id, $user_liked, "like");
		}
	}

    if(isset($_POST['unlike_button'])) {
		$total_likes--;
		$query = mysqli_query($con, "UPDATE posts SET likes='$total_likes' WHERE id='$post_id'");
		$total_user_likes--;
		$user_likes = mysqli_query($con, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_liked'");
		$insert_user = mysqli_query($con, "DELETE FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
	}
    
    $check_query = mysqli_query($con, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
	$num_rows = mysqli_num_rows($check_query);


	if($total_likes > 1){
		$add = "Likes";
	}
	else {
		$add = "Like";
	}

    if($num_rows > 0) {
		echo '<form action="like.php?post_id=' . $post_id . '" method="POST">
				<input type="submit" id="like" class="comment_like" name="unlike_button" value="Unlike">
				<label onclick="sound.play()" for ="like"><span class="fa fa-heart fa-lg text-danger"></span>					
				</label>
			</form>
		';
	}
	else {
		echo '<form action="like.php?post_id=' . $post_id . '" method="POST">
				<input type="submit" id="unlike" class="comment_like" name="like_button" value="Like">
				<label onclick="sound.play()" for ="unlike"><span class="fa fa-heart-o fa-lg"></span>
					
				</label>
			</form>
		';
	}
?>
<!-- <div class="like_value">
	'. $total_likes .' '.' '. $add .'
</div> -->

<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	<link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">
</head>
<body>

	<style type="text/css">
	* {
		font-family: Arial, Helvetica, Sans-serif;
	}
	body {
		background-color: inherit;
		color: var(--color);
	}

	form {
		position: absolute;
		top: 2;
	}
	.fa-heart-o{
		color: var(--color);
	}
	</style>
	
	<script>
		var sound = new Audio();
		sound.src = "button_click.mp3";

		var darkMode = localStorage.getItem('darkMode');

		const enableDarkMode = function(){
			document.body.classList.add('darkmode');
			localStorage.setItem('darkMode', 'enabled');
		}

		const disableDarkMode = () =>{
			document.body.classList.remove('darkmode')
			localStorage.setItem('darkMode', 'null');
		}

		if(darkMode === 'enabled'){
			enableDarkMode();
		}
		else{
			disableDarkMode();
		}
	</script>
    
</body>
</html>