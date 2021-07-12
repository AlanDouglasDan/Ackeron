<?php
    // require_once "includes/header.php";
    require_once "config/config.php";
	require_once "includes/classes/User.php";
	// require_once "includes/classes/Notification.php";
	$userLoggedIn = $_SESSION['username'];
?>
<div class="row" style='background-color: var(--bgc2); height: 6vh; display: flex'>
    <div class="input_group center-block" id="chat_row" style='width: 90%; margin: 1vh auto; height: 4vh;'>
        <input type="search" autocomplete='off' onkeyup='getLiveUsers(this.value, "<?php echo $userLoggedIn; ?>")' id="chat_search" placeholder='Search...' style="height: 100%; border-radius: 30px;" class='form-control inp'>
    </div>
</div>
<div id="searchesz"></div>
<div id="posts_area" style="height: 82vh; padding: 10px">
<style>
    .hold{
        overflow: hidden; 
        font-size: 12px;
        width: 100px;
    }
</style>
    <?php
        $query = mysqli_query($con, "SELECT * FROM searches WHERE username='$userLoggedIn' ORDER by id DESC");
        $searches = array();
        if(mysqli_num_rows($query)){
            while($row = mysqli_fetch_array($query)){
                array_push($searches, $row['searchee']);
            }
            ?>
                <div>
                    <div class="special_name center-block" style="margin: 10 0;">Recent Searches</div>
                    <div class="list">
                        <?php
                            foreach($searches as $person){
                                $person_obj = new User($con, $person);
                                echo "<span class='hold'><a href='$person'><img src='".$person_obj->getProfilePic()."'><br><span>". $person ."</span></a></span>";
                            }
                        ?>
                    </div>
                </div>
            <?php
        }
    ?>
    <div class="posts_area">
        <div class='special_name center-block'>Popular Posts</div><br>
        <div class="scrollTop" onclick ="scrollToTop();"><span class="fa fa-chevron-up" style="font-size: 170%; padding: 13; color: black;"></span></div>
    </div>
    <img id="loading" src="assets/images/icons/loading.gif">
</div>

<script>
	function scrollToTop(){
        document.getElementById("posts_area").scrollTo({
            top: 0,
            behaviour: 'smooth'
        })
    }

    $(function(){

        var userLoggedIn = '<?php echo $userLoggedIn; ?>';
        var inProgress = false;

        loadPosts(); //Load first posts

        $('#posts_area').scroll(function() {
            var bottomElement = $(".status_post").last();
            var noMorePosts = $('.posts_area').find('.noMorePosts').val();
            
            var scroll = document.querySelector(".scrollTop");
            var scroll_top = $(this).scrollTop();
            scroll.classList.toggle("active", scroll_top > 2000);

            // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
            if (isElementInView(bottomElement[0]) && noMorePosts == 'false') {
                loadPosts();
            }
        });

        function loadPosts() {
            if(inProgress) { //If it is already in the process of loading some posts, just return
                return;
            }
            
            inProgress = true;
            $('#loading').show();

            var page = $('.posts_area').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'

            $.ajax({
                url: "includes/handlers/ajax_load_post.php",
                type: "POST",
                data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
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
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
                rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
            );
        }
    });

</script>

<script>
    $(document).ready(function(){
        $('input[id="chat_search"]').on('keyup',function(){
            if($(this).val()){
                document.getElementById("posts_area").style.display="none";
            }
            else{
                document.getElementById("posts_area").style.display="block";
            }
        });
    });
</script>