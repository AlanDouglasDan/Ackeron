<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");
require_once("../classes/Message.php");

$query = sanitizeString($_POST['query']);
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query);
$namez = $namer = array();

if(strpos($query, "_") !== false) {
	$usersReturned = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '%$query%' AND user_closed='no' ORDER BY first_name");
}
else if(count($names) == 2) {
	$usersReturned = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed='no' ORDER BY first_name");
}
else {
	$usersReturned = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed='no' ORDER BY first_name");
}

$g_query = mysqli_query($con, "SELECT * FROM group_chats WHERE users LIKE '%$userLoggedIn,%'");

if($query != "") {
	echo "<br>";
	
	while($rowz = mysqli_fetch_array($g_query)) {
		$userz = $rowz['users'];
		$gname = $rowz['group_info'];
		$userz = substr($userz, 0, -1);
		$members = explode(",", $userz);

		$black = mysqli_query($con, "SELECT * FROM blacklist WHERE username='$userLoggedIn' AND chat='$gname'");
		if(mysqli_num_rows($black))
			continue;

		array_push($namer, $gname);
	}

	while($row = mysqli_fetch_array($usersReturned)) {

        $user = new User($con, $userLoggedIn);
		$username = $row['username'];

		if($row['username'] == $userLoggedIn)
			continue;

		$black = mysqli_query($con, "SELECT * FROM blacklist WHERE username='$userLoggedIn' AND chat='$username'");
		if(mysqli_num_rows($black))
			continue;		

		if($user->isFriend($username))
			array_push($namez, $username);
	}

	foreach($namer as $ee){
		$eed = substr($ee, 13);
		$na = strtolower($eed);
		if(strpos($na, $query)){
			array_push($namez, $ee);
		}
	}

	foreach($namez as $pes){
		$send_to = $pes;
		if(strpos($pes, "ACK_GROUP..??.") === false){
			$user_found_obj = new User($con, $pes);
			$pic = $user_found_obj->getProfilePic();
			$namee = $user_found_obj->getFirstAndLastName();
		}
		else{
			$hg = mysqli_query($con, "SELECT id FROM group_chats WHERE group_info='$pes'");
			$uto = mysqli_fetch_array($hg);
			$r_name = $uto['id'];
			$send_to = "ACK_GROUP..??.$r_name";
			$g_queryd = mysqli_query($con, "SELECT * FROM group_chats WHERE id='$r_name'");
			$af = mysqli_fetch_array($g_queryd);
			$pic = $af['group_pic'];
			if($pic == "")
				$pic = "assets/images/profile_pics/defaults/male.png";
			$namee = substr($pes, 14);
			// echo $send_to;
		}

		$check = mysqli_query($con, "SELECT * FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$send_to') OR (user_to='$send_to' AND user_from='$userLoggedIn')");
		if(mysqli_num_rows($check) > 0){
			$message = new Message($con, $userLoggedIn);
			$latest_message_details = $message->getLatestMessage($userLoggedIn, $send_to);
		}
		else{
			$latest_message_details[2] = "";
			$latest_message_details[1] = "";
			$latest_message_details[0] = "";
		}

		if(strpos($pes, "ACK_GROUP..??.") === false){
			$notifications_query = mysqli_query($con, "SELECT user_to, user_from, opened FROM messages WHERE user_to='$userLoggedIn' AND user_from='$pes' AND opened='no' AND deleted='no'");
			$unopened = mysqli_num_rows($notifications_query);
			if($unopened)
				$unopened = "<span class='unopened'><b style='color: white !important;'>$unopened</b></span>";
			else
				$unopened = "";
		}
		else{
			$idz = array();
			$num = 0;				
			$ensure = mysqli_query($con, "SELECT id FROM messages WHERE user_to='$send_to' AND user_from='$userLoggedIn' AND (body='ACK_G_MESSAGE..??..')");
			if(mysqli_num_rows($ensure)){
				$eh = mysqli_fetch_array($ensure);
				$iddd = $eh['id'];
				$lf = " AND id > $iddd";
			}
			else
				$lf = "";
			$notifications_query2 = mysqli_query($con, "SELECT id FROM messages WHERE user_to='$send_to' $lf AND body!='ACK_G_MESSAGE..??..' AND body!='ACK_Gr_MESSAGE..??..' AND user_from!='$userLoggedIn' AND deleted='no'");
			while($rows = mysqli_fetch_array($notifications_query2)){
				array_push($idz, $rows['id']);
			}
			foreach($idz as $idd){
				$notifications_query = mysqli_query($con, "SELECT * FROM message_views WHERE message_id='$idd' AND username='$userLoggedIn' AND date_viewed!='0000-00-00 00:00:00'");
				if(mysqli_num_rows($notifications_query))
					$num ++;
			}
			$unopened = count($idz) - $num;
			if($unopened > 0)
				$unopened = "<span class='unopened'><b style='color: white !important;'>$unopened</b></span>";
			else
				$unopened = "";
		}
		
		$body = $latest_message_details[1];
		$body_array = preg_split("/\s+/", $body);

		foreach($body_array as $key => $value) {

			if(strpos($value, "www.youtube.com/watch?v=") !== false) {

				$value = "Youtube video";
				$body_array[$key] = $value;

			}

			if(strpos($value, "post.php?id=") !== false) {

				$value = "Forwarded Post";
				$body_array[$key] = $value;

			}

			if((strpos($value, "status.php.??.id=") !== false) || (strpos($value, "reply.php.??.id=") !== false)){

				$value = "";
				$body_array[$key] = $value;

			}

			if(strpos($value, "ACK_G_MESSAGE..??..") !== false){
				// $name = substr($username, 14);
				$value = $latest_message_details[3]." just joined ". $namee;
				$body_array[$key] = $value;
			}

			if(strpos($value, "ACK_GR_MESSAGE..??..") !== false){
				// $name = substr($username, 14);
				$value = $latest_message_details[3]." was removed from ". $namee;
				$body_array[$key] = $value;
			}

		}
		$body = implode(" ", $body_array);

		if(strpos($body, "media.php.??.") === false){
			$dots = (strlen($body) >= 30) ? "..." : "";
			$split = str_split($body, 30);
			$split = $split[0] . $dots; 
		}
		else{
			if(strpos($body, ".mp4"))
				$split = "<span class='fa fa-video-camera'></span> Video";
			else
				$split = "<span class='fa fa-camera'></span> Photo";
		}

		if($unopened)
			$col = "color: #20aae5;";
		else
			$col = "";

		echo "<a href='messages.php?u=".$send_to."' id='link'> <div class='user_found_messages'>
		<img src='" . $pic . "' style='border-radius: 50%; margin-right: 5px;'>
		<b><span style='color: rgb(236, 81, 81);'>" . $namee . "</span></b>
		<center class='pull-right'><span class='time convos_time' id='grey' style='padding: 0; $col'> " . $latest_message_details[2] . "<br></span>".$unopened."</center>
		<p id='grey' style='margin: 0;'>" . $latest_message_details[0] . $split . " </p>
		</div>
		</a>";
	}
}
?>