<?php
require_once "includes/header.php";

if(isset($_GET['value']))
    $hashtag = "#". strtolower($_GET['value']);
else
    header("Location: index.php");

if(isset($_GET['type']))
    $type = $_GET['type'];
else
    $type = "top";

// $post = new Post($con, $userLoggedIn);
$query = mysqli_query($con, "SELECT * FROM posts WHERE body LIKE '%$hashtag%' AND deleted='no' ORDER BY likes DESC");
$num = mysqli_num_rows($query);
if($type == "top"){
    $top = "class='active'";
    $recent = "";
}
else if($type == "recent"){
    $top = "";
    $recent = "class='active'";
}
else
    $top = $recent = "";
?>

<div class="col-md-offset-3 col-md-9">
    <center class="special_name">
        <?php echo  $hashtag; ?>
        <?php echo $num; ?> reposts
    </center>
    <ul class="nav nav-tabs nav-justified" role="tablist" id="profileTabs" style="padding: 15px 0 0;">
        <li <?php echo $top; ?> role="presentation"><a href="hashtags.php?value=<?php echo $_GET['value']; ?>&type=top" style="margin:0;">Top Posts</a></li>
        <li <?php echo $recent; ?> role="presentation"><a href="hashtags.php?value=<?php echo $_GET['value']; ?>&type=recent" style="margin:0;">Recent Posts</a></li>
    </ul>
    <div class="column hsht" style="height: 93vh;" id="load_more">
        <div class="tab-content" style="padding: 0px;">

        <div role="tabpanel" class="tab-pane fade in active" id="top_div">
            <div class="posts_area"></div>
            <div class="scrollTop" onclick ="scrollToTop();"><span class="fa fa-chevron-up" style="font-size: 170%; padding: 13; color: black;"></span></div>
            <img id="loading" src="assets/images/icons/loading.gif">
        </div>

        <div class="tab-pane fade" id="recent_div" role='tabpanel'>
            <!-- <div class="posts"> -->
            <?php 
                // if($num2){
                //     while($row = mysqli_fetch_array($query2)){
                //         echo $post->getSinglePost($row['id'], $num);
                //     }
                // }
            ?>
            <!-- <div class="posts_area"></div>
            <img id="loading" src="assets/images/icons/loading.gif"> -->
            <!-- </div> -->
        </div>
    </div>
</div>

<script>
    function scrollToTop(){
        document.getElementById("load_more").scrollTo({
            top: 0,
            behaviour: 'smooth'
        })
    }

    $(function(){

        var userLoggedIn = '<?php echo $userLoggedIn; ?>';
        var type = '<?php echo $type; ?>';
        var hashtag = '<?php echo $hashtag; ?>';
        var inProgress = false;

        loadPosts(type); //Load first posts

        $('#load_more').scroll(function() {
            var bottomElement = $(".status_post").last();
            var noMorePosts = $('.posts_area').find('.noMorePosts').val();
            
            var scroll = document.querySelector(".scrollTop");
            var scroll_top = $(this).scrollTop();
            scroll.classList.toggle("active", scroll_top > 2000);

            // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
            if (isElementInView(bottomElement[0]) && noMorePosts == 'false') {
                loadPosts(type);
            }
        });

        function loadPosts(term) {
            if(inProgress) { //If it is already in the process of loading some posts, just return
                return;
            }
            
            inProgress = true;
            $('#loading').show();

            var page = $('.posts_area').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'

            $.ajax({
                url: "includes/handlers/ajax_load_hashtags.php",
                type: "POST",
                data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&term=" + term + "&value=" + hashtag,
                cache:false,

                success: function(response) {
                    $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
                    $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 
                    $('.posts_area').find('.noMorePostsText').remove(); //Removes current .nextpage 

                    $('#loading').hide();
                    $(".posts_area").append(response);

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
                rect.bottom <= (window.innerHeight + 400 || document.documentElement.clientHeight) && //* or $(window).height()
                rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
            );
        }
    });
</script>
