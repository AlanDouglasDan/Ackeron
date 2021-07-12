<?php 
// include("includes/header.php");
require_once 'config/config.php';
require_once("includes/classes/User.php");
require_once("includes/classes/Post.php");
require_once("includes/classes/Message.php");
require_once("includes/classes/Notification.php");
require_once("includes/form_handlers/status_handler.php");
require_once("includes/form_handlers/level_handler.php");

if (isset($_SESSION['username'])) {
    $userLoggedIn = $_SESSION['username'];
    // $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
    // $user = mysqli_fetch_array($user_details_query);
}

?>
<html class='animate__animated animate__fadeInDownBig'>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Ackeron!</title>

    <!-- Javascript -->
    <script src="assets/js/jquery-3.5.1.min.js"></script>
    <script src="assets/js/aa.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/bootbox.min.js"></script>
    <script src="assets/js/swup.min.js"></script>
    <script src="assets/js/demo.js"></script>
    <script src="assets/js/jquery.jcrop.js"></script>
	<script src="assets/js/jcrop_bits.js"></script>
    <script src="croppie/croppie.js"></script>
    <!-- <script src="jquery_form.js"></script> -->

    <!-- CSS -->
    <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
    <!-- <link rel="stylesheet" type="text/css" href="assets/bootstrap/css/bootstrap.css"> -->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/jquery.Jcrop.css" type="text/css" />
    <link rel="stylesheet" href="croppie/croppie.css">
    <link rel="stylesheet" href="assets/css/animate.css">

</head>
<body>
<?php
$message_obj = new Message($con, $userLoggedIn);

if(isset($_GET['u'])){
	$user_to = $_GET['u'];	
	$send_to = $user_to;
	if(strpos($user_to, "ACK_GROUP..??.") === false && $user_to!='new'){
		$scam_Query = mysqli_query($con, "SELECT * FROM users WHERE username='$user_to'");
		if(mysqli_num_rows($scam_Query) == 0)
			header("Location: messages.php");
	}
	else if(strpos($user_to, "ACK_GROUP..??.") !== false){
		$e = substr($user_to, 14);
		$hg = mysqli_query($con, "SELECT group_info, users FROM group_chats WHERE id='$e'");
		if(mysqli_num_rows($hg) == 1){
			$uto = mysqli_fetch_array($hg);
			$user_to = $uto['group_info'];
			$send_to = "ACK_GROUP..??.$e";
			$uu = $uto['users'];
			$uu = substr($uu, 0, -1);
			$poe = explode(",", $uu);
			if(!in_array($userLoggedIn, $poe))
				header("Location: messages.php");
		}
		else{
			header("Location: messages.php");
		}
	}
	if(isset($_GET['add'])){
		if($_GET['add'] == "ACK_ALLOW_GROUP_JOIN"){
			$adfas = mysqli_query($con, "SELECT * FROM group_chats WHERE users LIKE '%$userLoggedIn,%' AND group_info='$user_to'");
			if(mysqli_num_rows($adfas) == 0){
				$date = date("Y-m-d H:i:s");		
				$adf = $userLoggedIn . ",";
				$query = mysqli_query($con, "SELECT * FROM group_chats WHERE group_info='$user_to'");
				$row = mysqli_fetch_array($query);
				$others = $row['users'];
				$others .= $adf;
				$date = date("Y-m-d H:i:s");
				$query2 = mysqli_query($con, "UPDATE group_chats SET users='$others' WHERE group_info='$user_to'");
				$black = mysqli_query($con, "SELECT * FROM blacklist WHERE username='$userLoggedIn' AND chat='$user_to'");
				if(mysqli_num_rows($black))
					$de_black = mysqli_query($con, "DELETE FROM blacklist WHERE username='$userLoggedIn' AND chat='$user_to'");
				header("Location: messages.php?u=$send_to");
				$message_obj->sendMessage($send_to, 'ACK_G_MESSAGE..??..', $date);
			}			
		}
	}
}
else 
	$user_to = "list";


if($user_to == $userLoggedIn)
	$user_to = 'list';

if(isset($_GET['forward'])){
	// $post_id = $_GET['forward'];
	// $date = date("Y-m-d H:i:s");
	// $body = "post.php?id=$post_id";
	// $message_obj->sendMessage($send_to, $body, $date);
}

if($user_to != "new" && $user_to != "list")
	if(strpos($user_to, "ACK_GROUP..??.") === false)
		$user_to_obj = new User($con, $user_to);


if(isset($_POST['tb_done'])){
	$peeps = $_POST['to_blast'];
	$body = mysqli_real_escape_string($con, $_POST['text_tb']);
	$date = date("Y-m-d H:i:s");
	if($peeps){
		foreach($peeps as $pes){
			$message_obj->sendMessage($pes, $body, $date);
		}
		header("Location: messages.php?u=" .$peeps[count($peeps)-1]);
	}
	else{
		header("Location: messages.php");
	}
}

// echo $send_to . "<br>" . $user_to;
// echo $e;

// if(isset($_POST['msg_button'])){
// 	$body = mysqli_real_escape_string($con, $_POST['msg_body']);
// 	if($body){
// 		$date = date("Y-m-d H:i:s");
// 		$msg_id = $_POST['msg_id'];
// 		$body = "reply.php.??.id=$msg_id $body";
// 		$message_obj->sendMessage($send_to, $body, $date);
// 	}
// 	header("Location: messages.php?u=$send_to");
// }SELECT * FROM `messages` WHERE user_to='ACK_GROUP..??.The Ackerites'

if(isset($_POST['change_gname'])){
	$new_nam = $_POST['group_info'];
	$new_name = "ACK_GROUP..??." . $new_nam;
	$query = mysqli_query($con, "UPDATE messages SET user_to='$new_name' WHERE user_to='$user_to'");
	$query2 = mysqli_query($con, "UPDATE group_chats SET group_info='$new_name' WHERE id='$e'");
	header("Location: messages.php?u=" . $send_to);
}

if(isset($_POST['change_gdesc'])){
	$new_name = $_POST['group_desc'];
	$query2 = mysqli_query($con, "UPDATE group_chats SET group_desc='$new_name' WHERE id='$e'");
	header("Location: messages.php?u=" . $send_to);
}

if(isset($_POST['ng_image'])){
	$uploadOk = 1;
	$imageName = $_FILES['new_gimage']['name'];
	$errorMessage = "";

	if($imageName != "") {
		$targetDir = "assets/images/profile_pics/";
		$imageName = $targetDir . uniqid() . basename($imageName);
		$imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

		if($_FILES['new_gimage']['size'] > 50000000) {
			$errorMessage = "Sorry your file is too large";
			$uploadOk = 0;
		}

		if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg") {
			$errorMessage = "Sorry, only jpeg, jpg and png files are allowed";
			$uploadOk = 0;
		}

		if($uploadOk) {
			
			if(move_uploaded_file($_FILES['new_gimage']['tmp_name'], $imageName)) {
				//image uploaded okay
			}
			else {
				//image did not upload
				$uploadOk = 0;
			}
		}
	}
	if($uploadOk) {
		$quey = mysqli_query($con, "UPDATE group_chats SET group_pic='$imageName' WHERE id='$e'");
		header("Location: messages.php?u=" . $send_to);
	}
	else {
		echo "<div style='text-align:center;' class='alert alert-danger'>
				<button class='close' data-dismiss='alert' aria-label='Close'>
					<span aria-hidden='true'>&times;</span>
				</button>
				$errorMessage
			</div>";
	}
}

if(isset($_POST['post_media'])){
	$body = " ".mysqli_real_escape_string($con, $_POST['media_caption']);
	$uploadOk = 1;
	$errorMessage = "";
	$date = date("Y-m-d H:i:s");
	$imageNamed = $_FILES['fileToUpload']['name'][0];
	if($imageNamed){
		for($i=0; $i<count($_FILES["fileToUpload"]["name"]); $i++){
			$imageName[] = $_FILES['fileToUpload']['name'][$i];
	
			if($imageName[$i] != "") {
				$targetDir = "assets/images/messages/";
				$imageName[$i] = $targetDir . uniqid() . basename($imageName[$i]);
				$imageName[$i] = preg_split("/\s+/", $imageName[$i]);
				$imageName[$i] = implode("", $imageName[$i]);
				$imageFileType = pathinfo($imageName[$i], PATHINFO_EXTENSION);

				if($_FILES['fileToUpload']['size'][$i] > 50000000){
					$errorMessage = "Sorry your file is too large";
					$uploadOk = 0;
				}

				if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg" && strtolower($imageFileType) != "mp4" && strtolower($imageFileType) != "mov") {
					$errorMessage = "Sorry, only jpeg, jpg, png, mov and mp4 files are allowed";
					$uploadOk = 0;
				}
				
				if($uploadOk) {					
					if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'][$i], $imageName[$i])) {
						//image uploaded okay
					}
					else {
						//image did not upload
						$uploadOk = 0;
						$errorMessage = "hello";
					}
				}
				
				$body_array = explode(".", $imageName[$i]);
				if($body_array[count($body_array)-1] == "MP4") {
					$body_array[count($body_array)-1] = "mp4";
				}
				$imageName[$i] = implode(".", $body_array);
			}
		}
		if($body){
			array_push($imageName, $body);
		}
		if($uploadOk) {
			$message_obj->sendMessage($send_to, $imageName, $date);
		}
		else {
			echo "<div style='text-align:center;' class='alert alert-danger'>
					<button class='close' data-dismiss='alert' aria-label='Close'>
						<span aria-hidden='true'>&times;</span>
					</button>
					$errorMessage
				</div>";
		}
	}
	else{
		$message_obj->sendMessage($send_to, $body, $date);
	}
}

if(isset(($_POST['background_photo']))){
	$uploadOk = 1;
	$imageName = $_FILES['fileToUploadz']['name'];
	$errorMessage = "";

	if($imageName != "") {
		$targetDir = "assets/images/backgrounds/";
		$imageName = $targetDir . uniqid() . basename($imageName);
		$imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

		if($_FILES['fileToUploadz']['size'] > 50000000) {
			$errorMessage = "Sorry your file is too large";
			$uploadOk = 0;
		}

		if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg") {
			$errorMessage = "Sorry, only jpeg, jpg and png files are allowed";
			$uploadOk = 0;
		}

		if($uploadOk) {
			
			if(move_uploaded_file($_FILES['fileToUploadz']['tmp_name'], $imageName)) {
				//image uploaded okay
			}
			else {
				//image did not upload
				$uploadOk = 0;
			}
		}
	}
	if($uploadOk) {
		$q = mysqli_query($con, "SELECT * FROM backgrounds WHERE username='$userLoggedIn'");
		if(mysqli_num_rows($q)){
			$query = mysqli_query($con, "UPDATE backgrounds SET image='$imageName' WHERE username='$userLoggedIn'");
		}
		else
			$query = mysqli_query($con, "INSERT INTO backgrounds VALUES(NULL, '$userLoggedIn', '$imageName')");
	}
	else {
		echo "<div style='text-align:center;' class='alert alert-danger'>
				<button class='close' data-dismiss='alert' aria-label='Close'>
					<span aria-hidden='true'>&times;</span>
				</button>
				$errorMessage
			</div>";
	}
}

if(isset($_POST['add_to_group'])){
	$peepz = $_POST['to_add'];
    $peeps = "";
    foreach($peepz as $p)
		$peeps .= $p . ",";
	$date = date("Y-m-d H:i:s");
	$message_obj->addToGroup($peeps, $date, $user_to);
}

if(isset($_POST['link_to_group'])){
	$peepz = $_POST['to_add'];
    $peeps = "";
    foreach($peepz as $p)
		$peeps .= $p . ",";
	$date = date("Y-m-d H:i:s");
	$message_obj->linkToGroup($peeps, $date, $user_to);
}

$qu = mysqli_query($con, "SELECT * FROM backgrounds WHERE username='$userLoggedIn'");
if(mysqli_num_rows($qu)){
	$ss = mysqli_fetch_array($qu);
	$image = $ss['image'];
}
else
	$image = "";

$media = $med_dates = array();
$star_ids= array();
if(strpos($user_to, "ACK_GROUP..??.") !== false){
	$media_query = mysqli_query($con, "SELECT body, date, user_from, id FROM messages WHERE user_to='$send_to' AND deleted='no' ORDER BY id DESC");
	while($hl = mysqli_fetch_array($media_query)){
		$body_arrays = preg_split("/\s+/", $hl['body']);
		$ddte = $hl['date'];
		foreach($body_arrays as $key => $value) {
			if(strpos($value, "media.php.??.") !== false) {
				$dd = substr($value, 13, -1);
				$images = explode(",", $dd);
				$num = count($images);
				foreach($images as $s){
					array_push($media, $s);
					array_push($med_dates, $ddte);
				}
			}
		}
		$isd = $hl['id'];
		$star_check = mysqli_query($con, "SELECT * FROM starred_messages WHERE msg_id='$isd' AND username='$userLoggedIn'");
		if(mysqli_num_rows($star_check))
			array_push($star_ids, $isd);
		rsort($star_ids, SORT_NUMERIC);
	}
	$count = count($media);
	$counte = count($star_ids);
}
else{
	$media_query = mysqli_query($con, "SELECT * FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$user_to') or (user_to='$user_to' AND user_from='$userLoggedIn') ORDER BY id DESC
	");
	while($hl = mysqli_fetch_array($media_query)){
		$body_arrays = preg_split("/\s+/", $hl['body']);
		$ddte = $hl['date'];
		foreach($body_arrays as $key => $value) {
			if(strpos($value, "media.php.??.") !== false) {
				$dd = substr($value, 13, -1);
				$images = explode(",", $dd);
				$num = count($images);
				foreach($images as $s){
					array_push($media, $s);
					array_push($med_dates, $ddte);
				}			
			}
		}
		$isd = $hl['id'];
		$star_check = mysqli_query($con, "SELECT * FROM starred_messages WHERE msg_id='$isd' AND username='$userLoggedIn'");
		if(mysqli_num_rows($star_check))
			array_push($star_ids, $isd);
		rsort($star_ids, SORT_NUMERIC);
	}
	$count = count($media);
	$counte = count($star_ids);

	$asdd=$gn=$gna=$gno=$giic= array();
	$g_query = mysqli_query($con, "SELECT group_info, users FROM group_chats");
	if(mysqli_num_rows($g_query)){
		while($rr = mysqli_fetch_array($g_query)){        
			array_push($asdd, $rr['users']);
			array_push($gn, $rr['group_info']);
		}
		for($i=0; $i<count($gn); $i++){
			$pep = explode(",", $asdd[$i]);
			foreach($pep as $pess){
				if($pess == $userLoggedIn)
					array_push($gna, $gn[$i]);
				if($pess == $user_to)
					array_push($gno, $gn[$i]);
			}
		}
	}
	$num = 0;

	foreach($gna as $g){
		if(in_array($g, $gno)){
			$num ++;
			array_push($giic, $g);
		}
	}
	$gic = $num;
}
$blocked = "no";

if($user_to == "list"){
	$online_query = mysqli_query($con, "SELECT * FROM logins WHERE logout='0000-00-00 00:00:00' AND username!='$userLoggedIn'");
	$ac_num = mysqli_num_rows($online_query);
	?>
	<div class="col-md-12" id='msg_list'>
		<div class="user_detailz column" id="conversations" style="padding: 10 0 0; height: 100% !important;">			
			<center class="logo" style="border-bottom: 0.25em solid var(--secondary2); height: 5vh;">
				<a href="index.php" style="margin: 0; top: 0; font-size: 200%;">Ackeron!</a>
			</center>
			<div class="row">
				<div class="col-xs-6">
					<h4>Conversations</h4>
				</div>
				<div class="new_msg col-xs-6">
					<a href="messages.php?u=new"><span class='fa fa-pencil-square-o'></span></a>
				</div>
			</div>

			<center class="input_group" id="chat_row">
				<input style="width: 95%;" type="search" autocomplete='off' onkeyup='getChats(this.value, "<?php echo $userLoggedIn; ?>")' id="chat_search" placeholder='Search...' class='form-control inp'>
			</center>

			<center class="input_group" id="tb_row">
				<input style="width: 95%;" type="search" autocomplete='off' onkeyup='getTbs(this.value, "<?php echo $userLoggedIn; ?>")' id="chat_searchz" placeholder='Search...' class='form-control inp'>
			</center>

			<div id="hiden">
				<div class="row">
					<div class="col-xs-4">
						<h5 id='t_b' onclick='textBlast("<?php echo $userLoggedIn; ?>")'>Text Blast</h5>
					</div>
					<center class="col-xs-4">
						<h5 id="active">Active(<?php echo $ac_num; ?>)</h5>
					</center>
					<div class="col-xs-4">
						<a href="new_group.php"><h5 class='pull-right' id='n_g'>New Group</h5></a>
					</div>
				</div>

				<div class="loaded_conversations" id="loaded">
					<?php echo $message_obj->getConvos(); ?>
				</div>
			</div>
			<div class="searchez" id="searchez"></div>
			<div id="onliners">
				<div class="card-header fix" style="background-color: #20aae5;">
					<span id="bring_back14" class="fa fa-remove"></span>
					<span class="center-block">Online</span>
				</div>
				<?php
					$activez = array();
					while($actives = mysqli_fetch_array($online_query)){
						$pers = $actives['username'];
						$user_obj = new User($con, $userLoggedIn);
						if($user_obj->isFriend($pers)){
							array_push($activez, $pers);
						}
					}
					$activez  = array_unique($activez);
					foreach($activez as $hes){
						$hes_obj = new User($con, $hes);
						echo "<a href='messages.php?u=$hes' id='link'> <div class='user_found_messages'>
						<img src='" . $hes_obj->getProfilePic() . "' style='border-radius: 50%; margin-right: 5px; border-color: #20aae5;'>
						<b><span style='color: #20aae5'>" . $hes_obj->getFirstAndLastName() . "</span></b>
						<span class='time convos_time' style='color: #20aae5;'>Active</span>
						</div>
						</a>";
					}
				?>
			</div>
		</div>
	</div>
	<?php
}
?>

<style>
	#group_stars_preview, #personal_stars_preview{
		background-image: url(<?php echo $image; ?>);
		background-size: cover;
	}
	::-webkit-scrollbar-thumb{
		background: var(--nic);
		/* background: red; */
	}
	/* ::-webkit-scrollbar{
		width: 3px;
	} */
</style>


<!-- <div class="container-fluid" id='over-all'> -->

<script>
	$(document).ready(function(){
		$('input[id="chat_search2"]').on('keyup',function(){
			if($(this).val()){
				document.getElementById("g_list").style.display="none";
			}
			else{
				document.getElementById("g_list").style.display="block";
			}
		});
		$('input[id="post_button3"]').attr('disabled',true);
		$('input[id="post_button4"]').attr('disabled',true);
		var $charCount, maxCharCount;
		$charCount = $('#counter')
		$charCount2 = $('#counter2')
		maxCharCount = parseInt($charCount.data('max'), 10);
		maxCharCount2 = parseInt($charCount2.data('max'), 10);
		$('input[id="ng_text"]').on('keyup',function(e){
			if($(this).val()){
				var tweetLength = $(e.currentTarget).val().length;
				$charCount.html(maxCharCount - tweetLength);
				$('input[id="post_button3"]').attr('disabled',false);
				document.getElementById("post_button3").style.backgroundColor="var(--sbutton)";
				document.getElementById("counter").style.display="inline-block";
			}
			else{
				$('input[id="post_button3"]').attr('disabled',true);
				document.getElementById("post_button3").style.backgroundColor="var(--shadow2)";
				document.getElementById("counter").style.display="none";
			}
		});
		$('input[id="ng_text2"]').on('keyup',function(e){
			if($(this).val()){
				var tweetLength = $(e.currentTarget).val().length;
				$charCount2.html(maxCharCount2 - tweetLength);
				$('input[id="post_button4"]').attr('disabled',false);
				document.getElementById("post_button4").style.backgroundColor="var(--sbutton)";
				document.getElementById("counter2").style.display="inline-block";
				// document.getElementById("counter2").style.color="red";
				// document.getElementById("ng_text2").style.width="70%";
			}
			else{
				$('input[id="post_button4"]').attr('disabled',true);
				document.getElementById("post_button4").style.backgroundColor="var(--shadow2)";
				document.getElementById("counter2").style.display="none";
			}
		});
		$('input[id="new_btn"]').attr('disabled',true);
		$('textarea[id="message_textarea"]').on('keyup',function(){
			if($(this).val()){
				$('input[id="new_btn"]').attr('disabled',false);
				document.getElementById("new_btn").style.backgroundColor="red";
				document.getElementById("new_btn").style.color="white";
			}
			else{
				$('input[id="new_btn"]').attr('disabled',true);
				document.getElementById("new_btn").style.backgroundColor="var(--nic)";
				document.getElementById("new_btn").style.color="var(--nic)";
			}
		});

		$('input[id="chat_search"]').on('keyup',function(){
			if($(this).val()){
				document.getElementById("hiden").style.display="none";
			}
			else{
				document.getElementById("hiden").style.display="block";
			}
		});

		$('input[id="search_box"]').on('keyup',function(){
			if($(this).val()){
				document.getElementById("all_friends").style.display="none";
			}
			else{
				document.getElementById("all_friends").style.display="block";
			}
		});

		$('#t_b').on('click',function(){
			document.getElementById("hiden").style.display="none";
			document.getElementById("chat_row").style.display="none";
			document.getElementById("onliners").style.display="none";
			document.getElementById("tb_row").style.display="block";
		});

		$('#active').on('click',function(){
			document.getElementById("loaded").style.display="none";
			document.getElementById("onliners").style.display="block";
		});

		$('#img_msg').on('click',function(){
			document.getElementById("msg_containerz").style.display="none";
			document.getElementById("msg_preview").style.display="block";		
		});

		$('#add_members').on('click',function(){
			document.getElementById("msg_containerz").style.display="none";
			document.getElementById("group_preview").style.display="block";		
		});

		$('#gic').on('click',function(){
			document.getElementById("msg_containerz").style.display="none";
			document.getElementById("personal_preview").style.display="none";
			document.getElementById("gic_preview").style.display="block";		
		});

		$('#cgi').on('click',function(){
			document.getElementById("msg_containerz").style.display="none";
			document.getElementById("group_preview").style.display="none";
			document.getElementById("chang_gimage").style.display="block";		
		});

		$('#view_profile').on('click',function(){
			document.getElementById("msg_containerz").style.display="none";
			document.getElementById("personal_preview").style.display="block";		
		});

		$('#change_gname').on('click',function(){
			document.getElementById("msg_containerz").style.display="none";
			document.getElementById("group_preview").style.display="none";		
			document.getElementById("gname").style.display="block";
		});

		$('#change_gdesc').on('click',function(){
			document.getElementById("msg_containerz").style.display="none";
			document.getElementById("group_preview").style.display="none";		
			document.getElementById("g_desc").style.display="block";
		});

		$('#change_gdesc2').on('click',function(){
			document.getElementById("msg_containerz").style.display="none";
			document.getElementById("group_preview").style.display="none";		
			document.getElementById("g_desc").style.display="block";
		});

		$('#g_search').on('click',function(){
			document.getElementById("msg_containerz").style.display="none";
			document.getElementById("group_preview").style.display="none";		
			document.getElementById("group_search").style.display="block";
		});

		$('#group_media').on('click',function(){
			document.getElementById("msg_containerz").style.display="none";
			document.getElementById("group_preview").style.display="none";
			document.getElementById("group_media_preview").style.display="block";
		});

		$('#group_stars').on('click',function(){
			document.getElementById("msg_containerz").style.display="none";
			document.getElementById("group_preview").style.display="none";
			document.getElementById("group_stars_preview").style.display="block";
		});

		$('#group_searchf').on('click',function(){
			document.getElementById("msg_containerz").style.display="none";
			document.getElementById("group_preview").style.display="none";
			document.getElementById("group_chat_search").style.display="block";
		});

		$('#personal_stars').on('click',function(){
			document.getElementById("msg_containerz").style.display="none";
			document.getElementById("personal_preview").style.display="none";
			document.getElementById("personal_stars_preview").style.display="block";
		});

		$('#pchat_search').on('click',function(){
			document.getElementById("msg_containerz").style.display="none";
			document.getElementById("personal_preview").style.display="none";
			document.getElementById("personal_chat_search").style.display="block";
		});

		$('#personal_media').on('click',function(){
			document.getElementById("msg_containerz").style.display="none";
			document.getElementById("personal_preview").style.display="none";
			document.getElementById("personal_media_preview").style.display="block";
		});

		$('#msg-settings').on('click',function(){
			document.getElementById("msg_containerz").style.display="none";
			document.getElementById("msg_settings").style.display="block";
		});

		$('#to_group').on('click',function(){
			document.getElementById("msg_containerz").style.display="none";
			document.getElementById("group_addon").style.display="block";
			document.getElementById("group_preview").style.display="none";		
		});

		$('#ito_group').on('click',function(){
			document.getElementById("msg_containerz").style.display="none";
			document.getElementById("igroup_addon").style.display="block";
			document.getElementById("group_preview").style.display="none";		
		});

		$('#bring_back').on('click',function(){
			document.getElementById("msg_containerz").style.display="block";
			document.getElementById("group_preview").style.display="none";		
		});

		$('#bring_back2').on('click',function(){
			document.getElementById("msg_containerz").style.display="block";
			document.getElementById("group_addon").style.display="none";
		});					

		$('#bring_back3').on('click',function(){
			document.getElementById("msg_containerz").style.display="block";
			document.getElementById("personal_preview").style.display="none";
		});	

		$('#bring_back4').on('click',function(){
			document.getElementById("msg_containerz").style.display="block";
			document.getElementById("group_media_preview").style.display="none";
		});					

		$('#bring_back5').on('click',function(){
			document.getElementById("msg_containerz").style.display="block";
			document.getElementById("personal_media_preview").style.display="none";
		});					

		$('#bring_back6').on('click',function(){
			document.getElementById("msg_containerz").style.display="block";
			document.getElementById("group_stars_preview").style.display="none";
		});					

		$('#bring_back7').on('click',function(){
			document.getElementById("msg_containerz").style.display="block";
			document.getElementById("personal_stars_preview").style.display="none";
		});					

		$('#bring_back8').on('click',function(){
			document.getElementById("msg_containerz").style.display="block";
			document.getElementById("msg_settings").style.display="none";
		});	

		$('#bring_back9').on('click',function(){
			document.getElementById("msg_containerz").style.display="block";
			document.getElementById("gname").style.display="none";
		});	
		
		$('#bring_back10').on('click',function(){
			document.getElementById("msg_containerz").style.display="block";
			document.getElementById("g_desc").style.display="none";
		});	

		$('#bring_back11').on('click',function(){
			document.getElementById("msg_containerz").style.display="block";
			document.getElementById("group_search").style.display="none";
		});

		$('#bring_back12').on('click',function(){
			document.getElementById("msg_containerz").style.display="block";
			document.getElementById("gic_preview").style.display="none";
		});

		$('#bring_back13').on('click',function(){
			document.getElementById("msg_containerz").style.display="block";
			document.getElementById("chang_gimage").style.display="none";
		});

		$('#bring_back14').on('click',function(){
			document.getElementById("loaded").style.display="block";
			document.getElementById("onliners").style.display="none";
		});		

		$('#bring_back16').on('click',function(){
			document.getElementById("msg_containerz").style.display="block";
			document.getElementById("igroup_addon").style.display="none";
		});	

		$('#bring_back17').on('click',function(){
			document.getElementById("msg_containerz").style.display="block";
			document.getElementById("personal_chat_search").style.display="none";
		});		

		$('#bring_back18').on('click',function(){
			document.getElementById("msg_containerz").style.display="block";
			document.getElementById("group_chat_search").style.display="none";
		});		

		$('#bring_back19').on('click',function(){
			document.getElementById("msg_containerz").style.display="block";
			document.getElementById("msg_preview").style.display="none";
		});		

		// $("#fileToUpload").change(function(event){
		// 	$('#image_preview').html("");
		// 	$('#image_preview').css("display", "block");
		// 	var total_file=document.getElementById("fileToUpload").files.length;

		// 	let form = document.getElementById("status_form");
		// 	form.style.display = "block";

		// 	let body = document.getElementById("img_selector");
		// 	// body.style.display = "none";

		// 	for(var i=0;i<total_file;i++){
		// 		let file = event.target.files[i];                
		// 		if(file.type.match("image")){
		// 			$('#image_preview').append("<img src='"+URL.createObjectURL(event.target.files[i])+"'>");
		// 		}
		// 		else{
		// 			$('#image_preview').append("<video loop autoplay muted src='"+URL.createObjectURL(event.target.files[i])+"'></video>");
		// 		}
		// 	}
		// });
		
	});

	var sound = new Audio();
	sound.src = "button_click.mp3";				
</script>	
		
			<?php  
			// echo $user_to;
			if($user_to != "new" && $user_to != "list"){
				
				if(strpos($user_to, "ACK_GROUP..??.") !== false){
					$get_query = mysqli_query($con, "SELECT * FROM group_chats WHERE group_info='$user_to'");
					$rowsd = mysqli_fetch_array($get_query);
					$ima = $rowsd['group_pic'];
					$nam = $rowsd['group_info'];
					$creator = $rowsd['creator'];
					$desc = $rowsd['group_desc'];
					$group_id = $rowsd['id'];
					$date_created = $rowsd['date_added'];
					$userss = $rowsd['users'];
					$userss = substr($userss, 0, -1);
					$participants = explode(",", $userss);
					$total = count($participants);
					sort($participants, SORT_STRING);
					$userss = implode(", ", $participants);
					$usersz = str_replace($userLoggedIn . ",", "", $userss);
					$admins = $rowsd['admins'];
					$admins = explode(",", $admins);
					sort($admins, SORT_STRING);
					foreach($admins as $admin){
						if($admin == $userLoggedIn){
							$allow = 1;
							break;
						}
						else{
							$allow = "";
						}
					}
					$desc = $rowsd['group_desc'];
					$nam = substr($nam, 14);
					if($ima == "")
						$ima = "assets/images/profile_pics/defaults/male.png";
					if(!$desc){
						$desc = "Add Description";
						$desc2 = "";
					}
					else
						$desc2 = $desc;					
					foreach($gna as $d)
						// echo $d."br";
						if(!in_array($user_to, $gna))
							header("Location: messages.php");
				}
				$blacklist_query = mysqli_query($con, "SELECT * FROM blacklist WHERE username='$userLoggedIn' AND chat='$send_to'");
				if(mysqli_num_rows($blacklist_query) == 0){
					$blocked = 'no';
				}
				else
					$blocked = "yes";
				?>								
				<div id="msg_preview">
					<div class="card-header fix">
						<span id="bring_back19" class="fa fa-remove"></span>
						<span class="center-block">Send a media</span>
					</div>
					<form action="" method="post" enctype="multipart/form-data">
						<input type="file" name="fileToUpload[]" id="fileToUpload" multiple>
						<label for="fileToUpload" id="img_selector">
							<h1>Choose a photo</h1> <i class="fa fa-plus-square-o fa-lg"></i>
						</label>
					
						<div id="status_form" style="display:none; margin-top: 0vh">
							<center><textarea rows='5' name='media_caption' id='status_textarea' placeholder='Add a Caption...'></textarea></center>
							<!-- <input type='button' onclick='uploadFile()' name='post_media' id='media_submit'>
							<label onclick='sound.play()' id='' for='media_submit'><span class='fa fa-paper-plane'></span> -->
							<center><input type="button" onclick='sendMediaMessage()' id="post_button7" style='position: relative; width: 90%; margin: 0;' value="Post"></center>
						</div>
					</form><br>
					<div class="progress" style="display: none;" id="bars">
						<progress id="progressBar" value='0' max='100'></progress>
					</div>
					<h3 id="ssl"></h3>
					<div id="image_preview" style='margin: 10 0'></div>
					<script>
						var arr = [];
						function sendMediaMessage(){
							var total_file = arr.length;
							var body = _("status_textarea").value;
							_("image_preview").style.display="none";
							_("bars").style.display="block";
							var formdata = new FormData();
							var ajax = new XMLHttpRequest();
							ajax.upload.addEventListener("progress", progressHandler, false);
							ajax.addEventListener("load", completeHandlerr, true);
							ajax.addEventListener("error", errorHandler, false);
							ajax.addEventListener("abort", abortHandler, false);
							var name = '<?php echo $send_to; ?>';
							var userLoggedIn = '<?php echo $userLoggedIn; ?>';
							if(total_file >= 1){
								for(var filee of arr){
									formdata.append("file[]", filee);
								}
								formdata.append("fileb", body);
								formdata.append("filen", name);
								formdata.append("fileu", userLoggedIn);
								ajax.open("POST", "uploadMessageMedia.php");
								ajax.send(formdata);
							}
						}

						$('input[id="post_button7"]').attr('disabled',true);
						$('textarea[id="status_textarea"]').on('keyup',function(){
							if($(this).val()){
								$('input[id="post_button7"]').attr('disabled',false);
								document.getElementById("post_button7").style.backgroundColor="var(--sbutton)";
							}
							else{
								$('input[id="post_button7"]').attr('disabled',true);
								document.getElementById("post_button7").style.backgroundColor="var(--shadow2)";
							}
						});
						
						$("#fileToUpload").change(function(event){
							if($(this).val()){
								$('input[id="post_button7"]').attr('disabled',false);
								document.getElementById("post_button7").style.backgroundColor="var(--sbutton)";
								document.getElementById("image_preview").style.display="flex";
							}
							else{
								$('input[id="post_button7"]').attr('disabled',true);
								document.getElementById("post_button7").style.backgroundColor="var(--shadow2)";
								document.getElementById("image_preview").style.display="none";
							}

							$('#image_preview').css("height", "120px");
							$('#image_preview').css("alignItems", "center");
							var total_file=document.getElementById("fileToUpload").files.length;

							let form = document.getElementById("status_form");
							form.style.display = "block";
							
							for(var i=0;i<total_file;i++)
							{
								let file = event.target.files[i];            
								if(file.type.match("image")){
									arr.push(file);
									var f_name = arr.indexOf(file);
									var e = file.lastModified;
									$('#image_preview').append("<img class='rmv"+f_name+"' style='width:80px; height:80px; margin: 5;' src='"+URL.createObjectURL(event.target.files[i])+"'><span class='rmve"+f_name+" rmv_btn' onclick='remove(\""+f_name+"\", \""+e+"\")'>&times;</span>");
								}
								else{
									arr.push(file);
									var f_name = arr.indexOf(file);
									var e = file.lastModified;
									$('#image_preview').append("<video class='rmv"+f_name+"' style='width:80px; height:80px; margin: 5;' autoplay muted src='"+URL.createObjectURL(event.target.files[i])+"'></video><span class='rmve"+f_name+" rmv_btn' onclick='remove(\""+f_name+"\", \""+e+"\")'>&times;</span>");
								}
							}
						});

						function remove(gg, hh){
							$(".rmv"+gg).css("display", "none");
							$(".rmve"+gg).css("display", "none");														
							for(var filee of arr){
								if(filee.lastModified == hh){
									var ui = arr.indexOf(filee);
									arr.splice(ui, 1);
									// console.log(ui);
								}
							}
						}

						function progressHandler(event){
							var percent = (event.loaded / event.total) * 100;
							_("progressBar").value = Math.round(percent);
							_("ssl").innerHTML = Math.round(percent)+"% uploaded... please wait";
						}
						function completeHandlerr(event){
							// _("ssl").innerHTML = event.target.responseText;
							_("progressBar").value = 100;		
							var name = '<?php echo $send_to; ?>';							
							window.location.href = "messages.php?u="+name;
						}
						function errorHandler(event){
							_("ssl").innerHTML = "upload failed";
						}
						function abortHandler(event){
							_("ssl").innerHTML = "Upload Aborted";
						}
					</script>
				</div> 
				<?php
				if(strpos($user_to, "ACK_GROUP..??.") !== false){
					?>
					<div id="gname">
						<div class="card-header fix">
							<span id="bring_back9" class="fa fa-remove"></span>
							<span class="center-block">Group Subject</span>
						</div>
						<div class="holds">
							<form action="messages.php?u=<?php echo $send_to; ?>" method="post" style='padding: 10px'>
								<input type="text" style='margin-top: 10px;' maxlength='30' autocomplete='off' placeholder='<?php echo $nam; ?>' value='<?php echo $nam; ?>' name='group_info' id='ng_text'><span id='counter' data-max='30'></span><br>
								<input onclick='sound.play()' type="submit" value="Save" id='post_button3' name='change_gname' style="width: 100%; position: relative;">
							</form>
						</div>
					</div>
					<div id="g_desc">
						<div class="card-header fix">
							<span id="bring_back10" class="fa fa-remove"></span>
							<span class="center-block">Group Description</span>
						</div>
						<div class="holds">
							<form action="messages.php?u=<?php echo $send_to; ?>" method="post" style='padding: 10px'>
								<input type="text" style='margin-top: 10px;' maxlength='100' autocomplete='off' placeholder='<?php echo $desc; ?>' value='<?php echo $desc2; ?>' name='group_desc' id='ng_text2'><span id='counter2' data-max='100'></span><br>
								<input onclick='sound.play()' type="submit" value="Save" id='post_button4' name='change_gdesc' style="width: 100%; position: relative;">
							</form>
						</div>
					</div>
					<div id="group_search">
						<div class="card-header fix">
							<span id="bring_back11" class="fa fa-remove"></span>
							<span class="center-block">Search Participants</span>
						</div>
						<div class="holds">
							<div class="input_group" id="chat_row">
								<input type="search" autocomplete='off' onkeyup='getGroupees(this.value, "<?php echo $userLoggedIn; ?>", "<?php echo $user_to; ?>")' id="chat_search2" placeholder='Search...' class='form-control inp'>
							</div>
							<div class="searches"></div>
							<div id="g_list">
							<?php 
								$userloggedin_obj = new User($con, $userLoggedIn);
								if($allow)
									$nd = "Admin";
								else
									$nd = "";
								if($blocked == "no")
									echo "<div class='group_body'>
											<img class='small_img' src= ".$userloggedin_obj->getProfilePic().">&nbsp;&nbsp;&nbsp;&nbsp;
											You					
											<span class='time'>$nd</span>
										</div><hr class='marginless'>";	
								foreach($participants as $participant){
									if($participant == $userLoggedIn || $participant=="")
										continue;
									$participant_obj = new User($con, $participant);
									foreach($admins as $admin){
										if($admin == $participant){
											$nds = "Admin";
											break;
										}
										else{
											$nds = "";
										}
									}
									if($nd){
										if($nds){
											$adminate = "<li class='divider' role='seperator'></li><li><a class='text-danger' id='de_adminate2$participant' href='#'><span class='fa fa-legal fa-lg icons pull-left'></span>Dismiss as Admin</a></li>";
										}
										else{
											$adminate = "<li class='divider' role='seperator'></li><li><a class='text-primary' id='adminate2$participant' href='#'><span class='fa fa-mortar-board fa-lg icons pull-left'></span>Make Group Admin</a></li>";
										}									
										$remove = "<li class='divider' role='seperator'></li><li><a class='text-danger' id='remove2$participant' href='#'><span class='fa fa-remove fa-lg icons pull-left'></span>Remove from Group</a></li>";
									}
									else{
										$adminate = $remove = "";
									}
									echo "<div><div class='group_body dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='drop_down$participant'>
											<img class='small_img' src= ".$participant_obj->getProfilePic().">&nbsp;&nbsp;&nbsp;&nbsp;
											$participant									
											<span class='time'>$nds</span>
										</div>
										<ul class='dropdown-menu dd_menu' id='toggle$participant'>
											<li><a class='text-primary' href='messages.php?u=$participant'><span class='fa fa-envelope fa-lg icons pull-left'></span>Send Message to $participant</a></li>
											$adminate
											$remove
										</ul>
										<hr class='marginless'></div>";
								}
							?>
							</div>
						</div>
					</div>
					<div id="chang_gimage">
						<div class="card-header fix">
							<span id="bring_back13" class="fa fa-remove"></span>
							<span class="center-block">Group Info</span>						
						</div>
						<form action="messages.php?u=<?php echo $send_to;?>" method="post" enctype='multipart/form-data'>
							<input type="file" name="new_gimage" id="new_gimage">
							<label for="new_gimage" id="selecto" class='centz'>
								<span class="fa fa-camera g_icon"></span>
							</label>
							<input onclick='sound.play()' type='submit' name='ng_image' id='post_button5' class='tb_submit' style='float:right; margin-right:5;' value='Change'>
						</form>
						<script>
							$('input[id="new_gimage"]').on('change',function(){
								if($(this).val()){
									$('input[id="post_button5"]').attr('disabled',false);
									document.getElementById("post_button5").style.backgroundColor="var(--sbutton)";
								}
								else{
									$('input[id="post_button5"]').attr('disabled',true);
									document.getElementById("post_button5").style.backgroundColor="var(--shadow2)";
								}
							});
							var loader2 = function(e){
								let file2 = e.target.files;
								let output2 = document.getElementById("selecto");
								
								
								if(file2[0].type.match("image")){
									let reader2 = new FileReader();

									reader2.addEventListener("load", function(e){
										let data2 = e.target.result;
										let image2 = document.createElement("img");
										image2.src = data2;

										output2.innerHTML = "";
										output2.insertBefore(image2, null)
										output2.classList.add("imagex");
									});

									reader2.readAsDataURL(file2[0]);
								}
								else{
									let show2 = "<span>Selected File : </span>"
									show2 = show2 + file2[0].name;

									output2.innerHTML = show2;
									output2.classList.add("active");

									if(output2.classList.contains("image")){
										output2.classList.remove("image");
									}
								}
							};

							let fileInput2 = document.getElementById("new_gimage");
							fileInput2.addEventListener("change", loader2);
						</script>
					</div>
					<div id="group_preview">
						<div class="card-header fix">
							<span id="bring_back" class="fa fa-remove"></span>
							<span class="center-block">Group Info</span>						
						</div>
						<img class='group_image' src="<?php echo $ima; ?>" alt="">
						<span class="fa fa-camera" id='cgi'></span>
						<?php					
							// if($allow){
								echo "<div class='group_body' id='change_gname'>$nam<span class='f_right fa fa-angle-right'></span></div><hr class='marginless'>";
								if($desc){
									echo "<div class='group_body' id='change_gdesc'>$desc<span class='f_right fa fa-angle-right'></span></div><hr class='marginless'>";
								}
								else{
									echo "<div class='group_body' id='change_gdesc2'>Add group description<span class='f_right fa fa-angle-right'></span></div><hr class='marginless'>";
								}
							// }
							// else{
							// 	echo "<div class='group_body'>$nam<span class='f_right fa fa-info-circle'></span></div><hr class='marginless'>";
							// 	if($desc){
							// 		echo "<div class='group_body'>$desc</div><hr class='marginless'>";
							// 	}
							// }

						?>
						<div class="holds">
							<div class="group_body" id="group_media"><span class="fa fa-image"></span> Media (<?php echo $count; ?>)<span class='f_right fa fa-angle-right'></span></div><hr class='marginless'>
							<div class="group_body" id="group_stars"><span class="fa fa-star" style="font-size:100%;"></span> Starred Messages (<?php echo $counte; ?>) <span class='f_right fa fa-angle-right'></span></div><hr class='marginless'>
							<div class="group_body" id="group_searchf"><span class="fa fa-search"></span> Chat Search <span class='f_right fa fa-angle-right'></span></div>
						</div>
						<div class="holds">
							<div class="group_body" style='height:3em;'><span class="f_left"><?php echo $total; ?> PARTICIPANTS</span><span class="f_right" id='g_search' style='color: #42a5f5;'>Search</span></div>
						</div>
							<?php 							
							$userloggedin_obj = new User($con, $userLoggedIn);
							if($allow){
								$nd = "Admin";
								echo "<div style='color: #42a5f5;' class='group_body' id='to_group'>
										<span class='fa fa-plus-circle incon'></span>&nbsp;&nbsp;&nbsp;&nbsp;
										Add Participants									
									</div><hr class='marginless'>";
								echo "<div style='color: #42a5f5;' class='group_body' id='ito_group'>
										<span class='fa fa-plus-circle incon'></span>&nbsp;&nbsp;&nbsp;&nbsp;
										Invite to group via link
									</div><hr class='marginless'>";
							}
							else
								$nd = "";
							if($blocked == 'no')
								echo "<div class='holds'><div class='group_body'>
										<img class='small_img' src= ".$userloggedin_obj->getProfilePic().">&nbsp;&nbsp;&nbsp;&nbsp;
										You									
										<span class='time'>$nd</span>
									</div><hr class='marginless'>";	
							// foreach($admins as $admin){
							// 	if($admin == $userLoggedIn)
							// 		continue;
							// 	if($admin){
							// 		$admin_obj = new User($con, $admin);
							// 		if($nd){
							// 			$adminate = "<li class='divider' role='seperator'></li><li><a class='text-danger' id='de_adminate2$participant' href='#'><span class='fa fa-legal fa-lg icons pull-left'></span>Dismiss as Admin</a></li>
							// 			";
							// 			$remove = "<li class='divider' role='seperator'></li><li><a class='text-danger' id='remove2$participant' href='#'><span class='fa fa-remove fa-lg icons pull-left'></span>Remove from Group</a></li>
							// 			";
							// 		}
							// 		else
							// 			$adminate = $remove ="";
							// 		echo "<div><div class='group_body dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='drop_down$admin'>
							// 				<img class='small_img' src= ".$admin_obj->getProfilePic().">&nbsp;&nbsp;&nbsp;&nbsp;
							// 				$admin									
							// 				<span class='time'>Admin</span>
							// 			</div>
							// 			<ul class='dropdown-menu dd_menu' id='toggle$admin'>
							// 				<li><a class='text-primary' href='messages.php?u=$admin'><span class='fa fa-envelope fa-lg icons pull-left'></span>Send Message to $admin</a></li>
											
							// 				$adminate
							// 				$remove
							// 			</ul>
							// 			<hr class='marginless'></div>";
							// 	}
							// }
							foreach($participants as $participant){
								if($participant == $userLoggedIn || $participant=="")
									continue;
								$participant_obj = new User($con, $participant);
								foreach($admins as $admin){
									if($admin == $participant){
										$nds = "Admin";
										break;
									}
									else{
										$nds = "";
									}
								}
								if($nd){
									if($nds){
										$adminate = "<li class='divider' role='seperator'></li><li><a class='text-danger' id='de_adminate$participant' href='#'><span class='fa fa-legal fa-lg icons pull-left'></span>Dismiss as Admin</a></li>";
									}
									else{
										$adminate = "<li class='divider' role='seperator'></li><li><a class='text-primary' id='adminate$participant' href='#'><span class='fa fa-mortar-board fa-lg icons pull-left'></span>Make Group Admin</a></li>";
									}									
									$remove = "<li class='divider' role='seperator'></li><li><a class='text-danger' id='remove$participant' href='#'><span class='fa fa-remove fa-lg icons pull-left'></span>Remove from Group</a></li>";
								}
								else{
									$adminate = $remove = "";
								}
								echo "<div><div class='group_body dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='toggle$participant'>
										<img class='small_img' src= ".$participant_obj->getProfilePic().">&nbsp;&nbsp;&nbsp;&nbsp;
										$participant									
										<span class='time'>$nds</span>
									</div>
									<ul class='dropdown-menu dd_menu' id='toggle$participant'>
										<li><a class='text-primary' href='messages.php?u=$participant'><span class='fa fa-envelope fa-lg icons pull-left'></span>Send Message to $participant</a></li>
										$adminate
										$remove
									</ul>
									<hr class='marginless'></div>";
								?>
									<script>
										$(document).ready(function() {
											$('#adminate<?php echo $participant; ?>').on('click', function() {
												bootbox.confirm("Are you sure you want to make <?php echo $participant; ?> an admin?", function(result) {

													$.post("includes/form_handlers/adminate.php?name=<?php echo $participant; ?>&group=<?php echo $user_to; ?>", {result:result});

													if(result)
														location.reload();

												});
											});
											$('#de_adminate<?php echo $participant; ?>').on('click', function() {
												bootbox.confirm("Are you sure you want to remove <?php echo $participant; ?> as an admin?", function(result) {

													$.post("includes/form_handlers/de_adminate.php?name=<?php echo $participant; ?>&group=<?php echo $user_to; ?>", {result:result});

													if(result)
														location.reload();

												});
											});
											$('#remove<?php echo $participant; ?>').on('click', function() {
												bootbox.confirm("Are you sure you want to remove <?php echo $participant; ?> from <?php echo $nam; ?>?", function(result) {

													$.post("includes/form_handlers/g_remove.php?name=<?php echo $participant; ?>&group=<?php echo $user_to; ?>", {result:result});

													if(result)
														location.reload();

												});
											});
											$('#adminate2<?php echo $participant; ?>').on('click', function() {
												bootbox.confirm("Are you sure you want to make <?php echo $participant; ?> an admin?", function(result) {

													$.post("includes/form_handlers/adminate.php?name=<?php echo $participant; ?>&group=<?php echo $user_to; ?>", {result:result});

													if(result)
														location.reload();

												});
											});
											$('#de_adminate2<?php echo $participant; ?>').on('click', function() {
												bootbox.confirm("Are you sure you want to remove <?php echo $participant; ?> as an admin?", function(result) {

													$.post("includes/form_handlers/de_adminate.php?name=<?php echo $participant; ?>&group=<?php echo $user_to; ?>", {result:result});

													if(result)
														location.reload();

												});
											});
											$('#remove2<?php echo $participant; ?>').on('click', function() {
												bootbox.confirm("Are you sure you want to remove <?php echo $participant; ?> from <?php echo $nam; ?>?", function(result) {

													$.post("includes/form_handlers/g_remove.php?name=<?php echo $participant; ?>&group=<?php echo $user_to; ?>", {result:result});

													if(result)
														location.reload();

												});
											});
										});
									</script>
								<?php
							}
							$pin_query = mysqli_query($con, "SELECT * FROM pinned_chats WHERE username='$userLoggedIn' AND user_pinned='$send_to'");
							if(mysqli_num_rows($pin_query))
								$pinn = "<div class='group_body' id='unpin$e' style='color: #3498DB;'><span class='fa fa-thumb-tack'></span> Unpin Chat</div>";
							else
								$pinn = "<div class='group_body' id='pin$e' style='color: #3498DB;'><span class='fa fa-thumb-tack'></span> Pin Chat</div>";
								
							echo "</div><div class='holds'><div class='group_body text-danger' id='leave'>Exit Group</div><hr class='marginless'><div class='group_body' id='clear_chats' style='color: red;'>Clear Chat</div><hr class='marginless'>$pinn</div>";
							if(strpos($user_to, "ACK_GROUP..??.") !== false){
							?>
							<script>
								$(document).ready(function(){
									$('#leave').on('click', function() {
										bootbox.confirm("Are you sure you want to leave <?php echo $nam; ?> ?", function(result) {

											$.post("includes/form_handlers/leave_group.php?name=<?php echo $userLoggedIn; ?>&group=<?php echo $user_to; ?>", {result:result});

											if(result)
												location.reload();

										});
									});
									$('#clear_chats').on('click', function() {
										bootbox.confirm("Are you sure you want to delete all messages to this group chat?", function(result) {

											$.post("includes/form_handlers/clear_messages.php?chat=<?php echo $user_to; ?>", {result:result});

											if(result)
												location.reload();

										});
									});
									$('#pin<?php echo $e; ?>').on('click', function() {
										bootbox.confirm("Are you sure you want to pin this chat?", function(result) {

											$.post("includes/form_handlers/pin_chat.php?chat=<?php echo $send_to; ?>", {result:result});

										});
									});
									$('#unpin<?php echo $e; ?>').on('click', function() {
										bootbox.confirm("Are you sure you want to unpin this chat?", function(result) {

											$.post("includes/form_handlers/unpin_chat.php?chat=<?php echo $send_to; ?>", {result:result});

										});
									});
								});
							</script>
							<?php
							}
							?>
						<div class="s">This Group was Created by <?php echo $creator; ?></div>
						<div class="s">This Group was Created on <?php echo $date_created; ?></div>
					</div>
					<?php				
					if($allow){
						?>
							<div id="group_addon">
								<div class="card-header fix">
									<span class="fa fa-remove" id="bring_back2" style='float:left;'></span>
									<!-- <span class="f_right">Add</span> -->
									<span class="center-block">Add Participants</span>
								</div>
								<form action="messages.php?u=<?php echo $user_to; ?>" method="post">
								<?php
								$usersReturned = mysqli_query($con, "SELECT username FROM users");
								$counter=0;
								$users = array();
								while($row = mysqli_fetch_array($usersReturned)) {
									array_push($users, $row['username']);
								}
								sort($users, SORT_STRING);
								foreach($users as $usee){
									$counter++;
									$user = new User($con, $userLoggedIn);
									$user_found_obj = new User($con, $usee);
									$pp = $usee;
	
									if($usee == $userLoggedIn)
										continue;
	
									if($user->isFriend($usee)) {
										foreach($participants as $participant){
											if($participant == $usee){
												$hmph = "checked='checked' disabled='disabled'";
												break;
											}
											else{
												$hmph = "";
											}
										}
										echo "<label class='labell' for='c_box$counter'><div class='user_tb'>
												<img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 50%; margin-right: 5px;'>
												" . $user_found_obj->getFirstAndLastName() . "
												</div></label>
												<input type='checkbox' name='to_add[]' $hmph value='$pp' class='tb_ch_box' id='c_box$counter'>
											";
									}
									?>
									
									<script>
									$(document).ready(function(){
										$('input[id="post_button2"]').attr('disabled',true);
										$('input[id="c_box<?php echo $counter; ?>"]').on('change',function(){
											if($(this).val()){
												$('input[id="post_button2"]').attr('disabled',false);
												document.getElementById("post_button2").style.backgroundColor="var(--sbutton)";
											}
											else{
												$('input[id="post_button2"]').attr('disabled',true);
												document.getElementById("post_button2").style.backgroundColor="var(--shadow2)";
											}
										});
									});
	
									var sound = new Audio();
									sound.src = "button_click.mp3";
									</script>
									<?php
								}
								?>
								<input onclick='sound.play()' value='Add' type="submit" id="post_button2" name='add_to_group'>
								</form>
							</div>
							<div id="igroup_addon" style='display: none'>
								<div class="card-header fix">
									<span class="fa fa-remove" id="bring_back16" style='float:left;'></span>
									<span class="center-block">Add Participants via link</span>
								</div>
								<form action="messages.php?u=<?php echo $user_to; ?>" method="post">
									<?php
									$usersReturned = mysqli_query($con, "SELECT username FROM users");
									$counter=0;
									$users = array();
									while($row = mysqli_fetch_array($usersReturned)) {
										array_push($users, $row['username']);
									}
									sort($users, SORT_STRING);
									foreach($users as $usee){
										$counter++;
										$user = new User($con, $userLoggedIn);
										$user_found_obj = new User($con, $usee);
										$pp = $usee;
	
										if($usee == $userLoggedIn)
											continue;
	
										if($user->isFriend($usee)) {
											foreach($participants as $participant){
												if($participant == $usee){
													$hmph = "checked='checked' disabled='disabled'";
													break;
												}
												else{
													$hmph = "";
												}
											}
											echo "<label class='labell' for='c_boxx$counter'><div class='user_tb'>
													<img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 50%; margin-right: 5px;'>
													" . $user_found_obj->getFirstAndLastName() . "
													</div></label>
													<input type='checkbox' name='to_add[]' $hmph value='$pp' class='tb_ch_box' id='c_boxx$counter'>
												";
										}
										?>
										
										<script>
										$(document).ready(function(){
											$('input[id="post_button6"]').attr('disabled',true);
											$('input[id="c_boxx<?php echo $counter; ?>"]').on('change',function(){
												if($(this).val()){
													$('input[id="post_button6"]').attr('disabled',false);
													document.getElementById("post_button6").style.backgroundColor="var(--sbutton)";
												}
												else{
													$('input[id="post_button6"]').attr('disabled',true);
													document.getElementById("post_button6").style.backgroundColor="var(--shadow2)";
												}
											});
										});
	
										var sound = new Audio();
										sound.src = "button_click.mp3";
										</script>
										<?php
									}
									?>
									<input onclick='sound.play()' value='Add' type="submit" id="post_button6" name='link_to_group'>
								</form>
							</div>
						<?php
					}						
					?>				
					<div id="group_media_preview">
						<div class="card-header fix" style='position:fixed;'>
							<span id="bring_back4" class="fa fa-remove"></span>
							<span class="center-block"><?php echo substr($user_to, 14); ?></span>						
						</div>
						<?php
							foreach($media as $k => $asg){
								if($k == 0 || $k == 1 || $k==2 || $k==3)
									$class= "margin-top:35;";
								else
									$class =  "margin:0;";
								if(strpos($asg, ".mp4") || strpos($asg, ".MOV")){
									$modal_media = "<video loop controls src='$asg' style='height: 100%; width: 100%;'></video>";
									$value = "<video data-toggle='modal' data-target='#$k' src='$asg'></video>";
								}
								else{
									$value = "<img data-toggle='modal' data-target='#$k' src='$asg'>";
									$modal_media = "<img style='height: 100%; width: 100%;' src='$asg'>";
								}
								$time = $med_dates[$k];
								$sec = substr($time, 17);
								$min = substr($time, 14, -3);
								$hour = substr($time, 11, -6);
								$day = substr($time, 8, -9);
								$month = substr($time, 5, -12);
								$year = substr($time, 0, -15);
								$nice = mktime($hour, $min, $sec, $month, $day, $year);
								$formedd = date("g:i A", $nice);
								$formed = date("l g:i A", $nice);
								$forme = date("j F g:i A", $nice);
								$form = date("j F Y g:i A", $nice);
				
								$time_now = time();
								$diff = $time_now - $nice;
								$date_time_now = date("Y-m-d H:i:s");
								$year_now = substr($date_time_now, 0, -15);
								$day_now = substr($date_time_now, 8, -9);
								$month_now = substr($date_time_now, 5, -12);
								if($year_now != $year)
									$dyea = $form;
								else if($diff > 518400)
									$dyea = $forme;
								else if($diff > 86400)
									$dyea = $formed;
								else if($day_now == $day && $month_now == $month && $year_now == $year)
									$dyea = "Today at " . $formedd;
	
								echo "<div class='col-xs-3 med_hold' style='$class text-align:center;'>
										$value
									</div>
									<div class='modal fade' id=\"".$k."\" tabindex='-1' role='dialog' aria-labelledby='postModalLabel' aria-hidden='true' style='overflow: hidden; padding: 0;'>
										<div class='modal-dialog' style='position: static; margin: 0; width: 100%'>
											<div class='modal-content' style='height: 100%; border-radius: 0;'>
												<center class='sp_date' style='font-size: larger;'>$dyea</center>
												<div class='flexify' style='height: 87%; width: 100%;'>$modal_media</div>
												<button type='button' style='width:100%; height: 5%' class='btn btn-default modal_close_btn' onclick='pause()' data-dismiss='modal'>Close</button>
											</div>
										</div>
									</div>";
							}
						?>
					</div>
					<div id="group_stars_preview">
						<div class="card-header fix">
							<span id="bring_back6" class="fa fa-remove"></span>
							<span class="center-block"><?php echo substr($user_to, 14); ?></span>						
						</div>
						<?php
							$countss = 0;
							foreach($star_ids as $star){
								$countss++;
								echo $message_obj->getSingleMessage($star, $countss, "hide") . "<hr>";
							}
						?>
					</div>
					<div id="group_chat_search">
						<div class="card-header fix">
							<span id="bring_back18" class="fa fa-remove"></span>
							<span class="center-block">Search</span>
						</div>
						<div class="input_group" id="" style="margin: 15;">
							<input type="search" autocomplete='off' onkeyup='getChatsSearch(this.value, "<?php echo $userLoggedIn; ?>", "<?php echo $send_to; ?>")' id="" placeholder='Search this chat...' class='form-control inp'>
						</div>
						<div class="searches" id="searches" style='height: 78vh; overflow-y: auto;'></div>
					</div>
					<?php
				}
				?>
				<div id="personal_chat_search">
					<div class="card-header fix">
						<span id="bring_back17" class="fa fa-remove"></span>
						<span class="center-block">Search</span>
					</div>
					<div class="input_group" id="" style="margin: 15;">
						<input type="search" autocomplete='off' onkeyup='getChatsSearch(this.value, "<?php echo $userLoggedIn; ?>", "<?php echo $user_to; ?>")' id="" placeholder='Search this chat...' class='form-control inp'>
					</div>
					<div class="searches" style='height: 78vh; overflow-y: auto;'></div>
					<!-- <div class='loaded_messages'><div class='msg-inbox'><div class='chats'><div class='msg-page' id='scroll_messagez'>
					</div></div></div></div>
					<script>
						var dfg = document.getElementById("scroll_messagez");
						dfg.scrollTop = dfg.scrollHeight;
						console.log(dfg.scrollTop);
						console.log(dfg.scrollHeight);
					</script> -->
					<!-- <form action="" method="post">
						<?php
							// echo "<div class='message_post' id='blocks'>";
							// if($blocked == "no"){
								?>
								<div class="col-xs-1">
									<span id="img_msg">+</span>
								</div>
								<div class="col-xs-9">
									<textarea class='form-control' onkeypress='auto_grow(this)' onkeyup='typing(this)' rows='1' name='message_body' id='' style='border-radius: 20px; position: absolute; bottom: -40; border: none; font-size: 130%' placeholder='Write your message ...'></textarea>
								</div>
								<script>
									function typing(elem){
										if(elem.value != ""){
											<?php 
											// $check = mysqli_query($con, "SELECT * FROM typing WHERE username='$userLoggedIn' AND user_to='$user_to'");
											// if(mysqli_num_rows($check) == 0){
											// 	$insert = mysqli_query($con, "INSERT INTO typing VALUES(NULL, '$userLoggedIn', '$user_to', 'yes')");
											// }
											// else{
											// 	$update = mysqli_query($con, "UPDATE typing SET typing='yes' WHERE username='$userLoggedIn' AND user_to='$user_to'");
											// }
											?>
										}
									}
									function auto_grow(element){
										var block = document.getElementById("blocks");
										if(element.scrollHeight <= 68){
											element.style.height = (element.scrollHeight)+"px";
											block.style.height = (10.5)+"vh";
											element.style.bottom = "-70px";
										}
									}
									
								</script>
								<?php
							// 	echo "<input type='submit' name='post_message' id='message_submitz'>";
							// 	echo "<div class='col-xs-2'><label onclick='sound.play()' id='new_btn' style='position:relative; margin-left:0;' for='message_submitz'><span class='fa fa-paper-plane'></span></div></div>";
							// }
							// else{
							// 	echo "<div class='center-block' style='padding-top:3vh;'>You can't send messages to this group because you are no longer a member</div>";
							// }
						?>
					</form> -->
				</div>				
				<div id="personal_stars_preview">
					<div class="card-header fix">
						<span id="bring_back7" class="fa fa-remove"></span>
						<span class="center-block"><?php echo $user_to; ?></span>
					</div>
					<?php
						$counterr = 0;
						foreach($star_ids as $star){
							$counterr++;
							echo $message_obj->getSingleMessage($star, $counterr, "hide") . "<hr>";
						}
					?>
				</div>
				<div id="personal_media_preview">
					<div class="card-header fix" style='position:fixed;'>
						<span id="bring_back5" class="fa fa-remove"></span>
						<span class="center-block"><?php echo $user_to; ?></span>
					</div>
					<?php
						foreach($media as $k => $asg){
							if($k == 0 || $k == 1 || $k==2 || $k==3)
								$class= "margin-top:35;";
							else
								$class =  "margin:0;";
							if(strpos($asg, ".mp4") || strpos($asg, ".MOV")){
								$modal_media = "<video loop controls src='$asg' style='height: 100%; width: 100%;'></video>";
								$value = "<video data-toggle='modal' data-target='#$k' src='$asg'></video>";
							}
							else{
								$value = "<img data-toggle='modal' data-target='#$k' src='$asg'>";
								$modal_media = "<img style='height: 100%; width: 100%;' src='$asg'>";
							}
							$time = $med_dates[$k];
							$sec = substr($time, 17);
							$min = substr($time, 14, -3);
							$hour = substr($time, 11, -6);
							$day = substr($time, 8, -9);
							$month = substr($time, 5, -12);
							$year = substr($time, 0, -15);
							$nice = mktime($hour, $min, $sec, $month, $day, $year);
							$formedd = date("g:i A", $nice);
							$formed = date("l g:i A", $nice);
							$forme = date("j F g:i A", $nice);
							$form = date("j F Y g:i A", $nice);
			
							$time_now = time();
							$diff = $time_now - $nice;
							$date_time_now = date("Y-m-d H:i:s");
							$year_now = substr($date_time_now, 0, -15);
							$day_now = substr($date_time_now, 8, -9);
							$month_now = substr($date_time_now, 5, -12);
							if($year_now != $year)
								$dyea = $form;
							else if($diff > 518400)
								$dyea = $forme;
							else if($diff > 86400)
								$dyea = $formed;
							else if($day_now == $day && $month_now == $month && $year_now == $year)
								$dyea = "Today at " . $formedd;

							echo "<div class='col-xs-3 med_hold' style='$class text-align:center;'>
									$value
								</div>
								<div class='modal fade' id=\"".$k."\" tabindex='-1' role='dialog' aria-labelledby='postModalLabel' aria-hidden='true' style='overflow: hidden; padding: 0;'>
									<div class='modal-dialog' style='position: static; margin: 0; width: 100%'>
										<div class='modal-content' style='height: 100%; border-radius: 0;'>
											<center class='sp_date' style='font-size: larger;'>$dyea</center>
											<div class='flexify' style='height: 87%; width: 100%;'>$modal_media</div>
											<button type='button' style='width:100%; height: 5%' class='btn btn-default modal_close_btn' onclick='pause()' data-dismiss='modal'>Close</button>
										</div>
									</div>
								</div>";
						}
					?>
				</div>
				<div id="msg_settings">
					<div class="card-header fix" style='position:fixed;'>
						<span id="bring_back8" class="fa fa-remove"></span>
						<span class="center-block">Change background Image</span>
					</div>
					
					<script>
						$(document).ready(function(){
							$('input[id="post_button"]').attr('disabled',true);
							$('input[id="fileToUploadz"]').on('change',function(){
								if($(this).val()){
									$('input[id="post_button"]').attr('disabled',false);
									document.getElementById("post_button").style.backgroundColor="var(--sbutton)";
								}
								else{
									$('input[id="post_button"]').attr('disabled',true);
									document.getElementById("post_button").style.backgroundColor="var(--shadow2)";
								}
							});
						});
						
						var sound = new Audio();
						sound.src = "button_click.mp3";
					</script>

					<form action="messages.php?u=<?php echo $send_to; ?>;" method="post" enctype="multipart/form-data">
						<input type="file" name="fileToUploadz" id="fileToUploadz" accept="image/*">
						<label for="fileToUploadz" id="img-selector">
							<h1>Select an optional background photo</h1> <i class="fa fa-plus-square-o fa-lg"></i>
						</label>
						<div class='bg_form'><input onclick='sound.play()' type='submit' name='background_photo' class='bg_btn' id='post_button' value='Upload' style='width: auto;'></div>
					</form>

					<script>
						var loader = function(e){
							let file = e.target.files;
							let output = document.getElementById("img-selector");
									
							if(file[0].type.match("image")){
								let reader = new FileReader();

								reader.addEventListener("load", function(e){
									let data = e.target.result;
									let image = document.createElement("img");
									image.src = data;
									image.style = "width: 100%";

									output.innerHTML = "";
									output.insertBefore(image, null)
									output.classList.add("imagez");
								});

								reader.readAsDataURL(file[0]);
							}
							else{
								let show = "<span>Selected File : </span>"
								show = show + file[0].name;

								output.innerHTML = show;
								output.classList.add("active");

								if(output.classList.contains("imagez")){
									output.classList.remove("imagez");
								}
							}

							output.style.padding = "40px";
						};

						let fileInput = document.getElementById("fileToUploadz");
						fileInput.addEventListener("change", loader);
					</script>
				</div>
				<?php 
				if(strpos($user_to, "ACK_GROUP..??.") === false){
					// echo "hey";
					?>
					<div id="personal_preview">
						<div class="card-header fix">
							<span id="bring_back3" class="fa fa-remove"></span>
							<span class="center-block"><?php echo $user_to_obj->getFirstAndLastName(); ?></span>
						</div>
						<img class='group_image' style='border-bottom:20px solid;' src="<?php echo $user_to_obj->getProfilePic(); ?>" alt="">
						<div class="holds">
							<div class="group_body" id='personal_media'><span class="fa fa-image"></span> Media (<?php echo $count; ?>)<span class='f_right fa fa-angle-right'></span></div><hr class='marginless'>
							<div class="group_body" id='personal_stars'><span class="fa fa-star" style="font-size:100%;"></span> Starred Messages (<?php echo $counte; ?>)<span class='f_right fa fa-angle-right'></span></div><hr class='marginless'>
							<div class="group_body" id='pchat_search'><span class="fa fa-search"></span> Chat Search <span class='f_right fa fa-angle-right'></span></div>
						</div>
						<div class="holds">
							<div class="group_body" id='gic'><span class="fa fa-group"></span> Groups in common (<?php echo $gic; ?>)<span class='f_right fa fa-angle-right'></span></div>
						</div>
						<div class="holds">
							<div class="group_body center-block" id='clear<?php echo $user_to ?>' style='color: red;'><span class='fa fa-trash'></span> Clear Chat</div>
							<hr class="marginless">
							<?php 
								$pin_query = mysqli_query($con, "SELECT * FROM pinned_chats WHERE username='$userLoggedIn' AND user_pinned='$send_to'");
								if(mysqli_num_rows($pin_query))
									echo "<div class='group_body center-block' id='unpin$user_to' style='color: #3498DB;'><span class='fa fa-thumb-tack'></span> Unpin Chat</div>";
								else
									echo "<div class='group_body center-block' id='pin$user_to' style='color: #3498DB;'><span class='fa fa-thumb-tack'></span> Pin Chat</div>"
							?>
						</div>
						<script>
							$('#clear<?php echo $user_to; ?>').on('click', function() {
								bootbox.confirm("Are you sure you want to delete all messages with this chat?", function(result) {

									$.post("includes/form_handlers/clear_messages.php?chat=<?php echo $user_to; ?>", {result:result});

									if(result)
										location.reload();

								});
							});
							$('#pin<?php echo $user_to; ?>').on('click', function() {
								bootbox.confirm("Are you sure you want to pin this chat?", function(result) {

									$.post("includes/form_handlers/pin_chat.php?chat=<?php echo $send_to; ?>", {result:result});

								});
							});
							$('#unpin<?php echo $user_to; ?>').on('click', function() {
								bootbox.confirm("Are you sure you want to unpin this chat?", function(result) {

									$.post("includes/form_handlers/unpin_chat.php?chat=<?php echo $send_to; ?>", {result:result});

								});
							});
						</script>
					</div>
					<div id="gic_preview">
						<div class="card-header fix">
							<span id="bring_back12" class="fa fa-remove"></span>
							<span class="center-block">Groups in Common</span>
						</div>
						<?php
							foreach($giic as $group_in_common){
								$fetch = mysqli_query($con, "SELECT * FROM group_chats WHERE group_info='$group_in_common'");
								$fetched = mysqli_fetch_array($fetch);
								$g_id = $fetched['id'];
								$g_pic = $fetched['group_pic'];
								if(!$g_pic)
									$g_pic = "assets/images/profile_pics/defaults/male.png";
								$g_name = substr($group_in_common, 14);
								$g_users = substr($fetched['users'], 0, -1);
								$g_users = explode(",", $g_users);
								sort($g_users, SORT_STRING);
								$g_userz = $user_to . ", ";
								foreach($g_users as $g_user){
									if($g_user == $userLoggedIn || $g_user == $user_to)
										continue;
									$g_userz .= $g_user.", ";
								}
								$glink = "ACK_GROUP..??.$g_id";
								$g_userz = substr($g_userz, 0, -2);
								echo "<a href='messages.php?u=$glink'><div class='group_body' style='overflow: hidden; max-height:6em;'>
										<div class='g-pic col-xs-2'>
											<img style='width: 4em; height: 4em; border-radius: 50%;' src='$g_pic'>
										</div>
										<div class='g-text col-xs-10'>
											<b>$g_name</b><br>
											<span style='word-break: break-word;'>
												$g_userz
											</span>
										</div>
									</div></a>
									<hr class='marginless'>";
							}
						?>
					</div>
					<?php
					$typing_query = mysqli_query($con, "SELECT * FROM typing WHERE user_to='$userLoggedIn' AND username='$user_to' AND typing='yes'");
					if(mysqli_num_rows($typing_query)){
						$comp = "<span style='font-size: 150%'>typing...</span>";
					}
					else{
						$comp = $user_to_obj->getLastSeen($user_to);
					}
				}
				else{
					$typing_query = mysqli_query($con, "SELECT * FROM typing WHERE user_to='$user_to' AND username!='$userLoggedIn' AND typing='yes'");
					if(mysqli_num_rows($typing_query)){
						$row = mysqli_fetch_array($typing_query);
						$typist = $row['username'];
						$comp = "<span style='font-size: 150%'>$typist is typing...</span>";			
					}
					else{
						$comp = $usersz;
					}
				}
				?>
				<div class="col-md-12 message_column" id='msg_containerz'>
				<div class='msg-container' id='msg_container'>
				<div class="msg-header row">
				<div class='somen'>
				<style>
					.loaded_messages{
						background-image: url(<?php echo $image; ?>);
					}
				</style>
				<?php			
				if(strpos($user_to, "ACK_GROUP..??.") === false){							
					echo "<a href='messages.php'><span class='fa fa-angle-left'></span></a><span class='msg-header-img col-xs-1'><img src='" . $user_to_obj->getProfilePic() . "'></span>";
					echo "<span class='col-xs-9 ding'><h6 id='view_profile' style='margin-bottom: 0;'><b>" . $user_to_obj->getFirstAndLastName() . "</b></h6><p class='last_seen'>" .$comp."</p></span><span class='col-xs-1'><a id='msg-settings'><span class='fa fa-gear'></span></a></span></div></div>";

					echo "<div class='loaded_messages' id='blocks2'><div class='msg-inbox'><div class='chats'><div class='msg-page' id='scroll_messages'>";
					echo "<div class='scrollTop' onclick ='scrollToTop();'><span class='fa fa-chevron-up' style='font-size: 170%; padding: 13; color: black;'></span></div>";
						// echo $message_obj->getMessages($user_to);
						echo "<img id='loading' src='assets/images/icons/loading.gif'>";
				}
				else{
					echo "<a href='messages.php'><span class='fa fa-angle-left'></span></a><span class='msg-header-img col-xs-1'><img src='" . $ima . "'></span>";
					echo "<span class='col-xs-9 ding'><h6 id='add_members' style='margin-bottom: 0;'><b>" . $nam . "</b></h6><p class='last_seen'>" .$comp."</p></span><span class='col-xs-1'><a id='msg-settings'><span class='fa fa-gear'></span></a></span></div></div>";

					echo "<div class='loaded_messages' id='blocks2'><div class='msg-inbox'><div class='chats'><div class='msg-page' id='scroll_messages'>";
					echo "<div class='scrollTop' onclick ='scrollToTop();'><span class='fa fa-chevron-up' style='font-size: 170%; padding: 13; color: black;'></span></div>";
						// echo $message_obj->getMessages($user_to);
						echo "<img id='loading' src='assets/images/icons/loading.gif'>";
				}
				echo "</div></div></div></div>";
				?>
				<script>
					$(function(){
						var userLoggedIn = '<?php echo $userLoggedIn; ?>';
						var profileUsername = '<?php echo $send_to; ?>';
						var inProgress = false;

						loadPosts("first"); //Load first posts

						$('#scroll_messages').scroll(function() {
							var bottomElement = $(".msag").first();
							var noMorePosts = $('.msg-page').find('.noMorePosts').val();
							
							// var scroll = document.querySelector(".scrollTop");
							// var scroll_top = $(this).scrollTop();
							// scroll.classList.toggle("active", scroll_top < 2000);
							// console.log(scroll_top);

							// isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
							if (isElementInView(bottomElement[0]) && noMorePosts == 'false') {
								loadPosts("others");
							}
						});

						function loadPosts(term) {
							if(inProgress) { //If it is already in the process of loading some posts, just return
								return;
							}							
							
							inProgress = true;
							$('#loading').show();

							var page = $('.msg-page').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'

							$.ajax({
								url: "includes/handlers/ajax_load_messagez.php",
								type: "POST",
								data: "page=" + page + "&userLoggedIn=" + userLoggedIn  + "&profileUsername=" + profileUsername,
								cache:false,

								success: function(response) {
									$('.msg-page').find('.nextPage').remove(); //Removes current .nextpage 
									$('.msg-page').find('.noMorePosts').remove(); //Removes current .nextpage 

									$('#loading').hide();
									$(".msg-page").prepend(response);
									if(term == "first"){
										var div = document.getElementById("scroll_messages");
										div.scrollTop = div.scrollHeight;								
									}
									else{
										response.scrollTop = response.scrollHeight;
									}

									inProgress = false;
								}
							});														
						}

						//Check if the element is in view
						function isElementInView (el) {
							var rect = el.getBoundingClientRect();

							return (
								rect.top >= 0 &&
								rect.left >= 0 &&
								rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
								rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
							);
						}
					});
				</script>
				<?php
			}
			else if($user_to == "new") {
				echo "<div class='col-md-12'><div class='main_columni column' style='height: 100% !important; max-height: 100%;'>";
				echo "<a href='messages.php'><span class='fa fa-angle-left'></span></a><center><h4>New Message</h4></center>";
				echo "<center>Select the friend you would like to message</center><br>";
			}
			?>					
			<form action="" method="POST">
				<?php
				if($user_to == "new") {
					
					// echo "Select the friend you would like to message <br><br>";
					$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username = '$userLoggedIn' AND user_closed='no'");
					while($row = mysqli_fetch_array($usersReturnedQuery)){
						$user_obj = new User($con, $userLoggedIn);
						$view_obj = new User($con, $row['username']);
						$f_name = $row['first_name'];
						$friends = $view_obj->getFriendArray();
						$friends = substr($friends, 1, -1);
						$friendz = explode(',', $friends);
						sort($friendz, SORT_STRING);
					?> 
					<div class="input_group" id="chat_row">
						<input type="search" onkeyup='getUsers(this.value, "<?php echo $userLoggedIn; ?>")' autocomplete='off' id="search_box" placeholder='Search...' class='form-control inp'>
					</div>
					<div class="searches"></div>
					<div id='all_friends'>

					<?php
						foreach ($friendz as $friend) {
							if($friend){
								$query2 = mysqli_query($con, "SELECT * FROM users WHERE username = '$friend' AND user_closed='no'");
								while($row2 = mysqli_fetch_array($query2)){                            
									echo "<a href='messages.php?u=$friend'><div class='search_result'>
										<div class='result_profile_pic'>
											<img src='". $row2['profile_pic'] ."' style='height: 100px;'>
										</div>
											" . $row2['first_name'] . " " . $row2['last_name'] . "
											
											<p id='grey'> " . $row2['username'] ."</p>
									</div></a>
									<hr id='search_hr'>";
								}
							}
							else
								echo "You doesn't have any friends yet";
						}
					}
				}
				else if($user_to != "new" && $user_to != "list"){
					echo "<div class='message_post' id='blocks'>";
					if($blocked == "no"){
						?>
						<div class="col-xs-1">
							<span id="img_msg">+</span>
						</div>
						<div class="col-xs-11">
							<div class="input-group">
								<textarea rows="1" onkeypress="auto_grow(this);" onkeyup="typing(this.value, '<?php echo $user_to; ?>', '<?php echo $userLoggedIn; ?>');" type="text" placeholder="Write your message ..." id='message_textareaz' name='message_body' class="form-control"></textarea>
								<input type='button' onclick="uploadFile()" name='post_message' id='message_submit'>
								<div class="input-group-addon">
									<label onclick='sound.play()' for='message_submit' style="cursor: pointer;">
										<span id="new_btn4" class="input-group-text" style="color: transparent">
											<!-- <i class="fa fa-paper-plane"></i> -->
											Send
										</span>
									</label>									
								</div>
							</div>
						</div>

						<script>
							$(document).ready(function(){
								$('input[id="message_submit"]').attr('disabled',true);
								$('textarea[id="message_textareaz"]').on('keyup',function(){
									if($(this).val()){
										$('input[id="message_submit"]').attr('disabled',false);
										document.getElementById("new_btn4").style.color="var(--nic)";
									}
									else{
										$('input[id="message_submit"]').attr('disabled',true);
										document.getElementById("new_btn4").style.color="white";
									}
								});
							});
							
							function auto_grow(element){
								var block = document.getElementById("blocks");
								var block2 = document.getElementById("blocks2");						
								if(element.scrollHeight <= 68){
									element.style.height = (element.scrollHeight)+"px";
									// block.style.height = block.style.height + (20)+"px";
									// block2.style.height = block2.style.height - (20)+"px";
								}
							}

							var userLoggedIn = '<?php echo $userLoggedIn; ?>';
							var profileUsername = '<?php echo $user_to; ?>';								
							function uploadFile(){
								var formdata = new FormData();
								var ajax = new XMLHttpRequest();
								ajax.addEventListener("load", completeHandler, false);
								var text = document.getElementById("message_textareaz").value;
								formdata.append("username", profileUsername);
								formdata.append("userLoggedIn", userLoggedIn);
								formdata.append("text", text);
								ajax.open("POST", "includes/form_handlers/uploadMessage.php");
								ajax.send(formdata);
								document.getElementById("message_textareaz").value = "";
							}						

							function completeHandler(event){
								document.getElementById("message_textareaz").height = document.getElementById("message_textareaz").scrollHeight;
								document.getElementById("scroll_messages").innerHTML = event.target.responseText;
								var div = document.getElementById("scroll_messages");
								div.scrollTop = div.scrollHeight;
							}

							
						</script>
						<?php
						// echo "<input type='submit' name='post_message' id='message_submit'>";
						// echo "<div class='col-xs-2'><label onclick='sound.play()' id='new_btn' style='position:relative; margin-left:0;' for='message_submit'><span class='fa fa-paper-plane'></span></div></div>";
					}
					else{
						echo "<div class='center-block' style='padding-top:3vh;'>You can't send messages to this group because you are no longer a member</div>";
					}
				}

				?>
			</form>
			<!-- </div> -->
		</div>	
	</body>
<script>
    var darkMode = localStorage.getItem('darkMode');

    const enableDarkMode = function(){
        document.body.classList.add('darkmode');
        localStorage.setItem('darkMode', 'enabled');
    }

    const disableDarkMode = () =>{
        document.body.classList.remove('darkmode')
        localStorage.setItem('darkMode', 'null');
    }

    if(darkMode === 'enabled'){
        enableDarkMode();
    }
    else{
        disableDarkMode();
    }
</script>