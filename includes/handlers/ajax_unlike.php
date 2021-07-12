<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");

sleep(0.5);

if(isset($_COOKIE['user_login'])){
    $userLoggedIn = $_COOKIE['user_login'];
}
$id = $_POST['post_id'];

$get_likes = mysqli_query($con, "SELECT likes, added_by FROM posts WHERE id='$id'");
$row = mysqli_fetch_array($get_likes);
$total_likes = $row['likes']; 
$user_liked = $row['added_by'];

$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$user_liked'");
$row = mysqli_fetch_array($user_details_query);
$total_user_likes = $row['num_likes'];

$check = mysqli_query($con, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$id'");
if(mysqli_num_rows($check) == 1){
    $total_likes--;
    $query = mysqli_query($con, "UPDATE posts SET likes='$total_likes' WHERE id='$id'");
    $total_user_likes--;
    $user_likes = mysqli_query($con, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_liked'");
    $insert_user = mysqli_query($con, "DELETE FROM likes WHERE username='$userLoggedIn' AND post_id='$id'");
}

echo '<label onclick="like('.$id.')"><span onclick="sound.play()" class="fa fa-heart-o fa-lg"></span></label>';
?>