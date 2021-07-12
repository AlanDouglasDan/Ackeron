<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");

$query = sanitizeString($_POST['query']);
$id = sanitizeString($_POST['post_id']);
$userLoggedIn = $_COOKIE['user_login'];

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

$list = "";
// if($query != "") {
	while($row = mysqli_fetch_array($usersReturned)) {
        $username = $row['username'];
        $userl = new User($con, $userLoggedIn);
        $usern = new User($con, $row['username']);
        $pic = $usern->getProfilePic();
        $name = $usern->getFirstAndLastName();
        if($username == $userLoggedIn)
            continue;
        ?>
            <script>                
                var id = <?php echo $id; ?>;
                var toSend = _("tofow"+id).value;
                var toSen = toSend.length - 1;
                var res = toSend.substring(0, toSen);
                res = res.split(",");
                for(var check of res){
                    $("#fow"+check+id).addClass("checkedd");
                    $("#fow"+check+id).addClass("fa");
                    $("#fow"+check+id).addClass("fa-check");
                }
            </script>
        <?php
		if($userl->isFriend($row['username'])) {
            $list .= "<div class='resultDisplay' style='border-bottom: none;' onclick='check$id(\"".$row['username']."\")'>
                        <div class='liveSearchProfilePic'>
                            <img src='" . $row['profile_pic'] ."'>
                        </div>

                        <div class='liveSearchText'>
                            " . $row['first_name'] . " " . $row['last_name'] . "
                            <p>" . $row['username'] ."</p>
                        </div>
                        <div class='pull-right hrm' id='fow$username$id'></div>
                    </div>";
        }
    }
    echo "<ul class='dropdown-menu forwardee' style='overflow: auto; display: block;'>
            <li>
                $list
            </li>
        </ul>";
// }
?>