<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");

$query = sanitizeString($_POST['query']);
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
        $usern = new User($con, $row['username']);
        $pic = $usern->getProfilePic();
        $name = $usern->getFirstAndLastName();
		if($userl->isFriend($row['username'])) {
            ?>
            <li onclick="showOthers('<?php echo $row['username']; ?>')" style="list-style: none;">
                <a style='padding: 0; border-bottom: 1px solid #D3D3D3'>
                    <div class='resultDisplay' style='height: 60px;'>
                        <div class='liveSearchProfilePic'>
                            <img class='fa fa-flag fa-lg icons pull-left' src='<?php echo $pic; ?>'>
                        </div>
                        <div class='liveSearchText'>
                            <?php echo $name; ?>
                            <p><?php echo $row['username']; ?></p>
                        </div>
                    </div>
                </a>
            </li>
            <?php
		}
	}
}

?>