<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");

$query = sanitizeString($_POST['query']);
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query);

if(strpos($query, "_") !== false) {
	$usersReturned = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' ORDER BY first_name");
}
else if(count($names) == 2) {
	$usersReturned = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' AND last_name LIKE '%$names[1]%') AND user_closed='no' ORDER BY first_name");
}
else {
	$usersReturned = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' OR last_name LIKE '%$names[0]%') AND user_closed='no' ORDER BY first_name");
}

// echo "<br>";
echo "<div class='center-block'>Add Participants</div>";
echo "<form action='new_group.php' method='POST'>";
$counter=0;

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
echo "<input onclick='sound.play()' type='submit' name='ng_done' id='post_button2' class='tb_submit' value='Next'>";
echo "</form>";

?>

<script>
    $(document).ready(function(){
        $('input[id="post_button2"]').attr('disabled',true);
        $('input[class="tb_ch_box"]').on('change',function(){
            if($(this).val()){
                $('input[id="post_button2"]').attr('disabled',false);
                document.getElementById("post_button2").style.backgroundColor="red";
            }
            else{
                $('input[id="post_button2"]').attr('disabled',true);
                document.getElementById("post_button2").style.backgroundColor="#e27373";
            }
        });
    });
    
    var sound = new Audio();
    sound.src = "button_click.mp3";
</script>