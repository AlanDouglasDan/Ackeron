<?php  
    require_once("includes/classes/User.php");
    require_once("includes/classes/Post.php");
	require_once 'config/config.php';
	require_once("includes/classes/Notification.php");

    if (isset($_SESSION['username'])) {
        $userLoggedIn = $_SESSION['username'];
        $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
        $user = mysqli_fetch_array($user_details_query);
    }
    else {
        header("Location: register.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
	<link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">

</head>
<body>

    <style type="text/css">
        * {
            font-size: 12px;
			font-family: Arial, Helvetica, Sans-serif;
			overflow-y: auto;
		}
		.hhhg{
			word-break: break-word;
		}
	</style>

	<script src="assets/js/jquery-1.11.1.min.js"></script>
	<script src="assets/js/bootstrap.js"></script>
    
    <script>
        function toggle(){
            var element = document.getElementById("comment_section");

            if(element.style.display == "block")
                element.style.display = "none";
            else    
                element.style.display = "block";
		}
		
		$(document).ready(function(){
			$('input[id="post_btn_comment"]').attr('disabled',true);
			$('textarea[id="post_comment"]').on('keyup',function(){
				if($(this).val()){
					$('input[id="post_btn_comment"]').attr('disabled',false);
					document.getElementById("new_btn4").style.color="red";
				}
				else{
					$('input[id="post_btn_comment"]').attr('disabled',true);
					document.getElementById("new_btn4").style.color="white";
				}
			});
		});
		
		var sound = new Audio();
		sound.src = "button_clicked.mp3";
    </script>

    <?php
        if(isset($_GET['post_id'])) {
            $post_id = $_GET['post_id'];            
			$user_query = mysqli_query($con, "SELECT added_by, user_to FROM posts WHERE id='$post_id'");
			$row = mysqli_fetch_array($user_query);
		
			$posted_to = $row['added_by'];
			$user_to = $row['user_to'];
		
			if(isset($_POST['postComment' . $post_id])) {
				$post_body = $_POST['post_body'];
				$post_body = mysqli_escape_string($con, $post_body);
				$post_body = strip_tags($post_body); //removes html tags 
				$post_body = str_replace('\r\n', "\n", $post_body);
				$post_body = nl2br($post_body);
				$date_time_now = date("Y-m-d H:i:s");
				$insert_post = mysqli_query($con, "INSERT INTO comments VALUES ('', '$post_body', '$userLoggedIn', '$posted_to', '$date_time_now', 'no', '$post_id' ,'0')");
				// $dd = "";
				// $no = "no";
				// $query = "INSERT INTO comments VALUES(?, ?, ?, ?, ?, ?, ?)";
				// $stmt = mysqli_stmt_init($con);
				// if(!mysqli_stmt_prepare($stmt, $query)){
				//     echo "SQL ERROR";
				// }
				// else{
				//     mysqli_stmt_bind_param($stmt, "isssssi", $dd, $post_body, $userLoggedIn, $posted_to, $date_time_now, $no, $post_id);
				//     mysqli_stmt_execute($stmt);
				// }
				
				if($posted_to != $userLoggedIn) {
					$notification = new Notification($con, $userLoggedIn);
					$notification->insertNotification($post_id, $posted_to, "comment");
				}
				
				if($user_to != 'none' && $user_to != $userLoggedIn) {
					$notification = new Notification($con, $userLoggedIn);
					$notification->insertNotification($post_id, $user_to, "profile_comment");
				}
		
		
				$get_commenters = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$post_id'");
				$notified_users = array();
				while($row = mysqli_fetch_array($get_commenters)) {
		
					if($row['posted_by'] != $posted_to && $row['posted_by'] != $user_to 
						&& $row['posted_by'] != $userLoggedIn && !in_array($row['posted_by'], $notified_users)) {
		
						$notification = new Notification($con, $userLoggedIn);
						$notification->insertNotification($post_id, $row['posted_by'], "comment_non_owner");
		
						array_push($notified_users, $row['posted_by']);
					}
		
				}
				
				// $post_not_query = mysqli_query($con, "SELECT * FROM post_notifications WHERE post_id='$post_id'");
				// while($dd = mysqli_fetch_array($post_not_query)){
				// 	$p_name = $dd['username'];
				// 	if(!in_array($p_name, $notified_users) && ($p_name != $userLoggedIn)){
				// 		$ak2 = mysqli_query($con, "SELECT * FROM notifications WHERE user_to='$p_name' AND user_from='$userLoggedIn' AND message LIKE '%commented on this post%' AND link='post.php?id=$post_id'");
				// 		if(mysqli_num_rows($ak2) == 0){
				// 			$notification2 = new Notification($con, $userLoggedIn);
				// 			$notification2->insertNotification($post_id, $p_name, "commentz");
				// 		}
				// 	}
				// }
			
				echo "<center>Comment Posted! </center>";
			}
			?>

			<form action="comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="POST">
				<!-- <textarea name="post_body" id="post_comment"></textarea>
				<input onclick="sound.play()" type="submit" id="post_btn_comment" name="postComment<?php echo $post_id; ?>" value="Post"> -->
				<div class="col-xs-11" style="width: 100%">
					<div class="input-group" style="border: 1px solid; margin-bottom: 10px;">
					<textarea rows="1" placeholder="Leave a comment..." id='post_comment' name='post_body' class="form-control"></textarea>
						<input type='submit' name='postComment<?php echo $post_id; ?>' id='post_btn_comment' style="display: none;">
						<div class="input-group-addon">
							<label onclick='sound.play()' for='post_btn_comment' style="cursor: pointer; overflow: hidden;">
								<span id="new_btn4" class="input-group-text" style="color: transparent">
									Send
								</span>
							</label>									
						</div>
					</div>
				</div>
			</form>

			<?php  
			$get_comments = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$post_id' ORDER BY id DESC");
			$count = mysqli_num_rows($get_comments);
			
			if($count != 0) {

				while($comment = mysqli_fetch_array($get_comments)) {

					$comment_body = $comment['post_body'];
					$posted_to = $comment['posted_to'];
					$posted_by = $comment['posted_by'];
					$date_added = $comment['date_added'];
					$removed = $comment['removed'];
					$id = $comment['id'];

					//Timeframe
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_added); //Time of post
					$end_date = new DateTime($date_time_now); //Current time
					$interval = $start_date->diff($end_date); //Difference between dates 
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " year ago"; //1 year ago
						else 
							$time_message = $interval->y . " years ago"; //1+ year ago
					}
					else if ($interval->m >= 1) {
						if($interval->d == 0) {
							$days = " ago";
						}
						else if($interval->d == 1) {
							$days = $interval->d . " day ago";
						}
						else {
							$days = $interval->d . " days ago";
						}


						if($interval->m == 1) {
							$time_message = $interval->m . " month ago";
						}
						else {
							$time_message = $interval->m . " months ago";
						}

					}
					else if($interval->d >= 1) {
						if($interval->d == 1) {
							$time_message = "Yesterday";
						}
						else {
							$time_message = $interval->d . " days ago";
						}
					}
					else if($interval->h >= 1) {
						if($interval->h == 1) {
							$time_message = $interval->h . " hour ago";
						}
						else {
							$time_message = $interval->h . " hours ago";
						}
					}
					else if($interval->i >= 1) {
						if($interval->i == 1) {
							$time_message = $interval->i . " minute ago";
						}
						else {
							$time_message = $interval->i . " minutes ago";
						}
					}
					else {
						if($interval->s < 30) {
							$time_message = "Just now";
						}
						else {
							$time_message = $interval->s . " seconds ago";
						}
					}

					$user_obj = new User($con, $posted_by);

					$lie_query = mysqli_query($con, "SELECT * FROM comment_likes WHERE comment_id='$id'");
					$num_likes = mysqli_num_rows($lie_query);
					if($num_likes == 1)
						$num_likes .= " Like";
					else
						$num_likes .= " Likes";
							
					?>

					<div class="comment_section">
						<img src="<?php echo $user_obj->getProfilePic();?>" title="<?php echo $posted_by; ?>" style="float:left;" height="30">
						<a href="<?php echo $posted_by?>" target="_parent"> <b> <?php echo $user_obj->getFirstAndLastName(); ?> </b></a>
						&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $time_message . "<br>" . "<span class='hhhg'>$comment_body</span>"; ?> 
						<hr class="marginless">
					</div>

					<?php

				}
			}
			else{
				echo "<center><br><br>No Comments to Show!</center>";
			}
		}
		else if(isset($_GET['comment_id'])){
			$comment_id = $_GET['comment_id'];            
			$user_query = mysqli_query($con, "SELECT posted_by, posted_to FROM comments WHERE id='$comment_id'");
			$row = mysqli_fetch_array($user_query);
		
			$posted_by = $row['posted_by'];
			$posted_to = $row['posted_to'];

			if(isset($_POST['postComment' . $comment_id])) {
				$post_body = $_POST['post_body'];
				$post_body = mysqli_escape_string($con, $post_body);
				$post_body = strip_tags($post_body); //removes html tags 
				$post_body = str_replace('\r\n', "\n", $post_body);
				$post_body = nl2br($post_body);
				$date_time_now = date("Y-m-d H:i:s");
				$insert_post = mysqli_query($con, "INSERT INTO comment_comments VALUES ('', '$post_body', '$userLoggedIn', '$posted_by', '$date_time_now', 'no', '$comment_id')");

				echo "<center>Comment Posted! </center>";
			}
			?>

			<form action="comment_frame.php?comment_id=<?php echo $comment_id; ?>" id="comment_form" name="postComment<?php echo $comment_id; ?>" method="POST">
				<!-- <textarea name="post_body" id="post_comment"></textarea>
				<input onclick="sound.play()" type="submit" id="post_btn_comment" name="postComment<?php echo $comment_id; ?>" value="Post"> -->
				<div class="col-xs-11" style="width: 100%">
					<div class="input-group" style="border: 1px solid; margin-bottom: 10px;">
					<textarea rows="1" placeholder="Leave a comment..." id='post_comment' name='post_body' class="form-control"></textarea>
						<input type='submit' name='postComment<?php echo $comment_id; ?>' id='post_btn_comment' style="display: none;">
						<div class="input-group-addon">
							<label onclick='sound.play()' for='post_btn_comment' style="cursor: pointer; overflow: hidden;">
								<span id="new_btn4" class="input-group-text" style="color: transparent">
									Send
								</span>
							</label>									
						</div>
					</div>
				</div>
			</form>

			<?php
			$get_comments = mysqli_query($con, "SELECT * FROM comment_comments WHERE comment_id='$comment_id' ORDER BY id DESC");
			$count = mysqli_num_rows($get_comments);

			if($count != 0){
				while($comment = mysqli_fetch_array($get_comments)) {

					$comment_body = $comment['body'];
					$posted_to = $comment['posted_to'];
					$posted_by = $comment['posted_by'];
					$date_added = $comment['date_added'];
					$removed = $comment['removed'];
					// $id = $comment['id'];

					//Timeframe
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_added); //Time of post
					$end_date = new DateTime($date_time_now); //Current time
					$interval = $start_date->diff($end_date); //Difference between dates 
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " year ago"; //1 year ago
						else 
							$time_message = $interval->y . " years ago"; //1+ year ago
					}
					else if ($interval->m >= 1) {
						if($interval->d == 0) {
							$days = " ago";
						}
						else if($interval->d == 1) {
							$days = $interval->d . " day ago";
						}
						else {
							$days = $interval->d . " days ago";
						}


						if($interval->m == 1) {
							$time_message = $interval->m . " month ago";
						}
						else {
							$time_message = $interval->m . " months ago";
						}

					}
					else if($interval->d >= 1) {
						if($interval->d == 1) {
							$time_message = "Yesterday";
						}
						else {
							$time_message = $interval->d . " days ago";
						}
					}
					else if($interval->h >= 1) {
						if($interval->h == 1) {
							$time_message = $interval->h . " hour ago";
						}
						else {
							$time_message = $interval->h . " hours ago";
						}
					}
					else if($interval->i >= 1) {
						if($interval->i == 1) {
							$time_message = $interval->i . " minute ago";
						}
						else {
							$time_message = $interval->i . " minutes ago";
						}
					}
					else {
						if($interval->s < 30) {
							$time_message = "Just now";
						}
						else {
							$time_message = $interval->s . " seconds ago";
						}
					}

					$user_obj = new User($con, $posted_by);

					// $lie_query = mysqli_query($con, "SELECT * FROM comment_likes WHERE comment_id='$id'");
					// $num_likes = mysqli_num_rows($lie_query);
					// if($num_likes == 1)
					// 	$num_likes .= " Like";
					// else
					// 	$num_likes .= " Likes";
							
					?>

					<div class="comment_section">
						<img src="<?php echo $user_obj->getProfilePic();?>" title="<?php echo $posted_by; ?>" style="float:left;" height="30">
						<a href="<?php echo $posted_by?>" target="_parent"> <b> <?php echo $user_obj->getFirstAndLastName(); ?> </b></a>
						&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $time_message . "<br>" . "<span class='hhhg'>$comment_body</span>"; ?> 
						<hr class="marginless">
					</div>

					<?php

				}
			}
			else{
				echo "<center><br><br>No Comments to Show!</center>";
			}
		}
    ?>
	
</body>
</html>