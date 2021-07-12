<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");

if(isset($_COOKIE['user_login'])){
    $userLoggedIn = $_COOKIE['user_login'];
}
$id = $_POST['post_id'];

$qy = mysqli_query($con, "SELECT likes FROM posts WHERE id='$id'");
$ro = mysqli_fetch_array($qy);
$num_likes = $ro['likes']-1;
$likesQuery = mysqli_query($con, "SELECT username FROM likes WHERE post_id = '$id' AND username != '$userLoggedIn' ORDER BY id");
if(mysqli_num_rows($likesQuery)){
    while($row = mysqli_fetch_array($likesQuery))
        $person = $row['username'];
    if($num_likes >= 1){
        echo "<a href='likes.php?post_id=$id' class='black'>Liked by </a><a class='text-muted' href='$person'>$person</a><a href='likes.php?post_id=$id' class='black'> and $num_likes others </a>";
    }
    else{
        echo "";
    }
}
else
    echo "";

?>