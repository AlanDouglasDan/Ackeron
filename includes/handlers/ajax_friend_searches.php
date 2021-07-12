<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");

$query = sanitizeString($_POST['query']);
$username = $_POST['username'];
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query);

if(strpos($query, "_") !== false) {
	$usersReturned = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' ORDER BY first_name");
}
else if(count($names) == 2) {
	$usersReturned = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' AND last_name LIKE '%$names[1]%') AND user_closed='no' ORDER BY first_name");
}
else {
	$usersReturned = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' OR last_name LIKE '%$names[0]%') AND user_closed='no' ORDER BY first_name");
}
if($query != "") {
	while($row = mysqli_fetch_array($usersReturned)) {

        $userl = new User($con, $userLoggedIn);
        $usern = new User($con, $username);
        
        if($row['username'] == $username)
            continue;

		if($row['username'] != $userLoggedIn) {
			$mutual_friends = $userl->getMutualFriends($row['username']) . " friends in common";
		}
		else {
			$mutual_friends = "";
		}

		if($usern->isFriend($row['username'])) {
            if($username != $row['username']){
                echo "<a href='" . $row['username'] ."'><div class='search_result'>
                    <div class='result_profile_pic'>
                        <img src='". $row['profile_pic'] ."' style='height: 100px;'>
                    </div>                        
                        <p id='grey'> " . $row['username'] ."</p>                        
                        " . $mutual_friends ."<br>
                </div></a>
                <hr id='search_hr'>";
            }
			


		}


	}
}

?>