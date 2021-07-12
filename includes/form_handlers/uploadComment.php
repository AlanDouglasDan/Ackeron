<?php
require_once '../../config/config.php';
require_once("../classes/User.php");
require_once("../classes/Notification.php");
require_once("../classes/Post.php");

$username = $_POST['username'];
$userLoggedIn = $_POST['userLoggedIn'];
$message = $_POST['text'];
$id = $_POST['id'];
$user_to = $_POST['user_to'];

$notification = new Notification($con, $userLoggedIn);
$post = new Post($con, $userLoggedIn);

$post_body = mysqli_escape_string($con, $message);
$post_body = strip_tags($post_body); //removes html tags 
// $post_body = str_replace('\r\n', "\n", $post_body);
// $post_body = nl2br($post_body);
$date_time_now = date("Y-m-d H:i:s");
$insert_post = mysqli_query($con, "INSERT INTO comments VALUES ('', '$post_body', '$userLoggedIn', '$username', '$date_time_now', 'no', '$id', '0')");

if($username != $userLoggedIn) {
    $notification->insertNotification($id, $username, "comment");
}

if($user_to != 'none' && $user_to != $userLoggedIn) {
    $notification->insertNotification($id, $user_to, "profile_comment");
}

$get_commenters = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$id'");
$notified_users = array();
while($roww = mysqli_fetch_array($get_commenters)) {

    if($roww['posted_by'] != $username && $roww['posted_by'] != $user_to && $roww['posted_by'] != $userLoggedIn && !in_array($roww['posted_by'], $notified_users)){
            $notification->insertNotification($id, $roww['posted_by'], "comment_non_owner");
            array_push($notified_users, $roww['posted_by']);
    }

}

// $post_not_query = mysqli_query($con, "SELECT * FROM post_notifications WHERE post_id='$id'");
// while($dd = mysqli_fetch_array($post_not_query)){
//     $p_name = $dd['username'];
//     if(!in_array($p_name, $notified_users) && ($p_name != $userLoggedIn)){
//         $ak2 = mysqli_query($con, "SELECT * FROM notifications WHERE user_to='$p_name' AND user_from='$userLoggedIn' AND message LIKE '%commented on this post%' AND link='post.php?id=$id'");
//         if(mysqli_num_rows($ak2) == 0){
//             $notification->insertNotification($id, $p_name, "commentz");
//         }
//     }
// }

$array = array();
$array['page'] = 1;
$array['userLoggedIn'] = $userLoggedIn;
$array['id'] = $id;
echo $post->getComments($array, 15);

?>