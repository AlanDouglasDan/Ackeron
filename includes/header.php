<?php  
require_once 'config/config.php';
require_once("includes/classes/User.php");
require_once("includes/classes/Post.php");
require_once("includes/classes/Message.php");
require_once("includes/classes/Notification.php");
require_once("includes/form_handlers/status_handler.php");
require_once("includes/form_handlers/level_handler.php");

if (isset($_SESSION['username'])) {
    $userLoggedIn = $_SESSION['username'];
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
    $user = mysqli_fetch_array($user_details_query);
}
else {
    header("Location: register.php");
}

$updatez = mysqli_query($con, "UPDATE typing SET typing='no' WHERE username='$userLoggedIn'");

//Unread messages 
$messages = new Message($con, $userLoggedIn);
$num_messages = $messages->getUnreadNumber();
// $num_messages = 1;

//Unread notifications 
$notifications = new Notification($con, $userLoggedIn);
$num_notifications = $notifications->getUnreadNumber();

// //Unread notifications 
$user_obj = new User($con, $userLoggedIn);
$num_requests = $user_obj->getNumberOfFriendRequests();

$post_query = mysqli_query($con, "SELECT id FROM posts WHERE added_by='$userLoggedIn' AND deleted='no'");
$num_posts = mysqli_num_rows($post_query);
$friends = $user['friend_array'];
$friends = substr($friends, 1, -1);
$friends = explode(",", $friends);
$no_friends = count($friends);

?>
<!-- animate__fadeInTopLeft -->
<!-- animate__lightSpeedInRight -->
<!-- animate__bounceInUp -->
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

    <div class="top_bar"> 
        <div class="logo">
            <a href="index.php">Ackeron!</a>
        </div>

        <div class="search">
			<form action="search.php" method="GET" name="search_form">
				<input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" name="q" placeholder="Search..." autocomplete="off" id="search_text_input">
				<div class="button_holder">
					<img src="assets/images/icons/magnifying_glass.png">
				</div>
			</form>
			<div class="search_results">
			</div>
			<div class="search_results_footer_empty">
			</div>
		</div>

        <nav>
            <a class="hover" href="<?php echo $userLoggedIn; ?>">
                <?php echo $user['first_name'] . " " . $user['last_name']; ?>
            </a>
            <a class="hover" href="index.php">
                <i class="fa fa-home fa-lg"></i>
            </a>
            <a class="hover" href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'message')">
            <!-- <a class="hover" href="messages.php"> -->
				<i class="fa fa-envelope fa-lg"></i>
				<?php
				if($num_messages > 0)
				 echo '<span class="notification_badge" id="unread_message">' . $num_messages . '</span>';
				?>
			</a>
            <a class="hover" href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')">
				<i class="fa fa-bell fa-lg"></i>
				<?php
				if($num_notifications > 0)
				 echo '<span class="notification_badge" id="unread_notification">' . $num_notifications . '</span>';
				?>
			</a>
            <a class="hover" onclick='request()'>
                <i class="fa fa-users fa-lg"></i>
                <?php
				if($num_requests > 0)
				 echo '<span class="notification_badge" id="unread_request">' . $num_requests . '</span>';
				?>
            </a>
            <a class="hover" href="settings.php">
                <i class="fa fa-cog fa-lg"></i>
            </a>
            <a class="hover" href="logout.php">
                <i class="fa fa-sign-out fa-lg"></i>
            </a>
        </nav>

        <div class="dropdown_data_window" style="height:0px; border:none;"></div>
        <input type="hidden" id="dropdown_data_type" value="">
    </div>

    <div class="bottom_bar">
        <nav class="nav3">
            <a class="nav_link" onclick='notifications()'>
                <span class="fa fa-bell"></span>
                <?php
                    if($num_notifications > 0)
                        echo "<span class='notification_badge' id='unread_notification' style='top: 5px; left: 50%'>$num_notifications</span>";
                ?>
            </a>
            <a class="nav_link" onclick='searches()'>
                <span class="fa fa-search"></span>
            </a>
            <a href="<?php echo $userLoggedIn; ?>" class="nav_link">
                <img src='<?php echo $user_obj->getProfilePic()?>'>
            </a>
            <a class="nav_link" onclick='request()'>
                <span class="fa fa-users"></span>
                <?php
                if($num_requests > 0)
                 echo '<span class="notification_badge" id="unread_request" style="top: 5px; left: 50%">' . $num_requests . '</span>';
                ?>
            </a>
            <a href="settings.php" class="nav_link">
                <span class="fa fa-gear"></span>                
            </a>
        </nav>
    </div>

    <div class="top_bar2">
        <nav class="nav2">
            <img class='burger' src='<?php echo $user_obj->getProfilePic()?>'>
            <div class="logo">
                <a href="index.php">Ackeron!</a>
            </div>
            <a href="messages.php">
                <span class="fa fa-envelope"></span>
                <?php
                    if($num_messages > 0)
                       echo "<span class='notification_badge' id='unread_message' style='left: 50%'>$num_messages</span>";
                ?>
            </a>
        </nav>
    </div>

    <style>
        b{
            color: var(--color) !important;
        }
    </style>

    <ul class="nav-links">
        <li><a href="<?php echo $userLoggedIn; ?>"><b style='font-size: 120%'><?php echo $user_obj->getFirstAndLastName(); ?></b></a></li>        
        <li><a class='grey' href="<?php echo $userLoggedIn; ?>">@<?php echo $userLoggedIn; ?></a></li>     
        <br>                           
        <li><a href="#"><b><?php echo $num_posts; ?></b> posts &nbsp;&nbsp;&nbsp;&nbsp; <a href='friends.php?name=<?php echo $userLoggedIn ?>'><b><?php echo $no_friends; ?></b> friends</a></a></li>                                                
        <br>
        <li><a href="<?php echo $userLoggedIn; ?>"><span class="fa fa-lg fa-user"></span>&nbsp;&nbsp;&nbsp;&nbsp; Profile</a></li>                                
        <br>
        <li><a onclick="bookmarks()" id="other_burger"><span class="fa fa-lg fa-bookmark-o"></span>&nbsp;&nbsp;&nbsp;&nbsp; Bookmarks</a></li>                                
        <br>
        <li><a href="about.php"><span class="fa fa-lg fa-certificate"></span>&nbsp;&nbsp;&nbsp;&nbsp; About Us</a></li>                                
        <br>
        <li>
            <a href="logout.php">
                <i class="fa fa-lg fa-sign-out"></i>&nbsp;&nbsp;&nbsp;&nbsp; Logout
            </a>
        </li>                                
        <br>
        <li>
            <h4>Trending Hashtags</h4>
            <div class="trends">
                <?php 
                $query = mysqli_query($con, "SELECT * FROM trends ORDER BY hits DESC LIMIT 9");

                foreach ($query as $row) {                    
                    $word = $row['title'];
                    $word_dot = strlen($word) >= 14 ? "..." : "";

                    $trimmed_word = str_split($word, 14);
                    $trimmed_word = $trimmed_word[0];

                    echo "<div style'padding: 1px'>";
                    echo $trimmed_word . $word_dot;
                    echo "<br></div><br>";
                }

                ?>
            </div>
        </li>
        <span class='d_control'>
            <div>
                <input type="checkbox" class="checkbox" id="chkx">
                <label onclick="sound.play()" class="label" for="chkx">
                    <i class="fa fa-moon-o"></i>
                    <i class="fa fa-sun-o"></i>
                    <div class="ball"></div>
                </label>
            </div>
        </span>
    </ul>

    <script>
        chkx.addEventListener('change', () => {
            darkMode = localStorage.getItem('darkMode');
            
            if(darkMode !== 'enabled'){
                enableDarkMode();
            }
            else{
                disableDarkMode();
            }
        });
    
        const navSlide = ()=> {
            const burger = document.querySelector('.burger');
            const nav = document.querySelector('.nav-links');
            const burger2 = document.querySelector('#other_burger');
            const navLinks = document.querySelectorAll('.nav-links li');
            
            burger.addEventListener('click', ()=>{//Toggle nav
                nav.classList.toggle('nav-active');

                navLinks.forEach((link,index) => {
                    if(link.style.animation){
                        link.style.animation = "";
                    }
                    else{
                        link.style.animation = `navLinkFade 0.5s ease forwards ${index / 7 + 0.5}s`;
                    }
                });
            });
            burger2.addEventListener('click', ()=>{//Toggle nav
                nav.classList.toggle('nav-active');

                navLinks.forEach((link,index) => {
                    if(link.style.animation){
                        link.style.animation = "";
                    }
                    else{
                        link.style.animation = `navLinkFade 0.5s ease forwards ${index / 7 + 0.5}s`;
                    }
                });
            });
            
        }

        navSlide();

        var sound = new Audio();
        sound.src = "button_click.mp3";
    </script>

    <script>
	var userLoggedIn = '<?php echo $userLoggedIn; ?>';

	$(document).ready(function() {

		$('.dropdown_data_window').scroll(function() {
			var inner_height = $('.dropdown_data_window').innerHeight(); //Div containing data
			var scroll_top = $('.dropdown_data_window').scrollTop();
			var page = $('.dropdown_data_window').find('.nextPageDropdownData').val();
			var noMoreData = $('.dropdown_data_window').find('.noMoreDropdownData').val();

			if ((scroll_top + inner_height >= $('.dropdown_data_window')[0].scrollHeight) && noMoreData == 'false') {

				var pageName; //Holds name of page to send ajax request to
				var type = $('#dropdown_data_type').val();


				if(type == 'notification')
					pageName = "ajax_load_notifications.php";
				else if(type = 'message')
					pageName = "ajax_load_messages.php"


				var ajaxReq = $.ajax({
					url: "includes/handlers/" + pageName,
					type: "POST",
					data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
					cache:false,

					success: function(response) {
						$('.dropdown_data_window').find('.nextPageDropdownData').remove(); //Removes current .nextpage 
						$('.dropdown_data_window').find('.noMoreDropdownData').remove(); //Removes current .nextpage 


						$('.dropdown_data_window').append(response);
					}
				});

			} //End if 

			return false;

		}); //End (window).scroll(function())


	});

	</script>


    <div class="wrapper">

    
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