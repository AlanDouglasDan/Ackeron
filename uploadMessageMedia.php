<?php
require_once("config/config.php");
require_once("includes/classes/User.php");
require_once("includes/classes/Message.php");

$userLoggedIn = $_POST['fileu'];
$send_to = $_POST['filen'];
$message_obj = new Message($con, $userLoggedIn);

$body = " ".mysqli_real_escape_string($con, $_POST['fileb']);
$uploadOk = 1;
$errorMessage = "";
$date = date("Y-m-d H:i:s");
$imageNamed = $_FILES['file']['tmp_name'][0];

if($imageNamed){
    for($i=0; $i<count($_FILES["file"]["name"]); $i++){
        $imageName[] = $_FILES['file']['name'][$i];

        if($imageName[$i] != "") {
            $targetDir = "assets/images/messages/";
            $imageName[$i] = $targetDir . uniqid() . basename($imageName[$i]);
            $imageName[$i] = preg_split("/\s+/", $imageName[$i]);
            $imageName[$i] = implode("", $imageName[$i]);
            $imageFileType = pathinfo($imageName[$i], PATHINFO_EXTENSION);

            if($_FILES['file']['size'][$i] > 50000000){
                $errorMessage = "Sorry your file is too large";
                $uploadOk = 0;
            }

            if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg" && strtolower($imageFileType) != "mp4" && strtolower($imageFileType) != "mov") {
                $errorMessage = "Sorry, only jpeg, jpg, png, mov and mp4 files are allowed";
                $uploadOk = 0;
            }
            
            if($uploadOk) {					
                if(move_uploaded_file($_FILES['file']['tmp_name'][$i], $imageName[$i])) {
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

?>