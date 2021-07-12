<?php  
require_once("includes/header.php");

if(isset($_GET['id'])) {
	$id = $_GET['id'];
}
else {
	$id = 0;
}
$str = "";
?>

<div class="col-md-offset-3 col-md-9">
	<div class="column" id="main_column">

		<div class="posts_area">

			<?php 
				$post = new Post($con, $userLoggedIn);
				$post->getSinglePost($id, $str);
			?>

		</div>

	</div>
</div>