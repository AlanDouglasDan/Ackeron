<?php 
require_once 'includes/header.php';

if(isset($_POST['ng_doned'])){
    $uploadOk = 1;
    $imageName = $_FILES['fileToUpload']['name'];
    $g_info = mysqli_real_escape_string($con, $_POST['group_info']);
    $peepz = $_POST['to_add'];
    $peeps = "";
    foreach($peepz as $p)
        $peeps .= $p . ",";
    $peeps .= $userLoggedIn . ",";
	$errorMessage = "";

	if($imageName != "") {
		$targetDir = "assets/images/profile_pics/";
		$imageName = $targetDir . uniqid() . basename($imageName);
		$imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

		if($_FILES['fileToUpload']['size'] > 10000000) {
			$errorMessage = "Sorry your ffile is too large";
			$uploadOk = 0;
		}

		if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg") {
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

	}

	if($uploadOk) {
        $msg = new Message($con, $userLoggedIn);
        $msg->createGroup($userLoggedIn, $peeps, $imageName, $g_info);
    }
	else {
		echo "<div style='text-align:center;' class='alert alert-danger'>
				$errorMessage
			</div>";
	}
}

?>
<style>
    label.image{
        height: 50% !important;
    }
    .container_ng{
        overflow-y: auto;
        height: 83vh;
    }
</style>

<div class="center-block special_name">New Group</div>

<div class="container_ng">
    <form action="new_group.php" method="POST" enctype="multipart/form-data">
    <div class="row" style='text-align: center;'>
        <div class="col-sm-4">
            <input type="file" name='fileToUpload' accept="image/*" id="fileToUpload">
            <label for="fileToUpload" id="selector">
                <span class="fa fa-camera g_icon"></span>
            </label>
        </div>

        <div class="col-sm-6">
            <input type="text" maxlength='30' placeholder='Group Subject' autocomplete='off' name='group_info' id='ng_text' style='margin-top: 20px;'><span id='counter' data-max='30'>30</span>
            <p>Please do Provide a Group Subject and an optional group icon</p>
        </div>

        <div class="col-sm-2">
            <input onclick='sound.play()' type='submit' name='ng_doned' id='post_button2' class='tb_submit' value='Create'>
        </div>
    </div>

<script>
    var loader = function(e){
        let file = e.target.files;
        let output = document.getElementById("selector");
        
        
        if(file[0].type.match("image")){
            let reader = new FileReader();

            reader.addEventListener("load", function(e){
                let data = e.target.result;
                let image = document.createElement("img");
                image.src = data;

                output.innerHTML = "";
                output.insertBefore(image, null)
                output.classList.add("image");
            });

            reader.readAsDataURL(file[0]);
        }
        else{
            let show = "<span>Selected File : </span>"
            show = show + file[0].name;

            output.innerHTML = show;
            output.classList.add("active");

            if(output.classList.contains("image")){
                output.classList.remove("image");
            }
        }
    };

    let fileInput = document.getElementById("fileToUpload");
    fileInput.addEventListener("change", loader);

    $(document).ready(function(){
        $('input[id="post_button2"]').attr('disabled',true);
        var $charCount, maxCharCount;
        $charCount = $('#counter')
        maxCharCount = parseInt($charCount.data('max'), 10);
        $('input[id="ng_text"]').on('keyup',function(e){
            if($(this).val()){
                var tweetLength = $(e.currentTarget).val().length;
                $charCount.html(maxCharCount - tweetLength);
                $('input[id="post_button2"]').attr('disabled',false);
                document.getElementById("post_button2").style.backgroundColor="var(--sbutton)";
                document.getElementById("counter").style.display="inline-block";
            }
            else{
                $('input[id="post_button2"]').attr('disabled',true);
                document.getElementById("post_button2").style.backgroundColor="var(--shadow2)";
                document.getElementById("counter").style.display="none";
            }
        });
    });
    
    var sound = new Audio();
    sound.src = "button_click.mp3";
</script>

<?php

$usersReturned = mysqli_query($con, "SELECT username FROM users");
$counter=0;
echo "<div class='labeles'>";
while($row = mysqli_fetch_array($usersReturned)) {

    $counter++;
    $user = new User($con, $userLoggedIn);
    $user_found_obj = new User($con, $row['username']);
    $pp = $row['username'];

    if($row['username'] == $userLoggedIn)
        continue;

    if($row['username'] != $userLoggedIn) {
        $mutual_friends = $user->getMutualFriends($row['username']) . " friends in common";
    }
    else {
        $mutual_friends = "";
    }

    if($user->isFriend($row['username'])) {
        echo "<label class='labell' for='c_box$counter'><div class='user_tb'>
                <img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 50%; margin-right: 5px;'>
                " . $user_found_obj->getFirstAndLastName() . "
                </div></label>
                <input type='checkbox' name='to_add[]' value='$pp' class='tb_ch_box' id='c_box$counter'>
            ";
    }
}
echo "</div>";
?>
    </form>
</div>