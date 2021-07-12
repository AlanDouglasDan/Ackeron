<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");
require_once("../classes/Message.php");

$query = sanitizeString($_POST['query']);
$userLoggedIn = $_POST['userLoggedIn'];
$username = $_POST['username'];

$message_obj = new Message($con, $userLoggedIn);
$user = new User($con, $userLoggedIn);

$ids = array();

if(strpos($username, "ACK_GROUP..??.") === false)
    $usersReturned = mysqli_query($con, "SELECT * FROM messages WHERE body LIKE '%$query%' AND deleted='no' AND ((user_to='$userLoggedIn' AND user_from='$username') OR (user_to='$username' AND user_from='$userLoggedIn'))");
else
    $usersReturned = mysqli_query($con, "SELECT * FROM messages WHERE body LIKE '%$query%' AND deleted='no' AND user_to='$username'");

if($query != "") {
	while($row = mysqli_fetch_array($usersReturned)) {
        $id = $row['id'];
		$black = mysqli_query($con, "SELECT * FROM blacklist WHERE username='$userLoggedIn' AND message='$id'");
		if(mysqli_num_rows($black))
			continue;		

        array_push($ids, $id);
    }
    
    $counter = 0;
    foreach($ids as $m){
        $counter++;
        $q = mysqli_query($con, "SELECT * FROM messages WHERE id='$m'");
        $msg = mysqli_fetch_array($q);
        $body = $msg['body'];
        if($body == "ACK_G_MESSAGE..??.." || $body == "ACK_GR_MESSAGE..??.." || strpos($body, "invite.php..??..id=") !== false){
            continue;
        }

        $body_array = preg_split("/\s+/", $body);

		foreach($body_array as $key => $value) {
            if(strpos($value, "reply.php.??.") !== false || strpos($value, "status.php.??.") !== false || strpos($value, "media.php.??.") !== false ) {
                $value = "";
				$body_array[$key] = $value;
            }            
            if(strpos($value, "post.php?id=") !== false){
                $value = "Forwarded Post";
                $body_array[$key] = $value;
            }
        }
        $body = implode(" ", $body_array);

        if(strpos(" ".$body, $query)){
            echo $message_obj->getSingleMessage($m, $m, "hide");
            // echo $body . "<br>";
        }
    }
}
?>

<script>
    var div = document.getElementById("searches");
    div.scrollTop = div.scrollHeight;
</script>