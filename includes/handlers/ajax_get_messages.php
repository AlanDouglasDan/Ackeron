<?php  
require_once("../../config/config.php");
require_once("../classes/User.php");
require_once("../classes/Message.php");

$user_to = $_POST['username'];
$userLoggedIn = $_POST['userLoggedIn'];

$qu = mysqli_query($con, "SELECT * FROM backgrounds WHERE username='$userLoggedIn'");
if(mysqli_num_rows($qu)){
	$ss = mysqli_fetch_array($qu);
	$image = $ss['image'];
}
else
    $image = "";
    
$send_to = $user_to;
$blocked = "no";
$blacklist_query = mysqli_query($con, "SELECT * FROM blacklist WHERE username='$userLoggedIn' AND chat='$send_to'");
if(mysqli_num_rows($blacklist_query) == 0){
    $blocked = 'no';
}
else
    $blocked = "yes";
?>
<div class='msg-container' id='msg_container'>
<div class="msg-header row">
<div class='somen'>
<style>
    .loaded_messages{
        background-image: url(<?php echo $image; ?>);
    }
</style>
<?php			
if(strpos($user_to, "ACK_GROUP..??.") === false){			
    $user_to_obj = new User($con, $user_to);
    $typing_query = mysqli_query($con, "SELECT * FROM typing WHERE user_to='$userLoggedIn' AND username='$user_to' AND typing='yes'");
    if(mysqli_num_rows($typing_query)){
        $comp = "<span style='font-size: 150%'>typing...</span>";			
    }
    else{
        $comp = $user_to_obj->getLastSeen($user_to);
    }				
    echo "<a href='messages.php'><span class='fa fa-angle-left'></span></a><span class='msg-header-img col-xs-1'><img src='" . $user_to_obj->getProfilePic() . "'></span>";
    echo "<span class='col-xs-9 ding'><h6 id='view_profile' style='margin-bottom: 0;'><b>" . $user_to_obj->getFirstAndLastName() . "</b></h6><p class='last_seen'>" .$comp."</p></span><span class='col-xs-1'><a id='msg-settings'><span class='fa fa-gear'></span></a></span></div></div>";

    echo "<div class='loaded_messages'><div class='msg-inbox'><div class='chats'><div class='msg-page' id='scroll_messages'>";
    echo "<div class='scrollTop' onclick ='scrollToTop();'><span class='fa fa-chevron-up' style='font-size: 170%; padding: 13; color: black;'></span></div>";
        // echo $message_obj->getMessages($user_to);
        // <span class='col-xs-9 ding'><h6 id='view_profile' style='margin-bottom: 0;'><b>" . $user_to_obj->getFirstAndLastName() . "</b></h6><p class='last_seen'>" .$comp."</p></span><span class='col-xs-1'><a id='msg-settings'><span class='fa fa-gear'></span></a></span></div></div>";
        echo "<img id='loading' src='assets/images/icons/loading.gif'>";
}
else{    
    $e = substr($user_to, 14);
    $send_to = "ACK_GROUP..??.$e";
    $blacklist_query = mysqli_query($con, "SELECT * FROM blacklist WHERE username='$userLoggedIn' AND chat='$send_to'");
    if(mysqli_num_rows($blacklist_query) == 0){
        $blocked = 'no';
    }
    else
        $blocked = "yes";
    $get_query = mysqli_query($con, "SELECT * FROM group_chats WHERE id='$e'");
    $rowsd = mysqli_fetch_array($get_query);
    $userss = $rowsd['users'];
    $userss = substr($userss, 0, -1);
    $participants = explode(",", $userss);
    $total = count($participants);
    sort($participants, SORT_STRING);
    $userss = implode(", ", $participants);
    $usersz = str_replace($userLoggedIn . ",", "", $userss);
    $ima = $rowsd['group_pic'];
    if($ima == "")
        $ima = "assets/images/profile_pics/defaults/male.png";
    $nam = $rowsd['group_info'];
    $nam = substr($nam, 14);
    $typing_query = mysqli_query($con, "SELECT * FROM typing WHERE user_to='$user_to' AND username!='$userLoggedIn' AND typing='yes'");
    if(mysqli_num_rows($typing_query)){
        $row = mysqli_fetch_array($typing_query);
        $typist = $row['username'];
        $comp = "<span style='font-size: 150%'>$typist is typing...</span>";			
    }
    else{
        $comp = $usersz;
    }
    echo "<a href='messages.php'><span class='fa fa-angle-left'></span></a><span class='msg-header-img col-xs-1'><img src='" . $ima . "'></span>";
    echo "<span class='col-xs-9 ding'><h6 id='add_members' style='margin-bottom: 0;'><b>" . $nam . "</b></h6><p class='last_seen'>" .$comp."</p></span><span class='col-xs-1'><a id='msg-settings'><span class='fa fa-gear'></span></a></span></div></div>";

    echo "<div class='loaded_messages'><div class='msg-inbox'><div class='chats'><div class='msg-page' id='scroll_messages'>";
    echo "<div class='scrollTop' onclick ='scrollToTop();'><span class='fa fa-chevron-up' style='font-size: 170%; padding: 13; color: black;'></span></div>";
        // echo $message_obj->getMessages($user_to);
        echo "<img id='loading' src='assets/images/icons/loading.gif'>";
}
echo "</div></div></div></div>";
?>
<script>
    $(function(){
        var userLoggedIn = '<?php echo $userLoggedIn; ?>';
        var profileUsername = '<?php echo $send_to; ?>';
        var inProgress = false;

        loadPosts("first"); //Load first posts

        $('#scroll_messages').scroll(function() {
            var bottomElement = $(".msag").first();
            var noMorePosts = $('.msg-page').find('.noMorePosts').val();
            
            // var scroll = document.querySelector(".scrollTop");
            // var scroll_top = $(this).scrollTop();
            // scroll.classList.toggle("active", scroll_top < 2000);
            // console.log(scroll_top);

            // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
            if (isElementInView(bottomElement[0]) && noMorePosts == 'false') {
                loadPosts("others");
            }
        });

        function loadPosts(term) {
            if(inProgress) { //If it is already in the process of loading some posts, just return
                return;
            }							
            
            inProgress = true;
            $('#loading').show();

            var page = $('.msg-page').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'

            $.ajax({
                url: "includes/handlers/ajax_load_messagez.php",
                type: "POST",
                data: "page=" + page + "&userLoggedIn=" + userLoggedIn  + "&profileUsername=" + profileUsername,
                cache:false,

                success: function(response) {
                    $('.msg-page').find('.nextPage').remove(); //Removes current .nextpage 
                    $('.msg-page').find('.noMorePosts').remove(); //Removes current .nextpage 

                    $('#loading').hide();
                    $(".msg-page").prepend(response);
                    if(term == "first"){
                        var div = document.getElementById("scroll_messages");
                        div.scrollTop = div.scrollHeight;								
                    }
                    else{
                        response.scrollTop = response.scrollHeight;
                    }

                    inProgress = false;
                }
            });														
        }

        //Check if the element is in view
        function isElementInView (el) {
            var rect = el.getBoundingClientRect();

            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
                rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
            );
        }
    });
</script>

<form action="" method="POST">
    <?php
    echo "<div class='message_post' id='blocks'>";
    if($blocked == "no"){
        ?>
        <div class="col-xs-1">
            <span id="img_msg">+</span>
        </div>
        <div class="col-xs-11">
            <div class="input-group">
                <textarea rows="1" onkeypress="auto_grow(this);" onkeyup="typing(this.value, '<?php echo $user_to; ?>', '<?php echo $userLoggedIn; ?>');" type="text" placeholder="Write your message ..." id='message_textareaz' name='message_body' class="form-control"></textarea>
                <input type='button' onclick="uploadFile()" name='post_message' id='message_submit'>
                <div class="input-group-addon">
                    <label onclick='sound.play()' for='message_submit' style="cursor: pointer;">
                        <span id="new_btn4" class="input-group-text" style="color: transparent">
                            <!-- <i class="fa fa-paper-plane"></i> -->
                            Send
                        </span>
                    </label>									
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function(){
                $('input[id="message_submit"]').attr('disabled',true);
                $('textarea[id="message_textareaz"]').on('keyup',function(){
                    if($(this).val()){
                        $('input[id="message_submit"]').attr('disabled',false);
                        document.getElementById("new_btn4").style.color="var(--nic)";
                    }
                    else{
                        $('input[id="message_submit"]').attr('disabled',true);
                        document.getElementById("new_btn4").style.color="white";
                    }
                });
            });
            
            function auto_grow(element){
                // var block = document.getElementById("blocks");
                if(element.scrollHeight <= 68){
                    element.style.height = (element.scrollHeight)+"px";
                }
            }

            var userLoggedIn = '<?php echo $userLoggedIn; ?>';
            var profileUsername = '<?php echo $user_to; ?>';								
            function uploadFile(){
                var formdata = new FormData();
                var ajax = new XMLHttpRequest();
                ajax.addEventListener("load", completeHandler, false);
                var text = document.getElementById("message_textareaz").value;
                formdata.append("username", profileUsername);
                formdata.append("userLoggedIn", userLoggedIn);
                formdata.append("text", text);
                ajax.open("POST", "includes/form_handlers/uploadMessage.php");
                ajax.send(formdata);
                document.getElementById("message_textareaz").value = "";
            }						

            function completeHandler(event){
                document.getElementById("message_textareaz").height = document.getElementById("message_textareaz").scrollHeight;
                document.getElementById("scroll_messages").innerHTML = event.target.responseText;
                var div = document.getElementById("scroll_messages");
                div.scrollTop = div.scrollHeight;
            }

            
        </script>
        <?php
    }
    else{
        echo "<div class='center-block' style='padding-top:3vh;'>You can't send messages to this group because you are no longer a member</div>";
    }
    ?>
</form>
<?php

?>