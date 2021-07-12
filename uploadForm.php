<?php
require_once("config/config.php");
require_once("includes/classes/User.php");
require_once("includes/classes/Notification.php");
require_once("includes/classes/Post.php");

if(isset($_COOKIE['user_login'])){
    $userLoggedIn = $_COOKIE['user_login'];
}

$uploadOk = 1;

foreach($_FILES['file']['tmp_name'] as $key => $value){
    $imageName[] = $_FILES['file']['name'][$key];
    if($imageName[$key] != "") {
        $targetDir = "assets/images/posts/";
        $imageName[$key] = $targetDir . uniqid() . basename($imageName[$key]);
        $imageFileType = pathinfo($imageName[$key], PATHINFO_EXTENSION);		
        if($_FILES['file']['size'][$key] > 50000000){
            $errorMessage = "Sorry your file is too large";
            $uploadOk = 0;
        }
        if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg" && strtolower($imageFileType) != "mp4" && strtolower($imageFileType) != "mov") {
            $errorMessage = "Sorry, only jpeg, jpg, png and mp4 files are allowed";
            $uploadOk = 0;
        }
        if($uploadOk) {					
            if(move_uploaded_file($_FILES['file']['tmp_name'][$key], $imageName[$key])) {
                // image uploaded okay
            }
            else {
                //image did not upload
                $uploadOk = 0;
                $errorMessage = "Upload Failed";
            }
        }
        $body_array = explode(".", $imageName[$key]);
        if($body_array[count($body_array)-1] == "MP4") {
            $body_array[count($body_array)-1] = "mp4";
        }
        $imageName[$key] = implode(".", $body_array);
    }
}

$name = $_POST['filen'];
$body = $_POST['fileb'];
$tags = $_POST['filet'];
$location = sanitizeString($_POST['filel']);

if($uploadOk) {
    $post = new Post($con, $userLoggedIn);
    $post->submitPost($body, $name, $imageName, $location, $tags, "no");
    echo "<div style='text-align:center;' class='alert alert-success'>
        <button class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
        </button>
        Upload is complete
    </div>";
}
else {
    echo "<div style='text-align:center;' class='alert alert-danger'>
            <button class='close' data-dismiss='alert' aria-label='Close'>
                <span aria-hidden='true'>&times;</span>
            </button>
            $errorMessage
        </div>";
}

?>