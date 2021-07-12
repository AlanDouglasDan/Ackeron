<?php 
require_once("includes/header.php");

if(isset($_GET['profile_username'])) {
  $userLoggedIn = $_SESSION['username'];
	$username = $_GET['profile_username'];
  $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
  if(mysqli_num_rows($user_details_query) == 0)
    header("Location: ".$userLoggedIn);
	$user_array = mysqli_fetch_array($user_details_query);

	$num_friends = (substr_count($user_array['friend_array'], ",")) - 1;
}
else{
  header("Location: ".$userLoggedIn);
}

if(isset($_GET['name'])){
  $name = $_GET['name'];
  $qu = mysqli_query($con, "SELECT * FROM searches WHERE username='$userLoggedIn' AND searchee='$name'");
  if(mysqli_num_rows($qu) == 0){
    if($name != $userLoggedIn)
      $que = mysqli_query($con, "INSERT INTO searches VALUES(NULL, '$userLoggedIn', '$name')");
  }
  else{
    $que = mysqli_query($con, "SELECT * FROM searches WHERE username='$userLoggedIn' ORDER BY id DESC LIMIT 1");
    $sm = mysqli_fetch_array($que);
    if($sm['searchee'] != $name){
      $quer = mysqli_query($con, "DELETE FROM searches WHERE username='$userLoggedIn' AND searchee='$name'");
      $qu2 = mysqli_query($con, "INSERT INTO searches VALUES(NULL, '$userLoggedIn', '$name')");
    }
  }
}

// if(isset($_GET['show_about'])){
//   $link = '#profileTabs a[href="#about_div"]';
//   echo "<script> 
//           $(function() {
//               $('" . $link ."').tab('show');
//           });
//         </script>";
// }

$opened_query = mysqli_query($con, "UPDATE notifications SET opened='yes' WHERE user_from='$username' AND link='$username' AND message LIKE '%accepted your friend request%'");

require_once("includes/form_handlers/profile_handler.php");


if(isset($_POST['remove_friend'])) {
	$user = new User($con, $userLoggedIn);
	$user->removeFriend($username);
}

if(isset($_POST['add_friend'])) {
	$user = new User($con, $userLoggedIn);
	$user->sendRequest($username);
}
if(isset($_POST['respond_request'])) {
	header("Location: requests.php");
}

$post_query = mysqli_query($con, "SELECT id FROM posts WHERE added_by='$username' AND deleted='no'");
$num_posts = mysqli_num_rows($post_query);

?>

 	<style type="text/css">
	 	.wrapper {
	 		margin-left: 0px;
			padding-left: 0px;
	 	}
    .settings_input{
      height: 25px;
      outline: none;
    }
 	</style>
	
 	<div class="profile_left">
     <?php
      if($username == $userLoggedIn){
      echo "<a href='upload.php'><img src=".$user_array['profile_pic']."></a>";
      }
      else{
      echo "<a href=".$user_array['profile_pic']."><img src=".$user_array['profile_pic']."></a>";
      }
      ?>
    <p class="special_name">
    <?php
    echo $user_array['first_name'] . " " . $user_array['last_name'];
    if($userLoggedIn == $username)
      // echo " <span class='fa fa-check-circle hshs'></span>";
    ?>
    <svg xmlns="http://www.w3.org/2000/svg" style='height:20; position:relative; top:4px' viewBox="0 0 48 48"><polygon fill="#42a5f5" points="29.62,3 33.053,8.308 39.367,8.624 39.686,14.937 44.997,18.367 42.116,23.995 45,29.62 39.692,33.053 39.376,39.367 33.063,39.686 29.633,44.997 24.005,42.116 18.38,45 14.947,39.692 8.633,39.376 8.314,33.063 3.003,29.633 5.884,24.005 3,18.38 8.308,14.947 8.624,8.633 14.937,8.314 18.367,3.003 23.995,5.884"></polygon><polygon fill="#fff" points="21.396,31.255 14.899,24.76 17.021,22.639 21.428,27.046 30.996,17.772 33.084,19.926"></polygon></svg>

    </p>

    <p class='center-block grey'>@<?php echo $username; ?></p> 

 		<div class="profile_info">
 			<p><?php echo "Posts: " . $num_posts?> </p><br>
 			<p><?php echo "Friends: " . $num_friends ?></p>
 		</div>

 		<form action="<?php echo $username; ?>" method="POST">
 			<?php 
 			$profile_user_obj = new User($con, $username); 
 			if($profile_user_obj->isClosed()) {
 				header("Location: user_closed.php");
 			}

 			$logged_in_user_obj = new User($con, $userLoggedIn); 

 			if($userLoggedIn != $username) {

 				if($logged_in_user_obj->isFriend($username)) {
 					echo '<label class="center-block"><input onclick="sound.play()" type="submit" name="remove_friend" class="danger" value="De-Friend"></label><br>';
 				}
 				else if ($logged_in_user_obj->didReceiveRequest($username)) {
 					echo '<label class="center-block"><input onclick="request()" style="color: #fff; width: 90%;" type="button" name="respond_request" class="warning btn" value="Respond to Request"></label><br>';
 				}
 				else if ($logged_in_user_obj->didSendRequest($username)) {
 					echo '<label class="center-block"><input onclick="sound.play()" type="submit" name="" class="default" value="Request Sent"></label><br>';
 				}
 				else 
 					echo '<label class="center-block"><input onclick="sound.play()" type="submit" name="add_friend" class="success" value="Add Friend"></label><br>';

 			}

 			?>
    </form>

    <label onclick="sound.play()" class='center-block'><a role='button' class='btn profile_post_btn' href='to_post.php?name=<?php echo $username; ?>'>Post Something</a></label><br>
     
    <?php  
    $name = $user_array["first_name"];
    if($userLoggedIn == $username) {
      echo "<label onclick='sound.play()' class='center-block'><a role='button' class='btn btn_primary friends' href='friends.php?name=" . $username ."'>View your Friends</a></label>";
    }
    else{
      echo "<label onclick='sound.play()' class='center-block'><a role='button' class='btn btn_primary friends' href='friends.php?name=" . $username ."'>View " .$name."'s Friends</a></label>";
    }
    ?>

    
    <?php  
    if($userLoggedIn != $username) {
      echo '<div class="profile_info_bottom center-block grey">';
        echo $logged_in_user_obj->getMutualFriends($username) . " Mutual friends";
      echo '</div>';
    }
    ?>

 	</div>  

  <div class="profile_main_column column" style='height: 93vh; padding:0; overflow: auto;' id='load_more'>

    <div class="top">

      <div class="profile_top">
        <?php
        if($username == $userLoggedIn){
        echo "<a href='upload.php'><img src=".$user_array['profile_pic']."></a>";
        }
        else{
        echo "<a href=".$user_array['profile_pic']."><img src=".$user_array['profile_pic']."></a>";
        }
        ?>
      </div>

      <p class="special_name center-block">
      <?php
      echo $user_array['first_name'] . " " . $user_array['last_name'];
      ?>
      <svg xmlns="http://www.w3.org/2000/svg" style='height:20; position:relative; top:4px' viewBox="0 0 48 48"><polygon fill="#42a5f5" points="29.62,3 33.053,8.308 39.367,8.624 39.686,14.937 44.997,18.367 42.116,23.995 45,29.62 39.692,33.053 39.376,39.367 33.063,39.686 29.633,44.997 24.005,42.116 18.38,45 14.947,39.692 8.633,39.376 8.314,33.063 3.003,29.633 5.884,24.005 3,18.38 8.308,14.947 8.624,8.633 14.937,8.314 18.367,3.003 23.995,5.884"></polygon><polygon fill="#fff" points="21.396,31.255 14.899,24.76 17.021,22.639 21.428,27.046 30.996,17.772 33.084,19.926"></polygon></svg>
      <p class='center-block'>@<?php echo $username; ?></p>

      <?php  
      if($userLoggedIn != $username) {
        echo '<div class="center-block marginless">';
          echo $logged_in_user_obj->getMutualFriends($username) . " Mutual friends";
        echo '</div>';
      }
      ?>

      <div class="row" style='margin-top: 30;'>
        <div class="col-sm-4">
          <form action="<?php echo $username; ?>" method="POST">
          <?php 
            $profile_user_obj = new User($con, $username); 
            if($profile_user_obj->isClosed()) {
              header("Location: user_closed.php");
            }

            $logged_in_user_obj = new User($con, $userLoggedIn); 

            if($userLoggedIn != $username) {

              if($logged_in_user_obj->isFriend($username)) {
                echo '<label class="center-block"><input onclick="sound.play()" type="submit" name="remove_friend" class="danger" value="De-Friend" style="height: 34; margin: 0; width: 100%;"></label><br>';
              }
              else if ($logged_in_user_obj->didReceiveRequest($username)) {
                echo '<label class="center-block"><input onclick="request()" type="button" name="respond_request" class="warning btn" value="Respond to Request" style="height: 34; margin: 0; width: 100%; color: #fff;"></label><br>';
              }
              else if ($logged_in_user_obj->didSendRequest($username)) {
                echo '<label class="center-block"><input onclick="sound.play()" type="submit" name="" class="default" value="Request Sent" style="height: 34; margin: 0; width: 100%;"></label><br>';
              }
              else 
                echo '<label class="center-block"><input onclick="sound.play()" type="submit" name="add_friend" class="success" value="Add Friend" style="height: 34; margin: 0; width: 100%;"></label><br>';

            }
            else{
              $post_query = mysqli_query($con, "SELECT added_by FROM posts WHERE added_by='$userLoggedIn' AND deleted='no'");
              $num_posts = mysqli_num_rows($post_query);
              echo '<label class="center-block"><a role="button" style="width: 100%;" class="btn success profile_post_btn" href="#">'. $num_posts . ' Posts</a></label><br>';
            }

          ?>
          </form>      
        </div>

        <div class="col-sm-4">
          <label onclick="sound.play()" class='center-block'><a role='button' style='width: 100%;' class='btn profile_post_btn' href='to_post.php?name=<?php echo $username; ?>'>Post Something</a></label><br>
        </div>

        <div class="col-sm-4">
          <?php  
          $name = $user_array["first_name"];
          if($userLoggedIn == $username) {
            echo "<label onclick='sound.play()' class='center-block'><a role='button' style='width: 100%;' class='btn btn_primary friends' href='friends.php?name=" . $username ."'>View your Friends</a></label>";
          }
          else{
            echo "<label onclick='sound.play()' class='center-block'><a role='button' style='width: 100%;' class='btn btn_primary friends' href='friends.php?name=" . $username ."'>View " .$name."'s Friends</a></label>";
          }
          ?>
        </div>
      </div>
    </div>

    <?php
      if($userLoggedIn != $username){
        echo '<ul class="nav nav-tabs nav-justified" role="tablist" id="profileTabs" style="padding: 10px 0;">
        <li role="presentation" class="active"><a href="#newsfeed_div" aria-controls="newsfeed_div" role="tab" data-toggle="tab">Newsfeed</a></li>
        <li role="presentation"><a href="uploads.php?name='.$username.'">Uploads</a></li>
        <li role="presentation"><a href="#about_div" aria-controls="about_div" role="tab" data-toggle="tab">About</a></li>
        <li role="presentation"><a href="messages.php?u='.$username.'">Messages</a></li>
      </ul>
        ';
      }
      else{
        echo '
        <ul class="nav nav-tabs nav-justified" role="tablist" id="profileTabs" style="padding: 10px 0;">
          <li role="presentation" class="active"><a href="#newsfeed_div" aria-controls="newsfeed_div" role="tab" data-toggle="tab">Newsfeed</a></li>
          <li role="presentation"><a href="uploads.php?name='.$username.'">Uploads</a></li>
          <li role="presentation"><a href="#about_div" aria-controls="about_div" role="tab" data-toggle="tab">About</a></li>
        </ul>
        ';
      }
    ?>

    <script>
      $(document).ready(function(){
        $('input[id="post_button2"]').attr('disabled',true);
        $('input[id="bio_text"]').on('keyup',function(){
          if($(this).val() != ""){
            $('input[id="post_button2"]').attr('disabled',false);
            document.getElementById("post_button2").style.backgroundColor="var(--sbutton)";
          }
          else{
            $('input[id="post_button2"]').attr('disabled',true);
            document.getElementById("post_button2").style.backgroundColor="var(--shadow2)";
          }
        });
      });
    </script>

    <div class="tab-content" style="padding: 10px;">

      <div role="tabpanel" class="tab-pane fade in active" id="newsfeed_div">
        <div class="posts_area"></div>
        <img id="loading" src="assets/images/icons/loading.gif">
        <div class="scrollTop" onclick ="scrollToTop();"><span class="fa fa-chevron-up" style="font-size: 170%; padding: 13; color: black;"></span></div>
      </div>

      <div role="tabpanel" class="tab-pane fade" id="about_div">
        <?php
          $bio_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
          $row = mysqli_fetch_array($bio_query);
          if (mysqli_num_rows($bio_query)){
            $bio = $row['bio'];
            $address = $row['address'];
            $city = $row['city'];
            $country = $row['country'];
            $phone = $row['phone'];
            $work = $row['work'];
            $relationship = $row['relationship'];
            $signup_date = $row['signup_date'];
            $bday = $row['dob'];
            if ($relationship == "Single") {
              $single = "selected='selected'";
              $married = $in = $divorced = $it = "";
            }
            else if ($relationship == "Married") {
              $married = "selected='selected'";
              $single = $in = $divorced = $it = "";
            }
            else if ($relationship == "In a Relationship") {
              $in = "selected='selected'";
              $single = $married = $divorced = $it = "";
            }
            else if ($relationship == "Divorced") {
              $divorced = "selected='selected'";
              $single = $in = $married = $it = "";
            }
            else if ($relationship == "It's complicated") {
              $it = "selected='selected'";
              $single = $in = $divorced = $married = "";
            }
            else{
              $single = $in = $divorced = $married = $it = "";
            }
            //Timeframe
            // $date_time_now = date("Y-m-d H:i:s");
            // $start_date = new DateTime($bday); //Time of post
            // $end_date = new DateTime($date_time_now); //Current time
            // $interval = $start_date->diff($end_date); //Difference between dates 
            // $age = $interval->y;
          }else{
            $bio = "";
            $address = "";
            $city = "";
            $country= "";
            $phone = "";
            $work = "";
            $relationship = "";
          }
          echo "<h4 class='special_name center-block'>About $name </h4>";
          if($userLoggedIn == $username){
            ?>
              <form action='profile.php' method='POST' style="margin: 0 0 1em 1em">
                <input type="text" name="bio" class="bio" id="bio_text" value="<?php echo $bio; ?>" autocomplete="off">
                <input onclick="sound.play()" type='submit' class='margin-left' name='update' style='position: relative;' value='Update Bio' id='post_button2'>
              </form>
              <form action="profile.php" method="POST" style ="padding: 10;">
                Address: <input type="text" name="address" value="<?php echo $address; ?>" class="settings_input" autocomplete="off"><br>
                City: <input type="text" name="city" value="<?php echo $city; ?>" class="settings_input" autocomplete="off"><br>
                Country: <input type="text" name="country" value="<?php echo $country; ?>" class="settings_input" autocomplete="off"><br>
                Phone no: <input type="text" name="tel" value="<?php echo $phone; ?>" class="settings_input" autocomplete="off"><br>
                Work: <input type="text" name="work" value="<?php echo $work; ?>" class="settings_input" autocomplete="off"><br>
                Relationship: 
                <select name="relationship" style="background-color: var(--bgc);">
                  <option <?php echo $single; ?> value="Single">Single</option>
                  <option <?php echo $married; ?> value="Married">Married</option>
                  <option <?php echo $in; ?> value="In a Relationship">In a Relationship</option>
                  <option <?php echo $divorced; ?> value="Divorced">Divorced</option>
                  <option <?php echo $it; ?> value="It's complicated">It's complicated</option>
                </select>
                <br><br>
                <input onclick="sound.play()" type="submit" name="update_details" value="Update Details" class="info settings_submit"><br>
              </form>

            <?php
          }
          else{
            echo "<div class='card-header' style='font-size:100%; margin-bottom:8;'>BIO</div>";
            if($bio){
              echo "<div class='bio_text'>$bio</div><hr class='marginless'>";
            }
            else
              echo "<div class='bio_text'>Hey $username is on Ackeron</div><hr class='marginless'>";
            if($phone){
              echo "<div class='card-header' style='font-size:100%; margin-bottom:8;'>CONTACT INFO</div>";
              echo "<div class='row' style='padding: 5px;'><span class='f_left'>Mobile</span><span class='f_right'><b>$phone</b></span></div><hr class='marginless'>";
            }
            if($address){
              echo "<div class='card-header' style='font-size:100%; margin-bottom:8;'>BASIC INFO</div>";
              echo "<div class='row' style='padding: 5px;'><span class='f_left'><span class='fa fa-home'></span> Address</span><span class='f_right'><b>$address</b></span></div><hr class='marginless'>";
            }
            if($city){
              echo "<div class='row' style='padding: 5px;'><span class='f_left'><span class='fa fa-street-view'></span> City</span><span class='f_right'><b>$city</b></span></div><hr class='marginless'>";
            }
            if($country){
              echo "<div class='row' style='padding: 5px;'><span class='f_left'><span class='fa fa-flag-o'></span> Country</span><span class='f_right'><b>$country</b></span></div><hr class='marginless'>";
            }
            if($work)
              echo "<div class='row' style='padding: 5px;'><span class='f_left'><span class='fa fa-bank'></span> Work</span><span class='f_right'><b>$work</b></span></div><hr class='marginless'>";
            if($relationship)
              echo "<div class='row' style='padding: 5px;'><span class='f_left'><span class='fa fa-heart'></span> Relationship</span><span class='f_right'><b>$relationship</b></span></div><hr class='marginless'>";
            if($bday)
              echo "<div class='row' style='padding: 5px;'><span class='f_left'><span class='fa fa-birthday-cake'></span> Birthday</span><span class='f_right'><b>$bday</b></span></div><hr class='marginless'>";            
            // echo "<div class='holl'><span class='fa fa-birthday-cake'></span> $bday - $age years old</div>";
            echo "<div class='holl' style='padding: 5px;'><span class='fa fa-clock-o'></span> Joined on $signup_date</div>";
          }          
          
        ?>
        <div onclick="sound.play()" class="pull-right" style='padding: 5px;'><?php echo $profile_user_obj->getLevel(); ?></div>

        <script>
          var sound = new Audio();
          sound.src = "button_click.mp3";
        </script>
        
      </div>
      
    </div>

  </div>

<script>
  function scrollToTop(){
    document.getElementById("load_more").scrollTo({
      top: 0,
      behaviour: 'smooth'
    });
  }

  $(function(){

    var profileUsername = '<?php echo $username; ?>';
    var userLoggedIn = '<?php echo $userLoggedIn; ?>';
    var inProgress = false;

    loadPosts(); //Load first posts

    $('#load_more').scroll(function() {
      var bottomElement = $(".status_post").last();
      var noMorePosts = $('.posts_area').find('.noMorePosts').val();
      
      var scroll = document.querySelector(".scrollTop");
      var scroll_top = $(this).scrollTop();
      scroll.classList.toggle("active", scroll_top > 2000);

      // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
      if (isElementInView(bottomElement[0]) && noMorePosts == 'false') {
        loadPosts();
      }
    });

    function loadPosts() {
      if(inProgress) { //If it is already in the process of loading some posts, just return
        return;
      }
      
      inProgress = true;
      $('#loading').show();

      var page = $('.posts_area').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'

      $.ajax({
        url: "includes/handlers/ajax_load_profile_posts.php",
        type: "POST",
        data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
        cache:false,

        success: function(response) {
          $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
          $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 
          $('.posts_area').find('.noMorePostsText').remove(); //Removes current .nextpage 

          $('#loading').hide();
          $(".posts_area").append(response);

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
	</div>
</body>
</html>