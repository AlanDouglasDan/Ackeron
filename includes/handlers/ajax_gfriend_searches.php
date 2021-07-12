<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");

$query = sanitizeString($_POST['query']);
$gname = $_POST['username'];
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
    echo "<div style='border: 1px solid red'>";
	while($row = mysqli_fetch_array($usersReturned)) {
        $userl = new User($con, $userLoggedIn);

        $get_query = mysqli_query($con, "SELECT * FROM group_chats WHERE group_info='$gname'");
        $rowsd = mysqli_fetch_array($get_query);
        $userss = $rowsd['users'];
        $participants = explode(",", $userss);
        $admins = $rowsd['admins'];
        $admins = explode(",", $admins);

        foreach($participants as $participant){
            if($participant == $row['username']){                
                if(in_array($participant, $admins))
                    $nd = "Admin";
                else
                    $nd = "";
                echo "<div class='user_found_messages'>
                        <img src='" . $row['profile_pic'] . "' style='border-radius: 50%; margin-right: 5px;'>
                        <b><span style='color: #e52020;'>" . $row['first_name'] . " " . $row['last_name'] . "</span></b><br>
                        <span id='grey'> " . $row['username'] . "</span><span class='time'>$nd</span>
                    </div>";
            }
        }
    }
    echo "</div>";
}

?>