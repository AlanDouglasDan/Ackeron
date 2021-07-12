<?php
class User {
	private $user;
	private $con;

	public function __construct($con, $user){
		$this->con = $con;
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$user'");
		$this->user = mysqli_fetch_array($user_details_query);
	}

	public function getUsername() {
		return $this->user['username'];
	}

	public function getNumPosts() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT num_posts FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		return $row['num_posts'];
	}

	public function getFirstAndLastName() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT first_name, last_name FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		return $row['first_name'] . " " . $row['last_name'];
	}

	public function getFirstName() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT first_name FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		return $row['first_name'];
	}

	public function isClosed() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT user_closed FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);

		if($row['user_closed'] == 'yes')
			return true;
		else 
			return false;
	}

	public function isFriend($username_to_check) {
		$usernameComma = "," . $username_to_check . ",";

		if(strstr($this->user['friend_array'], $usernameComma) || $username_to_check == $this->user['username']){
			return true;
		}
		else return false;
	}

	public function getProfilePic() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT profile_pic FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		return $row['profile_pic'];
	}

	public function didReceiveRequest($user_from) {
		$user_to = $this->user['username'];
		$check_request_query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
		if(mysqli_num_rows($check_request_query) > 0) {
			return true;
		}
		else {
			return false;
		}
	}

	public function didSendRequest($user_to) {
		$user_from = $this->user['username'];
		$check_request_query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
		if(mysqli_num_rows($check_request_query) > 0) {
			return true;
		}
		else {
			return false;
		}
	}

	public function removeFriend($user_to_remove) {
		$logged_in_user = $this->user['username'];

		$query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$user_to_remove'");
		$row = mysqli_fetch_array($query);
		$friend_array_username = $row['friend_array'];

		$new_friend_array = str_replace($user_to_remove . ",", "", $this->user['friend_array']);
		$remove_friend = mysqli_query($this->con, "UPDATE users SET friend_array='$new_friend_array' WHERE username='$logged_in_user'");

		$new_friend_array = str_replace($this->user['username'] . ",", "", $friend_array_username);
		$remove_friend = mysqli_query($this->con, "UPDATE users SET friend_array='$new_friend_array' WHERE username='$user_to_remove'");
	}

	public function sendRequest($user_to) {
		$user_from = $this->user['username'];
		$query = mysqli_query($this->con, "INSERT INTO friend_requests VALUES('', '$user_to', '$user_from')");
	}

	public function getFriendArray() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		return $row['friend_array'];
	}

	public function getMutualFriends($user_to_check) {
		$mutualFriends = 0;
		$user_array = $this->user['friend_array'];
		$user_array_explode = explode(",", $user_array);

		$query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$user_to_check'");
		$row = mysqli_fetch_array($query);
		$user_to_check_array = $row['friend_array'];
		$user_to_check_array_explode = explode(",", $user_to_check_array);

		foreach($user_array_explode as $i) {

			foreach($user_to_check_array_explode as $j) {

				if($i == $j && $i != "") {
					$mutualFriends++;
				}
			}
		}
		return $mutualFriends;

	}

	public function getNumberOfFriendRequests() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$username'");
		return mysqli_num_rows($query);
	}

	public function getLastSeen($user_to_check){
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT * FROM logins WHERE username='$user_to_check' ORDER BY id DESC LIMIT 1");
		$row = mysqli_fetch_array($query);
		$time = $row['logout'];
		if($row['logout'] == '0000-00-00 00:00:00')
			return "Online";
		else{
			//Timeframe
			$date_time_now = date("Y-m-d H:i:s");
			$start_date = new DateTime($time); //Time of post
			$end_date = new DateTime($date_time_now); //Current time
			$interval = $start_date->diff($end_date); //Difference between dates 
			if($interval->y >= 1) {
				if($interval->y == 1)
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
					$time_message = $interval->s . " seconds ago";
				}
				else {
					$time_message = $interval->s . " seconds ago";
				}
			}
			return "Last seen: " .$time_message;
		}

	}

	public function getLevel(){
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT level FROM levels WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		$level = $row['level'];
		switch ($level) {
			case 'Beginner':
				echo "<a href='level.php?name=$username'><span class='fa fa-star' style='color:red'></span> <span class='special_name'>Beginner</span></a>";
				break;
			case 'Amateur':
				echo "<a href='level.php?name=$username'><span class='fa fa-star' style='color:purple'></span> <span class='special_name'>Amateur</span></a>";						
				break;
			case 'Up coming':
				echo "<a href='level.php?name=$username'><span class='fa fa-star' style='color:green'></span> <span class='special_name'>Up coming</span></a>";						
				break;
			case 'Enthusiast':
				echo "<a href='level.php?name=$username'><span class='fa fa-star' style='color:blue'></span> <span class='special_name'>Enthusiast</span></a>";						
				break;
			case 'Professional':
				echo "<a href='level.php?name=$username'><span class='fa fa-star' style='color:orange'></span> <span class='special_name'>Professional</span></a>";						
				break;
			case 'Ultimate Boss':
				echo "<a href='level.php?name=$username'><span class='fa fa-star' style='color:orchid'></span> <span class='special_name'>Ultimate Boss</span></a>";						
				break;
			case 'Upcoming Boss':
				echo "<a href='level.php?name=$username'><span class='fa fa-star' style='color:grey'></span> <span class='special_name'>Upcoming Boss</span></a>";						
				break;
			case 'Boss':
				echo "<a href='level.php?name=$username'><span class='fa fa-star' style='color:black'></span> <span class='special_name'>Boss</span></a>";						
				break;
			case 'Ackerite':
				echo "<a href='level.php?name=$username'><span class='fa fa-star' style='color:gold'></span> <span class='special_name'>Ackerite</span></a>";						
				break;			
			default:
				# code...
				break;
		}
	}
}

?>