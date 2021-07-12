<?php
require_once 'config/config.php';
require_once 'includes/classes/Message.php';
require_once 'includes/classes/User.php';

$userLoggedIn = $_SESSION['username'];
$name = $_POST['username'];

?>

<div id="msg_preview" style='display: block;'>
    <div class="card-header fix">
        <span id="bring_back" onclick='bring_back("<?php echo $name; ?>", "<?php echo $userLoggedIn; ?>")' class="fa fa-remove"></span>
        <span class="center-block">Send a media</span>
    </div>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="fileToUpload[]" id="fileToUpload" multiple>
        <label for="fileToUpload" id="img_selector">
            <h1>Choose a photo</h1> <i class="fa fa-plus-square-o fa-lg"></i>
        </label>
    
        <div id="status_form" style="display:none; margin-top: 0vh">
            <center><textarea rows='5' name='media_caption' id='status_textarea' placeholder='Add a Caption...'></textarea></center>
            <!-- <input type='button' onclick='uploadFile()' name='post_media' id='media_submit'>
            <label onclick='sound.play()' id='' for='media_submit'><span class='fa fa-paper-plane'></span> -->
            <center><input type="button" onclick='sendMediaMessage()' id="post_button7" style='position: relative; width: 90%; margin: 0;' value="Post"></center>
        </div>
    </form><br>
    <div class="progress" style="display: none;" id="bars">
        <progress id="progressBar" value='0' max='100'></progress>
    </div>
    <h3 id="ssl"></h3>
    <div id="image_preview" style='margin: 10 0'></div>
    <script>
        var arr = [];
        function sendMediaMessage(){
            var total_file = arr.length;
            var body = _("status_textarea").value;
            _("image_preview").style.display="none";
            _("bars").style.display="block";
            var formdata = new FormData();
            var ajax = new XMLHttpRequest();
            ajax.upload.addEventListener("progress", progressHandler, false);
            ajax.addEventListener("load", completeHandler, false);
            ajax.addEventListener("error", errorHandler, false);
            ajax.addEventListener("abort", abortHandler, false);
            var name = '<?php echo $name; ?>';
            var userLoggedIn = '<?php echo $userLoggedIn; ?>';
            if(total_file >= 1){
                for(var filee of arr){
                    formdata.append("file[]", filee);
                }
                formdata.append("fileb", body);
                formdata.append("filen", name);
                formdata.append("fileu", userLoggedIn);
                ajax.open("POST", "uploadMessageMedia.php");
                ajax.send(formdata);
            }
        }

        $('input[id="post_button7"]').attr('disabled',true);
        $('textarea[id="post_text2"]').on('keyup',function(){
            if($(this).val()){
                $('input[id="post_button7"]').attr('disabled',false);
                document.getElementById("post_button7").style.backgroundColor="var(--sbutton)";
            }
            else{
                $('input[id="post_button7"]').attr('disabled',true);
                document.getElementById("post_button7").style.backgroundColor="var(--shadow2)";
            }
        });
        
        $("#fileToUpload").change(function(event){
            if($(this).val()){
                $('input[id="post_button7"]').attr('disabled',false);
                document.getElementById("post_button7").style.backgroundColor="var(--sbutton)";
                document.getElementById("image_preview").style.display="flex";
            }
            else{
                $('input[id="post_button7"]').attr('disabled',true);
                document.getElementById("post_button7").style.backgroundColor="var(--shadow2)";
                document.getElementById("image_preview").style.display="none";
            }

            $('#image_preview').css("height", "120px");
            $('#image_preview').css("alignItems", "center");
            var total_file=document.getElementById("fileToUpload").files.length;

            let form = document.getElementById("status_form");
            form.style.display = "block";
            
            for(var i=0;i<total_file;i++)
            {
                let file = event.target.files[i];            
                if(file.type.match("image")){
                    arr.push(file);
                    var f_name = arr.indexOf(file);
                    $('#image_preview').append("<img class='rmv"+f_name+"' style='width:80px; height:80px; margin: 5;' src='"+URL.createObjectURL(event.target.files[i])+"'><span class='rmve"+f_name+" rmv_btn' onclick='remove(\""+f_name+"\")'>&times;</span>");
                }
                else{
                    arr.push(file);
                    var f_name = arr.indexOf(file);
                    $('#image_preview').append("<video class='rmv"+f_name+"' style='width:80px; height:80px; margin: 5;' autoplay muted src='"+URL.createObjectURL(event.target.files[i])+"'></video><span class='rmve"+f_name+" rmv_btn' onclick='remove(\""+f_name+"\")'>&times;</span>");
                }
            }
        });

        function remove(gg){			
            $(".rmv"+gg).css("display", "none");
            $(".rmve"+gg).css("display", "none");
            var img = arr[gg];
            arr.pop(img);
        }

        function progressHandler(event){
            var percent = (event.loaded / event.total) * 100;
            _("progressBar").value = Math.round(percent);
            _("ssl").innerHTML = Math.round(percent)+"% uploaded... please wait";
        }
        function completeHandler(event){
            // _("ssl").innerHTML = event.target.responseText;
            _("progressBar").value = 100;		
            var name = '<?php echo $name; ?>';					
            window.location.href="messages.php?u="+name
        }
        function errorHandler(event){
            _("ssl").innerHTML = "upload failed";
        }
        function abortHandler(event){
            _("ssl").innerHTML = "Upload Aborted";
        }						
    </script>
</div> 