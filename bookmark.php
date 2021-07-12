<?php
// require_once "includes/header.php";
    require_once "config/config.php";
	require_once "includes/classes/User.php";
	$userLoggedIn = $_SESSION['username'];
?>

<div class="main_column column" id="main_column">

	<h4 class="special_name center-block">Saved Posts</h4><hr>

    <div class="scrollTop" onclick ="scrollToTop();"><span class="fa fa-chevron-up" style="font-size: 170%; padding: 13; color: black;"></span></div>
    <div class="posts_area"></div>
    <img id="loading" src="assets/images/icons/loading.gif">

</div> 

<script>
    function scrollToTop(){
        document.getElementById("main_column").scrollTo({
            top: 0,
            behaviour: 'smooth'
        })
    }
    $(function(){

        var userLoggedIn = '<?php echo $userLoggedIn; ?>';
        var inProgress = false;

        loadPosts(); //Load first posts

        $('#main_column').scroll(function() {
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
                url: "includes/handlers/ajax_load_bookmarks.php",
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
            // console.log(rect);
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight + 800 || document.documentElement.clientHeight) && //* or $(window).height()
                rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
            );
        }
    });
</script>
