<?php 
require_once "includes/header.php";

    if(isset($_GET['post_id']))
        $id = $_GET['post_id'];

    $user_query = mysqli_query($con, "SELECT added_by, user_to FROM posts WHERE id='$id'");
    $rowz = mysqli_fetch_array($user_query);

    if(mysqli_num_rows($user_query) == 0){
        header("Location: index.php");
    }

    $posted_to = $rowz['added_by'];
    $user_to = $rowz['user_to'];

    if(isset($_POST['postComment' . $id])) {
        $post_body = $_POST['message_body'];
        if($post_body){
            if($post_body != " "){
            $post_body = mysqli_escape_string($con, $post_body);
            $post_body = strip_tags($post_body); //removes html tags 
            $post_body = str_replace('\r\n', "\n", $post_body);
            $post_body = nl2br($post_body);
            $date_time_now = date("Y-m-d H:i:s");
            $insert_post = mysqli_query($con, "INSERT INTO comments VALUES ('', '$post_body', '$userLoggedIn', '$posted_to', '$date_time_now', 'no', '$id', '0')");
            // $dd = "";
            // $no = "no";
            // $query = "INSERT INTO comments VALUES(?, ?, ?, ?, ?, ?, ?)";
            // $stmt = mysqli_stmt_init($con);
            // if(!mysqli_stmt_prepare($stmt, $query)){
            //     echo "SQL ERROR";
            // }
            // else{
            //     mysqli_stmt_bind_param($stmt, "isssssi", $dd, $post_body, $userLoggedIn, $posted_to, $date_time_now, $no, $id);
            //     mysqli_stmt_execute($stmt);
            // }
            
            if($posted_to != $userLoggedIn) {
                $notification = new Notification($con, $userLoggedIn);
                $notification->insertNotification($id, $posted_to, "comment");
            }
            
            if($user_to != 'none' && $user_to != $userLoggedIn) {
                $notification = new Notification($con, $userLoggedIn);
                $notification->insertNotification($id, $user_to, "profile_comment");
            }
        
            $get_commenters = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$id'");
            $notified_users = array();
            while($roww = mysqli_fetch_array($get_commenters)) {
    
                if($roww['posted_by'] != $posted_to && $roww['posted_by'] != $user_to 
                    && $roww['posted_by'] != $userLoggedIn && !in_array($roww['posted_by'], $notified_users)) {
    
                    $notification = new Notification($con, $userLoggedIn);
                    $notification->insertNotification($id, $roww['posted_by'], "comment_non_owner");
    
                    array_push($notified_users, $roww['posted_by']);
                }
    
            }

            $post_not_query = mysqli_query($con, "SELECT * FROM post_notifications WHERE post_id='$id'");
            while($dd = mysqli_fetch_array($post_not_query)){
                $p_name = $dd['username'];
                if(!in_array($p_name, $notified_users) && ($p_name != $userLoggedIn)){
                    $ak2 = mysqli_query($con, "SELECT * FROM notifications WHERE user_to='$p_name' AND user_from='$userLoggedIn' AND message LIKE '%commented on this post%' AND link='post.php?id=$id'");
                    if(mysqli_num_rows($ak2) == 0){
                        $notification2 = new Notification($con, $userLoggedIn);
                        $notification2->insertNotification($id, $p_name, "commentz");
                    }
                }
            }

            header("Location: comments.php?post_id=$id");
            }
        }
    }

    if(isset($_GET['post_id'])){
        $id = $_GET['post_id'];
        echo "<div class='main_column column center-block' id='main_column' style='padding: 0;'>";
        echo "<h4 class='special_name center-block'>Comments</h4>";
        echo "<div class='comments_area' id='comments_area'></div>";
        echo "<img id='loading' src='assets/images/icons/loading.gif'>";
        $comments_query = mysqli_query($con, "SELECT * FROM comments WHERE post_id=$id AND removed='no' ORDER BY id ASC");
        if(mysqli_num_rows($comments_query)){            
            ?>
            <form action="comments.php?post_id=<?php echo $id; ?>" name="postComment<?php echo $id; ?>" method="POST" style="padding: 10; background-color: red;">
                <div class="input-group" style="margin-top: 0; width: 95%; margin-left: auto; margin-right: auto;" >
                    <textarea rows="1" onkeypress="auto_grow(this);" type="text" placeholder="Add a comment ..." id='message_textareaz' name='message_body' class="form-control"></textarea>
                    <input type='button' onclick="uploadComment()" name='postComment<?php echo $id; ?>' id='comment_submit'>
                    <div class="input-group-addon addon">
                        <label onclick='sound.play()' for='comment_submit' style="cursor: pointer;">
                            <span id="new_btn4" class="input-group-text" style="color: transparent">
                                Send
                            </span>
                        </label>									
                    </div>
                </div>
            </form>
            <?php
        }
        else{
            echo "There are no comments on this post yet<br><br>";
            echo "<form action='comments.php?post_id=$id' name='postComment$id' method='POST'>";?>
            <div class="input-group" style="width: 95%; margin-left: auto; margin-right: auto;">
                <textarea rows="1" onkeypress="auto_grow(this);" type="text" placeholder="Add a comment ..." id='message_textareaz' name='message_body' class="form-control"></textarea>
                <input type='button' onclick="uploadComment()" name='postComment<?php echo $id; ?>' id='comment_submit'>
                <div class="input-group-addon addon">
                    <label onclick='sound.play()' for='comment_submit' style="cursor: pointer;">
                        <span id="new_btn4" class="input-group-text" style="color: transparent">
                            Send
                        </span>
                    </label>									
                </div>
            </div>
            </form>
            <?php
        }
    }
    else
        echo "<div class='main_column column' id='main_column'>You can't view the comments of a non-existent post</div>";
?>

<script>
    function auto_grow(element){
        if(element.scrollHeight <= 68){
            element.style.height = (element.scrollHeight)+"px";
        }
    }

    var userLoggedIn = '<?php echo $userLoggedIn; ?>';
    var profileUsername = '<?php echo $posted_to; ?>';
    var user_to = '<?php echo $user_to; ?>';
    var id = '<?php echo $id; ?>';
    function uploadComment(){
        var formdata = new FormData();
        var ajax = new XMLHttpRequest();
        ajax.addEventListener("load", completeHandler, false);
        var text = document.getElementById("message_textareaz").value;
        formdata.append("username", profileUsername);
        formdata.append("user_to", user_to);
        formdata.append("userLoggedIn", userLoggedIn);
        formdata.append("id", id);
        formdata.append("text", text);
        if(text){
            ajax.open("POST", "includes/form_handlers/uploadComment.php");
            ajax.send(formdata);
        }        
        document.getElementById("message_textareaz").value = "";
    }

    function completeHandler(event){
        document.getElementById("message_textareaz").height = document.getElementById("message_textareaz").scrollHeight;
        document.getElementById("comments_area").innerHTML = event.target.responseText;
        var div = document.getElementById("main_column");
        div.scrollTop = div.scrollHeight;
    }

    $(document).ready(function(){
        $('input[id="comment_submit"]').attr('disabled',true);
        $('textarea[id="message_textareaz"]').on('keyup',function(){
            if($(this).val()){
                $('input[id="comment_submit"]').attr('disabled',false);
                document.getElementById("new_btn4").style.color="var(--nic)";
            }
            else{
                $('input[id="comment_submit"]').attr('disabled',true);
                document.getElementById("new_btn4").style.color="white";
            }
        });

        var userLoggedIn = '<?php echo $userLoggedIn; ?>';
        var id = '<?php echo $id; ?>';
        var inProgress = false;

        loadPosts("first"); //Load first posts

        $('#main_column').scroll(function() {
            var bottomElement = $(".comment").first();
            var noMorePosts = $('.comments_area').find('.noMorePosts').val();

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

            var page = $('.comments_area').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'

            $.ajax({
                url: "includes/handlers/ajax_load_comments.php",
                type: "POST",
                data: "page=" + page + "&userLoggedIn=" + userLoggedIn  + "&id=" + id,
                cache:false,

                success: function(response) {
                    $('.comments_area').find('.nextPage').remove(); //Removes current .nextpage 
                    $('.comments_area').find('.noMorePosts').remove(); //Removes current .nextpage 

                    $('#loading').hide();
                    $(".comments_area").prepend(response);
                    if(term == "first"){
                        var div = document.getElementById("main_column");
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
                rect.bottom <= (window.innerHeight +700 || document.documentElement.clientHeight) && //* or $(window).height()
                rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
            );
        }
    });

    var sound = new Audio();
    sound.src = "button_click.mp3";

    // var div = document.getElementById("main_column");
    // div.scrollTop = div.scrollHeight;
</script>

</div>