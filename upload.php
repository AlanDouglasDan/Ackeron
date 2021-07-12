<?php
require_once("includes/header.php");
?>

	<div class="contain"><br>
		<h3>Edit profile</h3><br><br>
		<div class="panel panel-default">
			<div class="panel-heading">Select Profile Image</div>
			<div class="panel-body">
				<form action="index.php" method="post">
					<input type="file" name="upload_image" id="upload_image" accept="image/*"><br>
					<label for="upload_image" id="selector">
						<h1>Change profile photo</h1> <i class="fa fa-plus-square-o fa-lg"></i>
					</label>
				</form>
				<div id="uploaded_image"></div>
			</div>
		</div>
	</div>

	<div id="uploadimageModal" class="modal" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Upload & crop image</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-8 text-center">
							<div id="image_demo" style="width:350px; margin-top:30px"></div>
						</div>
						<div class="col-xs-4" style="padding-top:30px;"><br><br><br>
							<form action="index.php" method="post">
								<input onclick="sound.play()" type="submit" name="crop" class="btn btn-success crop_image pull-right" value="Save">
							</form>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	<script>
		var sound = new Audio();
		sound.src = "button_click.mp3";

		$(document).ready(function(){
			$image_crop = $('#image_demo').croppie({
				enableExif:true,
				viewport:{
					width:300,
					height:300,
					type:'circle'
				},
				boundary:{
					width:350,
					height:350
				}
			});
			$('#upload_image').on('change',function(){
				var reader = new FileReader();
				reader.onload = function(event){
					$image_crop.croppie('bind',{
						url: event.target.result
					}).then(function(){
						console.log('jQuery bind complete');
					});
				}
				reader.readAsDataURL(this.files[0]);
				$('#uploadimageModal').modal('show');
			});
			$('.crop_image').click(function(event){
				$image_crop.croppie('result',{
					type:'canvas',
					size:'viewport'
				}).then(function(response){
					$.ajax({
						url:"index.php",
						type:"POST",
						data:{"image":response},
						success:function(data){
							$('#uploadimageModal').modal('hide');
							$('#upload_image').html(data);
						}
					});
				});
			});
		});
	</script>
</div>