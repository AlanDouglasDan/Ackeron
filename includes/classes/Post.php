<?php
class Post {
	private $user_obj;
	private $con;

	public function __construct($con, $user){
		$this->con = $con;
		$this->user_obj = new User($con, $user);
	}

	public function submitStatus($body, $imageName, $color){
		if($color){
			$body = strip_tags($body); //removes html tags 
			$body = mysqli_real_escape_string($this->con, $body);
			$body = str_replace('\r\n', "\n", $body);
			$body = nl2br($body);
			$check_empty = preg_replace('/\s+/', '', $body); //Deltes all spaces
			$ze = 0;
			$no = "no";
			$id="";
			if($check_empty != "") {

				$body_array = preg_split("/\s+/", $body);
	
				foreach($body_array as $key => $value) {
	
					if(strpos($value, "www.") !== false) {

						$value = "<a target='_blank'>" . $value ."</a>";
						$body_array[$key] = $value;
	
					}
	
				}
				$body = implode(" ", $body_array);

				//Current date and time
				$date_added = date("Y-m-d H:i:s");
				//Get username
				$added_by = $this->user_obj->getUsername();

				// $query = mysqli_query($this->con, "INSERT INTO statuses VALUES (NULL, '$added_by', '$date_added', '$body', '$imageName', '0', 'no', '$color')");
				$query = "INSERT INTO statuses VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
				$stmt = mysqli_stmt_init($this->con);
				if(!mysqli_stmt_prepare($stmt, $query)){
					echo "SQL ERROR";
				}
				else{
					mysqli_stmt_bind_param($stmt, "issssiss", $id, $added_by, $date_added, $body, $imageName, $ze, $no, $color);
					mysqli_stmt_execute($stmt);
				}
			}
		}
		else{
			$ze = 0;
			$no = "no";
			$id="";
			if($body){
				$body = strip_tags($body); //removes html tags 
				$body = mysqli_real_escape_string($this->con, $body);
				$body = str_replace('\r\n', "\n", $body);
				$body = nl2br($body);
			}			
			//Current date and time
			$date_added = date("Y-m-d H:i:s");
			//Get username
			$added_by = $this->user_obj->getUsername();

			// $query = mysqli_query($this->con, "INSERT INTO statuses VALUES (NULL, '$added_by', '$date_added', '$body', '$imageName', '0', 'no', '$color')");
			$query = "INSERT INTO statuses VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
			$stmt = mysqli_stmt_init($this->con);
			if(!mysqli_stmt_prepare($stmt, $query)){
				echo "SQL ERROR";
			}
			else{
				mysqli_stmt_bind_param($stmt, "issssiss", $id, $added_by, $date_added, $body, $imageName, $ze, $no, $color);
				mysqli_stmt_execute($stmt);
			}
		}
		header("Location: index.php");
	}

	public function submitPost($body, $user_to, $imageName, $location, $tags, $reload) {
		$body = strip_tags($body); //removes html tags 
		$body = mysqli_real_escape_string($this->con, $body);
		$body = str_replace('\r\n', "\n", $body);
		$body = nl2br($body);
		$check_empty = preg_replace('/\s+/', '', $body); //Deltes all spaces 
		$images = "";
		if($imageName){
			foreach($imageName as $image)
				$images .= $image . ",";
		}
      
		if($check_empty != "" || $images != "") {

			$body_array = preg_split("/\s+/", $body);
			foreach($body_array as $key => $value) {

				if(strpos($value, "www.youtube.com/watch?v=") !== false) {

					$link = preg_split("!&!", $value);
					$value = preg_replace("!watch\?v=!", "embed/", $link[0]);
					$value = "<iframe width=\'100\%\' height=\'40\%\' src=\'" . $value ."\'></iframe><br>";
					$body_array[$key] = $value;

				}

			}
			$body = implode(" ", $body_array);

			//Current date and time
			$date_added = date("Y-m-d H:i:s");
			//Get username
			$added_by = $this->user_obj->getUsername();

			//If user is on own profile, user_to is 'none'
			if($user_to == $added_by)
				$user_to = "none";

			//insert post 
			$query = mysqli_query($this->con, "INSERT INTO posts VALUES (NULL, '$body', '$added_by', '$user_to', '$date_added', 'no', 'no', '0', '$images', '$location', '$tags')");
			// $ddd = "";
			// $asd = "no";
			// $ze = 0;
			// $query = "INSERT INTO posts VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
			// $stmt = mysqli_stmt_init($this->con);
			// if(!mysqli_stmt_prepare($stmt, $query)){
			// 	echo "SQL ERROR";
			// }
			// else{
			// 	mysqli_stmt_bind_param($stmt, "issssssis", $id, $body, $added_by, $user_to, $date_added, $asd, $asd, $ze, $images);
			// 	mysqli_stmt_execute($stmt);
			// }

			$returned_id = mysqli_insert_id($this->con);

			$notification = new Notification($this->con, $added_by);

			if($tags){				
				$tagged = substr($tags, 0, -1);
				$tagged = explode(",", $tagged);
				foreach($tagged as $rh){
					$notification->insertNotification($returned_id, $rh, "tag");
				}
			}
			else
				$tagged = array();

			$post_not_query = mysqli_query($this->con, "SELECT * FROM post_notifications WHERE person='$added_by'");
			if(mysqli_num_rows($post_not_query)){
				while($to = mysqli_fetch_array($post_not_query)){
					$ei = $to['username'];
					if(!in_array($ei, $tagged))
						$notification->insertNotification($returned_id, $ei, "post");
				}
			}

			//Update post count for user 
			$num_posts = $this->user_obj->getNumPosts();
			$num_posts++;
			$update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");


			$stopWords = "a about above across after again against all almost alone along already
			 also although always among am an and another any anybody anyone anything anywhere are 
			 area areas around as ask asked asking asks at away b back backed backing backs be became
			 because become becomes been before began behind being beings best better between big 
			 both br but by c came can cannot case cases certain certainly clear clearly come could
			 d did differ different differently do does done down down downed downing downs during
			 e each early either end ended ending ends enough even evenly ever every everybody
			 everyone everything everywhere f face faces fact facts far felt few find finds first
			 for four from full fully further furthered furthering furthers g gave general generally
			 get gets give given gives go going good goods got great greater greatest group grouped
			 grouping groups h had has have having he her here herself high high high higher
		     highest him himself hishow however i im if important in interest interested interesting
			 interests into is it its itself j just k keep keeps kind knew know known knows
			 large largely last later latest least less let lets like likely long longer
			 longest m made make making man many may me member members men might more most
			 mostly mr mrs much must my myself n necessary need needed needing needs never
			 new new newer newest next no nobody non noone not nothing now nowhere number
			 numbers o of off often old older oldest on once one only oo open opened opening
			 opens or order ordered ordering orders other others our out over p part parted
			 parting parts per perhaps place places point pointed pointing points possible
			 present presented presenting presents problem problems put puts q quite r
			 rather really right right room rooms s said same saw say says second seconds
			 see seem seemed seeming seems sees several shall she should show showed
			 showing shows side sides since small smaller smallest so some somebody
			 someone something somewhere state states still still such sure t take
			 taken than that the their them then there therefore these they thing
			 things think thinks this those though thought thoughts three through
	         thus to today together too took toward turn turned turning turns two
			 u under until up upon us use used uses v very w want wanted wanting
			 wants was way ways we well wells went were what when where whether
			 which while who whole whose why will with within without work
			 worked working works would x y year years yet you young younger
			 youngest your yours z lol haha omg hey ill iframe wonder else like 
             hate sleepy reason for some little yes bye choose";

             //Convert stop words into array - split at white space
			$stopWords = preg_split("/[\s,]+/", $stopWords);

			//Remove all punctionation
			$no_punctuation = preg_replace("/[^a-zA-Z 0-9#]+/", "", $body);

			//Predict whether user is posting a url. If so, do not check for trending words
			if(strpos($no_punctuation, "height") === false && strpos($no_punctuation, "www") === false
				&& strpos($no_punctuation, "http") === false && strpos($no_punctuation, "youtube") === false){
				//Convert users post (with punctuation removed) into array - split at white space
				$keywords = preg_split("/[\s,]+/", $no_punctuation);

				foreach($stopWords as $value) {
					foreach($keywords as $key => $value2){
						if(strtolower("#".$value) == strtolower($value2))
							$keywords[$key] = "";
					}
				}

				foreach ($keywords as $value) {
					if(substr($value, 0, 1) == "#")
						$this->calculateTrend(strtolower($value));
				}

             }

		}
		if($reload == "yes")
			header("Location: index.php");
	}

	public function calculateTrend($term) {

		if($term != '') {
			$query = mysqli_query($this->con, "SELECT * FROM trends WHERE title='$term'");

			if(mysqli_num_rows($query) == 0)
				$insert_query = mysqli_query($this->con, "INSERT INTO trends(title,hits) VALUES('$term','1')");
			else 
				$insert_query = mysqli_query($this->con, "UPDATE trends SET hits=hits+1 WHERE title='$term'");
		}

	}

	public function loadStatuses(){
		$userLoggedIn = $this->user_obj->getUsername();
		$user_logged_obj = new User($this->con, $userLoggedIn);

		$str = "";
		$data_query = mysqli_query($this->con, "SELECT username FROM statuses WHERE deleted='no' ORDER BY id DESC");

		$data_query2 = mysqli_query($this->con, "SELECT username FROM statuses WHERE deleted='no'");
		$go_to = "";

		while($row2 = mysqli_fetch_array($data_query2)){
			if($row2['username'] == $userLoggedIn)
				$go_to = "status.php?name=$userLoggedIn";
		}
		if(!$go_to)
			$go_to = "status_img.php";

		$buttons = "<div class='collect'><a href='status_img.php'><span class='fa fa-camera iconz'></span></a><a href='status_text.php'><span class='fa fa-pencil iconz'></span></a></div>";
		$to_post = "<span class='hold'><a href='" .$go_to. "'><img src='" .$user_logged_obj->getProfilePic(). "'><span class='fa fa-plus status_plus'></span></a>$buttons</span>";
		$str .= $to_post;
		$people = array();
		$viewei = array();

		if(mysqli_num_rows($data_query) > 0){
			while($row = mysqli_fetch_array($data_query)){
				$username = $row['username'];
				array_push($people, $username);
				$people = array_unique($people);
			}

			foreach($people as $person){
				$added_by_obj = new User($this->con, $person);
				if($added_by_obj->isClosed()) {
					continue;
				}
				if($person == $userLoggedIn)
					continue;
					
				$unopened_query = mysqli_query($this->con, "SELECT username FROM views WHERE added_by='$person' AND username='$userLoggedIn'");
				$unopened_query2 = mysqli_query($this->con, "SELECT username FROM statuses WHERE username='$person' AND DELETED='no'");
				$posts = mysqli_num_rows($unopened_query2);
				$postz = mysqli_num_rows($unopened_query);
				if($posts == $postz)
					$bgc = "#ddd";
				else
					$bgc = "red";
				
				if($user_logged_obj->isFriend($person)){
					$name = "<div class='status_name'>".$added_by_obj->getFirstAndLastName()."</div>";
					$pic = $added_by_obj->getProfilePic();

					$str .= "<span class='hold'><a href='status.php?name=$person'><img src='" .$pic. "' style='border-color: $bgc'></a><br>$name</span>";
				}
			}
		}
		echo $str;
	}

	public function loadPostsFriends($data, $limit) {

		$page = $data['page']; 
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1) 
			$start = 0;
		else 
			$start = ($page - 1) * $limit;

		$str = ""; //String to return 
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");

		if(mysqli_num_rows($data_query) > 0) {

			$num_iterations = 0; //Number of results checked (not necasserily posted)
			$count = 1;

			while($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
				$imagePath = $row['image'];
				$tags = $row['tags'];
				if($tags){
					$list2 = "";
					$tags = substr($tags, 0, -1);
					$tags = explode(",", $tags);
					$tag_no = count($tags) - 1;
					if($tag_no > 1)
						$tag_no = "<span style='cursor: pointer;' data-toggle='modal' data-target='#tagged$id'> and $tag_no others</span>";
					else if($tag_no == 1)
						$tag_no = "<span style='cursor: pointer;' data-toggle='modal' data-target='#tagged$id'> and $tag_no other person</span>";
					else {
						$tag_no = "";
					}
					$target = $tags[0];
					if($row['user_to'] == "none")
						$dk = "";
					else
						$dk = "and ";
					$tag_stmt = $dk."is with <a href='$target'><b>". $target ."</b></a>$tag_no";
					foreach($tags as $taf){
						$taf_obj = new User($this->con, $taf);						
						$list2 .= "<a href='$taf' style='padding: 0; border-bottom: 1px solid #D3D3D3'>
										<div class='resultDisplay' style='border-bottom: none;'>
											<div class='liveSearchProfilePic'>
												<img src='" . $taf_obj->getProfilePic() . "'>
											</div>						
											<div class='liveSearchText'>
												" . $taf_obj->getFirstAndLastName() . "
												<p>" . $taf ."</p>
											</div>
										</div>
									</a>";
					}
					$modal = "<div class='modal fade' id='tagged$id' tabindex='-1' role='dialog' aria-labelledby='postModalLabel' aria-hidden='true' style='overflow: hidden; padding-left: 0 !important;'>
								<div class='modal-dialog' style='top: 40vh; width: 90%; margin: 30px auto;'>
									<div class='modal-content' style='background-color: var(--bgc2);'>
										<div style='max-height: 40vh; overflow-y: auto; margin-bottom: 20px;'>
											<center class='special_name'>Tags</center>
											<ul class='dropdown-menu forwardee' style='display: block;'>
												<li>
													$list2
												</li>
											</ul>
										</div>
										<button type='button' style='width:100%;' class='btn btn-danger' data-dismiss='modal'>Cancel</button>
									</div>
								</div>
							</div>";
				}
				else
					$tag_stmt = $modal = "";
				$location = $row['location'];
				if($location)
					$location = "<span class='fa fa-map-marker fa-lg text-danger'></span> " . $location;
				$num_likes = $row['likes'] - 1;

				//Prepare user_to string so it can be included even if not posted to a user
				if($row['user_to'] == "none") {
					$user_to = $tag_stmt;
				}
				else {
					$user_to_obj = new User($this->con, $row['user_to']);
					$user_to_name = $user_to_obj->getFirstAndLastName();
					$user_to = "to <a href='" . $row['user_to'] ."'><b>" . $row['user_to'] . "</b></a> ".$tag_stmt;
				}

				//Check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					continue;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn);
				if($user_logged_obj->isFriend($added_by)){

					if($num_iterations++ < $start)
						continue; 

					//Once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

					if($userLoggedIn == $added_by){
						$delete_button = "<a class='delete_button text-danger' href='#' id='post$id'>Delete Post<span class='fa fa-remove fa-lg icons pull-left'></span></a><hr class='marginless'>";
						$edit_button = "<a href='to_post.php?id=$id' class='text-primary edit_button'>Edit Post<span class='fa fa-pencil fa-lg icons pull-left'></span></a><hr class='marginless'>";
						$unfriend_button = "";
					}
					else {
						$delete_button = "";
						$edit_button = "";
						if($user_logged_obj->isFriend($added_by))
							$unfriend_button = "<a class='text-danger' href='#' id='unfriend$added_by$id'>Unfriend @$added_by<span class='fa fa-frown-o fa-lg icons pull-left'></span></a><hr class='marginless'>";
						else
							$unfriend_button = "";
					}

					$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$first_name = $user_row['first_name'];
					$last_name = $user_row['last_name'];
					$profile_pic = $user_row['profile_pic'];


					?>
					<script> 
						function toggle<?php echo $id; ?>(event){
                                
						    var target = $(event.target);
						 
						    if (!target.is('a') && !target.is('button')) {
						        var element = document.getElementById("toggleComment<?php echo $id; ?>");
						 
						        if(element.style.display == "block")
						            element.style.display = "none";
						        else
						            element.style.display = "block";
						    }
						                                
						}

					</script>
					<?php

					$commentt = "";
					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id' AND posted_by!='$added_by' AND posted_by!='$userLoggedIn' AND removed='no' ORDER BY id DESC");
					$comments_check_num = mysqli_num_rows($comments_check);
					if($comments_check_num){
						while($os = mysqli_fetch_array($comments_check)){
							$commenter = $os['posted_by'];
							$commenter_obj = new User($this->con, $commenter);
							$com_pic = $commenter_obj->getProfilePic();
							if($this->user_obj->isFriend($commenter)){
								$comment = $os['post_body'];
								$comment = str_replace('<br />', " ", $comment);
								$commentt = "<img src='$com_pic' style='width: 1.4em; margin-right: 5px;'><b>".$commenter."</b>"." ". $comment. "<br>";
								break;
							}
						}
					}

					//Timeframe
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //Time of post
					$end_date = new DateTime($date_time_now); //Current time
					$interval = $start_date->diff($end_date); //Difference between dates 
					if($interval->y >= 1) {
						if($interval->y == 1)
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

					if($imagePath != "") {
						$imagePath = substr($imagePath, 0, -1);
						$imagePath = explode(",", $imagePath);
						$num = count($imagePath);
						$media = "";
						if($num == 1){
							if(strpos($imagePath[0], ".mp4") || strpos($imagePath[0], ".MOV")){
								$imageDiv = "<div class='carousel-container'><div class='carousel-slide'><video data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'></video></div></div>";
							}
							else
								$imageDiv = "<div class='carousel-container'><div class='carousel-slide'><img data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'></div></div>";
						}
	
						else if($num == 2){		
							if(strpos($imagePath[0], ".mp4") || strpos($imagePath[0], ".MOV"))
								$media = "<video style='border-top-left-radius: 25px; border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'></video>";
							else
								$media = "<img style='border-top-left-radius: 25px; border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'>";
							if(strpos($imagePath[1], ".mp4") || strpos($imagePath[1], ".MOV"))
								$media2 = "<video style='border-top-right-radius: 25px; border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'></video>";
							else
								$media2 = "<img style='border-top-right-radius: 25px; border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'>";
							$imageDiv = "<div class='carousel-container-2'><div class='carousel-slide-2'>$media$media2</div></div>";
						}
	
						else if($num == 3){
							if(strpos($imagePath[0], ".mp4") || strpos($imagePath[0], ".MOV"))
								$media = "<video class='img1' style='border-top-left-radius: 25px; border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'></video>";
							else
								$media = "<img class='img1' style='border-top-left-radius: 25px; border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'>";
							if(strpos($imagePath[1], ".mp4") || strpos($imagePath[1], ".MOV"))
								$media2 = "<video class='img2' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'></video>";
							else
								$media2 = "<img class='img2' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'>";							
							if(strpos($imagePath[2], ".mp4") || strpos($imagePath[2], ".MOV"))
								$media3 = "<video class='img3' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'></video>";
							else
								$media3 = "<img class='img3' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'>";
							$imageDiv = "<div class='carousel-container-2'><div class='carousel-slide-3'>$media$media2$media3</div></div>";
						}
						
						else if($num == 4){
							if(strpos($imagePath[0], ".mp4") || strpos($imagePath[0], ".MOV"))
								$media = "<video class='img1' style='border-top-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'></video>";
							else
								$media = "<img class='img1' style='border-top-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'>";							
							if(strpos($imagePath[1], ".mp4") || strpos($imagePath[1], ".MOV"))
								$media2 = "<video class='img2' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'></video>";
							else
								$media2 = "<img class='img2' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'>";							
							if(strpos($imagePath[2], ".mp4") || strpos($imagePath[2], ".MOV"))
								$media3 = "<video class='img3' style='border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'></video>";
							else
								$media3 = "<img class='img3' style='border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'>";							
							if(strpos($imagePath[3], ".mp4") || strpos($imagePath[3], ".MOV"))
								$media4 = "<video class='img4' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 3)' src='$imagePath[3]'></video>";
							else
								$media4 = "<img class='img4' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 3)' src='$imagePath[3]'>";
							$imageDiv = "<div class='carousel-container-3'><div class='carousel-slide-4'>$media$media2$media3$media4</div></div>";		
						}
	
						else if($num == 5){
							if(strpos($imagePath[0], ".mp4") || strpos($imagePath[0], ".MOV"))
								$media = "<video class='img1' style='border-top-left-radius: 25px; border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'></video>";
							else
								$media = "<img class='img1' style='border-top-left-radius: 25px; border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'>";							
							if(strpos($imagePath[1], ".mp4") || strpos($imagePath[1], ".MOV"))
								$media2 = "<video class='img2' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'></video>";
							else
								$media2 = "<img class='img2' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'>";							
							if(strpos($imagePath[2], ".mp4") || strpos($imagePath[2], ".MOV"))
								$media3 = "<video class='img3' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'></video>";
							else
								$media3 = "<img class='img3' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'>";
							if(strpos($imagePath[3], ".mp4") || strpos($imagePath[3], ".MOV"))
								$media4 = "<video class='img4' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 3)' src='$imagePath[3]'></video>";
							else
								$media4 = "<img class='img4' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 3)' src='$imagePath[3]'>";
							if(strpos($imagePath[4], ".mp4") || strpos($imagePath[4], ".MOV"))
								$media5 = "<video class='img5' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 4)' src='$imagePath[4]'></video>";
							else
								$media5 = "<img class='img5' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 4)' src='$imagePath[4]'>";
							$imageDiv = "<div class='carousel-container-6'><div class='carousel-slide-5'>$media$media2$media3$media4$media5</div></div>";
						}
	
						else{
							if(strpos($imagePath[0], ".mp4") || strpos($imagePath[0], ".MOV"))
								$media = "<video class='img1' style='border-top-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'></video>";
							else
								$media = "<img class='img1' style='border-top-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'>";
							if(strpos($imagePath[1], ".mp4") || strpos($imagePath[1], ".MOV"))
								$media2 = "<video class='img2' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'></video>";
							else
								$media2 = "<img class='img2' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'>";
							if(strpos($imagePath[2], ".mp4") || strpos($imagePath[2], ".MOV"))
								$media3 = "<video class='img3' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'></video>";
							else
								$media3 = "<img class='img3' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'>";
							if(strpos($imagePath[3], ".mp4") || strpos($imagePath[3], ".MOV"))
								$media4 = "<video class='img4' style='border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 3)' src='$imagePath[3]'></video>";
							else
								$media4 = "<img class='img4' style='border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 3)' src='$imagePath[3]'>";
							if(strpos($imagePath[4], ".mp4") || strpos($imagePath[4], ".MOV"))
								$media5 = "<video class='img5' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 4)' src='$imagePath[4]'></video>";
							else
								$media5 = "<img class='img5' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 4)' src='$imagePath[4]'>";
							if(strpos($imagePath[5], ".mp4") || strpos($imagePath[5], ".MOV"))
								$media6 = "<video class='img6' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 5)' src='$imagePath[5]'></video>";
							else
								$media6 = "<img class='img6' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 5)' src='$imagePath[5]'>";
							$imageDiv = "<div class='carousel-container-6'><div class='carousel-slide-6'>$media$media2$media3$media4$media5$media6</div></div>";
						}
					}
					else {
						$imageDiv = "";
					}
					
					if(strpos($body, "<iframe width='")){
						$imageDiv = $body;
						$body = "";
					}

					$body = nl2br($body);
					
					$body_array = preg_split("/\s+/", $body);

					foreach($body_array as $key => $value) {
						if(substr($value, 0, 1) == "#"){
							$hashtag = substr($value, 1);
							$value = "<a class='forward_link' href='hashtags.php?value=" . $hashtag ."' style='text-decoration: none';>$value</a>";
							$body_array[$key] = $value;
						}
						if(substr($value, 0, 1) == "@"){
							$name = substr($value, 1);
							$verify = mysqli_query($this->con, "SELECT * FROM users WHERE username='$name' AND user_closed='no'");
							if(mysqli_num_rows($verify)){
								$value = "<a class='forward_link' href='$name' style='text-decoration: none;'>$value</a>";
								$body_array[$key] = $value;
							}
						}
					}
					$body = implode(" ", $body_array);

					$pes_pic = $added_by_obj->getProfilePic();
					if($body && $imageDiv){
						$body = str_replace("<br />", "<div style='line-height: 0.7;'><br>.<br></div>", $body);						
						$text = "<a href='$added_by'><img src='$pes_pic' style='width: 1.4em; margin-right: 5px; margin-bottom: 5px;'><b style='color: grey !important;'>$added_by</b></a> $body <br>";
					}
					else
						$text = "";

					if($body){
						if(!$imageDiv){
							$rndImg = rand(0, 12);
							if($rndImg == 0)
								$image = "assets/images/a.jpg";
							if($rndImg == 1)
								$image = "assets/images/b.jpg";
							if($rndImg == 2)
								$image = "assets/images/c.jpg";
							if($rndImg == 3)
								$image = "assets/images/d.jpg";
							if($rndImg == 4)
								$image = "assets/images/e.jpg";
							if($rndImg == 5)
								$image = "assets/images/f.jpg";
							if($rndImg == 6)
								$image = "assets/images/g.jpg";
							if($rndImg == 7)
								$image = "assets/images/h.jpg";
							if($rndImg == 8)
								$image = "assets/images/i.jpg";
							if($rndImg == 9)
								$image = "assets/images/j.PNG";
							if($rndImg == 10)
								$image = "assets/images/k.jpg";
							if($rndImg == 11)
								$image = "assets/images/l.jpg";
							if($rndImg == 12)
								$image = "assets/images/m.jpg";
							$body = nl2br($body);
							$imageDiv = "<div class='bg_img' style='background-image: url($image);'><span class='no_img'>".$body."</span></div>";
							$body = "";
							$text = "";
						}
					}
					
					$usersReturnedQuery = mysqli_query($this->con, "SELECT * FROM users WHERE username = '$userLoggedIn' AND user_closed='no'");
					$row = mysqli_fetch_array($usersReturnedQuery);
					$view_obj = new User($this->con, $row['username']);
                    $friends = $view_obj->getFriendArray();
                    $friends = substr($friends, 1, -1);
					$friends = explode(',', $friends);
					sort($friends, SORT_STRING);
					$total=count($friends);
					$list = "";
					if($total>=1){
						$list .= "<div>Select all";
						for($k=0; $k<$total; $k++) {
							if($friends[$k]){
								$query2 = mysqli_query($this->con, "SELECT * FROM users WHERE username = '$friends[$k]' AND user_closed='no'");
								while($row2 = mysqli_fetch_array($query2)){            
									$fowadee = $row2['username'];
									$list .= "<div class='resultDisplay' style='border-bottom: none;' onclick='check$id(\"".$row2['username']."\")'>
												<div class='liveSearchProfilePic'>
													<img src='" . $row2['profile_pic'] ."'>
												</div>
							
												<div class='liveSearchText'>
													" . $row2['first_name'] . " " . $row2['last_name'] . "
													<p>" . $row2['username'] ."</p>
												</div>
												<div class='pull-right hrm' id='fow$fowadee$id'></div>
											</div>";
								}
							}
						}
					}
					// <a href='messages.php?u=$friends[$k]&forward=$id' style='padding: 0; border-bottom: 1px solid #D3D3D3'>
					// 							<div class='resultDisplay' style='border-bottom: none;'>
					// 								<div class='liveSearchProfilePic'>
					// 									<img src='" . $row2['profile_pic'] ."'>
					// 								</div>
								
					// 								<div class='liveSearchText'>
					// 									" . $row2['first_name'] . " " . $row2['last_name'] . "
					// 									<p>" . $row2['username'] ."</p>
					// 								</div>
					// 							</div>
					// 						</a>					
					else{
						$list .= "";
					}

					$check_query = mysqli_query($this->con, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$id'");
					$num_rows = mysqli_num_rows($check_query);
					if($num_rows > 0) {
						$lke = '<label onclick="unlike('.$id.')"><span onclick="sound.play()" class="fa fa-heart fa-lg text-danger"></span></label>';
					}
					else {
						$lke = '<label onclick="like('.$id.')"><span onclick="sound.play()" class="fa fa-heart-o fa-lg"></span></label>';
					}
					
					$likesQuery = mysqli_query($this->con, "SELECT username FROM likes WHERE post_id = '$id' AND username != '$userLoggedIn' ORDER BY id");
					if(mysqli_num_rows($likesQuery)){
						while($row = mysqli_fetch_array($likesQuery))
							$person = $row['username'];
						if($num_likes >= 1){
							$statement = "<div class='statement$id'><a href='likes.php?post_id=$id' class='black'>Liked by </a><a class='text-muted' href='$person'>$person</a><a href='likes.php?post_id=$id' class='black'> and $num_likes others </a></div>";
						}
						else{
							$statement = "";
						}
					}
					else
						$statement = "";

					$bookmarkQuery = mysqli_query($this->con, "SELECT * FROM bookmarks WHERE post_id = '$id' AND username = '$userLoggedIn'");
					if(mysqli_num_rows($bookmarkQuery))
						$bookmark_button = "<a href='#' id='unbookmark$id'><span class='fa fa-bookmark-o fa-lg icons pull-left'></span> Remove from Bookmarks</a>";
					else
						$bookmark_button = "<a href='#' id='bookmark$id'><span class='fa fa-bookmark-o fa-lg icons pull-left'></span> Bookmark this Post</a>";

					if($added_by != $userLoggedIn){
						$post_not_query = mysqli_query($this->con, "SELECT * FROM post_notifications WHERE person='$added_by' AND username='$userLoggedIn'");
						if(mysqli_num_rows($post_not_query))
							$post_not_button = "<hr class='marginless'><a href='#' id='unnotify$added_by$id'><span class='fa fa-chain-broken fa-lg icons pull-left'></span> Turn off post notifications</a>";
						else
							$post_not_button = "<hr class='marginless'><a href='#' id='notify$added_by$id'><span class='fa fa-chain fa-lg icons pull-left'></span> Turn on post notifications</a>";
					}
					else
						$post_not_button = "";

					$com_query = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id' AND removed='no'");
					$comm_num = mysqli_num_rows($com_query);
					if($comm_num > 1)
						$comm = "<a href='comments.php?post_id=$id' class='text-muted'>View all $comm_num comments</a> <br>";
					else
						$comm = "";

					$str .= "<div class='status_post'>
								<div class='row' style='margin-bottom: 10px; display: flex; align-items: center;'>
									<div class='post_profile_pic'>
										<img src='$profile_pic' width='50'>
									</div>

									<span class='posted_by col-xs-11' style='color:#ACACAC; line-height: 1.5; padding-left: 0;'>
										<a href='$added_by'><b>$added_by</b></a> $user_to <br>
										$location
									</span>
									<button class='btn ellipsis col-xs-1' data-toggle='modal' data-target='#menu$id'>...</button>
								</div>
								<div id='post_body' class='post$id'>
									$imageDiv
									<br>
								</div>

								<div class='newsfeedPostOptions'>
									<div class='row'>
										<div class='row' style='margin-bottom: 5;'>
											<div class='col-xs-1 like_box$id'>
												$lke
											</div>
											<div class='col-xs-1'>
												<a href='comments.php?post_id=$id'><span class='black fa fa-comment-o fa-lg'></span></a>
											</div>
											<button style='padding: 0; float: right; position: relative; height: 20;'' class='btn white' data-toggle='modal' data-target='#post_form$id'><span class='fa fa-paper-plane-o fa-lg'></span></button>
										</div>
										$statement
										$text
										$comm
										$commentt
										<section><a onClick='javascript:toggle$id(event)'><span class='black fa fa-comment-o'>&nbsp;
										<p class='inline text-muted'>Add a comment...</p></span>&nbsp;&nbsp;&nbsp;</a></section>
										<div class='time' style='float: none; font-size: 70%; padding: 0;'>$time_message</div> 
									</div>
								</div>

								<div class='modal fade' id='post_form$id' tabindex='-1' role='dialog' aria-labelledby='postModalLabel' aria-hidden='true' style='overflow: hidden; padding: 0 !important;'>
									<div class='modal-dialog modal_s' style='top: 40vh; width: 90%; margin: 30px auto;'>
										<div class='modal-content' style='background-color: var(--bgc2);'>
											<div class='input_group' style='color: var(--bgc); padding: 10px;'>
												<input type='search' autocomplete='off' onkeyup='getFriendz(this.value, $id)' id='search_box$id' placeholder='Search...' class='form-control inp'>
											</div>
											<div class='mentionees$id' style='max-height: 40vh; overflow-y: auto;'></div>
											<div id='mentionees$id' style='max-height: 40vh; overflow-y: auto;'>
												<ul class='dropdown-menu forwardee' style='display: block;'>
													<li>
														$list
													</li>
												</ul>
											</div>
											<input type='hidden' value='' id='tofow$id'>
											<input type='button' style='width:100%; background-color:var(--shadow2); color: #fff;' class='btn' disabled onclick='forwardMsgs$id()' id='forwardBtn$id' value='Send'>
										</div>
									</div>
								</div>
								<div class='modal fade' id='menu$id' tabindex='-1' role='dialog' aria-labelledby='postModalLabel' aria-hidden='true' style='overflow: hidden; padding-left: 0 !important;'>
									<div class='modal-dialog' style='top: 60vh; width: 90%; margin: 30px auto;'>
										<div class='modal-content' style='background-color: var(--bgc2);'>
										<ul class='dropdown-menu' style='display: block;'>
											<li><a class='text-danger' id='spam$id' href='#'><span class='fa fa-flag fa-lg icons pull-left'></span> Spam this post</a></li>
											<li class='divider' role='seperator'></li>
											<li>$unfriend_button</li>
											<li>$delete_button</li>
											<li>$edit_button</li>
											<li>$bookmark_button</li>
											<li>$post_not_button</li>
										</ul>
											<button type='button' style='width:100%;' class='btn btn-danger' data-dismiss='modal'>Cancel</button>
										</div>
									</div>
								</div>
								<div class='modal fade' id='med_modal$id' tabindex='-1' role='dialog' aria-labelledby='postModalLabel' aria-hidden='true' style='overflow: hidden; padding: 0;'>
									<div class='modal-dialog' style='position: static; margin: 0; width: 100%'>
										<div class='modal-content' style='height: 100%; border-radius: 0; background-color: var(--bgc);'>
											<div class='modal$id'></div>
											<button type='button' class='btn btn-default modal_close_btn' data-dismiss='modal' onclick='pause()'>Close</button>
										</div>
									</div>
								</div>
								$modal
							</div>
							<div class='post_comment' id='toggleComment$id' style='display:none;'>
								<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
							</div>
							<hr style='opacity :0;'>";
				}

				?>
				<script>
					function forwardMsgs<?php echo $id; ?>(){
						var id = <?php echo $id; ?>;
						var userLoggedIn = '<?php echo $userLoggedIn; ?>';
						var toSend = _("tofow"+id).value;
						var toSen = toSend.length - 1;
						var res = toSend.substring(0, toSen);
						res = res.split(",");
						for(var check of res){
							$("#fow"+check+id).removeClass("checkedd");
							$("#fow"+check+id).removeClass("fa");
							$("#fow"+check+id).removeClass("fa-check");
						}					
						var formdata = new FormData();
						var ajax = new XMLHttpRequest();
						// ajax.addEventListener('load', completeHandler+id, false);
						formdata.append('usernames', toSend);
						formdata.append('userLoggedIn', userLoggedIn);
						// formdata.append('text', text$idd);
						formdata.append('id', id);
						if(toSend){
							ajax.open('POST', 'includes/form_handlers/uploadForwardMessage.php');
							ajax.send(formdata);
						}
						_("tofow"+id).value = '';
						$("#post_form"+id).modal('hide');
						$('input[id="forwardBtn<?php echo $id; ?>"]').attr('disabled',true);
						$('input[id="forwardBtn<?php echo $id; ?>"]').css('background-color','var(--shadow2)');
					}
					function check<?php echo $id; ?>(person){
						var id = <?php echo $id; ?>;
						$("#fow"+person+id).toggleClass("checkedd");
						$("#fow"+person+id).toggleClass("fa");
						$("#fow"+person+id).toggleClass("fa-check");
						let body = _("tofow"+id).value
						var str = body.indexOf(person);
						if(str != -1){
							body = body.replace(person+",", "");
							_("tofow"+id).value = body;
							var bodd = _("tofow"+id).value
						}
						else{
							_("tofow"+id).value = body + person+",";
							var bodd = _("tofow"+id).value
						}
						if(bodd){							
							$('input[id="forwardBtn<?php echo $id; ?>"]').attr('disabled',false);
							$('input[id="forwardBtn<?php echo $id; ?>"]').css('background-color','var(--sbutton)');
						}
						else{
							$('input[id="forwardBtn<?php echo $id; ?>"]').attr('disabled',true);
							$('input[id="forwardBtn<?php echo $id; ?>"]').css('background-color','var(--shadow2)');
						}
					}
					$(document).ready(function() {

						$('input[id="search_box<?php echo $id; ?>"]').on('keyup',function(){							
							if($(this).val()){
								document.getElementById("mentionees<?php echo $id; ?>").style.display="none";
								// $('.mentionees<?php echo $id; ?>').css("marginBottom", "20px");
							}
							else{
								// document.getElementById("mentionees<?php echo $id; ?>").style.display="block";
								// $('.mentionees<?php echo $id; ?>').css("marginBottom", "0");
							}
						});

						$('#post<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to delete this post?", function(result) {

								$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

								if(result)
									location.reload();

							});
						});

						$('#spam<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to spam this post?", function(result) {

								$.post("includes/form_handlers/spam_post.php?post_id=<?php echo $id; ?>&username=<?php echo $userLoggedIn; ?>", {result:result});

								// if(result)
								// 	location.reload();

							});
						});
						
						$('#bookmark<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to save this post?", function(result) {

								$.post("includes/form_handlers/bookmark_post.php?post_id=<?php echo $id; ?>&username=<?php echo $userLoggedIn; ?>", {result:result});

								// if(result)
								// 	location.reload();

							});
						});
						
						$('#unbookmark<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to remove this post from your bookmarks?", function(result) {

								$.post("includes/form_handlers/unbookmark_post.php?post_id=<?php echo $id; ?>&username=<?php echo $userLoggedIn; ?>", {result:result});

								// if(result)
								// 	location.reload();

							});
						});

						$('#unfriend<?php echo $added_by; ?><?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to unfriend <?php echo $added_by; ?>?", function(result) {

								$.post("includes/form_handlers/unfriend.php?name=<?php echo $added_by; ?>", {result:result});

							});
						});		

						$('#notify<?php echo $added_by; ?><?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to turn on post notifications for <?php echo $added_by; ?>?", function(result) {

								$.post("includes/form_handlers/post_not.php?person=<?php echo $added_by; ?>&username=<?php echo $userLoggedIn; ?>", {result:result});

							});
						});	

						$('#unnotify<?php echo $added_by; ?><?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to turn off post notifications for <?php echo $added_by; ?>?", function(result) {

								$.post("includes/form_handlers/unpost_not.php?person=<?php echo $added_by; ?>&username=<?php echo $userLoggedIn; ?>", {result:result});

							});
						});	

					});

				</script>
				<?php

			} //End while loop

			if($count > $limit) 
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
			else 
				$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: center;' class='noMorePostsText'> No more posts to show! </p>";
		}

		echo $str;
	}

	public function fetchPopularPosts($data, $limit){
		$userLoggedIn = $this->user_obj->getUsername();

		$page = $data['page']; 

		if($page == 1) 
			$start = 0;
		else 
			$start = ($page - 1) * $limit;

		$str = $strs = "" ;

		$num_iterations = 0; //Number of results checked (not necasserily posted)
		$count = 1;

		$query = mysqli_query($this->con, "SELECT id FROM posts WHERE added_by!='$userLoggedIn' ORDER BY likes DESC");
		while($row = mysqli_fetch_array($query)){
			if($num_iterations++ < $start)
				continue;

			//Once 10 posts have been loaded, break
			if($count > $limit) {
				break;
			}
			else {
				$count++;
			}
			
			$id = $row['id'];
			$str.= $this->getSinglePost($id, $strs);
		}
		if($count > $limit) 
			$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
						<input type='hidden' class='noMorePosts' value='false'>";
		else 
			$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: center;' class='noMorePostsText'> No more posts to show! </p>";

		echo $str;
	}

	public function fetchHashtags($data, $limit){
		$userLoggedIn = $this->user_obj->getUsername();

		$page = $data['page']; 
		$type = $data['term']; 
		$hashtag = $data['value'];

		if($page == 1) 
			$start = 0;
		else 
			$start = ($page - 1) * $limit;

		$str = $strs = "" ;

		$num_iterations = 0; //Number of results checked (not necasserily posted)
		$count = 1;

		if($type == "top")
			$query = mysqli_query($this->con, "SELECT * FROM posts WHERE body LIKE '%$hashtag%' AND deleted='no' ORDER BY likes DESC");
		else if($type == "recent")
			$query = mysqli_query($this->con, "SELECT * FROM posts WHERE body LIKE '%$hashtag%' AND deleted='no' ORDER BY id DESC");
		else
			return;
		while($row = mysqli_fetch_array($query)){
			if($num_iterations++ < $start)
				continue;

			//Once 10 posts have been loaded, break
			if($count > $limit) {
				break;
			}
			else {
				$count++;
			}
			
			$id = $row['id'];
			$str.= $this->getSinglePost($id, $strs);
		}
		if($count > $limit) 
			$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
						<input type='hidden' class='noMorePosts' value='false'>";
		else 
			$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: center;' class='noMorePostsText'> No more posts to show! </p>";

		echo $str;
	}

	public function fetchBookmarks($data, $limit){
		$page = $data['page']; 
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1) 
			$start = 0;
		else 
			$start = ($page - 1) * $limit;

		$str = $strs = "" ;

		$num_iterations = 0; //Number of results checked (not necasserily posted)
		$count = 1;

		$query = mysqli_query($this->con, "SELECT * FROM bookmarks WHERE username='$userLoggedIn' ORDER BY id DESC");
		if(mysqli_num_rows($query) == 0)
			echo "You have no saved posts at this time!";
		else {
			while($row = mysqli_fetch_array($query)) {
				if($num_iterations++ < $start)
					continue;

				//Once 10 posts have been loaded, break
				if($count > $limit) {
					break;
				}
				else {
					$count++;
				}
				$id = $row['post_id'];
				$str .= $this->getSinglePost($id, "");
			}
		}    
		if($count > $limit) 
			$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
						<input type='hidden' class='noMorePosts' value='false'>";
		else 
			if($count > 1)
				$bloke = "<p style='text-align: center;' class='noMorePostsText'> No more posts to show! </p>";
			else
				$bloke = "";
			$str .= "<input type='hidden' class='noMorePosts' value='true'>$bloke";

		echo $str;
	}

	public function loadProfilePosts($data, $limit) {

		$page = $data['page']; 
		$profileUser = $data['profileUsername'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1) 
			$start = 0;
		else 
			$start = ($page - 1) * $limit;

		$str = ""; //String to return 
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND ((added_by='$profileUser') OR user_to='$profileUser')  ORDER BY id DESC");

		if(mysqli_num_rows($data_query) > 0) {


			$num_iterations = 0; //Number of results checked (not necasserily posted)
			$count = 1;

			while($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
				$imagePath = $row['image'];
				$tags = $row['tags'];
				if($tags){
					$list2 = "";
					$tags = substr($tags, 0, -1);
					$tags = explode(",", $tags);
					$tag_no = count($tags) - 1;
					if($tag_no > 1)
						$tag_no = "<span style='cursor: pointer;' data-toggle='modal' data-target='#tagged$id'> and $tag_no others</span>";
					else if($tag_no == 1)
						$tag_no = "<span style='cursor: pointer;' data-toggle='modal' data-target='#tagged$id'> and $tag_no other person</span>";
					else {
						$tag_no = "";
					}
					$target = $tags[0];
					$tag_stmt = "is with <a href='$target'><b>". $target ."</b></a>$tag_no";
					foreach($tags as $taf){
						$taf_obj = new User($this->con, $taf);						
						$list2 .= "<a href='$taf' style='padding: 0; border-bottom: 1px solid #D3D3D3'>
										<div class='resultDisplay' style='border-bottom: none;'>
											<div class='liveSearchProfilePic'>
												<img src='" . $taf_obj->getProfilePic() . "'>
											</div>						
											<div class='liveSearchText'>
												" . $taf_obj->getFirstAndLastName() . "
												<p>" . $taf ."</p>
											</div>
										</div>
									</a>";
					}
					$modal = "<div class='modal fade' id='tagged$id' tabindex='-1' role='dialog' aria-labelledby='postModalLabel' aria-hidden='true' style='overflow: hidden; padding-left: 0 !important;'>
								<div class='modal-dialog' style='top: 40vh; width: 90%; margin: 30px auto;'>
									<div class='modal-content' style='background-color: var(--bgc2);'>
										<div style='max-height: 40vh; overflow-y: auto; margin-bottom: 20px;'>
											<center class='special_name'>Tags</center>
											<ul class='dropdown-menu forwardee' style='display: block;'>
												<li>
													$list2
												</li>
											</ul>
										</div>
										<button type='button' style='width:100%;' class='btn btn-danger' data-dismiss='modal'>Cancel</button>
									</div>
								</div>
							</div>";
				}
				else
					$tag_stmt = $modal = "";
				$location = $row['location'];
				if($location)
					$location = "<span class='fa fa-map-marker fa-lg text-danger'></span> " . $location;

				$num_likes = $row['likes'] - 1;
				$user_to = $tag_stmt;

				if($num_iterations++ < $start)
					continue; 

				//Once 10 posts have been loaded, break
				if($count > $limit) {
					break;
				}
				else {
					$count++;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn);
				if($userLoggedIn == $added_by){
					$delete_button = "<a class='delete_button text-danger' href='#' id='post$id'>Delete Post<span class='fa fa-remove fa-lg icons pull-left'></span></a><hr class='marginless'>";
					$edit_button = "<a href='to_post.php?id=$id' class='text-primary edit_button'>Edit Post<span class='fa fa-pencil fa-lg icons pull-left'></span></a><hr class='marginless'>";
					$unfriend_button = "";
				}
				else {
					$delete_button = "";
					$edit_button = "";
					if($user_logged_obj->isFriend($added_by))
						$unfriend_button = "<a class='text-danger' href='#' id='unfriend$added_by$id'>Unfriend @$added_by<span class='fa fa-frown-o fa-lg icons pull-left'></span></a><hr class='marginless'>";
					else
						$unfriend_button = "";
				}


				$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
				$user_row = mysqli_fetch_array($user_details_query);
				$first_name = $user_row['first_name'];
				$last_name = $user_row['last_name'];
				$profile_pic = $user_row['profile_pic'];


				?>
				<script> 
					function toggle<?php echo $id; ?>(e) {

						if( !e ) e = window.event;

						var target = $(e.target);
						if (!target.is("a") && !target.is("button")) { 
							var element = document.getElementById("toggleComment<?php echo $id; ?>");

							if(element.style.display == "block") 
								element.style.display = "none";
							else 
								element.style.display = "block";
						}
					}

				</script>
				<?php

				$commentt = "";
				$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id' AND posted_by!='$added_by' AND posted_by!='$userLoggedIn' AND removed='no' ORDER BY id DESC");
				$comments_check_num = mysqli_num_rows($comments_check);
				if($comments_check_num){
					while($os = mysqli_fetch_array($comments_check)){
						$commenter = $os['posted_by'];
						$commenter_obj = new User($this->con, $commenter);
						$com_pic = $commenter_obj->getProfilePic();
						if($this->user_obj->isFriend($commenter)){
							$comment = $os['post_body'];
							$comment = str_replace('<br />', " ", $comment);
							$commentt = "<img src='$com_pic' style='width: 1.4em; margin-right: 5px; margin-bottom: 5px;'><b>".$commenter."</b>"." ". $comment. "<br>";
							break;
						}
					}
				}

				//Timeframe
				$date_time_now = date("Y-m-d H:i:s");
				$start_date = new DateTime($date_time); //Time of post
				$end_date = new DateTime($date_time_now); //Current time
				$interval = $start_date->diff($end_date); //Difference between dates 
				if($interval->y >= 1) {
					if($interval->y == 1)
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

				if($imagePath != "") {
					$imagePath = substr($imagePath, 0, -1);
					$imagePath = explode(",", $imagePath);
					$num = count($imagePath);
					$media = "";
					if($num == 1){
						if(strpos($imagePath[0], ".mp4") || strpos($imagePath[0], ".MOV")){
							$imageDiv = "<div class='carousel-container'><div class='carousel-slide'><video data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'></video></div></div>";
						}
						else
							$imageDiv = "<div class='carousel-container'><div class='carousel-slide'><img data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'></div></div>";
					}

					else if($num == 2){		
						if(strpos($imagePath[0], ".mp4") || strpos($imagePath[0], ".MOV"))
							$media = "<video style='border-top-left-radius: 25px; border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'></video>";
						else
							$media = "<img style='border-top-left-radius: 25px; border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'>";
						if(strpos($imagePath[1], ".mp4") || strpos($imagePath[1], ".MOV"))
							$media2 = "<video style='border-top-right-radius: 25px; border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'></video>";
						else
							$media2 = "<img style='border-top-right-radius: 25px; border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'>";
						$imageDiv = "<div class='carousel-container-2'><div class='carousel-slide-2'>$media$media2</div></div>";
					}

					else if($num == 3){
						if(strpos($imagePath[0], ".mp4") || strpos($imagePath[0], ".MOV"))
							$media = "<video class='img1' style='border-top-left-radius: 25px; border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'></video>";
						else
							$media = "<img class='img1' style='border-top-left-radius: 25px; border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'>";
						if(strpos($imagePath[1], ".mp4") || strpos($imagePath[1], ".MOV"))
							$media2 = "<video class='img2' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'></video>";
						else
							$media2 = "<img class='img2' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'>";							
						if(strpos($imagePath[2], ".mp4") || strpos($imagePath[2], ".MOV"))
							$media3 = "<video class='img3' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'></video>";
						else
							$media3 = "<img class='img3' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'>";
						$imageDiv = "<div class='carousel-container-2'><div class='carousel-slide-3'>$media$media2$media3</div></div>";
					}
					
					else if($num == 4){
						if(strpos($imagePath[0], ".mp4") || strpos($imagePath[0], ".MOV"))
							$media = "<video class='img1' style='border-top-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'></video>";
						else
							$media = "<img class='img1' style='border-top-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'>";							
						if(strpos($imagePath[1], ".mp4") || strpos($imagePath[1], ".MOV"))
							$media2 = "<video class='img2' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'></video>";
						else
							$media2 = "<img class='img2' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'>";							
						if(strpos($imagePath[2], ".mp4") || strpos($imagePath[2], ".MOV"))
							$media3 = "<video class='img3' style='border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'></video>";
						else
							$media3 = "<img class='img3' style='border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'>";							
						if(strpos($imagePath[3], ".mp4") || strpos($imagePath[3], ".MOV"))
							$media4 = "<video class='img4' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 3)' src='$imagePath[3]'></video>";
						else
							$media4 = "<img class='img4' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 3)' src='$imagePath[3]'>";
						$imageDiv = "<div class='carousel-container-3'><div class='carousel-slide-4'>$media$media2$media3$media4</div></div>";		
					}

					else if($num == 5){
						if(strpos($imagePath[0], ".mp4") || strpos($imagePath[0], ".MOV"))
							$media = "<video class='img1' style='border-top-left-radius: 25px; border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'></video>";
						else
							$media = "<img class='img1' style='border-top-left-radius: 25px; border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'>";							
						if(strpos($imagePath[1], ".mp4") || strpos($imagePath[1], ".MOV"))
							$media2 = "<video class='img2' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'></video>";
						else
							$media2 = "<img class='img2' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'>";							
						if(strpos($imagePath[2], ".mp4") || strpos($imagePath[2], ".MOV"))
							$media3 = "<video class='img3' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'></video>";
						else
							$media3 = "<img class='img3' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'>";
						if(strpos($imagePath[3], ".mp4") || strpos($imagePath[3], ".MOV"))
							$media4 = "<video class='img4' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 3)' src='$imagePath[3]'></video>";
						else
							$media4 = "<img class='img4' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 3)' src='$imagePath[3]'>";
						if(strpos($imagePath[4], ".mp4") || strpos($imagePath[4], ".MOV"))
							$media5 = "<video class='img5' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 4)' src='$imagePath[4]'></video>";
						else
							$media5 = "<img class='img5' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 4)' src='$imagePath[4]'>";
						$imageDiv = "<div class='carousel-container-6'><div class='carousel-slide-5'>$media$media2$media3$media4$media5</div></div>";
					}

					else{
						if(strpos($imagePath[0], ".mp4") || strpos($imagePath[0], ".MOV"))
							$media = "<video class='img1' style='border-top-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'></video>";
						else
							$media = "<img class='img1' style='border-top-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'>";
						if(strpos($imagePath[1], ".mp4") || strpos($imagePath[1], ".MOV"))
							$media2 = "<video class='img2' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'></video>";
						else
							$media2 = "<img class='img2' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'>";
						if(strpos($imagePath[2], ".mp4") || strpos($imagePath[2], ".MOV"))
							$media3 = "<video class='img3' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'></video>";
						else
							$media3 = "<img class='img3' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'>";
						if(strpos($imagePath[3], ".mp4") || strpos($imagePath[3], ".MOV"))
							$media4 = "<video class='img4' style='border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 3)' src='$imagePath[3]'></video>";
						else
							$media4 = "<img class='img4' style='border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 3)' src='$imagePath[3]'>";
						if(strpos($imagePath[4], ".mp4") || strpos($imagePath[4], ".MOV"))
							$media5 = "<video class='img5' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 4)' src='$imagePath[4]'></video>";
						else
							$media5 = "<img class='img5' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 4)' src='$imagePath[4]'>";
						if(strpos($imagePath[5], ".mp4") || strpos($imagePath[5], ".MOV"))
							$media6 = "<video class='img6' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 5)' src='$imagePath[5]'></video>";
						else
							$media6 = "<img class='img6' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 5)' src='$imagePath[5]'>";
						$imageDiv = "<div class='carousel-container-6'><div class='carousel-slide-6'>$media$media2$media3$media4$media5$media6</div></div>";
					}
				}
				else {
					$imageDiv = "";
				}

				if(strpos($body, "<iframe width=")){
					$imageDiv = $body;
					$body = "";
					// header("Location: about.php");
				}
				// <iframe width='100%' height='40%' src='http://www.youtube.com/embed/ajhsdkfasdkfaf'></iframe><br>

				$body = nl2br($body);

				$body_array = preg_split("/\s+/", $body);

				foreach($body_array as $key => $value) {
					if(substr($value, 0, 1) == "#"){
						$hashtag = substr($value, 1);
						$value = "<a class='forward_link' href='hashtags.php?value=" . $hashtag ."' style='text-decoration: none';>$value</a>";
						$body_array[$key] = $value;
					}
					if(substr($value, 0, 1) == "@"){
						$name = substr($value, 1);
						$verify = mysqli_query($this->con, "SELECT * FROM users WHERE username='$name' AND user_closed='no'");
						if(mysqli_num_rows($verify)){
							$value = "<a class='forward_link' href='$name' style='text-decoration: none;'>$value</a>";
							$body_array[$key] = $value;
						}
					}
				}
				$body = implode(" ", $body_array);

				$added_by_obj = new User($this->con, $added_by);
				$pes_pic = $added_by_obj->getProfilePic();
				if($body && $imageDiv){
					$body = str_replace("<br />", "<div style='line-height: 0.7;'><br>.<br></div>", $body);
					$text = "<a href='$added_by'><img src='$pes_pic' style='width: 1.4em; margin-right: 5px; margin-bottom: 5px;'><b style='color: grey !important;'>$added_by</b></a> $body <br>";
				}
				else
					$text = "";

				if($body){
					if(!$imageDiv){
						$rndImg = rand(0, 12);
						if($rndImg == 0)
							$image = "assets/images/a.jpg";
						if($rndImg == 1)
							$image = "assets/images/b.jpg";
						if($rndImg == 2)
							$image = "assets/images/c.jpg";
						if($rndImg == 3)
							$image = "assets/images/d.jpg";
						if($rndImg == 4)
							$image = "assets/images/e.jpg";
						if($rndImg == 5)
							$image = "assets/images/f.jpg";
						if($rndImg == 6)
							$image = "assets/images/g.jpg";
						if($rndImg == 7)
							$image = "assets/images/h.jpg";
						if($rndImg == 8)
							$image = "assets/images/i.jpg";
						if($rndImg == 9)
							$image = "assets/images/j.PNG";
						if($rndImg == 10)
							$image = "assets/images/k.jpg";
						if($rndImg == 11)
							$image = "assets/images/l.jpg";
						if($rndImg == 12)
							$image = "assets/images/m.jpg";
						// $body = nl2br($body);
						$imageDiv = "<div class='bg_img' style='background-image: url($image);'><span class='no_img'>".$body."</span></div>";
						$body = "";
						$text = "";
					}
				}

				$usersReturnedQuery = mysqli_query($this->con, "SELECT * FROM users WHERE username = '$userLoggedIn' AND user_closed='no'");
				$row = mysqli_fetch_array($usersReturnedQuery);
				$view_obj = new User($this->con, $row['username']);
				$friends = $view_obj->getFriendArray();
				$friends = substr($friends, 1, -1);
				$friends = explode(',', $friends);
				sort($friends, SORT_STRING);
				$total=count($friends);
				$list = "";
				if($total>=1){
					for($k=0; $k<$total; $k++) {
						if($friends[$k]){
							$query2 = mysqli_query($this->con, "SELECT * FROM users WHERE username = '$friends[$k]' AND user_closed='no'");
							while($row2 = mysqli_fetch_array($query2)){            
								$fowadee = $row2['username'];
								$list .= "<div class='resultDisplay' style='border-bottom: none;' onclick='check$id(\"".$row2['username']."\")'>
											<div class='liveSearchProfilePic'>
												<img src='" . $row2['profile_pic'] ."'>
											</div>
						
											<div class='liveSearchText'>
												" . $row2['first_name'] . " " . $row2['last_name'] . "
												<p>" . $row2['username'] ."</p>
											</div>
											<div class='pull-right hrm' id='fow$fowadee$id'></div>
										</div>";
							}
						}
					}
				}
				else{
					$list.="";
				}				
				
				$check_query = mysqli_query($this->con, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$id'");
				$num_rows = mysqli_num_rows($check_query);
				if($num_rows > 0) {
					$lke = '<label onclick="unlike('.$id.')"><span onclick="sound.play()" class="fa fa-heart fa-lg text-danger"></span></label>';
				}
				else {
					$lke = '<label onclick="like('.$id.')"><span onclick="sound.play()" class="fa fa-heart-o fa-lg"></span></label>';
				}

				$likesQuery = mysqli_query($this->con, "SELECT username FROM likes WHERE post_id = '$id' AND username != '$userLoggedIn' ORDER BY id");
				while($row = mysqli_fetch_array($likesQuery))
					$person = $row['username'];
				if($num_likes >= 1){
					$statement = "<div class='statement$id'><a href='likes.php?post_id=$id' class='black'>Liked by </a><a class='text-muted' href='$person'>$person</a><a href='likes.php?post_id=$id' class='black'> and $num_likes others </a></div>";
				}
				else{
					$statement = "";
				}

				$com_query = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id' AND removed='no'");
				$comm_num = mysqli_num_rows($com_query);
				if($comm_num > 1)
					$comm = "<a href='comments.php?post_id=$id' class='text-muted'>View all $comm_num comments</a> <br>";
				else
					$comm = "";

				$bookmarkQuery = mysqli_query($this->con, "SELECT * FROM bookmarks WHERE post_id = '$id' AND username = '$userLoggedIn'");
				if(mysqli_num_rows($bookmarkQuery))
					$bookmark_button = "<a href='#' id='unbookmark$id'><span class='fa fa-bookmark-o fa-lg icons pull-left'></span> Remove from Bookmarks</a>";
				else
					$bookmark_button = "<a href='#' id='bookmark$id'><span class='fa fa-bookmark-o fa-lg icons pull-left'></span> Bookmark this Post</a>";
			
				if($added_by != $userLoggedIn){
					$post_not_query = mysqli_query($this->con, "SELECT * FROM post_notifications WHERE person='$added_by' AND username='$userLoggedIn'");
					if(mysqli_num_rows($post_not_query))
						$post_not_button = "<hr class='marginless'><a href='#' id='unnotify$added_by$id'><span class='fa fa-chain-broken fa-lg icons pull-left'></span> Turn off post notifications</a>";
					else
						$post_not_button = "<hr class='marginless'><a href='#' id='notify$added_by$id'><span class='fa fa-chain fa-lg icons pull-left'></span> Turn on post notifications</a>";
				}
				else
					$post_not_button = "";

					$str .= "<div class='status_post'>
					<div class='row' style='margin-bottom: 10px; display: flex; align-items: center;'>
						<div class='post_profile_pic'>
							<img src='$profile_pic' width='50'>
						</div>
						<span class='posted_by col-xs-11' style='color:#ACACAC; line-height: 1.5; padding-left: 0;'>
							<a href='$added_by'><b>$added_by</b></a> $user_to <br>
							$location
						</span>
						<button class='btn ellipsis col-xs-1' data-toggle='modal' data-target='#menu$id'>...</button>
					</div>
					<div id='post_body' class='post$id'>						
						$imageDiv			
						<br>															
					</div>

					<div class='newsfeedPostOptions'>
						<div class='row'>
							<div class='row' style='margin-bottom: 5;'>
								<div class='col-xs-1 like_box$id'>
									$lke
								</div>
								<div class='col-xs-1'>
									<a href='comments.php?post_id=$id'><span class='black fa fa-comment-o fa-lg'></span></a>
								</div>
								<button style='padding: 0; float: right; position: relative; height: 20;'' class='btn white' data-toggle='modal' data-target='#post_form$id'><span class='fa fa-paper-plane-o fa-lg'></span></button>
							</div>
							$statement
							$text
							$comm
							$commentt
							<section><a onClick='javascript:toggle$id(event)'><span class='black fa fa-comment-o'>&nbsp;
							<p class='inline text-muted'>Add a comment...</p></span></a></section>
							<div class='time' style='float: none; font-size: 70%; padding: 0;'>$time_message</div> 
						</div>
					</div>
					<div class='modal fade' id='post_form$id' tabindex='-1' role='dialog' aria-labelledby='postModalLabel' aria-hidden='true' style='overflow: hidden; padding: 0 !important;'>
						<div class='modal-dialog modal_s' style='top: 40vh; width: 90%; margin: 30px auto;'>
							<div class='modal-content' style='background-color: var(--bgc2);'>
								<div class='input_group' style='color: var(--bgc); padding: 10px;'>
									<input type='search' autocomplete='off' onkeyup='getFriendz(this.value, $id)' id='search_box$id' placeholder='Search...' class='form-control inp'>
								</div>
								<div class='mentionees$id' style='max-height: 40vh; overflow-y: auto;'></div>
								<div id='mentionees$id' style='max-height: 40vh; overflow-y: auto;'>
									<ul class='dropdown-menu forwardee' style='display: block;'>
										<li>
											$list
										</li>
									</ul>
								</div>
								<input type='hidden' value='' id='tofow$id'>
								<input type='button' style='width:100%; background-color:var(--shadow2); color: #fff;' class='btn' disabled onclick='forwardMsgs$id()' id='forwardBtn$id' value='Send'>
							</div>
						</div>
					</div>
					<div class='modal fade' id='menu$id' tabindex='-1' role='dialog' aria-labelledby='postModalLabel' aria-hidden='true' style='overflow: hidden; padding-left: 0 !important;'>
						<div class='modal-dialog' style='top: 60vh; width: 90%; margin: 30px auto;'>
							<div class='modal-content' style='background-color: var(--bgc2);'>
							<ul class='dropdown-menu' style='display: block;'>
								<li><a class='text-danger' id='spam$id' href='#'><span class='fa fa-flag fa-lg icons pull-left'></span> Spam this post</a></li>
								<li class='divider' role='seperator'></li>
								<li>$unfriend_button</li>
								<li>$delete_button</li>
								<li>$edit_button</li>
								<li>$bookmark_button</li>
								<li>$post_not_button</li>
							</ul>
								<button type='button' style='width:100%;' class='btn btn-danger' data-dismiss='modal'>Cancel</button>
							</div>
						</div>
					</div>
					<div class='modal fade' id='med_modal$id' tabindex='-1' role='dialog' aria-labelledby='postModalLabel' aria-hidden='true' style='overflow: hidden; padding: 0;'>
						<div class='modal-dialog' style='position: static; margin: 0; width: 100%'>
						<div class='modal-content' style='height: 100%; border-radius: 0; background-color: var(--bgc);'>
								<div class='modal$id'></div>
								<button type='button' class='btn btn-default modal_close_btn' data-dismiss='modal' onclick='pause()'>Close</button>
							</div>
						</div>
					</div>
					$modal
				</div>
						<div class='post_comment' id='toggleComment$id' style='display:none;'>
							<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
						</div>
						<hr style='opacity: 0;'>";

				?>
				<script>
					function forwardMsgs<?php echo $id; ?>(){
						var id = <?php echo $id; ?>;
						var userLoggedIn = '<?php echo $userLoggedIn; ?>';
						var toSend = _("tofow"+id).value;
						var toSen = toSend.length - 1;
						var res = toSend.substring(0, toSen);
						res = res.split(",");
						for(var check of res){
							$("#fow"+check+id).removeClass("checkedd");
							$("#fow"+check+id).removeClass("fa");
							$("#fow"+check+id).removeClass("fa-check");
						}					
						var formdata = new FormData();
						var ajax = new XMLHttpRequest();
						// ajax.addEventListener('load', completeHandler+id, false);
						formdata.append('usernames', toSend);
						formdata.append('userLoggedIn', userLoggedIn);
						// formdata.append('text', text$idd);
						formdata.append('id', id);
						if(toSend){
							ajax.open('POST', 'includes/form_handlers/uploadForwardMessage.php');
							ajax.send(formdata);
						}
						_("tofow"+id).value = '';
						$("#post_form"+id).modal('hide');
						$('input[id="forwardBtn<?php echo $id; ?>"]').attr('disabled',true);
						$('input[id="forwardBtn<?php echo $id; ?>"]').css('background-color','var(--shadow2)');
					}
					function check<?php echo $id; ?>(person){
						var id = <?php echo $id; ?>;
						$("#fow"+person+id).toggleClass("checkedd");
						$("#fow"+person+id).toggleClass("fa");
						$("#fow"+person+id).toggleClass("fa-check");
						let body = _("tofow"+id).value
						var str = body.indexOf(person);
						if(str != -1){
							body = body.replace(person+",", "");
							_("tofow"+id).value = body;
							var bodd = _("tofow"+id).value
						}
						else{
							_("tofow"+id).value = body + person+",";
							var bodd = _("tofow"+id).value
						}
						if(bodd){							
							$('input[id="forwardBtn<?php echo $id; ?>"]').attr('disabled',false);
							$('input[id="forwardBtn<?php echo $id; ?>"]').css('background-color','var(--sbutton)');
						}
						else{
							$('input[id="forwardBtn<?php echo $id; ?>"]').attr('disabled',true);
							$('input[id="forwardBtn<?php echo $id; ?>"]').css('background-color','var(--shadow2)');
						}
					}

					$(document).ready(function() {

						$('input[id="search_box<?php echo $id; ?>"]').on('keyup',function(){							
							if($(this).val()){
								document.getElementById("mentionees<?php echo $id; ?>").style.display="none";
							}
						});
				
						$('#post<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to delete this post?", function(result) {

								$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

								if(result)
									location.reload();

							});
						});

						$('#spam<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to spam this post?", function(result) {

								$.post("includes/form_handlers/spam_post.php?post_id=<?php echo $id; ?>&username=<?php echo $userLoggedIn; ?>", {result:result});

							});
						});

						$('#bookmark<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to save this post?", function(result) {

								$.post("includes/form_handlers/bookmark_post.php?post_id=<?php echo $id; ?>&username=<?php echo $userLoggedIn; ?>", {result:result});

							});
						});

						$('#unbookmark<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to remove this post from your bookmarks?", function(result) {

								$.post("includes/form_handlers/unbookmark_post.php?post_id=<?php echo $id; ?>&username=<?php echo $userLoggedIn; ?>", {result:result});

							});
						});

						$('#unfriend<?php echo $added_by; ?><?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to unfriend <?php echo $added_by; ?> ?", function(result) {

								$.post("includes/form_handlers/unfriend.php?name=<?php echo $added_by; ?>", {result:result});

							});
						});

						$('#notify<?php echo $added_by; ?><?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to turn on post notifications for <?php echo $added_by; ?>?", function(result) {

								$.post("includes/form_handlers/post_not.php?person=<?php echo $added_by; ?>&username=<?php echo $userLoggedIn; ?>", {result:result});

							});
						});	

						$('#unnotify<?php echo $added_by; ?><?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to turn off post notifications for <?php echo $added_by; ?>?", function(result) {

								$.post("includes/form_handlers/unpost_not.php?person=<?php echo $added_by; ?>&username=<?php echo $userLoggedIn; ?>", {result:result});

							});
						});	

					});

				</script>
				<?php

			} //End while loop

			if($count > $limit) 
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
			else 
				$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: center;' class='noMorePostsText'> No more posts to show! </p>";
		}

		echo $str;
	}

	public function loadMedia($data, $limit) {
		$page = $data['page']; 
		$username = $data['profileUsername'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1) 
			$start = 0;
		else 
			$start = ($page - 1) * $limit;

		$str = ""; //String to return 
		$query = mysqli_query($this->con, "SELECT * FROM posts WHERE (added_by='$username' OR user_to='$username') AND image!='' ORDER BY id DESC");
		$pics = $ids = $keyz = $date = $loc = array();
		if(mysqli_num_rows($query)){
			$num_iterations = 0; //Number of results checked (not necasserily posted)
			$count = 1;

			while($row = mysqli_fetch_array($query)){				
				$media = $row['image'];
				$id = $row['id'];
				$location = $row['location'];
				$date_added = $row['date_added'];
				$media = explode(",", $media);
				foreach($media as $keyy => $med)
					if($med){
						array_push($pics, $med);
						array_push($keyz, $keyy);
						array_push($ids, $id);
						array_push($loc, $location);
						array_push($date, $date_added);
					}
			}

			foreach($pics as $key => $pic){				
				if($num_iterations++ < $start)
					continue; 

				//Once 10 posts have been loaded, break
				if($count > $limit) {
					break;
				}
				else {
					$count++;
				}

				$index = $keyz[$key];
				$idx = $ids[$key];				
				$loct = $loc[$key];
				if($loct)
					$loct = "<div style='font-size: large;'>" .$loc[$key]. "</div>";
				$time = $date[$key];

				$yiu = $idx."_".$index;	
				
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

				if(strpos($pic, ".mp4") || strpos($pic, ".MOV")){
					$str .= "<div class='med_hold col-xs-4 col-sm-3' data-toggle='modal' data-target='#$yiu'><video src='$pic'></video></div>
							<div class='modal fade' id=\"".$yiu."\" tabindex='-1' role='dialog' aria-labelledby='postModalLabel' aria-hidden='true' style='overflow: hidden; padding: 0;'>
								<div class='modal-dialog' style='position: static; margin: 0; width: 100%'>
									<div class='modal-content' style='height: 100%; border-radius: 0;'>
										<center class='sp_date'>$loct<div>$dyea</div></center>
										<video loop controls src='$pic' style='height: 87%; width: 100%;' id='vid$yiu'></video>
										<button type='button' style='width:100%; height: 5%' class='btn btn-default' onclick='pause$yiu()' data-dismiss='modal'>Close</button>
									</div>
								</div>
							</div>
							<script>
								function pause$yiu(){
									_('vid$yiu').pause();
								}
							</script>";						
				}
				else
					$str .= "<div class='med_hold col-xs-4 col-sm-3' data-toggle='modal' data-target='#$yiu'><img src='$pic'></div>					
							<div class='modal fade' id=\"".$yiu."\" tabindex='-1' role='dialog' aria-labelledby='postModalLabel' aria-hidden='true' style='overflow: hidden; padding: 0;'>
								<div class='modal-dialog' style='position: static; margin: 0; width: 100%'>
									<div class='modal-content' style='height: 100%; border-radius: 0;'>									
										<center class='sp_date'>$loct<div>$dyea</div></center>
										<img src='$pic' style='height: 87%; width: 100%;'>
										<button type='button' style='width:100%; height: 5%' class='btn btn-default' data-dismiss='modal'>Close</button>
									</div>
								</div>
							</div>";
			}
			if($count > $limit) 
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
			else 
				$str .= "<input type='hidden' class='noMorePosts' value='true'>";
		}
		echo $str;
	}

	public function getSinglePost($post_id, $pc) {

		$userLoggedIn = $this->user_obj->getUsername();

		$opened_query = mysqli_query($this->con, "UPDATE notifications SET opened='yes' WHERE user_to='$userLoggedIn' AND link LIKE '%=$post_id'");

		$str = ""; //String to return 
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND id='$post_id'");

		if(mysqli_num_rows($data_query) > 0) {
				$row = mysqli_fetch_array($data_query); 
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
				$imagePath = $row['image'];
				$tags = $row['tags'];
				if($tags){
					$list2 = "";
					$tags = substr($tags, 0, -1);
					$tags = explode(",", $tags);
					$tag_no = count($tags) - 1;
					if($tag_no > 1)
						$tag_no = "<span style='cursor: pointer;' data-toggle='modal' data-target='#tagged$id'> and $tag_no others</span>";
					else if($tag_no == 1)
						$tag_no = "<span style='cursor: pointer;' data-toggle='modal' data-target='#tagged$id'> and $tag_no other person</span>";
					else {
						$tag_no = "";
					}
					$target = $tags[0];
					if($row['user_to'] == "none")
						$dk = "";
					else
						$dk = "and ";
					$tag_stmt = $dk."is with <a href='$target'><b>". $target ."</b></a>$tag_no";
					foreach($tags as $taf){
						$taf_obj = new User($this->con, $taf);						
						$list2 .= "<a href='$taf' style='padding: 0; border-bottom: 1px solid #D3D3D3'>
										<div class='resultDisplay' style='border-bottom: none;'>
											<div class='liveSearchProfilePic'>
												<img src='" . $taf_obj->getProfilePic() . "'>
											</div>						
											<div class='liveSearchText'>
												" . $taf_obj->getFirstAndLastName() . "
												<p>" . $taf ."</p>
											</div>
										</div>
									</a>";
					}
					$modal = "<div class='modal fade' id='tagged$id' tabindex='-1' role='dialog' aria-labelledby='postModalLabel' aria-hidden='true' style='overflow: hidden; padding-left: 0 !important;'>
								<div class='modal-dialog' style='top: 40vh; width: 90%; margin: 30px auto;'>
									<div class='modal-content' style='background-color: var(--bgc2);'>
										<div style='max-height: 40vh; overflow-y: auto; margin-bottom: 20px;'>
											<center class='special_name'>Tags</center>
											<ul class='dropdown-menu forwardee' style='display: block;'>
												<li>
													$list2
												</li>
											</ul>
										</div>
										<button type='button' style='width:100%;' class='btn btn-danger' data-dismiss='modal'>Cancel</button>
									</div>
								</div>
							</div>";
				}
				else
					$tag_stmt = $modal = "";
				$location = $row['location'];
				if($location)
					$location = "<span class='fa fa-map-marker fa-lg text-danger'></span> " . $location;
				$num_likes = $row['likes'] - 1;

				//Prepare user_to string so it can be included even if not posted to a user
				if($row['user_to'] == "none") {
					$user_to = $tag_stmt;
				}
				else {
					$user_to_obj = new User($this->con, $row['user_to']);
					$user_to_name = $user_to_obj->getFirstAndLastName();
					$user_to = "to <a href='" . $row['user_to'] ."'><b>" . $row['user_to'] . "</b></a> ".$tag_stmt;
				}

				//Check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					return;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn);
				// if($user_logged_obj->isFriend($added_by)){


					if($userLoggedIn == $added_by){
						$delete_button = "<a href='#' class='delete_button text-danger' id='post$id'>Delete Post<span class='fa fa-remove fa-lg icons pull-left'></span></a><hr class='marginless'>";
						$edit_button = "<a href='to_post.php?id=$id' class='text-primary edit_button' id=''>Edit Post<span class='fa fa-pencil fa-lg icons pull-left'></span></a><hr class='marginless'>";
						$unfriend_button = "";
					}
					else {
						$delete_button = "";
						$edit_button = "";
						if($user_logged_obj->isFriend($added_by))
							$unfriend_button = "<a class='text-danger' href='#' id='unfriend$added_by$id'>Unfriend @$added_by<span class='fa fa-frown-o fa-lg icons pull-left'></span></a><hr class='marginless'>";
						else
							$unfriend_button = "";
					}


					$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$first_name = $user_row['first_name'];
					$last_name = $user_row['last_name'];
					$profile_pic = $user_row['profile_pic'];


					?>
					<script> 
						function toggle<?php echo $id; ?>(e) {

 							if( !e ) e = window.event;

							var target = $(e.target);
							if (!target.is("a")) {
								var element = document.getElementById("toggleComment<?php echo $id; ?>");

								if(element.style.display == "block") 
									element.style.display = "none";
								else 
									element.style.display = "block";
							}
						}

					</script>
					<?php

						$commentt = "";
						$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id' AND posted_by!='$added_by' AND posted_by!='$userLoggedIn' AND removed='no' ORDER BY id DESC");
						$comments_check_num = mysqli_num_rows($comments_check);
						if($comments_check_num){
							while($os = mysqli_fetch_array($comments_check)){
								$commenter = $os['posted_by'];
								$commenter_obj = new User($this->con, $commenter);
								$com_pic = $commenter_obj->getProfilePic();
								if($this->user_obj->isFriend($commenter)){
									$comment = $os['post_body'];
									$comment = str_replace('<br />', " ", $comment);
									$commentt = "<img src='$com_pic' style='width: 1.4em; margin-right: 5px; margin-bottom: 5px;'><b>".$commenter."</b>"." ". $comment. "<br>";
									break;
								}
							}
						}

					//Timeframe
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //Time of post
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

					if($imagePath != "") {
						$imagePath = substr($imagePath, 0, -1);
						$imagePath = explode(",", $imagePath);
						$num = count($imagePath);
						$media = "";
						if($num == 1){
							if(strpos($imagePath[0], ".mp4") || strpos($imagePath[0], ".MOV")){
								$imageDiv = "<div class='carousel-container'><div class='carousel-slide'><video data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'></video></div></div>";
							}
							else
								$imageDiv = "<div class='carousel-container'><div class='carousel-slide'><img data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'></div></div>";
						}
	
						else if($num == 2){		
							if(strpos($imagePath[0], ".mp4") || strpos($imagePath[0], ".MOV"))
								$media = "<video style='border-top-left-radius: 25px; border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'></video>";
							else
								$media = "<img style='border-top-left-radius: 25px; border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'>";
							if(strpos($imagePath[1], ".mp4") || strpos($imagePath[1], ".MOV"))
								$media2 = "<video style='border-top-right-radius: 25px; border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'></video>";
							else
								$media2 = "<img style='border-top-right-radius: 25px; border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'>";
							$imageDiv = "<div class='carousel-container-2'><div class='carousel-slide-2'>$media$media2</div></div>";
						}
	
						else if($num == 3){
							if(strpos($imagePath[0], ".mp4") || strpos($imagePath[0], ".MOV"))
								$media = "<video class='img1' style='border-top-left-radius: 25px; border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'></video>";
							else
								$media = "<img class='img1' style='border-top-left-radius: 25px; border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'>";
							if(strpos($imagePath[1], ".mp4") || strpos($imagePath[1], ".MOV"))
								$media2 = "<video class='img2' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'></video>";
							else
								$media2 = "<img class='img2' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'>";							
							if(strpos($imagePath[2], ".mp4") || strpos($imagePath[2], ".MOV"))
								$media3 = "<video class='img3' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'></video>";
							else
								$media3 = "<img class='img3' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'>";
							$imageDiv = "<div class='carousel-container-2'><div class='carousel-slide-3'>$media$media2$media3</div></div>";
						}
						
						else if($num == 4){
							if(strpos($imagePath[0], ".mp4") || strpos($imagePath[0], ".MOV"))
								$media = "<video class='img1' style='border-top-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'></video>";
							else
								$media = "<img class='img1' style='border-top-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'>";							
							if(strpos($imagePath[1], ".mp4") || strpos($imagePath[1], ".MOV"))
								$media2 = "<video class='img2' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'></video>";
							else
								$media2 = "<img class='img2' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'>";							
							if(strpos($imagePath[2], ".mp4") || strpos($imagePath[2], ".MOV"))
								$media3 = "<video class='img3' style='border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'></video>";
							else
								$media3 = "<img class='img3' style='border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'>";							
							if(strpos($imagePath[3], ".mp4") || strpos($imagePath[3], ".MOV"))
								$media4 = "<video class='img4' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 3)' src='$imagePath[3]'></video>";
							else
								$media4 = "<img class='img4' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 3)' src='$imagePath[3]'>";
							$imageDiv = "<div class='carousel-container-3'><div class='carousel-slide-4'>$media$media2$media3$media4</div></div>";		
						}
	
						else if($num == 5){
							if(strpos($imagePath[0], ".mp4") || strpos($imagePath[0], ".MOV"))
								$media = "<video class='img1' style='border-top-left-radius: 25px; border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'></video>";
							else
								$media = "<img class='img1' style='border-top-left-radius: 25px; border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'>";							
							if(strpos($imagePath[1], ".mp4") || strpos($imagePath[1], ".MOV"))
								$media2 = "<video class='img2' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'></video>";
							else
								$media2 = "<img class='img2' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'>";							
							if(strpos($imagePath[2], ".mp4") || strpos($imagePath[2], ".MOV"))
								$media3 = "<video class='img3' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'></video>";
							else
								$media3 = "<img class='img3' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'>";
							if(strpos($imagePath[3], ".mp4") || strpos($imagePath[3], ".MOV"))
								$media4 = "<video class='img4' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 3)' src='$imagePath[3]'></video>";
							else
								$media4 = "<img class='img4' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 3)' src='$imagePath[3]'>";
							if(strpos($imagePath[4], ".mp4") || strpos($imagePath[4], ".MOV"))
								$media5 = "<video class='img5' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 4)' src='$imagePath[4]'></video>";
							else
								$media5 = "<img class='img5' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 4)' src='$imagePath[4]'>";
							$imageDiv = "<div class='carousel-container-6'><div class='carousel-slide-5'>$media$media2$media3$media4$media5</div></div>";
						}
	
						else{
							if(strpos($imagePath[0], ".mp4") || strpos($imagePath[0], ".MOV"))
								$media = "<video class='img1' style='border-top-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'></video>";
							else
								$media = "<img class='img1' style='border-top-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 0)' src='$imagePath[0]'>";
							if(strpos($imagePath[1], ".mp4") || strpos($imagePath[1], ".MOV"))
								$media2 = "<video class='img2' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'></video>";
							else
								$media2 = "<img class='img2' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 1)' src='$imagePath[1]'>";
							if(strpos($imagePath[2], ".mp4") || strpos($imagePath[2], ".MOV"))
								$media3 = "<video class='img3' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'></video>";
							else
								$media3 = "<img class='img3' style='border-top-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 2)' src='$imagePath[2]'>";
							if(strpos($imagePath[3], ".mp4") || strpos($imagePath[3], ".MOV"))
								$media4 = "<video class='img4' style='border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 3)' src='$imagePath[3]'></video>";
							else
								$media4 = "<img class='img4' style='border-bottom-left-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 3)' src='$imagePath[3]'>";
							if(strpos($imagePath[4], ".mp4") || strpos($imagePath[4], ".MOV"))
								$media5 = "<video class='img5' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 4)' src='$imagePath[4]'></video>";
							else
								$media5 = "<img class='img5' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 4)' src='$imagePath[4]'>";
							if(strpos($imagePath[5], ".mp4") || strpos($imagePath[5], ".MOV"))
								$media6 = "<video class='img6' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 5)' src='$imagePath[5]'></video>";
							else
								$media6 = "<img class='img6' style='border-bottom-right-radius: 25px;' data-toggle='modal' data-target='#med_modal$id' onclick='image($id, 5)' src='$imagePath[5]'>";
							$imageDiv = "<div class='carousel-container-6'><div class='carousel-slide-6'>$media$media2$media3$media4$media5$media6</div></div>";
						}
					}
					else {
						$imageDiv = "";
					}

					if(strpos($body, "<iframe width='")){
						$imageDiv = $body;
						$body = "";
					}

					$body = nl2br($body);

					$body_array = preg_split("/\s+/", $body);

					foreach($body_array as $key => $value) {
						if(substr($value, 0, 1) == "#"){
							$hashtag = substr($value, 1);
							$value = "<a class='forward_link' href='hashtags.php?value=" . $hashtag ."' style='text-decoration: none';>$value</a>";
							$body_array[$key] = $value;
						}
						if(substr($value, 0, 1) == "@"){
							$name = substr($value, 1);
							$verify = mysqli_query($this->con, "SELECT * FROM users WHERE username='$name' AND user_closed='no'");
							if(mysqli_num_rows($verify)){
								$value = "<a class='forward_link' href='$name' style='text-decoration: none;'>$value</a>";
								$body_array[$key] = $value;
							}
						}
					}
					$body = implode(" ", $body_array);

					$pes_pic = $added_by_obj->getProfilePic();
					if($body && $imageDiv){
						$body = str_replace("<br />", "<div style='line-height: 0.7;'><br>.<br></div>", $body);
						$text = "<a href='$added_by'><img src='$pes_pic' style='width: 1.4em; margin-right: 5px; margin-bottom: 5px;'><b style='color: grey !important;'>$added_by</b></a> $body <br>";
					}
					else
						$text = "";

					if($body){
						if(!$imageDiv){
							$rndImg = rand(0, 12);
							if($rndImg == 0)
								$image = "assets/images/a.jpg";
							if($rndImg == 1)
								$image = "assets/images/b.jpg";
							if($rndImg == 2)
								$image = "assets/images/c.jpg";
							if($rndImg == 3)
								$image = "assets/images/d.jpg";
							if($rndImg == 4)
								$image = "assets/images/e.jpg";
							if($rndImg == 5)
								$image = "assets/images/f.jpg";
							if($rndImg == 6)
								$image = "assets/images/g.jpg";
							if($rndImg == 7)
								$image = "assets/images/h.jpg";
							if($rndImg == 8)
								$image = "assets/images/i.jpg";
							if($rndImg == 9)
								$image = "assets/images/j.PNG";
							if($rndImg == 10)
								$image = "assets/images/k.jpg";
							if($rndImg == 11)
								$image = "assets/images/l.jpg";
							if($rndImg == 12)
								$image = "assets/images/m.jpg";
							$body = nl2br($body);
							$imageDiv = "<div class='bg_img' style='background-image: url($image);'><span class='no_img'>".$body."</span></div>";
							$body = "";
							$text = "";
						}
					}

					$usersReturnedQuery = mysqli_query($this->con, "SELECT * FROM users WHERE username = '$userLoggedIn' AND user_closed='no'");
					$row = mysqli_fetch_array($usersReturnedQuery);
					$view_obj = new User($this->con, $row['username']);
                    $friends = $view_obj->getFriendArray();
                    $friends = substr($friends, 1, -1);
					$friends = explode(',', $friends);
					sort($friends, SORT_STRING);
					$total=count($friends);
					$list = "";
					if($total>=1){
						for($k=0; $k<$total; $k++) {
							if($friends[$k]){
								$query2 = mysqli_query($this->con, "SELECT * FROM users WHERE username = '$friends[$k]' AND user_closed='no'");
								while($row2 = mysqli_fetch_array($query2)){            
									$fowadee = $row2['username'];
									$list .= "<div class='resultDisplay' style='border-bottom: none;' onclick='check$id(\"".$row2['username']."\")'>
												<div class='liveSearchProfilePic'>
													<img src='" . $row2['profile_pic'] ."'>
												</div>
							
												<div class='liveSearchText'>
													" . $row2['first_name'] . " " . $row2['last_name'] . "
													<p>" . $row2['username'] ."</p>
												</div>
												<div class='pull-right hrm' id='fow$fowadee$id'></div>
											</div>";
								}
							}
						}
					}
					else{
						$list .= "";
					}

					$check_query = mysqli_query($this->con, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$id'");
					$num_rows = mysqli_num_rows($check_query);
					if($num_rows > 0) {
						$lke = '<label onclick="unlike('.$id.')"><span onclick="sound.play()" class="fa fa-heart fa-lg text-danger"></span></label>';
					}
					else {
						$lke = '<label onclick="like('.$id.')"><span onclick="sound.play()" class="fa fa-heart-o fa-lg"></span></label>';
					}

					$likesQuery = mysqli_query($this->con, "SELECT username FROM likes WHERE post_id='$id' AND username != '$userLoggedIn' ORDER BY id");
					while($row = mysqli_fetch_array($likesQuery))
						$person = $row['username'];
					if($num_likes >= 1){
						$statement = "<div class='statement$id'><a href='likes.php?post_id=$id' class='black'>Liked by </a><a class='text-muted' href='$person'>$person</a><a href='likes.php?post_id=$id' class='black'> and $num_likes others </a></div>";
					}
					else{
						$statement = "";
					}

					$com_query = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id' AND removed='no'");
					$comm_num = mysqli_num_rows($com_query);
					if($comm_num > 1)
						$comm = "<a href='comments.php?post_id=$id' class='text-muted'>View all $comm_num comments</a> <br>";
					else
						$comm = "";

					if(!$pc){
						$yee = "<section><a onClick='javascript:toggle$id(event)'><span class='black fa fa-comment-o'>&nbsp;
						<p class='inline text-muted'>Add a comment...</p></span>&nbsp;&nbsp;&nbsp;</a></section>";
					}
					else
						$yee = "";

					$bookmarkQuery = mysqli_query($this->con, "SELECT * FROM bookmarks WHERE post_id = '$id' AND username = '$userLoggedIn'");
					if(mysqli_num_rows($bookmarkQuery))
						$bookmark_button = "<a href='#' id='unbookmark$id'><span class='fa fa-bookmark-o fa-lg icons pull-left'></span> Remove from Bookmarks</a>";
					else
						$bookmark_button = "<a href='#' id='bookmark$id'><span class='fa fa-bookmark-o fa-lg icons pull-left'></span> Bookmark this Post</a>";

					if($added_by != $userLoggedIn){
						$post_not_query = mysqli_query($this->con, "SELECT * FROM post_notifications WHERE person='$added_by' AND username='$userLoggedIn'");
						if(mysqli_num_rows($post_not_query))
							$post_not_button = "<hr class='marginless'><a href='#' id='unnotify$added_by$id'><span class='fa fa-chain-broken fa-lg icons pull-left'></span> Turn off post notifications</a>";
						else
							$post_not_button = "<hr class='marginless'><a href='#' id='notify$added_by$id'><span class='fa fa-chain fa-lg icons pull-left'></span> Turn on post notifications</a>";
					}
					else
						$post_not_button = "";

						$str .= "<div class='status_post'>
							<div class='row' style='margin-bottom: 10px; display: flex; align-items: center;'>
								<div class='post_profile_pic'>
									<img src='$profile_pic' width='50'>
								</div>

								<span class='posted_by col-xs-11' style='color:#ACACAC; line-height: 1.5; padding-left: 0;'>
									<a href='$added_by'><b>$added_by</b></a> $user_to <br>
									$location
								</span>
								<button class='btn ellipsis col-xs-1' data-toggle='modal' data-target='#menu$id'>...</button>
							</div>
							<div id='post_body' class='post$id'>		
								$imageDiv			
								<br>										
							</div>

							<div class='newsfeedPostOptions'>
								<div class='row'>
									<div class='row' style='margin-bottom: 5;'>
										<div class='col-xs-1 like_box$id'>
											$lke
										</div>
										<div class='col-xs-1'>
											<a href='comments.php?post_id=$id'><span class='black fa fa-comment-o fa-lg' style='height: 20;'></span></a>
										</div>																				
										<button style='padding: 0; float: right; position: relative; height: 20;'' class='btn white' data-toggle='modal' data-target='#post_form$id'><span class='fa fa-paper-plane-o fa-lg'></span></button>
									</div>
									$statement
									$text
									$comm
									$commentt
									$yee
									<div class='time' style='float: none; font-size: 70%; padding: 0;'>$time_message</div> 
								</div>
							</div>
							<div class='modal fade' id='post_form$id' tabindex='-1' role='dialog' aria-labelledby='postModalLabel' aria-hidden='true' style='overflow: hidden; padding: 0 !important;'>
								<div class='modal-dialog modal_s' style='top: 40vh; width: 90%; margin: 30px auto;'>
									<div class='modal-content' style='background-color: var(--bgc2);'>
										<div class='input_group' style='color: var(--bgc); padding: 10px;'>
											<input type='search' autocomplete='off' onkeyup='getFriendz(this.value, $id)' id='search_box$id' placeholder='Search...' class='form-control inp'>
										</div>
										<div class='mentionees$id' style='max-height: 40vh; overflow-y: auto;'></div>
										<div id='mentionees$id' style='max-height: 40vh; overflow-y: auto;'>
											<ul class='dropdown-menu forwardee' style='display: block;'>
												<li>
													$list
												</li>
											</ul>
										</div>
										<input type='hidden' value='' id='tofow$id'>
										<input type='button' style='width:100%; background-color:var(--shadow2); color: #fff;' class='btn' disabled onclick='forwardMsgs$id()' id='forwardBtn$id' value='Send'>
									</div>
								</div>
							</div>
							<div class='modal fade' id='menu$id' tabindex='-1' role='dialog' aria-labelledby='menuModalLabel' aria-hidden='true' style='overflow: hidden; padding: 0 !important;'>
								<div class='modal-dialog' style='top: 60vh; width: 90%; margin: 30px auto;'>
									<div class='modal-content' style='background-color: var(--bgc2);'>
									<ul class='dropdown-menu' style='display: block;'>
										<li><a class='text-danger' id='spam$id' href='#'><span class='fa fa-flag fa-lg icons pull-left'></span> Spam this post</a></li>
										<li class='divider' role='seperator'></li>
										<li>$unfriend_button</li>
										<li>$delete_button</li>
										<li>$edit_button</li>
										<li>$bookmark_button</li>
										<li>$post_not_button</li>
									</ul>
										<button type='button' style='width:100%;' class='btn btn-danger' data-dismiss='modal'>Cancel</button>
									</div>
								</div>
							</div>
							<div class='modal fade' id='med_modal$id' tabindex='-1' role='dialog' aria-labelledby='postModalLabel' aria-hidden='true' style='overflow: hidden; padding: 0;'>
								<div class='modal-dialog' style='position: static; margin: 0; width: 100%'>
									<div class='modal-content' style='height: 100%; border-radius: 0; background-color: var(--bgc);'>
										<div class='modal$id'></div>
										<button type='button' class='btn btn-default modal_close_btn' data-dismiss='modal' onclick='pause()'>Close</button>
									</div>
								</div>
							</div>
							$modal
							</div>
							<div class='post_comment' id='toggleComment$id' style='display:none;'>
								<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
							</div>
							<hr style='opacity: 0;'>";
				?>
				<script>
					function forwardMsgs<?php echo $id; ?>(){
						var id = <?php echo $id; ?>;
						var userLoggedIn = '<?php echo $userLoggedIn; ?>';
						var toSend = _("tofow"+id).value;
						var toSen = toSend.length - 1;
						var res = toSend.substring(0, toSen);
						res = res.split(",");
						for(var check of res){
							$("#fow"+check+id).removeClass("checkedd");
							$("#fow"+check+id).removeClass("fa");
							$("#fow"+check+id).removeClass("fa-check");
						}					
						var formdata = new FormData();
						var ajax = new XMLHttpRequest();
						// ajax.addEventListener('load', completeHandler+id, false);
						formdata.append('usernames', toSend);
						formdata.append('userLoggedIn', userLoggedIn);
						// formdata.append('text', text$idd);
						formdata.append('id', id);
						if(toSend){
							ajax.open('POST', 'includes/form_handlers/uploadForwardMessage.php');
							ajax.send(formdata);
						}
						_("tofow"+id).value = '';
						$("#post_form"+id).modal('hide');
						$('input[id="forwardBtn<?php echo $id; ?>"]').attr('disabled',true);
						$('input[id="forwardBtn<?php echo $id; ?>"]').css('background-color','var(--shadow2)');
					}
					function check<?php echo $id; ?>(person){
						var id = <?php echo $id; ?>;
						$("#fow"+person+id).toggleClass("checkedd");
						$("#fow"+person+id).toggleClass("fa");
						$("#fow"+person+id).toggleClass("fa-check");
						let body = _("tofow"+id).value
						var str = body.indexOf(person);
						if(str != -1){
							body = body.replace(person+",", "");
							_("tofow"+id).value = body;
							var bodd = _("tofow"+id).value
						}
						else{
							_("tofow"+id).value = body + person+",";
							var bodd = _("tofow"+id).value
						}
						if(bodd){							
							$('input[id="forwardBtn<?php echo $id; ?>"]').attr('disabled',false);
							$('input[id="forwardBtn<?php echo $id; ?>"]').css('background-color','var(--sbutton)');
						}
						else{
							$('input[id="forwardBtn<?php echo $id; ?>"]').attr('disabled',true);
							$('input[id="forwardBtn<?php echo $id; ?>"]').css('background-color','var(--shadow2)');
						}
					}

					$(document).ready(function() {

						$('input[id="search_box<?php echo $id; ?>"]').on('keyup',function(){							
							if($(this).val()){
								document.getElementById("mentionees<?php echo $id; ?>").style.display="none";
							}
						});

						$('#post<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to delete this post?", function(result) {

								$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

								if(result)
									location.reload();

							});
						});

						$('#spam<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to spam this post?", function(result) {

								$.post("includes/form_handlers/spam_post.php?post_id=<?php echo $id; ?>&username=<?php echo $userLoggedIn; ?>", {result:result});

								// if(result)
								// 	location.reload();

							});
						});

						$('#bookmark<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to save this post?", function(result) {

								$.post("includes/form_handlers/bookmark_post.php?post_id=<?php echo $id; ?>&username=<?php echo $userLoggedIn; ?>", {result:result});

								// if(result)
								// 	location.reload();

							});
						});

						$('#unbookmark<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to remove this post from your bookmarks?", function(result) {

								$.post("includes/form_handlers/unbookmark_post.php?post_id=<?php echo $id; ?>&username=<?php echo $userLoggedIn; ?>", {result:result});

								// if(result)
								// 	location.reload();

							});
						});

						$('#unfriend<?php echo $added_by; ?><?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to unfriend <?php echo $added_by; ?> ?", function(result) {

								$.post("includes/form_handlers/unfriend.php?name=<?php echo $added_by; ?>", {result:result});

								if(result)
									location.reload();
							});
						});

						$('#notify<?php echo $added_by; ?><?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to turn on post notifications for <?php echo $added_by; ?>?", function(result) {

								$.post("includes/form_handlers/post_not.php?person=<?php echo $added_by; ?>&username=<?php echo $userLoggedIn; ?>", {result:result});

							});
						});	

						$('#unnotify<?php echo $added_by; ?><?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to turn off post notifications for <?php echo $added_by; ?>?", function(result) {

								$.post("includes/form_handlers/unpost_not.php?person=<?php echo $added_by; ?>&username=<?php echo $userLoggedIn; ?>", {result:result});

							});
						});	

					});

					var sound = new Audio();
					sound.src = "button_click.mp3";

				</script>
				<?php
		}
		else {
			echo "<p>No post found. If you clicked a link, it may be broken.</p>";
					return;
		}

		echo $str;
	}

	public function getStatus($username, $iddd){
		if($this->user_obj == $this->con){
			// 	$usernames = array();
			// 	$general_query = mysqli_query($this->con, "SELECT * FROM statuses WHERE deleted='no'");
			// 	if(mysqli_num_rows($general_query)){
			// 		while($row2 = mysqli_fetch_array($general_query)){
			// 			$usernamee = $row2['username'];
			// 			if($usernamee != $userLoggedIn)
			// 				array_push($usernames, $usernamee);
			// 		}
			// 	}
			// 	$usernames = array_unique($usernames);
			// 	$ar = array();
			// 	foreach($usernames as $s)
			// 		array_push($ar, $s);

			// 	// for($i=0; $i<count($ar); $i++){
			// 	// 	if($ar[$i] == $username){
			// 	// 		$pin = $i + 1;
			// 	// 		$pen = $i - 1;
			// 	// 	}
			// 	// 	echo $ar[$i];
			// 	// }
			// 	// 	echo $pen . "<br>" .$pin ;

			// 	$str = ""; //String to return 
			// 	$ids = array();
			// 	$countee = $counter + 1;
			// 	$countei = $counter - 1;

			// 	$data_query = mysqli_query($this->con, "SELECT * FROM statuses WHERE deleted='no' AND username='$username'");

			// 	if(mysqli_num_rows($data_query) > 0) {
			// 		while($row = mysqli_fetch_array($data_query)){
			// 			$id = $row['id'];
			// 			array_push($ids, $id);
			// 		}; 

			// 		$total = count($ids);
			// 		// if($counter > 0){
			// 		// 	if($countee == $total){
			// 		// 		$countee = 0;
			// 				// $username = $ar[$pin];
			// 			// }
			// 			// if($countei == -1){
			// 			// 	$countei = 0;
			// 				// $username = $ar[$pin];
			// 			// }
			// 		// }
			// 		// else{
			// 		// 	if($countee == $total)
			// 		// 		$countee = 0;
			// 		// 	if($countei == -1)
			// 		// 		$countei = $total - 1;
			// 		// }

			// 		$link = "status.php?name=$username&counter=$countei";
			// 		$linkk = "status.php?name=$username&counter=$countee";

			// 		if($countee == $total)
			// 			$linkk = "index.php";
			// 		if($countei == -1)
			// 			$link = "status.php?name=$username&counter=0";

			// 		$status_query = mysqli_query($this->con, "SELECT * FROM statuses WHERE id='$ids[$counter]' LIMIT 1");
			// 		if(mysqli_num_rows($status_query)){
			// 			$status = mysqli_fetch_array($status_query);

			// 			$userr = $status['username'];
			// 			$body = $status['text'];
			// 			$date = $status['date_added'];
			// 			$image = $status['images'];
			// 			$views = $status['views'];
			// 			$color = $status['color'];
			// 			$viewz = $views + 1;				

			// 			$sec = substr($date, 17);
			// 			$min = substr($date, 14, -3);
			// 			$hour = substr($date, 11, -6);
			// 			$day = substr($date, 8, -9);
			// 			$month = substr($date, 5, -12);
			// 			$year = substr($date, 0, -15);
			// 			$nice = mktime($hour, $min, $sec, $month, $day, $year);
			// 			$formedd = date("g:i A", $nice);
						
			// 			$date_time_now = date("Y-m-d H:i:s");
			// 			$tmg = substr($date_time_now, 8, -9);

			// 			if($day != $tmg)
			// 				$y = "Yesterday, ";
			// 			else
			// 				$y = "";

			// 			$time_message = $y . $formedd;

			// 			if($userr != $userLoggedIn){
			// 				$update_query2 = mysqli_query($this->con, "SELECT * FROM views WHERE status_id='$ids[$counter]' AND username='$userLoggedIn'");
			// 				if(mysqli_num_rows($update_query2) == 0){
			// 					$update_query = mysqli_query($this->con, "UPDATE statuses SET views ='$viewz' WHERE id='$ids[$counter]'");					
			// 					$add_query = mysqli_query($this->con, "INSERT INTO views VALUES (NULL, '$userLoggedIn', '$date_time_now', '$ids[$counter]', '$userr')");
			// 				}
			// 			}

						
			// 			$user_to_check_obj = new User($this->con, $username);
			// 			$name = $user_to_check_obj->getFirstAndLastName();
			// 			$pic = $user_to_check_obj->getProfilePic();

			// 			$user_details = "<div class='status_info'><img src='". $pic ."'>&nbsp;&nbsp;&nbsp;<a href='$username'><div class='addi'>$name</div></a><br><span class='addy'>$time_message</span></div>";

			// 			$viewers = "";
			// 			$view_query = mysqli_query($this->con, "SELECT * FROM views WHERE status_id='$ids[$counter]' ORDER BY id DESC");
			// 			if(mysqli_num_rows($view_query)){
			// 				while($view = mysqli_fetch_array($view_query)){
			// 					$viewer = $view['username'];
			// 					$viewer_obj = new User($this->con, $viewer);
			// 					$fl_name = $viewer_obj->getFirstAndLastName();
			// 					$time = $view['date'];
			// 					$secv = substr($time, 17);
			// 					$minv = substr($time, 14, -3);
			// 					$hourv = substr($time, 11, -6);
			// 					$dayv = substr($time, 8, -9);
			// 					$monthv = substr($time, 5, -12);
			// 					$yearv = substr($time, 0, -15);
			// 					$nicev = mktime($hourv, $minv, $secv, $monthv, $dayv, $yearv);
			// 					$formed = date("g:i A", $nicev);
			// 					$viewers .= "<a href=$viewer><li class='dropdown-item bo_bo'>$fl_name<span class='status_date'>$formed</li></a>";
			// 				}
			// 			}
									
			// 			$msg_boxx = "<div class='status_msg_footer2'>
			// 							<form id='marginless_form' action='status.php?name=$username&counter=0' method='POST'>
			// 								<textarea name='message_body' id='status_textareaz' placeholder='Write your message ...'></textarea>
			// 								<input type='hidden' name='id' value=$ids[$counter]>
			// 								<input type='hidden' name='to_who' value=$username>
			// 								<input type='submit' name='post_message' id='message_submit'>
			// 								<label onclick='sound.play()' id='status_resend_btn' for='message_submit'><span class='fa fa-paper-plane plane'></span>
			// 							</form>
			// 						</div>";

			// 			if($userLoggedIn == $username){
			// 				$view_count = "<div class='view_count dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><span class='fa fa-eye'></span> $views views</div>
			// 									<div class='dropdown-menu dropup-menu'>
			// 										$viewers
			// 									</div>";
			// 				$view_cound = "<div class='view_cound dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><span class='fa fa-eye'></span> $views views</div>
			// 									<div class='dropdown-menu dropup-menu'>
			// 										$viewers
			// 									</div>";
			// 			}
			// 			else{
			// 				$view_count="<div class='view_count'>$msg_boxx</div>";
			// 				$view_cound="<div class='view_count'>$msg_boxx</div>";
			// 			}
						
			// 			if($body)
			// 				$text = "<div class='text_status'>$body<hr class='wid less'></div>";
			// 			else
			// 				$text = "";

			// 			$imagex = explode(".", $image);
			// 			if($imagex[count($imagex)-1] == "mp4")
			// 				$media = "<video id='status_img' loop autoplay controls src='$image'></video>";
			// 			else
			// 				$media = "<img id='status_img' src='$image'>";

			// 			if($color){
			// 				$str .= "<div id='status' style='background-color:$color'>
			// 							$user_details
			// 							<a href=$link><i class='fa fa-arrow-left' id='prevBtn'></i></a>
			// 							<a href=$linkk><i class='fa fa-arrow-right' id='nextBtn'></i></a>
			// 							<div class='status_body_text'>$body</div>
			// 							$view_count
			// 						</div>";
			// 			}
			// 			else{
			// 				$str .= "<div id='statuz'>
			// 							$user_details
			// 							<a href=$link><i class='fa fa-arrow-left' id='prevBtn'></i></a>
			// 							<a href=$linkk><i class='fa fa-arrow-right' id='nextBtn'></i></a>
			// 							$media
			// 							$text
			// 							$view_cound
			// 						</div>";
			// 			}
			// 		}			
			// 		else{
			// 			// echo $status;
			// 		}
			// 	}			
			// 	echo $str;
		}
		$userLoggedIn = $this->user_obj->getUsername();
		
		if($iddd){
			$str = "";
			$special_query = mysqli_query($this->con, "SELECT * FROM statuses WHERE id='$iddd' AND deleted='no'");
			
			$status = mysqli_fetch_array($special_query);
			$userr = $status['username'];
			$body = $status['text'];
			$date = $status['date_added'];
			$image = $status['images'];
			$views = $status['views'];
			$color = $status['color'];
			$viewz = $views + 1;			

			$sec = substr($date, 17);
			$min = substr($date, 14, -3);
			$hour = substr($date, 11, -6);
			$day = substr($date, 8, -9);
			$month = substr($date, 5, -12);
			$year = substr($date, 0, -15);
			$nice = mktime($hour, $min, $sec, $month, $day, $year);
			$formedd = date("g:i A", $nice);
			
			$date_time_now = date("Y-m-d H:i:s");
			$tmg = substr($date_time_now, 8, -9);

			if($day != $tmg)
				$y = "Yesterday, ";
			else
				$y = "";

			$time_message = $y . $formedd;

			if($userr != $userLoggedIn){
				$update_query2 = mysqli_query($this->con, "SELECT * FROM views WHERE status_id='$iddd' AND username='$userLoggedIn'");
				if(mysqli_num_rows($update_query2) == 0){
					$update_query = mysqli_query($this->con, "UPDATE statuses SET views ='$viewz' WHERE id='$iddd'");					
					$add_query = mysqli_query($this->con, "INSERT INTO views VALUES (NULL, '$userLoggedIn', '$date_time_now', '$iddd', '$userr')");
				}
			}

			
			$user_to_check_obj = new User($this->con, $username);
			$name = $user_to_check_obj->getFirstAndLastName();
			$pic = $user_to_check_obj->getProfilePic();

			$user_details = "<div class='status_info'><img src='". $pic ."'>&nbsp;&nbsp;&nbsp;<a href='$username'><div class='addi'>$name</div></a><br><span class='addy'>$time_message</span></div>";

			$viewers = "";
			$view_query = mysqli_query($this->con, "SELECT * FROM views WHERE status_id='$iddd' ORDER BY id DESC");
			if(mysqli_num_rows($view_query)){
				while($view = mysqli_fetch_array($view_query)){
					$viewer = $view['username'];
					$viewer_obj = new User($this->con, $viewer);
					$fl_name = $viewer_obj->getFirstAndLastName();
					$time = $view['date'];
					$secv = substr($time, 17);
					$minv = substr($time, 14, -3);
					$hourv = substr($time, 11, -6);
					$dayv = substr($time, 8, -9);
					$monthv = substr($time, 5, -12);
					$yearv = substr($time, 0, -15);
					$nicev = mktime($hourv, $minv, $secv, $monthv, $dayv, $yearv);
					$formed = date("g:i A", $nicev);
					$viewers .= "<a href=$viewer><li class='dropdown-item bo_bo'>$fl_name<span class='status_date'>$formed</li></a>";
				}
			}
						
			$msg_boxx = "<div class='status_msg_footer2'>
							<form action='status.php?name=$username' method='POST'>
								<textarea name='message_body' class='status_textareaz2' placeholder='Write your message ...'></textarea>
								<input type='hidden' name='id' value=$iddd>
								<input type='submit' name='post_message' id='message_submit'>
								<label onclick='sound.play()' id='status_resend_btn' for='message_submit'><span class='fa fa-paper-plane plane'></span>
							</form>
						</div>";

			if($userLoggedIn == $username){
				$view_count = "<div class='view_count dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><span class='fa fa-eye'></span> $views views</div>
									<div class='dropdown-menu dropup-menu'>
										$viewers
									</div>";
				$view_cound = "<div class='view_cound dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><span class='fa fa-eye'></span> $views views</div>
									<div class='dropdown-menu dropup-menu'>
										$viewers
									</div>";
			}
			else{
				$view_count="<div class='view_count'>$msg_boxx</div>";
				$view_cound="<div class='view_count'>$msg_boxx</div>";
			}
			
			if($body)
				$text = "<div class='text_status'>$body<hr class='wid less'></div>";
			else
				$text = "";

			$imagex = explode(".", $image);
			if($imagex[count($imagex)-1] == "mp4")
				$media = "<video id='status_img' loop autoplay controls src='$image'></video>";
			else
				$media = "<img id='status_img' src='$image'>";

			if($color){
				$str .= "<div id='status' style='background-color:$color'>
							$user_details
							<div class='status_body_text'>$body</div>
							$view_count
						</div>";
			}
			else{
				$str .= "<div id='statuz'>
							$user_details
							$media
							$text
							$view_cound
						</div>";
			}
			?>
			<script>
				var sound = new Audio();
				sound.src = "button_click.mp3";
			</script>
			<?php
		}
		else{
			$idz = array();
			$query = mysqli_query($this->con, "SELECT id FROM statuses WHERE username='$username' AND deleted='no'");
			if(mysqli_num_rows($query)){
				while($row = mysqli_fetch_array($query)){
					array_push($idz, $row['id']);
				}
				$navs = $str = $strs = "";
				$total = count($idz);
				for($i=1; $i<count($idz); $i++){
					$navs.="<li data-target='#gallery-carousel' data-slide-to='$i'></li> ";
				}
				$counter = 0;
				foreach($idz as $id){
					$counter++;
					$query2 = mysqli_query($this->con, "SELECT * FROM statuses WHERE id='$id'");
					$row2 = mysqli_fetch_array($query2);

					$userr = $row2['username'];
					$body = $row2['text'];
					$date = $row2['date_added'];
					$image = $row2['images'];
					$views = $row2['views'];
					$viewz = $views + 1;
					$color = $row2['color'];

					$sec = substr($date, 17);
					$min = substr($date, 14, -3);
					$hour = substr($date, 11, -6);
					$day = substr($date, 8, -9);
					$month = substr($date, 5, -12);
					$year = substr($date, 0, -15);
					$nice = mktime($hour, $min, $sec, $month, $day, $year);
					$formedd = date("g:i A", $nice);
					
					$date_time_now = date("Y-m-d H:i:s");
					$tmg = substr($date_time_now, 8, -9);

					if($day != $tmg)
						$y = "Yesterday, ";
					else
						$y = "";

					$time_message = $y . $formedd;

					if($userr != $userLoggedIn){
						$update_query2 = mysqli_query($this->con, "SELECT * FROM views WHERE status_id='$id' AND username='$userLoggedIn'");
						if(mysqli_num_rows($update_query2) == 0){
							$update_query = mysqli_query($this->con, "UPDATE statuses SET views ='$viewz' WHERE id='$id'");					
							$add_query = mysqli_query($this->con, "INSERT INTO views VALUES (NULL, '$userLoggedIn', '$date_time_now', '$id', '$userr')");
						}
					}

					$user_to_check_obj = new User($this->con, $username);
					$name = $user_to_check_obj->getFirstAndLastName();
					$pic = $user_to_check_obj->getProfilePic();

					$user_details = "<div class='status_info'><img src='". $pic ."'>&nbsp;&nbsp;&nbsp;<a href='$username'><div class='addi'>$name</div></a><br><span class='addy'>$time_message</span></div>";

					$viewers = "";
					$view_query = mysqli_query($this->con, "SELECT * FROM views WHERE status_id='$id' ORDER BY id DESC");
					if(mysqli_num_rows($view_query)){
						while($view = mysqli_fetch_array($view_query)){
							$viewer = $view['username'];
							$viewer_obj = new User($this->con, $viewer);
							$fl_name = $viewer_obj->getFirstAndLastName();
							$time = $view['date'];
							$secv = substr($time, 17);
							$minv = substr($time, 14, -3);
							$hourv = substr($time, 11, -6);
							$dayv = substr($time, 8, -9);
							$monthv = substr($time, 5, -12);
							$yearv = substr($time, 0, -15);
							$nicev = mktime($hourv, $minv, $secv, $monthv, $dayv, $yearv);
							$formed = date("g:i A", $nicev);
							$viewers .= "<li class='dropdown-item bo_bo'><a href='$viewer'>$fl_name<span class='status_date'>$formed</span></a></li>";
						}
					}

					$msg_boxx = "<div class='status_msg_footer2'>
									<form action='status.php?name=$username' method='POST'>
										<textarea name='message_body' class='status_textareaz2' placeholder='Write your message ...'></textarea>
										<input type='hidden' name='id' value='$id'>
										<input type='submit' name='post_message' id='message_submit$counter'>
										<label onclick='sound.play()' id='status_resend_btn' for='message_submit$counter'><span class='fa fa-paper-plane plane'></span>
									</form>
								</div>";

					if($userLoggedIn == $username){
						$view_count = "<div class='view_count dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><span class='fa fa-eye'></span> $views views</div>
											<div class='dropdown-menu dropup-menu'>
												$viewers
											</div>";
						$view_cound = "<div class='view_cound dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><span class='fa fa-eye'></span> $views views</div>
											<div class='dropdown-menu dropup-menu'>
												$viewers
											</div>";
					}
					else{
						$view_count="<div class='view_count'>$msg_boxx</div>";
						$view_cound="<div class='view_count'>$msg_boxx</div>";
					}
					
					if($body)
						$text = "<div class='text_status'>$body<hr class='wid less'></div>";
					else
						$text = "";

					$imagex = explode(".", $image);
					if($imagex[count($imagex)-1] == "mp4" || $imagex[count($imagex)-1] == "MOV")
						$media = "<video id='status_img' controls src='$image'></video>";
					else
						$media = "<img id='status_img' src='$image'>";

					if($counter == 1){
						$add = "active";
					}
					else{
						$add = "";
					}
					$textx = "<div class='carousel-caption'>
								$body
							</div>";
					if($color){
						$strs .= "<div class='carousel-inner item $add' style='background-color:$color'>
									<div id='status'>
										$user_details
										<div class='status_body_text'>$body</div>
										$view_count
									</div>
								</div>";
					}
					else{
						$strs .= "<div class='carousel-inner item $add'>
									<div id='statuz'>
										$user_details
										$media
										$textx
										$view_cound
									</div>
								</div>";
					}								
					?>
					<style>
						.carousel-indicators{
							top: 70px;
						}
						.carousel-indicators .active{
							width: 12% !important;
						}
						.carousel-indicators li{
							width: 10%;
							margin-left: 5 !important;
						}
						.carousel-control{
							height: 15%;
							margin: auto;
						}
						#message_submit<?php echo $counter; ?>{
							display: none;
						}
					</style>
					<?php
				}	
				$str .= "<div id='gallery-carousel' class='carousel slide' data-ride='carousel' data-interval=''>
							<ol class='carousel-indicators'>
								<li class='active' data-target='#gallery-carousel' data-slide-to='0'></li>
								$navs
							</ol>
							<div class='carousel-inner' role='listbox'>
								$strs
							</div>
							<a href='#gallery-carousel' role='button' data-slide='prev' class='left carousel-control'><i class='fa fa-arrow-left' id='prevBtn'></i></a>
							<a href='#gallery-carousel' role='button' data-slide='next' class='right carousel-control'><i class='fa fa-arrow-right' id='nextBtn'></i></a>
						</div>";
			}
			else
				$str = "";
		}
		echo $str;
	}

	public function getComments($data, $limit){
		$userLoggedIn = $this->user_obj->getUsername();
		$page = $data['page']; 
		$id = $data['id'];
		$str = "";
		$container = array();

		if($page == 1) 
			$start = 0;
		else 
			$start = ($page - 1) * $limit;

		$num_iterations = 0; //Number of results checked (not necasserily posted)
		$count = 1;

		$user_query = mysqli_query($this->con, "SELECT added_by, user_to FROM posts WHERE id='$id'");
		$rowz = mysqli_fetch_array($user_query);
		$posted_to = $rowz['added_by'];

		$comments_query = mysqli_query($this->con, "SELECT id FROM comments WHERE post_id='$id' AND removed='no' ORDER BY id DESC");
        if(mysqli_num_rows($comments_query)){
            while($comment = mysqli_fetch_array($comments_query)){
                // $body .= $comment['post_body'] . ",";
                // $poster .= $comment['posted_by'] . ",";
                // $time .= $comment['date_added'] . ",";
				// $c_id .= $comment['id'] . ",";

				if($num_iterations++ < $start)
				continue; 

				//Once 10 posts have been loaded, break
				if($count > $limit) {
					break;
				}
				else {
					$count++;
				}
				array_push($container, $comment['id']);
			}
			sort($container, SORT_NUMERIC);
			if($count > $limit) 
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
					<input type='hidden' class='noMorePosts' value='false'>";
			else 
				$str .= "<input type='hidden' class='noMorePosts' value='true'>";

			foreach($container as $com){
				// echo $com . "<br>";
				//Timeframe
				$qu = mysqli_query($this->con, "SELECT * FROM comments WHERE id='$com'");
				$ro = mysqli_fetch_array($qu);
				$time = $ro['date_added'];
				$poster = $ro['posted_by'];
				$body = $ro['post_body'];
                $date_time_now = date("Y-m-d H:i:s");
                $start_date = new DateTime($time); //Time of post
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
            
                $comment_obj = new User($this->con, $poster);
                if($poster == $userLoggedIn)
                    $name = "You";
                else
					$name = $comment_obj->getFirstAndLastName();
					
				// $body = str_replace('<br />', " ", $body);
				$body = nl2br($body);

                $lie_query = mysqli_query($this->con, "SELECT * FROM comment_likes WHERE comment_id='$com'");
                $num_likes = mysqli_num_rows($lie_query);
                if($num_likes == 1)
                    $num_likes .= " Like";
                else
                    $num_likes .= " Likes";
                $com_like_query = mysqli_query($this->con, "SELECT * FROM comment_likes WHERE username='$userLoggedIn' AND comment_id='$com'");
                if(mysqli_num_rows($com_like_query) == 0){
                    $like = '<label onclick="c_like('.$com.')"><span onclick="sound.play()" class="fa fa-heart-o fa-lg"></span> '.$num_likes.'</label>';
                }
                else
                    $like = '<label onclick="c_unlike('.$com.')"><span onclick="sound.play()" class="fa fa-heart fa-lg text-danger"></span> '.$num_likes.'</label>';

                if($poster == $userLoggedIn){
                    $delete = "<div class='col-xs-4' id='delete$com'><span class='fa fa-remove fa-lg'></span> Delete</div>";
                }
                else if($posted_to == $userLoggedIn){
                    $delete = "<div class='col-xs-4' id='delete$com'><span class='fa fa-remove fa-lg'></span> Delete</div>";
                }
                else
                    $delete = "";

                echo "<div class='comment'><div class='row'><img class='friend_img' src=" .$comment_obj->getProfilePic() ."><div class='time' style='padding-right: 10;'>$time_message</div>";
                echo "<p class='bot' style='margin-left: 100px;'><a href=". $poster .">". $name."</a><a class='black'> said&nbsp;&nbsp; $body</a></p></div>
                        <div class='row3'>
                            <div class='col-xs-4'><div class='com$com'>$like</div></div>
                            <div class='col-xs-4' onClick='javascript:toggle$com(event)' style='cursor: pointer;'><span class='fa fa-comment-o fa-lg'></span> Comment</div>
                            $delete
                        </div>
                        <div class='post_comment' id='toggleComment$com' style='display:none;'>
                            <iframe src='comment_frame.php?comment_id=$com' id='comment_iframe' frameborder='0'></iframe>
						</div>
						</div>
                        <hr style='margin-top: 0;'>";
                ?>
                <script>
                    function toggle<?php echo $com; ?>(e) {
                        // if( !e ) e = window.event;

                        // var target = $(e.target);
                        // if (!target.is("a") && !target.is("button")) { 
							var element = document.getElementById("toggleComment<?php echo $com; ?>");

							if(element.style.display == "block") 
								element.style.display = "none";
							else 
								element.style.display = "block";
                        // }
                    }
                    $(document).ready(function() {
                        $('#delete<?php echo $com; ?>').on('click', function() {
                            bootbox.confirm("Are you sure you want to delete this comment?", function(result) {
                                $.post("includes/form_handlers/delete_comment.php?post_id=<?php echo $com; ?>", {result:result});
                                if(result)
                                    location.reload();
                            });
                        });
                    });
                </script>
                <?php
			}			
		}
		else{

		}
		echo $str;
	}

	public function loadLikes($data, $limit){
		// echo $limit;
		$page = $data['page'];
		$id = $data['id'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1) 
			$start = 0;
		else 
			$start = ($page - 1) * $limit;

		$str = "";
		$list = "";
		$likesQuery = mysqli_query($this->con, "SELECT username FROM likes WHERE post_id = '$id' ORDER BY id");
		if(mysqli_num_rows($likesQuery)){
			$num_iterations = 0; //Number of results checked (not necasserily posted)
			$count = 1;

		    while($row = mysqli_fetch_array($likesQuery)){
		        $list .= $row['username'] . ",";
			}
			
		    $list = substr($list, 0, -1);
		    $list = explode(",", $list);
		    foreach ($list as $key) {

				if($num_iterations++ < $start)
						continue; 

				//Once 10 posts have been loaded, break
				if($count > $limit) {
					break;
				}
				else {
					$count++;
				}
				
		        $like_obj = new User($this->con, $key);
		        if($key == $userLoggedIn)
		            $name = "You";
		        else
		            $name = $like_obj->getFirstAndLastName();
		        $str .= "<div class='row' style='display: flex; align-items: center;'><img class='friend_img' src=" .$like_obj->getProfilePic() .">";
		        $str .= "<p class='bot marginless' style='margin: 0 !important;'><a href=". $key .">". $name."</a> liked <a href='post.php?id=$id'>this post</a></p></div><hr style='margin: 10 0 10 100;'>";
			}
			if($count > $limit) 
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
			else 
				$str .= "<input type='hidden' class='noMorePosts' value='true'>";
		}
		else
		    $str .= "You can't view the likes of a non-existent post</div>";
		echo $str;		
	}
}
	
?>