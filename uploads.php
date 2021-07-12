<?php
    require_once "includes/header.php";
    if(isset($_GET['name']))
        $username = $_GET['name'];

    $user_obj = new User($con, $username);
?>
<div class="main_column column" id="upload_div" style="padding: 0; overflow-y: auto; height: 93vh;">
    <center style="margin: 10px;" class='special_name'><a href='<?php echo $username; ?>'><?php echo $username; ?></a></center>
    <div class="posts"></div>
    <img id="loading" src="assets/images/icons/loading.gif">
    <div class="scrollTop" onclick ="scrollToTop();"><span class="fa fa-chevron-up" style="font-size: 170%; padding: 13; color: black;"></span></div>
</div>

<script>
    function scrollToTop(){
        document.getElementById("upload_div").scrollTo({
        top: 0,
        behaviour: 'smooth'
        });
    }

    $(function(){
        var profileUsername = '<?php echo $username; ?>';
        var userLoggedIn = '<?php echo $userLoggedIn; ?>';
        var inProgress = false;

        loadPostz();

        $('#upload_div').scroll(function() {
            var bottomElement = $(".med_hold").last();
            var noMorePosts = $('.posts').find('.noMorePosts').val();

            var scroll = document.querySelector(".scrollTop");
            var scroll_top = $(this).scrollTop();
            scroll.classList.toggle("active", scroll_top > 2000);

            // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
            if (isElementInView(bottomElement[0]) && noMorePosts == 'false') {
                loadPostz();
            }
        });

        function loadPostz() {
            if(inProgress) { //If it is already in the process of loading some posts, just return
                return;
            }
            
            inProgress = true;
            $('#loading').show();

            var page = $('.posts').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'

            $.ajax({
                url: "includes/handlers/ajax_load_profile_media.php",
                type: "POST",
                data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
                cache:false,

                success: function(response) {
                $('.posts').find('.nextPage').remove(); //Removes current .nextpage 
                $('.posts').find('.noMorePosts').remove(); //Removes current .nextpage 
                $('.posts').find('.noMorePostsText').remove(); //Removes current .nextpage 

                $('#loading').hide();
                $(".posts").append(response);

                inProgress = false;
                }
            });
        }

        function isElementInView (el) {
            var rect = el.getBoundingClientRect();

            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight + 500 || document.documentElement.clientHeight) && //* or $(window).height()
                rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
            );
        }
    });
</script>