<?php 
require_once("includes/header.php");

if(isset($_POST['post_status_text'])){
	$post = new Post($con, $userLoggedIn);
	$post->submitStatus($_POST['status_text'], '', $_POST['bg']);
}

if(isset($_POST['post_status_img'])){
	$uploadOk = 1;
	$imageName = $_FILES['fileToUpload']['name'];
	$errorMessage = "";

	if($imageName != "") {
		$targetDir = "assets/images/statuses/";
		$imageName = $targetDir . uniqid() . basename($imageName);
		$imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);		
		
		if($_FILES['fileToUpload']['size'] > 50000000){
			$errorMessage = "Sorry your file is too large";
			$uploadOk = 0;
		}	

		if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg" && strtolower($imageFileType) != "mp4" && strtolower($imageFileType) != "mov") {
			$errorMessage = "Sorry, only jpeg, jpg and png files are allowed";
			$uploadOk = 0;
		}

		if($uploadOk) {
			
			if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName)) {
				//image uploaded okay
			}
			else {
				//image did not upload
				$uploadOk = 0;
			}
		}
		$body_array = explode(".", $imageName);
		if($body_array[count($body_array)-1] == "MP4") {
			$body_array[count($body_array)-1] = "mp4";
		}
		$imageName = implode(".", $body_array);
	}

	if($uploadOk) {
		$post = new Post($con, $userLoggedIn);
		$post->submitStatus($_POST['status_body'], $imageName, '');
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

if(isset($_POST['post'])){
	$post = new Post($con, $userLoggedIn);
	$post->submitPost($_POST['post_text'], 'none', '', '', '', 'yes');
}
	
if(isset($_POST['to_post'])){
	$uploadOk = 1;
	$errorMessage = "";
	$name = $_POST['name'];
	$imageNamed = $_FILES['fileToUpload']['name'][0];
	if($imageNamed){
		for($i=0; $i<count($_FILES["fileToUpload"]["name"]); $i++){
			$imageName[] = $_FILES['fileToUpload']['name'][$i];
	
			if($imageName[$i] != "") {
				$targetDir = "assets/images/posts/";
				$imageName[$i] = $targetDir . uniqid() . basename($imageName[$i]);
				$imageFileType = pathinfo($imageName[$i], PATHINFO_EXTENSION);		
				if($_FILES['fileToUpload']['size'][$i] > 50000000){
					$errorMessage = "Sorry your file is too large " . $_FILES['fileToUpload']['size'][$i];
					$uploadOk = 0;
				}
				if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg" && strtolower($imageFileType) != "mp4" && strtolower($imageFileType) != "mov") {
					$errorMessage = "Sorry, only jpeg, jpg, png and mp4 files are allowed";
					$uploadOk = 0;
				}
				if($uploadOk) {					
					if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'][$i], $imageName[$i])) {
						//image uploaded okay
					}
					else {
						//image did not upload
						$uploadOk = 0;
					}
				}
				$body_array = explode(".", $imageName[$i]);
				if($body_array[count($body_array)-1] == "MP4") {
					$body_array[count($body_array)-1] = "mp4";
				}
				$imageName[$i] = implode(".", $body_array);
			}
		}
		if($uploadOk) {
			$post = new Post($con, $userLoggedIn);
			$post->submitPost($_POST['post_text'], $name, $imageName);
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
		$post = new Post($con, $userLoggedIn);
		$post->submitPost($_POST['post_text'], $name, '');
	}
}

if(isset($_POST['edit_post'])){
	$uploadOk = 1;
	$errorMessage = "";
	$name = $_POST['name'];
	$imageNamed = $_FILES['fileToUpload']['name'][0];
	if($imageNamed){
		for($i=0; $i<count($_FILES["fileToUpload"]["name"]); $i++){
			$imageName[] = $_FILES['fileToUpload']['name'][$i];
	
			if($imageName[$i] != "") {
				$targetDir = "assets/images/posts/";
				$imageName[$i] = $targetDir . uniqid() . basename($imageName[$i]);
				$imageFileType = pathinfo($imageName[$i], PATHINFO_EXTENSION);
		
				if($_FILES['fileToUpload']['size'][$i] > 100000000) {
					$errorMessage = "Sorry your file is too large";
					$uploadOk = 0;
				}
		
				if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg") {
					$errorMessage = "Sorry, only jpeg, jpg and png files are allowed";
					$uploadOk = 0;
				}
		
				if($uploadOk) {
					
					if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'][$i], $imageName[$i])) {
						//image uploaded okay
					}
					else {
						//image did not upload
						$uploadOk = 0;
					}
				}
			}
		}
		if($uploadOk) {
			$id = $_POST['id'];
			$update_query = mysqli_query($con, "UPDATE posts SET deleted='yes' WHERE id='$id'");
			// $check_query = mysqli_query($con, "SELECT * FROM posts WHERE id='$id'");
			// $editee = mysqli_fetch_array($check_query);
			// if($editee['image'])
			// 	$imageName = $editee['image'];
			$post = new Post($con, $userLoggedIn);
			$post->submitPost($_POST['post_text'], $name, $imageName);
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
		$id = $_POST['id'];
		$update_query = mysqli_query($con, "UPDATE posts SET deleted='yes' WHERE id='$id'");
		$post = new Post($con, $userLoggedIn);
		$post->submitPost($_POST['post_text'], $name, '');
	}
}

if(isset($_POST["image"])){
	$data = $_POST["image"];
	$image_array_1 = explode(";", $data);
	$image_array_2 = explode(",", $image_array_1[1]);
	$data = base64_decode($image_array_2[1]);
	$imageName = 'ACK_IMG_'.$userLoggedIn.'_'.time().'.png';
	file_put_contents($_SERVER['DOCUMENT_ROOT'].'/Ackeron/assets/images/profile_pics'."/".$imageName, $data);
	$result_path ="assets/images/profile_pics/".$imageName;
	$insert_pic_query = mysqli_query($con, "UPDATE users SET profile_pic='$result_path' WHERE username='$userLoggedIn'");
	header("Location: index.php");
}

$post_query = mysqli_query($con, "SELECT id FROM posts WHERE added_by='$userLoggedIn' AND deleted='no'");
$num_posts = mysqli_num_rows($post_query);
$friends = $user['friend_array'];
$friends = substr($friends, 1, -1);
$friends = explode(",", $friends);
$no_friends = count($friends);
?>

	<div class="row">
		<div class="col-md-4 hidden-xs hidden-sm">
			<div class="user_details column">
				<a href="upload.php">  <img src="<?php echo $user['profile_pic']; ?>"> </a>

				<div class="user_details_left_right">
					<a href="<?php echo $userLoggedIn; ?>">@<?php echo $userLoggedIn; ?></a>
					<br>
					<?php echo "Posts: " . $num_posts. "<br>"; 
					echo "Friends: " . $no_friends;

					?>
				</div>

			</div>

			<div class="user_details column">

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


			</div>
		</div>

		<script>
			$(document).ready(function(){
				$('input[id="post_button"]').attr('disabled',true);
				$('textarea[id="post_text"]').on('keyup',function(){
					if($(this).val() != ""){
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
			sound.src = "button_clicked.mp3";
		</script>

		<div class="col-md-8">
			<div class="column" style='height: 93vh; overflow: auto;' id='load_more'>

				<div class="status">

					<?php 
						$post = new Post($con, $userLoggedIn);
						$post->loadStatuses();
					?>
					
				</div>

				<!-- <form class="post_form" action="index.php" method="POST" enctype="multipart/form-data">
					<textarea name="post_text" id="post_text" placeholder="Got something to say?"></textarea>
					<input onclick="sound.play()" type="submit" name="post" id="post_button" value="Post" style='position: relative;'>
					<hr>
				</form> -->

				<form id="new-message" action="index.php" method="POST">
					<div class="input-group">
						<textarea name="post_text" type="text" style='height: 4em; background-color: var(--bgc); border-right: none; color: var(--color);' placeholder="Got something to say?" class="form-control"></textarea>
						<input type="submit" name="post" id="to_p" style='display:none;'>
						<label for="to_p" class="input-group-addon" style='height: 4em; background-color: var(--bgc);'>
							<span class="fa fa-paper-plane" aria-hidden="true" style="color: var(--color);"></span>
						</label>
					</div>
				</form>

				<label onclick="sound.play()" class="to_post" for="post"><a id="post" href="to_post.php"><span class="fa fa-paper-plane fa-lg"></span></a></label>
				<div class="scrollTop" onclick ="scrollToTop();"><span class="fa fa-chevron-up" style="font-size: 170%; padding: 13; color: black;"></span></div>

				<div class="posts_area"></div>
				<img id="loading" src="assets/images/icons/loading.gif">

			</div>
		</div>
	</div>
	

	<!-- <script>
		
		var userLoggedIn = '<?php echo $userLoggedIn; ?>';

		$(document).ready(function() {

			$('#loading').show();

			//Original ajax request for loading first posts 
			$.ajax({
				url: "includes/handlers/ajax_load_posts.php",
				type: "POST",
				data: "page=1&userLoggedIn=" + userLoggedIn,
				cache:false,

				success: function(data) {
					$('#loading').hide();
					$('.posts_area').html(data);
				}
			});

			$('#load_more').scroll(function() {
				var ddd = document.getElementById("load_more");
				var scroll = document.querySelector(".scrollTop");
				var scroll_top = $(this).scrollTop();
				scroll.classList.toggle("active", scroll_top > 2000);
				// setTimeout(() => {
				// 	scroll.classList.remove("active");
				// }, 5000);
				
				var height = $('.posts_area').height(); //Div containing posts
				// var scroll_top = scroll_top + 2;
				var page = $('.posts_area').find('.nextPage').val();
				var noMorePosts = $('.posts_area').find('.noMorePosts').val();
				var he = document.getElementById('load_more').scrollHeight;
				var hes = ($('#load_more').innerHeight());
				console.log(he);
				console.log(hes);
				console.log(scroll_top);
				// console.log(page);

				if ((scroll_top + hes == he) && noMorePosts == 'false') {
					$('#loading').show();

					var ajaxReq = $.ajax({
						url: "includes/handlers/ajax_load_posts.php",
						type: "POST",
						data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
						cache:false,

						success: function(response) {
							$('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
							$('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 

							$('#loading').hide();
							$('.posts_area').append(response);
						}
					});

				} //End if 

				return false;

			}); //End (window).scroll(function())


		});
	</script> -->
	<script>
		function scrollToTop(){
			document.getElementById("load_more").scrollTo({
				top: 0,
				behaviour: 'smooth'
			})
		}

		$(function(){
			var userLoggedIn = '<?php echo $userLoggedIn; ?>';
			var inProgress = false;

			loadPosts(); //Load first posts

			$('#load_more').scroll(function() {
				var bottomElement = $(".status_post").last();
				// console.log(bottomElement);
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
					url: "includes/handlers/ajax_load_posts.php",
					type: "POST",
					data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
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
				// console.log(rect);
				// console.log($('#load_more').innerHeight() + 800);

				return (
					rect.top >= 0 &&
					rect.left >= 0 &&
					rect.bottom <= ($('#load_more').innerHeight() + 800 || document.documentElement.clientHeight + 800) && //* or $(window).height()
					rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
				);
			}
		});
	</script>

	</div>
</body>
</html>