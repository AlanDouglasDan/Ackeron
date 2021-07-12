<?php
require_once("../../config/config.php");
require_once("../../includes/classes/User.php");

$query = sanitizeString($_POST['query']);
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query);

//If query contains an underscore, assume user is searching for usernames
if(strpos($query, '_') !== false) 
	$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' ORDER BY first_name");
//If there are two words, assume they are first and last names respectively
else if(count($names) == 2)
	$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' AND last_name LIKE '%$names[1]%') AND user_closed='no' ORDER BY first_name");
//If query has one word only, search first names or last names 
else 
	$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' OR last_name LIKE '%$names[0]%') AND user_closed='no' ORDER BY first_name");


if($query != ""){

    $counter =0;
	while($row = mysqli_fetch_array($usersReturnedQuery)) {
        $counter++;
		$user = new User($con, $userLoggedIn);

		if($row['username'] != $userLoggedIn)
			$mutual_friends = $user->getMutualFriends($row['username']) . " friends in common";
		else 
			$mutual_friends = "";

		echo "<div class='resultDisplay' style='background-color: var(--searches);'>
				<a href='profile.php?profile_username=" . $row['username'] . "&name=" .$row['username'] ."' style='color: #1485BD'>
					<div class='liveSearchProfilePic'>
						<img src='" . $row['profile_pic'] ."'>
					</div>

					<div class='liveSearchText'>
						" . $row['first_name'] . " " . $row['last_name'] . "
						<p>" . $row['username'] ."</p>
						<p id='grey'>" . $mutual_friends ."</p>
					</div>
				</a>
            </div>";
        // if($counter == 10){
            // echo "<div class='' id='show_more'>See More</a>";
            // break;
        // }
	}
}

?>