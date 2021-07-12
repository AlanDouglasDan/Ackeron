<?php
class Message {
	private $user_obj;
	private $con;

	public function __construct($con, $user){
		$this->con = $con;
		$this->user_obj = new User($con, $user);
	}

	public function getMostRecentUser() {
		$userLoggedIn = $this->user_obj->getUsername();

		$query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY id DESC LIMIT 1");

		if(mysqli_num_rows($query) == 0)
			return false;

		$row = mysqli_fetch_array($query);
		$user_to = $row['user_to'];
		$user_from = $row['user_from'];

		if($user_to != $userLoggedIn){
			if(strpos($user_to, "ACK_GROUP..??.") === false)
				return $user_to;
			else{
				$black = mysqli_query($this->con, "SELECT * FROM blacklist WHERE username='$userLoggedIn' AND chat='$user_to'");
				if(mysqli_num_rows($black)){
					return $user_from;
				}
				else{
					return $user_to;
				}				
			}
		}
		else 
			return $user_from;

		
	}

	public function sendMessage($user_to, $body, $date) {
		$userLoggedIn = $this->user_obj->getUsername();
		$id = "";
		$asd = "no";
		if(is_array($body)){
			$images = "media.php.??.";
			for($i=0; $i<(count($body)-1); $i++){
				$images .= $body[$i] . ",";
			}
			$text = $body[count($body)-1];			
			$text = strip_tags($text); //removes html tags 
			// $text = str_replace('\r\n', "\n", $text);
			// $text = nl2br($text);
			$images .= $text;
			$query = mysqli_query($this->con, "INSERT INTO messages VALUES('', '$user_to', '$userLoggedIn', '$images', '$date', 'no', 'no', 'no')");
			// $query = "INSERT INTO messages VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
			// $stmt = mysqli_stmt_init($this->con);
            // if(!mysqli_stmt_prepare($stmt, $query)){
            //     echo "SQL ERROR";
            // }
            // else{
            //     mysqli_stmt_bind_param($stmt, "isssssss", $id, $user_to, $userLoggedIn, $images, $date, $asd, $asd, $asd);
            //     mysqli_stmt_execute($stmt);
            // }
		}
		else{
			$body = strip_tags($body); //removes html tags 
			// $body = str_replace('\r\n', "\n", $body);
			// $body = nl2br($body);
			if($body != "") {
				$query = mysqli_query($this->con, "INSERT INTO messages VALUES('', '$user_to', '$userLoggedIn', '$body', '$date', 'no', 'no', 'no')");
				// $query = "INSERT INTO messages VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
				// $stmt = mysqli_stmt_init($this->con);
				// if(!mysqli_stmt_prepare($stmt, $query)){
				// 	echo "SQL ERROR";
				// }
				// else{
				// 	mysqli_stmt_bind_param($stmt, "isssssss", $id, $user_to, $userLoggedIn, $body, $date, $asd, $asd, $asd);
				// 	mysqli_stmt_execute($stmt);
				// }
			}
		}
		header("Location: messages.php?u=$user_to");
	}

	public function getMessages($data, $limit) {
		?>
		<script>
			var formdata = new FormData();
			var ajax = new XMLHttpRequest();
			ajax.addEventListener('load', completeHandler, false);
			
			function completeHandler(event){
				_('scroll_messages').innerHTML = event.target.responseText;
				var div = _('scroll_messages');
				div.scrollTop = div.scrollHeight;
			}
		</script>
		<?php
		$userLoggedIn = $this->user_obj->getUsername();
		$page = $data['page']; 
		$otherUser = $data['profileUsername'];
		$counter = 0;

		if($page == 1) 
			$start = 0;
		else 
			$start = ($page - 1) * $limit;

		$data = $bg = "";
		$container = $dates = $bottle = array();
		$date_time_now = date("Y-m-d H:i:s");
		$group_name = substr($otherUser, 14);
		if(strpos($otherUser, "ACK_GROUP..??.") === false){
			$get_messages_query = mysqli_query($this->con, "SELECT * FROM messages WHERE ((user_to='$userLoggedIn' AND user_from='$otherUser') OR (user_from='$userLoggedIn' AND user_to='$otherUser')) ORDER BY id DESC");
			$get_messages_query2 = mysqli_query($this->con, "SELECT * FROM messages WHERE ((user_to='$userLoggedIn' AND user_from='$otherUser') OR (user_from='$userLoggedIn' AND user_to='$otherUser')) ORDER BY id DESC");
		}
		else{
			$get_messages_query = mysqli_query($this->con, "SELECT * FROM messages WHERE user_to='$otherUser' ORDER BY id DESC");
			$get_messages_query2 = mysqli_query($this->con, "SELECT * FROM messages WHERE user_to='$otherUser' ORDER BY id DESC");
		}

		if(strpos($otherUser, "ACK_GROUP..??.") === false)	
			$query = mysqli_query($this->con, "UPDATE messages SET opened='yes' WHERE user_to='$userLoggedIn' AND user_from='$otherUser'");

		$num_iterations = 0; //Number of results checked (not necasserily posted)
		$count = 1;

		while($dddd = mysqli_fetch_array($get_messages_query)) {
			if($num_iterations++ < $start)
				continue; 

			//Once 10 posts have been loaded, break
			if($count > $limit) {
				break;
			}
			else {
				$count++;
			}
			array_push($container, $dddd['id']);
		}
		sort($container, SORT_NUMERIC);

		while($fkf = mysqli_fetch_array($get_messages_query2)){
			$da = $fkf['date'];
			$di = substr($da, 0, 10);
			array_push($dates, $di);
		}
		$dates = array_unique($dates);

		foreach($dates as $dif){
			if(strpos($otherUser, "ACK_GROUP..??.") === false)
				$gk = mysqli_query($this->con, "SELECT id FROM messages WHERE ((user_to='$userLoggedIn' AND user_from='$otherUser') OR (user_from='$userLoggedIn' AND user_to='$otherUser')) AND date LIKE '%$dif%' LIMIT 1");
			else
				$gk = mysqli_query($this->con, "SELECT id FROM messages WHERE user_to='$otherUser' AND date LIKE '%$dif%' LIMIT 1");

			$kf = mysqli_fetch_array($gk);
			array_push($bottle, $kf['id']);
			// echo $dif . "<br>";
		}

		// foreach($bottle as $fk){
		// 	echo $fk . "<br>";
		// }
		
		if($count > $limit) 
			$data .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
				<input type='hidden' class='noMorePosts' value='false'>";
		else 
			$data .= "<input type='hidden' class='noMorePosts' value='true'>";

		foreach($container as $ssl){
			// echo $ssl . "<br>";
			$style = "";
			$counter++;
			$aed = mysqli_query($this->con, "SELECT * FROM messages WHERE id='$ssl'");
			$row = mysqli_fetch_array($aed);
			$user_to = $row['user_to'];
			$user_from = $row['user_from'];

			if(strpos($otherUser, "ACK_GROUP..??.") !== false)
				$by_who = "<div class='g_name'>@$user_from</div>";
			else
				$by_who = "";

			$body = $row['body'];
			$body = str_replace('<br />', " ", $body);
			$body = nl2br($body);
			$date = $row['date'];
			$idd = $row['id'];
			if(($user_to == $userLoggedIn) || (strpos($user_to, "ACK_GROUP..??.") !== false)){
				$query3 = mysqli_query($this->con, "SELECT * FROM message_views WHERE username='$userLoggedIn' AND message_id='$idd'");
				if(mysqli_num_rows($query3) == 1)
					$query4 = mysqli_query($this->con, "UPDATE message_views SET date_viewed='$date_time_now' WHERE username='$userLoggedIn' AND message_id='$idd'");
			}

		
			if(strpos($otherUser, "ACK_GROUP..??.") === false){
				$vi = mysqli_query($this->con, "SELECT * FROM message_views WHERE username='$user_to' AND message_id='$idd'");
				if(mysqli_num_rows($vi)){
					$re = mysqli_fetch_array($vi);
					if($user_from == $userLoggedIn){
						if($re['date_viewed'] != '0000-00-00 00:00:00'){
							$picz = "<span class='fa fa-check seen' style='padding-top: 5; font-size:130%;'></span>";
						}
						else if($re['date_delivered'] != '0000-00-00 00:00:00' && $re['date_viewed'] == '0000-00-00 00:00:00'){
							$picz = "<span class='fa fa-check warning_msg' style='padding-top: 5; font-size:130%;'></span>";
						}
						else{
							$picz = "<span class='fa fa-check text-muted' style='padding-top: 5; font-size:130%;'></span>";
						}
					}
					else
						$picz = "";
				}
				else{
					$picz = "<span class='fa fa-check text-muted' style='padding-top: 5; font-size:130%;'></span>";
				}
			}
			else	
				$picz = "";

			$black = mysqli_query($this->con, "SELECT * FROM blacklist WHERE username='$userLoggedIn' AND message='$idd'");
			if(mysqli_num_rows($black))
				continue;

			$is_deleted = $row['deleted'];

			$sec = substr($date, 17);
			$min = substr($date, 14, -3);
			$hour = substr($date, 11, -6);
			$day = substr($date, 8, -9);
			$month = substr($date, 5, -12);
			$year = substr($date, 0, -15);
			$nice = mktime($hour, $min, $sec, $month, $day, $year);
			$formedd = date("g:i A", $nice);
			$formed = date("l", $nice);
			$forme = date("D, j M", $nice);
			$form = date("D, j M Y", $nice);

			// echo $day."<br>";			
			// else if($diff > 86400)
			// 	$time_message = $formed;
			// if($diff < 86400)
			$time_message = $formedd;

			if(in_array($idd, $bottle)){
				$time_now = time();
				$diff = $time_now - $nice;				
				$year_now = substr($date_time_now, 0, -15);
				$day_now = substr($date_time_now, 8, -9);
				$month_now = substr($date_time_now, 5, -12);
				if($year_now != $year)
					$dyea = "<div class='center-blocks'><div class='sp_msg' style='font-size: 80%;'><span>$form</span></div></div>";
				else if($diff > 518400)
					$dyea = "<div class='center-blocks'><div class='sp_msg' style='font-size: 80%;'><span>$forme</span></div></div>";
				// else if($diff > 86400)
				// 	$dyea = "<div class='center-blocks'><div class='sp_msg' style='font-size: 80%;'><span>$formed</span></div></div>";
				else if($day_now == $day && $month_now == $month && $year_now == $year)
					$dyea = "<div class='center-blocks'><div class='sp_msg' style='font-size: 80%;'><span>Today</span></div></div>";
				else 
					$dyea = "<div class='center-blocks'><div class='sp_msg' style='font-size: 80%;'><span>$formed</span></div></div>";
			}
			else
				$dyea = "";

			$body_array = preg_split("/\s+/", $body);

			foreach($body_array as $key => $value) {

				if(strpos($value, "www.") !== false) {

					$value = "<a target='_blank' class='forward_link' href='" . $value ."'>$value</a>";
					$body_array[$key] = $value;

				}

				if(strpos($value, "post.php?id=") !== false) {

					$value = "<a class='forward_link' href='" . $value ."'>Forwarded Post</a>";
					$body_array[$key] = $value;

				}
				
				if(strpos($value, "status.php.??.id=") !== false) {

					$bg = ($user_to == $userLoggedIn) ? "var(--sblue)" : "var(--sgreen)";
					$co = ($user_to == $userLoggedIn) ? "black" : "var(--color)";
					?>
					<style>
						#status_forwarde<?php echo $idd; ?>{
							background-color: <?php echo $bg; ?>;
							color: <?php echo $co; ?>;
						}
					</style>

					<?php
					$dd = substr($value, 17);
					$retrieve_query = mysqli_query($this->con, "SELECT * FROM statuses WHERE id='$dd'");
					$stuff = mysqli_fetch_array($retrieve_query);
					$text = $stuff['text'];
					$name = $stuff['username'];
					$del = $stuff['deleted'];	
					$media = $stuff['images'];
					if($media){
						if($text){
							$stuf = explode(".", $stuff['images']);		
							if($stuf[count($stuf)-1] == "mp4")
								$image = "";
							else
								$image = "<div class='status_forwarded_img col-xs-2'><img src=$media></div>";
							if($del == 'no')
								if($image)
									$value = "<a href='status.php?name=$name&id=$dd'><div class='status_forwarded' id='status_forwarde$idd'><div class='col-xs-10'><span class='fa fa-camera status_icon'></span> $text</div>$image</div></a>";
								else
									$value = "<a href='status.php?name=$name&id=$dd'><div class='status_forwarded' id='status_forwarde$idd'><div class='col-xs-12 text_pad'><span class='fa fa-video-camera'></span> $text</div>$image</div></a>";
							else
								if($image)
									$value = "<div class='status_forwarded' id='status_forwarde$idd'><div class='col-xs-10'><span class='fa fa-camera status_icon'></span> $text</div>$image</div>";
								else
									$value = "<div class='status_forwarded' id='status_forwarde$idd'><div class='col-xs-12 text_pad'><span class='fa fa-video-camera'></span> $text</div>$image</div>";
						}
						else{
							$stuf = explode(".", $stuff['images']);	
							if($stuf[count($stuf)-1] == "mp4")
								$image = "";
							else
								$image = "<div class='status_forwarded_img col-xs-3'><img src=$media></div>";	
							if($del == 'no')
								if($image)
									$value = "<a href='status.php?name=$name&id=$dd'><div class='status_forwarded' id='status_forwarde$idd'><div class='col-xs-9'><span class='fa fa-camera status_icon'></span> Photo</div>$image</div></a>";
								else
									$value = "<a href='status.php?name=$name&id=$dd'><div class='status_forwarded' id='status_forwarde$idd'><div class='col-xs-12 text_pad'><span class='fa fa-video-camera'></span> Video</div>$image</div></a>";
							else
								if($image)
									$value = "<div class='status_forwarded' id='status_forwarde$idd'><div class='col-xs-9'><span class='fa fa-camera status_icon'></span> Photo</div>$image</div>";
								else
									$value = "<div class='status_forwarded' id='status_forwarde$idd'><div class='col-xs-12 text_pad'><span class='fa fa-video-camera'></span> Video</div>$image</div>";
						}						
					}
					else{
						if($del == 'no')
							$value = "<a href='status.php?name=$name&id=$dd'><div class='status_forwarded' id='status_forwarde$idd'><div class='col-xs-12 text_pad'>$text</div></div></a>";
						else
							$value = "<div class='status_forwarded' id='status_forwarde$idd'><div class='col-xs-12 text_pad'>$text</div></div>";
					}

					$body_array[$key] = $value;

				}

				if(strpos($value, "reply.php.??.id=") !== false) {

					$bg = ($user_from != $userLoggedIn) ? "var(--sblue)" : "var(--sgreen)";
					$co = ($user_from != $userLoggedIn) ? "black" : "var(--color)";
					?>
					<style>
						#reply_forwarde<?php echo $idd; ?>{
							background-color: <?php echo $bg; ?>;
							color: <?php echo $co; ?>;
						}
					</style>
					<?php
					$dd = substr($value, 16);					
					$retrieve_query = mysqli_query($this->con, "SELECT * FROM messages WHERE id='$dd'");
					$stuff = mysqli_fetch_array($retrieve_query);
					$text = $stuff['body'];					
					$body_arrays = preg_split("/\s+/", $text);
					foreach($body_arrays as $k => $v){
						if(strpos($v, "status.php.??.id=") !== false) {
							$v = "";
							$body_arrays[$k] = $v;
						}	
						if(strpos($v, "reply.php.??.id=") !== false) {
							$v = "";
							$body_arrays[$k] = $v;
						}
						if(strpos($v, "post.php?id=") !== false) {
							$v = "Forwarded Post";
							$body_arrays[$k] = $v;
						}	
						if(strpos($v, "invite.php..??..id=") !== false) {
							$v = "Group Invite";
							$body_arrays[$k] = $v;
						}	
						if(strpos($v, "media.php.??.") !== false){
							$stuf = substr($v, 13, -1);
							if(strpos($stuf, ".mp4")){
								$v = "<span class='fa fa-video-camera status_icon'></span>";
							}
							else{
								// $image = "<span class='sm_img'><img class='img-responsive' src=$stuf></span>";
								$v = "<span class='fa fa-camera status_icon'></span>";
							}
							// $v = "<span class='fa fa-camera status_icon'></span>";
							$body_arrays[$k] = $v;
						}
					}
					$text = implode(" ", $body_arrays);

					$value = "<a href='#msg$dd'><div class='status_forwarded' id='reply_forwarde$idd'><div class='col-xs-12 text_pad'>$text</div></div></a>";					

					$body_array[$key] = $value;

				}

				if(strpos($value, "invite.php..??..id=") !== false){
					$bg = ($user_from != $userLoggedIn) ? "var(--sblue)" : "var(--sgreen)";
					$co = ($user_from != $userLoggedIn) ? "black" : "var(--color)";
					?>
					<style>
						#invite_forwarde<?php echo $idd; ?>{
							background-color: <?php echo $bg; ?>;
							color: <?php echo $co; ?>;
						}
					</style>
					<?php
					$g_id = substr($value, 19);
					$g_q = mysqli_query($this->con, "SELECT * FROM group_chats WHERE id='$g_id'");
					$g_row = mysqli_fetch_array($g_q);
					$g_pic = $g_row['group_pic'];
					$g_nam = $g_row['group_info'];
					$g_name = substr($g_nam, 14);						
					if($g_pic == ""){
						$g_pic = "assets/images/profile_pics/defaults/male.png";
					}
					$value = "<div class='status_forwarded' style='height:6em; display:flex; align-items:center; max-height: 6em; border: none;' id='invite_forwarde$idd'><div class='' style='padding:10;'><img src='$g_pic' class='img-responsive' style='height: 5em; border-radius: 50%; width: 5em;'></div><div style='padding: 10;'><b>$g_name</b><br>Ackeron Group Invite</div></div><a href='messages.php?u=$g_nam&add=ACK_ALLOW_GROUP_JOIN' class='forward_link'>Click Here to join this group</a>";
					$body_array[$key] = $value;
				}

				if(strpos($value, "media.php.??.") !== false) {

					$dd = substr($value, 13, -1);
					$images = explode(",", $dd);
					$num = count($images);
					$value = "";
					foreach($images as $kk=> $s){
						if($num == 1){
							if(strpos($s, ".mp4") || strpos($s, ".MOV"))
								$value = "<video class='sent_img' src='$s' data-toggle='modal' data-target='#med_modal$ssl' onclick='msg_image($ssl, $kk)' style='height:300;'></video>";
							else
								$value = "<img src='$s' data-toggle='modal' data-target='#med_modal$ssl' onclick='msg_image($ssl, $kk)' class='img-responsive sent_img' style='height:300;'>";
						}
						else if($num == 2){
							if(strpos($s, ".mp4") || strpos($s, ".MOV"))
								$value .= "<video class='sent_img half' src='$s' data-toggle='modal' data-target='#med_modal$ssl' onclick='msg_image($ssl, $kk)'></video>";
							else
								$value .= "<img src='$s' data-toggle='modal' data-target='#med_modal$ssl' onclick='msg_image($ssl, $kk)' class='img-responsive sent_img half'>";
						}
						else if($num == 3){
							if(strpos($s, ".mp4") || strpos($s, ".MOV"))
								$value .= "<video class='sent_img third' src='$s' data-toggle='modal' data-target='#med_modal$ssl' onclick='msg_image($ssl, $kk)'></video>";
							else
								$value .= "<img src='$s' data-toggle='modal' data-target='#med_modal$ssl' onclick='msg_image($ssl, $kk)' class='img-responsive sent_img third'>";
						}
						else if($num == 4){
							if(strpos($s, ".mp4") || strpos($s, ".MOV"))
								$value .= "<video class='sent_img half' src='$s' data-toggle='modal' data-target='#med_modal$ssl' onclick='msg_image($ssl, $kk)'></video>";
							else
								$value .= "<img class='sent_img half' src='$s' data-toggle='modal' data-target='#med_modal$ssl' onclick='msg_image($ssl, $kk)'>";
						}
						else {
							if($kk <= 3){
								if($kk == 3)
									$spare = "<div style='font-size: 300%; color: antiquewhite; height: 100%; text-align: center; margin-top: -100;'>+".($num - 4)."</div>";
								else 
									$spare = "";
								if(strpos($s, ".mp4") || strpos($s, ".MOV"))
									$value .= "<span class='sent_img half'><video style='width: 100%; height: 100%;' src='$s' data-toggle='modal' data-target='#med_modal$ssl' onclick='msg_image($ssl, $kk)'></video>$spare</span>";
								else
									$value .= "<span class='sent_img half'><img style='width: 100%; height: 100%;' src='$s' data-toggle='modal' data-target='#med_modal$ssl' onclick='msg_image($ssl, $kk)'>$spare</span>";
							}
						}
					}
					
					$body_array[$key] = $value;
					$style = "style='width: 100%;'";

				}

			}
			$body = implode(" ", $body_array);

			$reply_query = mysqli_query($this->con, "SELECT * FROM messages WHERE id='$idd'");
			$rep = mysqli_fetch_array($reply_query);
			$msg = $rep['body'];
			$body_arrays = preg_split("/\s+/", $msg);
			foreach($body_arrays as $key => $value) {
				if(strpos($value, "status.php.??.id=") !== false) {
					$value = "";
					$body_arrays[$key] = $value;
				}
				if(strpos($value, "reply.php.??.id=") !== false) {
					$value = "";
					$body_arrays[$key] = $value;
				}
				if(strpos($value, "post.php?id=") !== false){
					$value = "Forwarded Post";
					$body_arrays[$key] = $value;
				}
				if(strpos($value, "media.php.??.") !== false){
					if(strpos($value, ".mp4"))
						$value = "<span class='fa fa-video-camera'></span> Video: ";
					else
						$value = "<span class='fa fa-camera'></span> Photo: ";
					$body_arrays[$key] = $value;
				}
				if(strpos($value, "invite.php..??..id=") !== false) {
					$value = "Group Invite";
					$body_arrays[$key] = $value;
				}
			}
			$msg = implode(" ", $body_arrays);

			$rep_obj = new User($this->con, $rep['user_from']);
			$names = $rep_obj->getFirstAndLastName();
			if($rep['user_from'] == $userLoggedIn)
				$head = "Your";
			else
				$head = $names;

			$star_query2 = mysqli_query($this->con, "SELECT id FROM starred_messages WHERE msg_id='$idd' AND username='$userLoggedIn'");
			if(mysqli_num_rows($star_query2)){
				$star = "<span class='fa fa-star' style='font-size:140%; color:gold; padding-top:3;'></span>&nbsp;&nbsp;&nbsp;";
				$to_star = "<li>
								<a href='#' id='unstar$idd' class='transition'>Unstar</a>
							</li>";
			}
			else{
				$star = "";
				$to_star = "<li>
								<a href='#' id='star$idd' class='transition'>Star</a>
							</li>";
			}

			$reply = "<div class='modal fade' id='reply_modal$idd' tabindex='-1' role='dialog'>
						<div class='modal-dialog' role='document'>
							<div class='modal-content'>

								<div class='modal-header'>
									<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
									<h4 class='modal-title' id='postModalLabel'>Reply to $head message</h4>
								</div>
						
								<div class='modal-body'>
									<p class='left_align'>$msg</p>
						
									<form class='profile_post' method='POST'>
										<div class='form-group'>
											<textarea class='form-control' name='msg_body' id='reply_textarea$idd'></textarea>
											<input type='hidden' name='msg_id' value='$idd' id='reply_id$idd'>
										</div>
										<div class='right_align'>
											<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
											<input type='button' onclick='uploaddFile$idd($idd)' class='btn btn-primary' name='msg_button' value='Send'>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<script>
						var userLoggedIn = '$userLoggedIn';
						var profileUsername = '$otherUser';
						function uploaddFile$idd(id){
							var text$idd = _('reply_textarea$idd').value;
							formdata.append('username', profileUsername);
							formdata.append('userLoggedIn', userLoggedIn);
							formdata.append('text', text$idd);
							formdata.append('id', id);
							if(text$idd){
								ajax.open('POST', 'includes/form_handlers/uploadReplyMessage.php');
								ajax.send(formdata);
							}
							_('reply_textarea$idd').value = '';
						}
					</script>";

			if($user_from == $userLoggedIn)
				$msg_info = "<li>
								<a href='#' onclick='infolise($idd)' class='transition'>Info</a>
							</li>";
			else
				$msg_info = "";

			$col = "<a href='#dd$idd' class='transition collapsed' role='button' data-toggle='collapse' aria-expanded='false' aria-controls='dd$idd'>";

			$ssss = "<ul class='list-unstyled msg_dd collapse' id='dd$idd'>
						<li>
							<a href='#' class='transition' data-toggle='modal' data-target='#reply_modal$idd'<br>Reply</a>$reply
						</li>
						<li>
							<a href='#' id='delete$idd' class='transition'>Delete</a>
						</li>
						<li>
							<a href='#' onclick='forward($idd)' class='transition'>Forward</a>
						</li>
						$msg_info
						$to_star
					</ul>";
			
			if($is_deleted == 'no'){
				if(strpos($body, "ACK_G_MESSAGE..??..") !== false)
					$to_add = "was added";
				if(strpos($body, "ACK_GR_MESSAGE..??..") !== false)
					$to_add = "was removed";
				if(strpos($body, "ACK_G_MESSAGE..??..") === false && strpos($body, "ACK_GR_MESSAGE..??..") === false){
					$div_top = ($user_from != $userLoggedIn) ? "<div id='msg$idd' class='msag'>$dyea<div class='message row' id='green' $style>$by_who" : "<div id='msg$idd' class='msag' style='text-align: right;'>$dyea<div class='message row' id='blue' $style>";
					$data = $data .$div_top . $body . "&nbsp;&nbsp;&nbsp;&nbsp; $col<span id='time$idd' class='time ad_timez'>" .$star.$time_message. "&nbsp;&nbsp;$picz</span></a> $ssss</div></div>";
					$data.="<div class='modal fade' id='med_modal$ssl' tabindex='-1' role='dialog' aria-labelledby='postModalLabel' aria-hidden='true' style='overflow: hidden; padding: 0 !important;'>
								<div class='modal-dialog' style='position: static; margin: 0; width: 100%'>
									<div class='modal-content' style='height: 100%; border-radius: 0; background-color: var(--bgc);'>
										<div class='modal$ssl'></div>
										<button type='button' class='btn btn-default modal_close_btn' data-dismiss='modal' onclick='pause()'>Close</button>
									</div>
								</div>
							</div>";
				}
				else{
					$sdf = substr($otherUser, 14);
					$hed = mysqli_query($this->con, "SELECT group_info FROM group_chats WHERE id='$sdf'");
					$ni = mysqli_fetch_array($hed);
					$kk = $ni['group_info'];
					$kk = substr($kk, 14);
					$data .= "<div class='center-blocks msag'><div class='sp_msg'><span>@$user_from $to_add</span></div></div>";
				}
			}
			else{
				$div_top = ($user_from != $userLoggedIn) ? "<div id='msg$idd' class='msag'><div class='message row' id='green'>" : "<div id='msg$idd' class='msag' style='text-align: right;'><div class='message row' id='blue'>";
				$data = $data . $div_top . "<span class='fa fa-ban grey'></span> <span class='grey'>This message has been deleted</span></a></div></div>";
			}
			?>	
				<script>
					$(document).ready(function() {
						$('#delete<?php echo $idd; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to delete this message?", function(result) {

								$.post("includes/form_handlers/delete_message.php?id=<?php echo $idd; ?>", {result:result});

								if(result)
									location.reload();

							});
						});
						$('#star<?php echo $idd; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to star this message?", function(result) {

								$.post("includes/form_handlers/star_message.php?id=<?php echo $idd; ?>&username=<?php echo $userLoggedIn; ?>", {result:result});

								if(result)
									location.reload();

							});
						});
						$('#unstar<?php echo $idd; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to unstar this message?", function(result) {

								$.post("includes/form_handlers/unstar_message.php?id=<?php echo $idd; ?>&username=<?php echo $userLoggedIn; ?>", {result:result});

								if(result)
									location.reload();

							});
						});
					});

					var sound = new Audio();
					sound.src = "button_click.mp3";
				</script>					

			<?php	
		}

		echo $data;
	}

	public function getSingleMessage($id, $cont, $ak){
		$userLoggedIn = $this->user_obj->getUsername();
		$data = $bg = "";
		$get_messages_query = mysqli_query($this->con, "SELECT * FROM messages WHERE id='$id'");

		$row = mysqli_fetch_array($get_messages_query);
		$user_to = $otherUser = $row['user_to'];
		$group_name = substr($otherUser, 14);
		$user_from = $row['user_from'];

		$pes_obj = new User($this->con, $user_from);		
		$pes_obj2 = new User($this->con, $user_to);		
		if($user_from == $userLoggedIn){
			$adi = "text-align: right;";
			$pes_name = "You";
		}
		else{
			$adi = "";
			$pes_name = $pes_obj->getFirstAndLastName();
		}
		if(strpos($user_to, "ACK_GROUP..??.") === false)
			$to_name = ($user_to == $userLoggedIn) ? "You" : $pes_obj2->getFirstAndLastName();
		else{
			$dd = substr($user_to, 14);
			$df = mysqli_query($this->con, "SELECT group_info FROM group_chats WHERE id='$dd'");
			$dff = mysqli_fetch_array($df);
			$g_name = $dff['group_info'];
			$to_name = substr($g_name, 14);
		}

		$pes_pic = $pes_obj->getProfilePic();

		if($ak == "show"){
			$data .= "<div style='padding:10; $adi'><img src='$pes_pic' style='width: 3em;'> $pes_name <span class='fa fa-share'></span> $to_name</div>";
		}

		if(strpos($otherUser, "ACK_GROUP..??.") !== false)
			$by_who = "<div class='g_name'>@$user_from</div>";
		else
			$by_who = "";

		$body = $row['body'];
		$date = $row['date'];
		$idd = $row['id'];
		// if(strpos($otherUser, "ACK_GROUP..??.") === false)	
		// 	$query = mysqli_query($this->con, "INSERT INTO group_views VALUES(NULL, '$group_name', '$userLoggedIn', '$idd')");
		$is_deleted = $row['deleted'];
		$sec = substr($date, 17);
		$min = substr($date, 14, -3);
		$hour = substr($date, 11, -6);
		$day = substr($date, 8, -9);
		$month = substr($date, 5, -12);
		$year = substr($date, 0, -15);
		$nice = mktime($hour, $min, $sec, $month, $day, $year);
		$formedd = date("g:i A", $nice);
		$formed = date("l g:i A", $nice);
		$forme = date("D, j M, g:i A", $nice);
		$form = date("D, j M, g:i A Y", $nice);
		$forr = date("Y-m-d", $nice);

		$body_array = preg_split("/\s+/", $body);

		foreach($body_array as $key => $value) {

			if(strpos($value, "www.") !== false) {

				$value = "<a target='_blank' class='forward_link' href='" . $value ."'>$value</a>";
				$body_array[$key] = $value;

			}

			if(strpos($value, "post.php?id=") !== false) {

				$value = "<a class='forward_link' href='" . $value ."'>Forwarded Post</a>";
				$body_array[$key] = $value;

			}
			
			if(strpos($value, "status.php.??.id=") !== false) {

				$bg = ($user_to == $userLoggedIn) ? "var(--sblue)" : "var(--sgreen)";
				$co = ($user_to == $userLoggedIn) ? "black" : "var(--color)";
				?>
				<style>
					#status_forward<?php echo $cont; ?>{
						background-color: <?php echo $bg; ?>;
						color: <?php echo $co; ?>;
					}
				</style>

				<?php
				$dd = substr($value, 17);
				$retrieve_query = mysqli_query($this->con, "SELECT * FROM statuses WHERE id='$dd'");
				$stuff = mysqli_fetch_array($retrieve_query);
				$text = $stuff['text'];
				$name = $stuff['username'];
				$del = $stuff['deleted'];	
				$media = $stuff['images'];
				if($media){
					if($text){
						$stuf = explode(".", $stuff['images']);		
						if($stuf[count($stuf)-1] == "mp4")
							$image = "";
						else
							$image = "<div class='status_forwarded_img col-xs-2'><img src=$media></div>";
						if($del == 'no')
							if($image)
								$value = "<a href='status.php?name=$name&id=$dd'><div class='status_forwarded' id='status_forward$cont'><div class='col-xs-10'><span class='fa fa-camera status_icon'></span> $text</div>$image</div></a>";
							else
								$value = "<a href='status.php?name=$name&id=$dd'><div class='status_forwarded' id='status_forward$cont'><div class='col-xs-12 text_pad'><span class='fa fa-video-camera'></span> $text</div>$image</div></a>";
						else
							if($image)
								$value = "<div class='status_forwarded' id='status_forward$cont'><div class='col-xs-10'><span class='fa fa-camera status_icon'></span> $text</div>$image</div>";
							else
								$value = "<div class='status_forwarded' id='status_forward$cont'><div class='col-xs-12 text_pad'><span class='fa fa-video-camera'></span> $text</div>$image</div>";
					}
					else{
						$stuf = explode(".", $stuff['images']);	
						if($stuf[count($stuf)-1] == "mp4")
							$image = "";
						else
							$image = "<div class='status_forwarded_img col-xs-3'><img src=$media></div>";	
						if($del == 'no')
							if($image)
								$value = "<a href='status.php?name=$name&id=$dd'><div class='status_forwarded' id='status_forward$cont'><div class='col-xs-9'><span class='fa fa-camera status_icon'></span> Photo</div>$image</div></a>";
							else
								$value = "<a href='status.php?name=$name&id=$dd'><div class='status_forwarded' id='status_forward$cont'><div class='col-xs-12 text_pad'><span class='fa fa-video-camera'></span> Video</div>$image</div></a>";
						else
							if($image)
								$value = "<div class='status_forwarded' id='status_forward$cont'><div class='col-xs-9'><span class='fa fa-camera status_icon'></span> Photo</div>$image</div>";
							else
								$value = "<div class='status_forwarded' id='status_forward$cont'><div class='col-xs-12 text_pad'><span class='fa fa-video-camera'></span> Video</div>$image</div>";
					}						
				}
				else{
					if($del == 'no')
						$value = "<a href='status.php?name=$name&id=$dd'><div class='status_forwarded' id='status_forward$cont'><div class='col-xs-12 text_pad'>$text</div></div></a>";
					else
						$value = "<div class='status_forwarded' id='status_forward$cont'><div class='col-xs-12 text_pad'>$text</div></div>";
				}

				$body_array[$key] = $value;

			}

			if(strpos($value, "reply.php.??.id=") !== false) {

				$bg = ($user_from != $userLoggedIn) ? "var(--sblue)" : "var(--sgreen)";
				$co = ($user_from != $userLoggedIn) ? "black" : "var(--color)";
				?>
				<style>
					#status_forwardd<?php echo $cont; ?>{
						background-color: <?php echo $bg; ?>;
						color: <?php echo $co; ?>;
					}
				</style>

				<?php
				$dd = substr($value, 16);
				$retrieve_query = mysqli_query($this->con, "SELECT * FROM messages WHERE id='$dd'");
				$stuff = mysqli_fetch_array($retrieve_query);
				$text = $stuff['body'];					
				$body_arrays = preg_split("/\s+/", $text);
				foreach($body_arrays as $k => $v){
					if(strpos($v, "status.php.??.id=") !== false) {
						$v = "";
						$body_arrays[$k] = $v;
					}	
					if(strpos($v, "reply.php.??.id=") !== false) {
						$v = "";
						$body_arrays[$k] = $v;
					}
					if(strpos($v, "post.php?id=") !== false) {
						$v = "Forwarded Post";
						$body_arrays[$k] = $v;
					}	
					if(strpos($v, "media.php.??.") !== false){
						$stuf = substr($v, 13, -1);
						if(strpos($stuf, ".mp4")){
							$v = "<span class='fa fa-video-camera status_icon' style='padding: 0;'></span>";
						}
						else{
							// $image = "<span class='sm_img'><img class='img-responsive' src=$stuf></span>";
							$v = "<span class='fa fa-camera status_icon' style='padding: 0;'></span>";
						}
						// $v = "<span class='fa fa-camera status_icon'></span>";
						$body_arrays[$k] = $v;
					}
				}
				$text = implode(" ", $body_arrays);

				$value = "<a href='messages.php?u=$user_to#msg$dd'><div class='status_forwarded' id='status_forwardd$cont'><div class='col-xs-12 text_pad'>$text</div></div></a>";	

				$body_array[$key] = $value;

			}

			if(strpos($value, "media.php.??.") !== false) {

				$dd = substr($value, 13, -1);
				$images = explode(",", $dd);
				$num = count($images);
				$value = "";
				foreach($images as $s){
					if($num == 1){
						if(strpos($s, ".mp4"))
							$value = "<video controls class='sent_img' src='$s' style='height:300;'></video>";
						else
							$value = "<img src='$s' class='img-responsive sent_img' style='height:300;'>";				
					}
					else if($num == 2){
						if(strpos($s, ".mp4"))
							$value = "<video controls class='sent_img' src='$s' style='height:300;'></video>";
						else
							$value .= "<img src='$s' class='img-responsive sent_img half' style='height:300;'>";
					}
					else if($num == 3){
						if(strpos($s, ".mp4"))
							$value = "<video controls class='sent_img' src='$s' style='height:300;'></video>";
						else
							$value .= "<img src='$s' class='img-responsive sent_img third' style='height:300;'>";
					}
					else{
						if(strpos($s, ".mp4"))
							$value = "<video controls class='sent_img' src='$s' style='height:300;'></video>";
						else
							$value .= "<img src='$s' class='img-responsive sent_img half' style='height:300;'>";
					}
				}
				
				$body_array[$key] = $value;

			}

		}
		$body = implode(" ", $body_array);

		$reply_query = mysqli_query($this->con, "SELECT * FROM messages WHERE id='$idd'");
		$rep = mysqli_fetch_array($reply_query);
		$msg = $rep['body'];
		$body_arrays = preg_split("/\s+/", $msg);
		foreach($body_arrays as $key => $value) {
			if(strpos($value, "status.php.??.id=") !== false) {
				$value = "";
				$body_arrays[$key] = $value;
			}
			if(strpos($value, "reply.php.??.id=") !== false) {
				$value = "";
				$body_arrays[$key] = $value;
			}
			if(strpos($value, "post.php?id=") !== false){
				$value = "Forwarded Post";
				$body_arrays[$key] = $value;
			}
			if(strpos($value, "media.php.??.") !== false){
				if(strpos($value, ".mp4"))
					$value = "<span class='fa fa-video-camera'></span> Video: ";
				else
					$value = "<span class='fa fa-camera'></span> Photo: ";
				$body_arrays[$key] = $value;
			}
		}
		$msg = implode(" ", $body_arrays);

		$star_query2 = mysqli_query($this->con, "SELECT username FROM starred_messages WHERE msg_id='$idd' AND username='$userLoggedIn'");
		if(mysqli_num_rows($star_query2)){
			$star = "<span class='fa fa-star' style='font-size:100%; color:gold;'></span>";
		}
		else{
			$star = "";
		}
		
		$data .= "<div class='center-blocks'><span class='sp_msg' style='padding: 7 20; font-size: 90%;'>$forr</span></div>";

		// $div_top = "<div class='msgidd'><div class='message row' id='blue' style='float: none; margin-left:0 !important; max-height:58.9vh;'>";
		$div_top = ($user_from != $userLoggedIn) ? "<div class='msgidd' style='text-align: left;'><div class='message row' style='float: none; margin-left:2% !important;' id='green'>" : "<div class='msgidd' style='text-align: right;'><div class='message row' style='float: none; margin-left:0 !important; max-height:58.9vh;' id='blue'>";
		$data = $data .$div_top . "<span class='no_align' style='width:auto;'>" . $body . "&nbsp;&nbsp;&nbsp;&nbsp;<span class='time ad_timez'>" .$star."&nbsp;".$formedd. "</span></span></a> </div></div>";

		return $data;
	}

	public function getLatestMessage($userLoggedIn, $user2) {
		$details_array = array();

		if(strpos($user2, "ACK_GROUP..??.") === false)
			$query = mysqli_query($this->con, "SELECT * FROM messages WHERE ((user_to='$userLoggedIn' AND user_from='$user2') OR (user_to='$user2' AND user_from='$userLoggedIn')) AND deleted='no' ORDER BY id DESC LIMIT 1");
		else
			$query = mysqli_query($this->con, "SELECT * FROM messages WHERE user_to='$user2' AND deleted='no' ORDER BY id DESC LIMIT 1");

		$row = mysqli_fetch_array($query);
		if($row['viewed'] == 'no' && $row['opened'] == 'no')
			$check = "<span class='text-muted fa fa-check'></span>";
		if($row['viewed'] == 'yes')
			$check = "<span class='warning_msg fa fa-check'></span>";
		if($row['opened'] == 'yes')
			$check = "<span class= 'seen fa fa-check'></span>";				
		
		$fname_obj = new User($this->con, $row['user_from']);
		$fname = $fname_obj->getFirstName();
		if(strpos($user2, "ACK_GROUP") === false){
			$sent_by = ($row['user_to'] == $userLoggedIn) ? "" : $check. " ";
		}
		else
			$sent_by = ($row['user_from'] == $userLoggedIn) ? "" : $fname. ": ";

		$ide = $row['id'];
		$black = mysqli_query($this->con, "SELECT id FROM blacklist WHERE username='$userLoggedIn' AND message='$ide'");
		if(mysqli_num_rows($black))
			$bodd = "";
		else
			$bodd = $row['body'];

		//Timeframe
		$date_time_now = date("Y-m-d H:i:s");
		$start_date = new DateTime($row['date']); //Time of post
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

		if($sent_by)
			array_push($details_array, $sent_by);
		else
			array_push($details_array, "");
		// if($row['body'])
		array_push($details_array, $bodd);
		// else
		// 	array_push($details_array, "");
		if($time_message)
			array_push($details_array, $time_message);
		else
			array_push($details_array, "");			
		array_push($details_array, $fname);

		return $details_array;
	}

	public function getConvos() {
		$userLoggedIn = $this->user_obj->getUsername();
		$return_string = "";
		$convos = array();

		$asdd=$gn=$gna= array();
		$g_query = mysqli_query($this->con, "SELECT group_info, users FROM group_chats");
		if(mysqli_num_rows($g_query)){
			while($rr = mysqli_fetch_array($g_query)){        
				array_push($asdd, $rr['users']);
				array_push($gn, $rr['group_info']);
			}
			for($i=0; $i<count($gn); $i++){
				$pep = explode(",", $asdd[$i]);
				foreach($pep as $pess)
					if($pess == $userLoggedIn)
						array_push($gna, $gn[$i]);
			}
		}
		$a = "";				
		// echo count($gna);
		foreach($gna as $group){
			$jff = mysqli_query($this->con, "SELECT id FROM group_chats WHERE group_info='$group'");
			$ffh = mysqli_fetch_array($jff);
			$r_ndam = $ffh['id'];
			$gg = "ACK_GROUP..??.$r_ndam";
			$a .= "OR user_to='$gg' ";
			// echo $group ."<br>";
		}

		$query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE (user_from='$userLoggedIn' OR user_to='$userLoggedIn' $a)  AND deleted='no' ORDER BY id DESC");
		while($row = mysqli_fetch_array($query)) {
			$user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];

			if(!in_array($user_to_push, $convos)) {
				array_push($convos, $user_to_push);
			}
		}
		
		// pinned chats
		$pinned = array();
		$pinned_query = mysqli_query($this->con, "SELECT * FROM pinned_chats WHERE username='$userLoggedIn' ORDER BY id DESC");
		while($roo = mysqli_fetch_array($pinned_query)){
			$pees = $roo['user_pinned'];
			array_push($pinned, $pees);
			$black = mysqli_query($this->con, "SELECT * FROM blacklist WHERE username='$userLoggedIn' AND chat='$pees'");
			if(mysqli_num_rows($black))
				continue;
			if(strpos($pees, "ACK_GROUP..??.") === false){
				$notifications_query = mysqli_query($this->con, "SELECT user_to, user_from, opened FROM messages WHERE user_to='$userLoggedIn' AND user_from='$pees' AND opened='no' AND deleted='no'");
				$unopened = mysqli_num_rows($notifications_query);
				if($unopened)
					$unopened = "<span class='unopened'><b style='color: white !important;'>$unopened</b></span>";
				else
					$unopened = "";
			}
			else{
				$idz = array();
				$num = 0;				
				$ensure = mysqli_query($this->con, "SELECT id FROM messages WHERE user_to='$pees' AND user_from='$userLoggedIn' AND body='ACK_G_MESSAGE..??..'");
				if(mysqli_num_rows($ensure)){
					$eh = mysqli_fetch_array($ensure);
					$iddd = $eh['id'];
					$lf = " AND id > $iddd";
				}
				else
					$lf = "";
				$notifications_query2 = mysqli_query($this->con, "SELECT id FROM messages WHERE user_to='$pees' $lf AND body!='ACK_G_MESSAGE..??..' AND body!='ACK_GR_MESSAGE..??..' AND user_from!='$userLoggedIn' AND deleted='no'");
				while($rows = mysqli_fetch_array($notifications_query2)){
					array_push($idz, $rows['id']);
				}
				foreach($idz as $idd){
					$notifications_query = mysqli_query($this->con, "SELECT * FROM message_views WHERE message_id='$idd' AND username='$userLoggedIn' AND date_viewed!='0000-00-00 00:00:00'");
					if(mysqli_num_rows($notifications_query))
						$num ++;
				}
				$unopened = count($idz) - $num;
				if($unopened > 0)
					$unopened = "<span class='unopened'><b style='color: white !important;'>$unopened</b></span>";
				else
					$unopened = "";
				$r_name = substr($pees, 14);
			}

			$latest_message_details = $this->getLatestMessage($userLoggedIn, $pees);
			
			$body = $latest_message_details[1];
			$body = str_replace('<br />', " ", $body);
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

				if(strpos($value, "invite.php..??..id=") !== false) {
					$value = "Group Invite";
					$body_array[$key] = $value;
				}

				if((strpos($value, "status.php.??.id=") !== false) || (strpos($value, "reply.php.??.id=") !== false)){

					$value = "";
					$body_array[$key] = $value;

				}

				if(strpos($value, "ACK_G_MESSAGE..??..") !== false){
					$name = substr($pees, 14);
					$value = $latest_message_details[3]." just joined ". $name;
					$body_array[$key] = $value;
				}

				if(strpos($value, "ACK_GR_MESSAGE..??..") !== false){
					$name = substr($pees, 14);
					$value = $latest_message_details[3]." was removed from ". $name;
					$body_array[$key] = $value;
				}

			}
			$body = implode(" ", $body_array);

			if(strpos($body, "media.php.??.") === false){
				$dots = (strlen($body) >= 40) ? "..." : "";
				$split = str_split($body, 40);
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

			if(strpos($pees, "ACK_GROUP..??.") === false){
				$user_found_obj = new User($this->con, $pees);	
				$typing_query = mysqli_query($this->con, "SELECT * FROM typing WHERE user_to='$userLoggedIn' AND username='$pees' AND typing='yes'");
				if(mysqli_num_rows($typing_query)){
					$comp = "<span class='text-success'>typing...</span>";			
				}
				else{
					$comp = $latest_message_details[0] . $split;
				}

				$return_string .= "<a href='messages.php?u=$pees' id='link'> <div class='user_found_messages'>
									<img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 50%; margin-right: 5px;'>
									<b><span style='color: rgb(236, 81, 81);'>" . $user_found_obj->getFirstAndLastName() . "</span></b>
									<center class='pull-right'><span class='time convos_time' id='grey' style='padding: 0; $col'> " . $latest_message_details[2] . "<br></span><span class='fa fa-thumb-tack' style='margin-top: 18; margin-right: -25; font-size: larger;'></span>$unopened</center>
									<p id='grey' style='margin: 0;'>" . $comp . " </p>
									</div>
									</a>";
			}
			else{
				$get_query = mysqli_query($this->con, "SELECT * FROM group_chats WHERE id='$r_name'");
				$rowsd = mysqli_fetch_array($get_query);
				$ima = $rowsd['group_pic'];
				if($ima == "")
					$ima = "assets/images/profile_pics/defaults/male.png";
				$nam = $rowsd['group_info'];
				$nam = substr($nam, 14);
				$typing_query = mysqli_query($this->con, "SELECT * FROM typing WHERE user_to='$pees' AND username!='$userLoggedIn' AND typing='yes'");
				if(mysqli_num_rows($typing_query)){
					$row = mysqli_fetch_array($typing_query);
					$typist = $row['username'];
					$comp = "<span class='text-success'>$typist is typing...</span>";			
				}
				else{
					$comp = $latest_message_details[0] . $split;
				}
				$return_string .= "<a href='messages.php?u=$pees' id='link'> <div class='user_found_messages'>
									<img src='" . $ima . "' style='border-radius: 50%; margin-right: 5px;'>
									<b><span style='color: rgb(236, 81, 81);'>" . $nam . "</span></b>
									<center class='pull-right'><span class='time convos_time' id='grey' style='padding: 0; $col'> " . $latest_message_details[2] . "<br></span><span class='fa fa-thumb-tack' style='margin-top: 18; margin-right: -25; font-size: larger;'></span>$unopened</center>
									<p id='grey' style='margin: 0; width:75%'>" . $comp . " </p>
									</div>
									</a>";
			}
		}

		for($i=0; $i<count($convos); $i++) {
			// echo $convos[$i];
			if(in_array($convos[$i], $pinned))
				continue;
			$black = mysqli_query($this->con, "SELECT * FROM blacklist WHERE username='$userLoggedIn' AND chat='$convos[$i]'");
			if(mysqli_num_rows($black))
				continue;
			if(strpos($convos[$i], "ACK_GROUP..??.") === false){
				$notifications_query = mysqli_query($this->con, "SELECT user_to, user_from, opened FROM messages WHERE user_to='$userLoggedIn' AND user_from='$convos[$i]' AND opened='no' AND deleted='no'");
				$unopened = mysqli_num_rows($notifications_query);
				if($unopened)
					$unopened = "<span class='unopened'><b style='color: white !important;'>$unopened</b></span>";
				else
					$unopened = "";
			}
			else{
				// $rg = mysqli_query($this->con, "SELECT id FROM group_chats WHERE group_info='$convos[$i]'");
				// $ffe = mysqli_fetch_array($rg);
				// echo $convos[$i];
				// $gr = $ffe['id'];
				// $gg = "ACK_GROUP..??.$gr";
				$idz = array();
				$num = 0;				
				$ensure = mysqli_query($this->con, "SELECT id FROM messages WHERE user_to='$convos[$i]' AND user_from='$userLoggedIn' AND body='ACK_G_MESSAGE..??..'");
				if(mysqli_num_rows($ensure)){
					$eh = mysqli_fetch_array($ensure);
					$iddd = $eh['id'];
					$lf = " AND id > $iddd";
				}
				else
					$lf = "";
				$notifications_query2 = mysqli_query($this->con, "SELECT id FROM messages WHERE user_to='$convos[$i]' $lf AND body!='ACK_G_MESSAGE..??..' AND body!='ACK_GR_MESSAGE..??..' AND user_from!='$userLoggedIn' AND deleted='no'");
				while($rows = mysqli_fetch_array($notifications_query2)){
					array_push($idz, $rows['id']);
				}
				foreach($idz as $idd){
					$notifications_query = mysqli_query($this->con, "SELECT * FROM message_views WHERE message_id='$idd' AND username='$userLoggedIn' AND date_viewed!='0000-00-00 00:00:00'");
					if(mysqli_num_rows($notifications_query))
						$num ++;
				}
				$unopened = count($idz) - $num;
				if($unopened > 0)
					$unopened = "<span class='unopened'><b style='color: white !important;'>$unopened</b></span>";
				else
					$unopened = "";
				$r_name = substr($convos[$i], 14);
				// $jf = mysqli_query($this->con, "SELECT group_info FROM group_chats WHERE id='$r_name'");
				// $fh = mysqli_fetch_array($jf);
				// $r_nam = $fh['group_info'];
				// echo $r_name;
			}

			$latest_message_details = $this->getLatestMessage($userLoggedIn, $convos[$i]);
			
			$body = $latest_message_details[1];
			$body = str_replace('<br />', " ", $body);
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

				if(strpos($value, "invite.php..??..id=") !== false) {
					$value = "Group Invite";
					$body_array[$key] = $value;
				}

				if((strpos($value, "status.php.??.id=") !== false) || (strpos($value, "reply.php.??.id=") !== false)){

					$value = "";
					$body_array[$key] = $value;

				}

				if(strpos($value, "ACK_G_MESSAGE..??..") !== false){
					$name = substr($convos[$i], 14);
					$value = $latest_message_details[3]." just joined ". $name;
					$body_array[$key] = $value;
				}

				if(strpos($value, "ACK_GR_MESSAGE..??..") !== false){
					$name = substr($convos[$i], 14);
					$value = $latest_message_details[3]." was removed from ". $name;
					$body_array[$key] = $value;
				}

			}
			$body = implode(" ", $body_array);

			if(strpos($body, "media.php.??.") === false){
				$dots = (strlen($body) >= 40) ? "..." : "";
				$split = str_split($body, 40);
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

			if(strpos($convos[$i], "ACK_GROUP..??.") === false){
				$user_found_obj = new User($this->con, $convos[$i]);	
				$typing_query = mysqli_query($this->con, "SELECT * FROM typing WHERE user_to='$userLoggedIn' AND username='$convos[$i]' AND typing='yes'");
				if(mysqli_num_rows($typing_query)){
					$comp = "<span class='text-success'>typing...</span>";			
				}
				else{
					$comp = $latest_message_details[0] . $split;
				}

				$return_string .= "<a href='messages.php?u=$convos[$i]' id='link'> <div class='user_found_messages'>
									<img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 50%; margin-right: 5px;'>
									<b><span style='color: rgb(236, 81, 81);'>" . $user_found_obj->getFirstAndLastName() . "</span></b>
									<center class='pull-right'><span class='time convos_time' id='grey' style='padding: 0; $col'> " . $latest_message_details[2] . "<br></span>$unopened</center>
									<p id='grey' style='margin: 0;'>" . $comp . " </p>
									</div>
									</a>";
			}
			else{
				$get_query = mysqli_query($this->con, "SELECT * FROM group_chats WHERE id='$r_name'");
				$rowsd = mysqli_fetch_array($get_query);
				$ima = $rowsd['group_pic'];
				if($ima == "")
					$ima = "assets/images/profile_pics/defaults/male.png";
				$nam = $rowsd['group_info'];
				$nam = substr($nam, 14);
				$typing_query = mysqli_query($this->con, "SELECT * FROM typing WHERE user_to='$convos[$i]' AND username!='$userLoggedIn' AND typing='yes'");
				if(mysqli_num_rows($typing_query)){
					$row = mysqli_fetch_array($typing_query);
					$typist = $row['username'];
					$comp = "<span class='text-success'>$typist is typing...</span>";			
				}
				else{
					$comp = $latest_message_details[0] . $split;
				}
				$return_string .= "<a href='messages.php?u=$convos[$i]' id='link'> <div class='user_found_messages'>
									<img src='" . $ima . "' style='border-radius: 50%; margin-right: 5px;'>
									<b><span style='color: rgb(236, 81, 81);'>" . $nam . "</span></b>
									<center class='pull-right'><span class='time convos_time' id='grey' style='padding: 0; $col'> " . $latest_message_details[2] . "<br></span>$unopened</center>
									<p id='grey' style='margin: 0; width:75%'>" . $comp . " </p>
									</div>
									</a>";
			}
			
		}
		return $return_string;
	}

	public function getConvosDropdown($data, $limit) {

		$page = $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();
		$return_string = "";
		$convos = $idz = array();

		if($page == 1)
			$start = 0;
		else 
			$start = ($page - 1) * $limit;

		$set_viewed_query = mysqli_query($this->con, "UPDATE messages SET viewed='yes' WHERE user_to='$userLoggedIn'");

		$asdd=$gn=$gna= array();
		$g_query = mysqli_query($this->con, "SELECT group_info, users FROM group_chats");
		if(mysqli_num_rows($g_query)){
			while($rr = mysqli_fetch_array($g_query)){        
				array_push($asdd, $rr['users']);
				array_push($gn, $rr['group_info']);
			}
			for($i=0; $i<count($gn); $i++){
				$pep = explode(",", $asdd[$i]);
				foreach($pep as $pess)
					if($pess == $userLoggedIn)
						array_push($gna, $gn[$i]);
			}
		}
		$a = "";		
		foreach($gna as $group){
			$jff = mysqli_query($this->con, "SELECT id FROM group_chats WHERE group_info='$group'");
			$ffh = mysqli_fetch_array($jff);
			$r_ndam = $ffh['id'];
			$gg = "ACK_GROUP..??.$r_ndam";
			$a .= "OR user_to='$gg' ";
		}

		$query = mysqli_query($this->con, "SELECT user_to, user_from, id FROM messages WHERE (user_from='$userLoggedIn' OR user_to='$userLoggedIn' $a)  AND deleted='no' ORDER BY id DESC");

		while($row = mysqli_fetch_array($query)) {
			$user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];

			if(!in_array($user_to_push, $convos)) {
				array_push($convos, $user_to_push);
			}
			array_push($idz, $row['id']);
		}

		$num_iterations = 0; //Number of messages checked 
		$count = 1; //Number of messages posted

		for($i=0; $i<count($convos); $i++) {

			if($num_iterations++ < $start)
				continue;

			if($count > $limit)
				break;
			else 
				$count++;

			$black = mysqli_query($this->con, "SELECT * FROM blacklist WHERE username='$userLoggedIn' AND chat='$convos[$i]'");
			if(mysqli_num_rows($black))
				continue;

			if(strpos($convos[$i], "ACK_GROUP..??.") === false){
				$is_unread_query = mysqli_query($this->con, "SELECT opened FROM messages WHERE user_to='$userLoggedIn' AND user_from='$convos[$i]' ORDER BY id DESC");
				$row = mysqli_fetch_array($is_unread_query);
				$style = (isset($row['opened']) && $row['opened'] == 'no') ? "background-color: darkred; color:white;" : "";
				$style2 = (isset($row['opened']) && $row['opened'] == 'no') ? "" : "id='grey'";
			}
			else{
				$aas = mysqli_query($this->con, "SELECT id FROM messages WHERE user_to='$convos[$i]' ORDER BY id DESC LIMIT 1");
				$asdf = mysqli_fetch_array($aas);
				$iddd = $asdf['id'];
				// echo $iddd;
				$is_posted_query = mysqli_query($this->con, "SELECT user_from FROM messages WHERE id='$iddd'");
				$hhh = mysqli_fetch_array($is_posted_query);
				if($hhh['user_from'] != $userLoggedIn){
					$is_unread_query = mysqli_query($this->con, "SELECT * FROM message_views WHERE message_id='$iddd' AND username='$userLoggedIn' AND date_viewed!='0000-00-00 00:00:00'");
					$style = (mysqli_num_rows($is_unread_query) == 0) ? "background-color: darkred; color:white;" : "";
					$style2 = (mysqli_num_rows($is_unread_query) == 0) ? "" : "id='grey'";
				}
				else
					$style = "";
					$style2 = "";
				$r_name = substr($convos[$i], 14);
			}

			$user_found_obj = new User($this->con, $convos[$i]);
			$latest_message_details = $this->getLatestMessage($userLoggedIn, $convos[$i]);

			$body = $latest_message_details[1];
			$body = str_replace('<br />', " ", $body);
			$body_array = preg_split("/\s+/", $body);

			foreach($body_array as $key => $value) {

				if(strpos($value, "post.php?id=") !== false) {

					$value = "Forwarded Post";
					$body_array[$key] = $value;

				}

				if((strpos($value, "status.php.??.id=") !== false) || (strpos($value, "reply.php.??.id=") !== false)){

					$value = "";
					$body_array[$key] = $value;

				}

				if(strpos($value, "invite.php..??..id=") !== false) {
					$value = "Group Invite";
					$body_array[$key] = $value;
				}

				if(strpos($value, "ACK_G_MESSAGE..??..") !== false){
					$name = substr($convos[$i], 14);
					$value = $latest_message_details[3]." just joined ". $name;
					$body_array[$key] = $value;

				}

				if(strpos($value, "ACK_GR_MESSAGE..??..") !== false){
					$name = substr($convos[$i], 14);
					$value = $latest_message_details[3]." was removed from ". $name;
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

			if(strpos($convos[$i], "ACK_GROUP..??.") === false){
				$user_found_obj = new User($this->con, $convos[$i]);			

				$return_string .= "<a href='messages.php?u=$convos[$i]'> 
									<div class='user_found_messages' style='" . $style . "'>
									<img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 5px; margin-right: 5px;'>
									<b><span style='color: rgb(236, 81, 81);'>" . $user_found_obj->getFirstAndLastName() . "</span></b>
									<span class='timestamp_smaller' $style2> " . $latest_message_details[2] . "</span>
									<p $style2>" . $latest_message_details[0] . $split . " </p>
									</div>
									</a>";
			}
			else{
				$get_query = mysqli_query($this->con, "SELECT * FROM group_chats WHERE id='$r_name'");
				$rowsd = mysqli_fetch_array($get_query);
				$ima = $rowsd['group_pic'];
				if($ima == "")
					$ima = "assets/images/profile_pics/defaults/male.png";
				$nam = $rowsd['group_info'];
				$nam = substr($nam, 14);
				$return_string .= "<a href='messages.php?u=$convos[$i]'> 
									<div class='user_found_messages' style='" . $style . "'>
									<img src='" . $ima . "' style='border-radius: 5px; margin-right: 5px;'>
									<b><span style='color: rgb(236, 81, 81);'>" . $nam . "</span></b>
									<span class='timestamp_smaller' $style2> " . $latest_message_details[2] . "</span>
									<p $style2>" . $latest_message_details[0] . $split . " </p>
									</div>
									</a>";
			}
		}


		//If posts were loaded
		if($count > $limit)
			$return_string .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='false'>";
		else 
			$return_string .= "<input type='hidden' class='noMoreDropdownData' value='true'> <p class='red-bg' style='text-align: center;'>No more messages to load!</p>";

		return $return_string;
	}

	public function getUnreadNumber() {
		$userLoggedIn = $this->user_obj->getUsername();
		$query = mysqli_query($this->con, "SELECT * FROM messages WHERE opened='no' AND user_to='$userLoggedIn' AND deleted='no'");

		$asdd=$gn=$gna=$idz= array();
		$g_query = mysqli_query($this->con, "SELECT group_info, users FROM group_chats");
		if(mysqli_num_rows($g_query)){
			while($rr = mysqli_fetch_array($g_query)){        
				array_push($asdd, $rr['users']);
				array_push($gn, $rr['group_info']);
			}
			for($i=0; $i<count($gn); $i++){
				$pep = explode(",", $asdd[$i]);
				foreach($pep as $pess)
					if($pess == $userLoggedIn)
						array_push($gna, $gn[$i]);
			}
		}
		foreach($gna as $group){			
			$f = mysqli_query($this->con, "SELECT id FROM group_chats WHERE group_info='$group'");
			$ie = mysqli_fetch_array($f);
			$fll = $ie['id'];
			$gll = "ACK_GROUP..??.$fll";			
			$ensure = mysqli_query($this->con, "SELECT id FROM messages WHERE user_to='$gll' AND user_from='$userLoggedIn' AND (body='ACK_G_MESSAGE..??..')");
			if(mysqli_num_rows($ensure)){
				$eh = mysqli_fetch_array($ensure);
				$iddd = $eh['id'];
				$lf = " AND id > $iddd";
			}
			else
				$lf = "";
			$query2 = mysqli_query($this->con, "SELECT id FROM messages WHERE user_to='$gll' AND deleted='no' $lf AND (body!='ACK_G_MESSAGE..??..' AND body!='ACK_GR_MESSAGE..??..') AND user_from!='$userLoggedIn'");
			while($row2 = mysqli_fetch_array($query2)){
				array_push($idz, $row2['id']);
			}
		}
		$num = 0;
		foreach($idz as $id){
			$query3 = mysqli_query($this->con, "SELECT * FROM message_views WHERE message_id='$id' AND username='$userLoggedIn' AND date_viewed='0000-00-00 00:00:00'");
			if(mysqli_num_rows($query3))
				$num++;
		}

		return mysqli_num_rows($query) + $num;
	}
	
	public function createGroup($creator, $people, $pic, $info){
		$admins=$id=$inffo="";
		$date_time_now = date("Y-m-d H:i:s");
		$admins .= $creator. ",";
		$inffo .= "ACK_GROUP..??.". $info;
		$query = mysqli_query($this->con, "INSERT INTO group_chats VALUES(NULL, '$creator', '$admins', '$date_time_now', '$people', '$pic', '$inffo', '')");
		// $query = "INSERT INTO group_chats VALUES(?, ?, ?, ?, ?, ?, ?)";
		// $stmt = mysqli_stmt_init($this->con);
		// if(!mysqli_stmt_prepare($stmt, $query)){
		// 	echo "SQL ERROR";
		// }
		// else{
		// 	mysqli_stmt_bind_param($stmt, "issssss", $id, $creator, $admins, $date_time_now, $people, $pic, $inffo);
		// 	mysqli_stmt_execute($stmt);
		// }
		header("Location: messages.php?u=$inffo");
		$people = explode(",", $people);
		foreach($people as $person){
			$query2 = mysqli_query($this->con, "INSERT INTO messages VALUES(NULL, '$inffo', '$person', 'ACK_G_MESSAGE..??..', '$date_time_now', 'no', 'no', 'no')");
		}
	}

	public function addToGroup($people, $time, $group){
		$query = mysqli_query($this->con, "SELECT * FROM group_chats WHERE group_info='$group'");
		$row = mysqli_fetch_array($query);
		$others = $row['users'];
		$others .= $people. ",";
		$others = substr($others, 0, -1);
		$query2 = mysqli_query($this->con, "UPDATE group_chats SET users='$others' WHERE group_info='$group'");
		$people = explode(",", $people);
		for($i=0; $i<count($people)-1; $i++){
			$black = mysqli_query($this->con, "SELECT * FROM blacklist WHERE username='$people[$i]' AND chat='$group'");
			if(mysqli_num_rows($black))
				$de_black = mysqli_query($this->con, "DELETE FROM blacklist WHERE username='$people[$i]' AND chat='$group'");
			$query3 = mysqli_query($this->con, "INSERT INTO messages VALUES(NULL, '$group', '$people[$i]', 'ACK_G_MESSAGE..??..', '$time', 'no', 'no', 'no')");
		}
		header("Location: messages.php?u=$group");
	}

	public function linkToGroup($people, $time, $group){
		$userLoggedIn = $this->user_obj->getUsername();
		$query = mysqli_query($this->con, "SELECT id FROM group_chats WHERE group_info='$group'");
		$row = mysqli_fetch_array($query);
		$id = $row['id'];
		$people = substr($people, 0, -1);
		$people = explode(",", $people);
		for($i=0; $i<count($people); $i++){
			$query3 = mysqli_query($this->con, "INSERT INTO messages VALUES(NULL, '$people[$i]', '$userLoggedIn', 'invite.php..??..id=$id', '$time', 'no', 'no', 'no')");
		}
		header("Location: messages.php?u=$people[0]");
	}

}

?>
