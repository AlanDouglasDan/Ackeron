<?php
    require_once "includes/header.php";

    if(isset($_GET['post_id'])) {
        $id = $_GET['post_id'];
        $query = mysqli_query($con, "SELECT likes FROM posts WHERE id='$id'");
        $ro = mysqli_fetch_array($query);
        $num_likes = $ro['likes'];
        if($num_likes > 1)
            $num_likes .= " people";
        else if($num_likes == 0)
            $num_likes = "nobody yet";
        else
            $num_likes .= " person";
    }
    else
        echo "<div class='main_column column' id='main_column'>You can't view the likes of a non-existent post</div>";
?>
<div class="main_column column center-block" id="main_column" style="padding: 0;">

    <h4 class="special_name center-block">Liked By <?php echo $num_likes; ?></h4>
    <div class="input_group" id="chat_row">
        <center><input style='width: 90%;' type="search" autocomplete='off' onkeyup='getLikers(this.value, "<?php echo $id; ?>")' id="search_box" placeholder='Search...' class='form-control inp'></center>
    </div>
    <br>
    <div id="likes" class="searches_likes"></div>
    <div id="all_likers" class="posts_area"></div>
    <img id="loading" src="assets/images/icons/loading.gif">

<script>
    $('#likes').css("padding","0");
    $(document).ready(function(){
        $('input[id="search_box"]').on('keyup',function(){
            if($(this).val()){
                document.getElementById("likes").style.padding="1em 0 0 0";
                document.getElementById("all_likers").style.display="none";
            }
            else{
                document.getElementById("likes").style.padding="0";
                document.getElementById("all_likers").style.display="block";
            }
        });
        var userLoggedIn = '<?php echo $userLoggedIn; ?>';
        var id = '<?php echo $id; ?>';
        var inProgress = false;

        loadPosts(); //Load first posts

        $('#main_column').scroll(function() {
            var bottomElement = $(".row").last();
            var noMorePosts = $('.posts_area').find('.noMorePosts').val();
            
            // var scroll = document.querySelector(".scrollTop");
            // var scroll_top = $(this).scrollTop();
            // scroll.classList.toggle("active", scroll_top > 2000);

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
                url: "includes/handlers/ajax_load_likes.php",
                type: "POST",
                data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&id=" + id,
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
                rect.bottom <= ($('#main_column').innerHeight() + 800 || document.documentElement.clientHeight + 800) && //* or $(window).height()
                rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
            );
        }
    });
</script>