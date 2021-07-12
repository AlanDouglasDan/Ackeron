<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");
require_once("../classes/Notification.php");

sleep(0.5);

$username = $_POST['username'];
$userLoggedIn = $_POST['userLoggedIn'];
$decision = $_POST['decision'];

$user_logged_obj = new User($con, $userLoggedIn);
$user_obj = new User($con, $username);
$pic = $user_obj->getProfilePic();

$add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array, '$username,') WHERE username='$userLoggedIn'");
$add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array, '$userLoggedIn,') WHERE username='$username'");

$delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$username'");
$notification = new Notification($con, $userLoggedIn);
$notification->insertNotification("0", $username, "friend_request");

if($decision == "no")
    echo "";
else{
    $mutual_friends = $user_logged_obj->getMutualFriends($username). " friends in common";
    ?>
    <div class='request<?php echo $username; ?>'>
        <div class='search_result'>
            <div class='searchPageFriendButtons'>
                <form action='' method='POST'>
                    <span class='btn<?php echo $username; ?>'>
                        <input onclick='unfriend("<?php echo $username; ?>", "<?php echo $userLoggedIn; ?>")' type='button' class='danger' value='Remove Friend'>
                    </span>
                    <br>
                </form>
            </div>

            <div class='result_profile_pic'>
                <img src='<?php echo $pic ?>' style='height: 100px;'>
            </div>

            <a href='<?php echo $username; ?>'><p id='grey'> <?php echo $username; ?></p>
                
                <?php echo $mutual_friends; ?></a><br>

        </div>
        <hr id='search_hr'>
    </div>
    <?php
}
?>