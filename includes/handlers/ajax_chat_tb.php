<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");

$userLoggedIn = $_POST['userLoggedIn'];

$usersReturned = mysqli_query($con, "SELECT username FROM users");
echo "<br>";
?>

<div class="card-header fix" style="background-color: #20aae5;">
    <span id="bring_back15" class="fa fa-remove"></span>
    <span class="center-block">Text Blast</span>
</div>
<?php
echo "<form action='messages.php' method='POST'>";
$counter=0;
echo "<div class='labeles'>";
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
                <input type='checkbox' name='to_blast[]' value='$pp' class='tb_ch_box' id='c_box$counter'>
            ";
    }
}
echo "</div>";
echo "<textarea name='text_tb' id='post_blast' rows='1' onkeypress='auto_grow(this)' style='border-radius: 20px; margin-top: 5;' class='form-control' placeholder='Write your text blast...'></textarea>";
echo "<input onclick='sound.play()' type='submit' name='tb_done' id='post_button2' class='tb_submit' value='Send'>";
echo "</form>";

?>

<script>
    function auto_grow(element){
        // if(element.scrollHeight <= 68){
            element.style.height = (element.scrollHeight)+"px";
        // }
    }
    
    $(document).ready(function(){
        $('input[id="post_button2"]').attr('disabled',true);
        $('textarea[id="post_blast"]').on('keyup',function(){
            if($(this).val()){
                $('input[id="post_button2"]').attr('disabled',false);
                document.getElementById("post_button2").style.backgroundColor="var(--sbutton)";
            }
            else{
                $('input[id="post_button2"]').attr('disabled',true);
                document.getElementById("post_button2").style.backgroundColor="var(--shadow2)";
            }
        });

        $('#bring_back15').on('click',function(){
			document.getElementById("chat_row").style.display="block";
			document.getElementById("hiden").style.display="block";
			document.getElementById("loaded").style.display="block";
			document.getElementById("tb_row").style.display="none";
			document.getElementById("searchez").style.display="none";
		});
    });
    
    var sound = new Audio();
    sound.src = "button_click.mp3";
</script>