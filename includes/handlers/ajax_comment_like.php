<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");

sleep(0.5);

if(isset($_COOKIE['user_login'])){
    $userLoggedIn = $_COOKIE['user_login'];
}
$id = $_POST['comment_id'];

$get_likes = mysqli_query($con, "SELECT likes, posted_by FROM comments WHERE id='$id'");
$row = mysqli_fetch_array($get_likes);
$total_likes = $row['likes']; 
$user_liked = $row['posted_by'];

$check = mysqli_query($con, "SELECT * FROM comment_likes WHERE username='$userLoggedIn' AND comment_id='$id'");
if(mysqli_num_rows($check) == 0){
    $total_likes++;
    $query = mysqli_query($con, "UPDATE comments SET likes='$total_likes' WHERE id='$id'");
    $insert_user = mysqli_query($con, "INSERT INTO comment_likes VALUES('', '$userLoggedIn', '$id')");
    
    if($total_likes == 1)
        $total_likes .= " Like";
    else
        $total_likes .= " Likes";
}
else{    
    if($total_likes == 1)
        $total_likes .= " Like";
    else
        $total_likes .= " Likes";
}

echo '<label onclick="c_unlike('.$id.')"><span onclick="sound.play()" class="fa fa-heart fa-lg text-danger"></span> '.$total_likes.'</label>';
?>