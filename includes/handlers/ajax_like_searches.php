<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");

$query = sanitizeString($_POST['query']);
$id = $_POST['post_id'];

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
        $username = $row['username'];
        $likers = mysqli_query($con, "SELECT * FROM likes WHERE username='$username' AND post_id='$id'");
        if(mysqli_num_rows($likers)){
            $liker_obj = new User($con, $username);
            echo "<div class='row'><img class='friend_img' src=" .$liker_obj->getProfilePic() .">";
            echo "<p class='bot marginless'><a href=". $username .">". $liker_obj->getFirstAndLastName()."</a></p></div><hr>";
        }
	}
}

?>