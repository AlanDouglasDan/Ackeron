<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");

$query = sanitizeString($_POST['query']);
$user_to = $_POST['username'];
$userLoggedIn = $_POST['userLoggedIn'];

if($query){
    $check = mysqli_query($con, "SELECT * FROM typing WHERE username='$userLoggedIn' AND user_to='$user_to'");
    if(mysqli_num_rows($check) == 0){
        $insert = mysqli_query($con, "INSERT INTO typing VALUES(NULL, '$userLoggedIn', '$user_to', 'yes')");
    }
    else{
        $update = mysqli_query($con, "UPDATE typing SET typing='yes' WHERE username='$userLoggedIn' AND user_to='$user_to'");
    }
}
else{
    $close = mysqli_query($con, "UPDATE typing SET typing='no' WHERE username='$userLoggedIn' AND user_to='$user_to'");
}
?>