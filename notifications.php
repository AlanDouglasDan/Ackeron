<?php
    // require_once "includes/header.php";
	require_once "config/config.php";
	require_once "includes/classes/User.php";
	require_once "includes/classes/Notification.php";
	$userLoggedIn = $_SESSION['username'];
    $post = new Notification($con, $userLoggedIn);
?>

<div class="column" id="load_more" style='padding: 0'>
    <div class="posts_area"></div>
    <img id="loading" src="assets/images/icons/loading.gif">
</div>

<script>
	var userLoggedIn = '<?php echo $userLoggedIn; ?>';

	$(document).ready(function() {

		$('#loading').show();

		//Original ajax request for loading first posts 
		$.ajax({
			url: "includes/handlers/ajax_load_notification.php",
			type: "POST",
			data: "page=1&userLoggedIn=" + userLoggedIn,
			cache:false,

			success: function(data) {
				$('#loading').hide();
				$('.posts_area').html(data);
			}
		});

		$('#load_more').scroll(function() {
			var height = $('.posts_area').height(); //Div containing posts
			var scroll_top = $(this).scrollTop();
			var scroll_top = scroll_top + 2;
			var page = $('.posts_area').find('.nextPage').val();
			var noMorePosts = $('.posts_area').find('.noMorePosts').val();
			var he = document.getElementById('load_more').scrollHeight;
			var hes = $('#load_more').innerHeight();
			// console.log(he);
			// console.log(hes);
			// console.log(scroll_top);

			if ((scroll_top + hes >= he) && noMorePosts == 'false') {
				$('#loading').show();

				var ajaxReq = $.ajax({
					url: "includes/handlers/ajax_load_notification.php",
					type: "POST",
					data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
					cache:false,

					success: function(response) {
						$('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
						$('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 

						$('#loading').hide();
						$('.posts_area').append(response);
					}
				});

			} //End if 

			return false;

		}); //End (window).scroll(function())


	});

</script>