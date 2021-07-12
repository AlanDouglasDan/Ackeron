<?php
class Notification {
	private $user_obj;
	private $con;

	public function __construct($con, $user){
		$this->con = $con;
		$this->user_obj = new User($con, $user);
	}

	public function getUnreadNumber() {
		$userLoggedIn = $this->user_obj->getUsername();
		$query = mysqli_query($this->con, "SELECT * FROM notifications WHERE viewed='no' AND user_to='$userLoggedIn'");
		return mysqli_num_rows($query);
	}

	public function getNotifications($data, $limit) {

		$page = $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();
		$return_string = "";

		if($page == 1)
			$start = 0;
		else 
			$start = ($page - 1) * $limit;

		$set_viewed_query = mysqli_query($this->con, "UPDATE notifications SET viewed='yes' WHERE user_to='$userLoggedIn'");

		$query = mysqli_query($this->con, "SELECT * FROM notifications WHERE user_to='$userLoggedIn' ORDER BY id DESC");

		if(mysqli_num_rows($query) == 0) {
			echo "<p class='red-bg' style='text-align: center;'>You have no notifications!</p>";
			return;
		}

		$num_iterations = 0; //Number of messages checked 
		$count = 1; //Number of messages posted

		while($row = mysqli_fetch_array($query)) {

			if($num_iterations++ < $start)
				continue;

			if($count > $limit)
				break;
			else 
				$count++;


			$user_from = $row['user_from'];

			$user_data_query = mysqli_query($this->con, "SELECT * FROM users WHERE username='$user_from'");
			$user_data = mysqli_fetch_array($user_data_query);


			//Timeframe
			$date_time_now = date("Y-m-d H:i:s");
			$start_date = new DateTime($row['datetime']); //Time of post
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

			$opened = $row['opened'];
			$style = ($opened == 'no') ? "background-color: rgb(180, 57, 57);" : "";

			$return_string .=   "<a href='" . $row['link'] . "'> 
									<div class='resultDisplay resultDisplayNotification' style='" . $style . "'>
										<div class='notificationsProfilePic'>
											<img src='" . $user_data['profile_pic'] . "'>
										</div>
										<p class='timestamp_smaller'>" . $time_message . "</p>  <p>" . $row['message'] . "</p>
									</div>
								</a>";
		}


		//If posts were loaded
		if($count > $limit){
			$return_string .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='false'>";
		}
		else 
			$return_string .= "<input type='hidden' class='noMoreDropdownData' value='true'> <p class='red-bg' style='text-align: center;'>No more notifications to load!</p>";

		return $return_string;
	}

	public function getNotification($data, $limit){
		$page = $data['page']; 
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1) 
			$start = 0;
		else 
			$start = ($page - 1) * $limit;


		$str = ""; //String to return 

		$set_viewed_query = mysqli_query($this->con, "UPDATE notifications SET viewed='yes' WHERE user_to='$userLoggedIn'");

		$query = mysqli_query($this->con, "SELECT * FROM notifications WHERE user_to='$userLoggedIn' ORDER BY id DESC");

		if(mysqli_num_rows($query) == 0) {
			echo "<p class='red-bg' style='text-align: center;'>You have no notifications!</p>";
			return;
		}

		$num_iterations = 0; //Number of messages checked 
		$count = 1; //Number of messages posted

		while($row = mysqli_fetch_array($query)) {
			if($num_iterations++ < $start)
				continue;

			if($count > $limit)
				break;
			else 
				$count++;

			$user_from = $row['user_from'];

			$user_data_query = mysqli_query($this->con, "SELECT * FROM users WHERE username='$user_from'");
			$user_data = mysqli_fetch_array($user_data_query);


			//Timeframe
			$date_time_now = date("Y-m-d H:i:s");
			$start_date = new DateTime($row['datetime']); //Time of post
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

			$opened = $row['opened'];
			$style = ($opened == 'no') ? "background-color: rgb(180, 57, 57);" : "";

			$str .=   "<a href='" . $row['link'] . "'> 
									<div class='resultDisplay resultDisplayNotification' style='" . $style . "'>
										<div class='notificationsProfilePic'>
											<img src='" . $user_data['profile_pic'] . "'>
										</div>
										<p class='timestamp_smaller'>" . $time_message . "</p>  <p>" . $row['message'] . "</p>
									</div>
								</a>";			
		}

		if($count > $limit){
			$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'><input type='hidden' class='noMorePosts' value='false'>";
		}
		else 
			$str .= "<input type='hidden' class='noMorePosts' value='true'> <p class='red-bg' style='text-align: center;'>No more notifications to load!</p>";

		echo $str;
	}

	public function insertNotification($post_id, $user_to, $type) {

		$userLoggedIn = $this->user_obj->getUsername();
		$userLoggedInName = $this->user_obj->getFirstAndLastName();

		$date_time = date("Y-m-d H:i:s");

		$link = "post.php?id=" . $post_id;

		if($post_id != 0){
			$fdi = mysqli_query($this->con, "SELECT tags FROM posts WHERE id='$post_id'");
			$dd = mysqli_fetch_array($fdi);
			$tf = $dd['tags'];
			if($tf){
				$tf = substr($tf, 0, -1);
				$tf = explode(",", $tf);
				$num_tag = count($tf) - 1;
				if($num_tag != 0)
					$num_tag = "and ".$num_tag." others ";
				else
					$num_tag = "";
			}
		}

		switch($type) {
			case 'comment':
				$message = $userLoggedInName . " commented on your post";
				break;
			case 'commentz':
				$message = $userLoggedInName . " commented on this post";
				break;
			case 'like':
				$message = $userLoggedInName . " liked your post";
				break;
			case 'likez':
				$message = $userLoggedInName . " liked this post";
				break;
			case 'profile_post':
				$message = $userLoggedInName . " posted on your profile";
				break;
			case 'post':
				$message = $userLoggedInName . " just made a post";
				break;
			case 'comment_non_owner':
				$message = $userLoggedInName . " commented on a post you commented on";
				break;
			case 'profile_comment':
				$message = $userLoggedInName . " commented on your profile post";
				break;
			case 'tag':
				$message = $userLoggedInName . " tagged you $num_tag in a post";
				break;
			case 'friend_request':
				$message = $userLoggedInName . " accepted your friend request";
				$link = $userLoggedIn;
				break;
		}

		$insert_query = mysqli_query($this->con, "INSERT INTO notifications VALUES('', '$user_to', '$userLoggedIn', '$message', '$link', '$date_time', 'no', 'no')");
	}

	public function levelUpNotification($level){
		$userLoggedIn = $this->user_obj->getUsername();
		$userLoggedInName = $this->user_obj->getFirstAndLastName();
		$date_time = date("Y-m-d H:i:s");
		$link = "level.php?name=$userLoggedIn";
		$message = $userLoggedInName . " Just leveled up to " . $level;

		$usersReturnedQuery = mysqli_query($this->con, "SELECT * FROM users WHERE username = '$userLoggedIn' AND user_closed='no'");
		while($row = mysqli_fetch_array($usersReturnedQuery)){
			$user_obj = new User($this->con, $userLoggedIn);
			$friends = $user_obj->getFriendArray();
			$friends = substr($friends, 1, -1);
			$friendz = explode(',', $friends);
		}
		foreach($friendz as $sss){
			$user_to = $sss;
			$insert_query = mysqli_query($this->con, "INSERT INTO notifications VALUES('', '$user_to', '$userLoggedIn', '$message', '$link', '$date_time', 'no', 'no')");
		}
		$insert_query2 = mysqli_query($this->con, "INSERT INTO notifications VALUES('', '$userLoggedIn', '$userLoggedIn', 'Congratulobia You just leveled up to $level', '$link', '$date_time', 'no', 'no')");
	}

	public function bdayNotification(){
		$username = $this->user_obj->getUsername();
		$userLoggedInName = $this->user_obj->getFirstAndLastName();
		$date_time = date("Y-m-d H:i:s");
		$message = "$userLoggedInName is a year older today";
		$link = "bday.php?name=$username";

		$query = mysqli_query($this->con, "SELECT * FROM notifications WHERE message='Happy Birthday $userLoggedInName' AND user_to='$username'");
		if(mysqli_num_rows($query) == 0){			
			$friends = $this->user_obj->getFriendArray();
			$friends = substr($friends, 1, -1);
			$friendz = explode(',', $friends);
			foreach($friendz as $sss){				
				$ia = mysqli_query($this->con, "INSERT INTO notifications VALUES('', '$sss', '$username', '$message', '$link', '$date_time', 'no', 'no')");
			}
			$insert_query = mysqli_query($this->con, "INSERT INTO notifications VALUES('', '$username', '$username', 'Happy Birthday $userLoggedInName', '$link', '$date_time', 'no', 'no')");
		}
	}

}

?>