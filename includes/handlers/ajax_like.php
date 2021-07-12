<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");
require_once("../classes/Notification.php");

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
if(mysqli_num_rows($check) == 0){
    $total_likes++;
    $query = mysqli_query($con, "UPDATE posts SET likes='$total_likes' WHERE id='$id'");
    $total_user_likes++;
    $user_likes = mysqli_query($con, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_liked'");
    $insert_user = mysqli_query($con, "INSERT INTO likes VALUES('', '$userLoggedIn', '$id')");
    
    if($user_liked != $userLoggedIn) {
        $ak = mysqli_query($con, "SELECT * FROM notifications WHERE user_to='$user_liked' AND user_from='$userLoggedIn' AND message LIKE '%liked your post%' AND link='post.php?id=$id'");
        if(mysqli_num_rows($ak) == 0){
            $notification = new Notification($con, $userLoggedIn);
            $notification->insertNotification($id, $user_liked, "like");            
        }    
    }
    // $post_not_query = mysqli_query($con, "SELECT * FROM post_notifications WHERE post_id='$id'");
    // while($dd = mysqli_fetch_array($post_not_query)){
    //     $p_name = $dd['username'];
    //     $ak2 = mysqli_query($con, "SELECT * FROM notifications WHERE user_to='$p_name' AND user_from='$userLoggedIn' AND message LIKE '%liked this post%' AND link='post.php?id=$id'");
    //     if(mysqli_num_rows($ak2) == 0){
    //         $notification2 = new Notification($con, $userLoggedIn);
    //         $notification2->insertNotification($id, $p_name, "likez");
    //     }
    // }
}

echo '<label onclick="unlike('.$id.')"><span onclick="sound.play()" class="fa fa-heart fa-lg text-danger"></span></label>';
?>