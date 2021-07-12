<?php 
require_once("includes/header.php");
?>

<script>
    $(document).ready(function(){
        $('input[id="post_button2"]').attr('disabled',true);
        $('textarea[id="post_text2"]').on('keyup',function(){
            if($(this).val()){
                $('input[id="post_button2"]').attr('disabled',false);
                document.getElementById("post_button2").style.backgroundColor="var(--sbutton)";
            }
            else{
                $('input[id="post_button2"]').attr('disabled',true);
                document.getElementById("post_button2").style.backgroundColor="var(--shadow2)";
            }
        });
        $('input[id="fileToUpload"]').on('change',function(){
            if($(this).val()){
                $('input[id="post_button2"]').attr('disabled',false);
                document.getElementById("post_button2").style.backgroundColor="var(--sbutton)";
                document.getElementById("image_preview").style.display="flex";
            }
            else{
                $('input[id="post_button2"]').attr('disabled',true);
                document.getElementById("post_button2").style.backgroundColor="var(--shadow2)";
                document.getElementById("image_preview").style.display="none";
            }
        });
        $('input[id="fileToUpload2"]').on('change',function(){
            if($(this).val()){
                $('input[id="post_button2"]').attr('disabled',false);
                document.getElementById("post_button2").style.backgroundColor="var(--sbutton)";
                document.getElementById("image_preview").style.display="flex";
            }
            else{
                $('input[id="post_button2"]').attr('disabled',true);
                document.getElementById("post_button2").style.backgroundColor="var(--shadow2)";
                document.getElementById("image_preview").style.display="none";
            }
        });
        $('input[id="search_box"]').on('keyup',function(){
            if($(this).val()){
                document.getElementById("mentionees").style.display="none";
            }
            else{
                document.getElementById("mentionees").style.display="block";
            }
        });
        $('input[id="search_box2"]').on('keyup',function(){
            if($(this).val()){
                document.getElementById("mentioneefs").style.display="none";
            }
            else{
                document.getElementById("mentioneefs").style.display="block";
            }
        });
    });
    
    var sound = new Audio();
    sound.src = "button_click.mp3";
</script>

<div class="main_column column" style="height: 93vh;">
            
    <?php
        if(isset($_GET['name']))
            $name = $_GET['name'];
        else
            $name = "none";
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $post_query = mysqli_query($con, "SELECT * FROM posts WHERE id='$id' AND deleted='no' AND added_by='$userLoggedIn'");
            if(mysqli_num_rows($post_query)){
                while($post = mysqli_fetch_array($post_query)){
                    if($post['body']){
                        $body = $post['body'];
                        $body = str_replace('<br />', "\n", $body);
                    }
                    else
                        $body = "";
                    $location = $post['location'];
                    $tagz = $post['tags'];
                    $tags = substr($tagz, 0 , -1);
                    $tags = explode(",", $tags);
                    $tgg = "";
                    foreach($tags as $tag){
                        if($tag)
                            $tgg .= "<span style='background-color: #ccc; padding: 5; margin: 10 0;'>".$tag."</span> ";
                    }
                }
            ?>
                <form class="post_form" style='height: 50vh' method="POST" enctype="multipart/form-data">
                    <div class="row" id='originals'>
                        <div class="pull-left">
                            <a class="link" href="index.php">Cancel</a>
                        </div><br><br>
                        <div class="markere" style='max-height: 15vh; overflow-y: auto;'>
                            <b><?php echo $user_obj->getFirstAndLastName(); ?></b><br>
                            <span style='font-size: 85%;'>Location : <span id='placem'><?php echo $location; ?></span></span><br>
                            <span style='font-size: 85%; line-height: 2.5;'>Tagged Friends : <span id='placek' style='color: black;'><?php echo $tgg; ?></span></span>
                        </div>
                        <textarea rows="10" style="height: auto;" name="body" id="post_text2" placeholder="Got something to say?"><?php echo $body; ?></textarea>
                        <div class="_row">
                            <input type="hidden" value="<?php echo $name; ?>" name="name" id="s_name">
                            <input type="hidden" value="<?php echo $tagz; ?>" name="tags" id="_tags">
                            <input type="hidden" value="<?php echo $id; ?>" name="id" id="s_id"><br><br><br>
                            <center><input type="button" onclick='editFile()' id="post_button2" style='position: relative; width: 90%; margin: 0;' value="Post"></center>
                            <div class="progress" style="display: none;" id="bars">
                                <progress id="progressBar" value='0' max='100'></progress>
                            </div>
                            <h3 id="ssl"></h3>
                        </div>
                        <div id="image_preview" style='margin: 10 0'></div>
                        <input type="file" name="fileToUpload[]" id="fileToUpload" multiple capture accept="image/*">
                        <label for="fileToUpload" id="selector" style="display: block;">
                        <div class='markere'>
                            <span class='fa fa-camera-retro fa-lg' style='color: var(--spn);'></span> Photo
                        </div>
                        </label>
                        <input type="file" name="fileToUpload[]" id="fileToUpload2" multiple capture accept="video/*">
                        <label for="fileToUpload2" id="selector" style="display: block;">
                            <div class='markere'>
                                <span class='fa fa-video-camera fa-lg' style='color: #bdc3c7;'></span> Video
                            </div>
                        </label>
                        <div class='markere' onclick='localt()'><span class='fa fa-map-marker text-success fa-lg'></span> Location</div>
                        <div class='markere' data-toggle="modal" data-target="#tag_form"><span class='fa fa-tags text-primary fa-lg'></span> Tag Friends</div>
                        <div class='markere' data-toggle="modal" data-target="#post_form"><span class='fa fa-user-plus text-danger fa-lg'></span> Mention Friends</div>
                        <div class='markere'><span class='fa fa-smile-o fa-lg' style='color: orange;'></span> Feeling / Activity</div>
                        <br><p class="center-block extra"><b>Note:</b> in order to re-post you have to edit the write up to be able to make the new upload and re-select the image chosen before</p>
                    </div>
                    <div style="display: none;" id="locatione">
                        <div class="card-header fix" style="margin: 0 -10px; width: auto;">
                            <span class="center-block">Enter your location</span>
                        </div>
                        <input type="text" name="location" class="bio" id="bio_text" autocomplete="off" style="width: 100%;" value="<?php echo $location; ?>">
                        <input onclick="originate()" type='button' style='margin: 10 0 0; width: 100%; position: relative; background-color: red;' value='Done' id="bgd_btn">
                    </div>
                </form>
            <?php
            }
            else
                echo "You cannot edit a post that you did not upload or might be deleted";
        }
        else{
            // $p_name = $user_obj->getFirstAndLastName()
            ?>
            <form class="post_form" style='height: 50vh' method="POST" enctype="multipart/form-data">
                <div class="row" id='originals'>
                    <div class="pull-left">
                        <a class="link" href="index.php">Cancel</a>
                    </div><br><br>
                    <div class="markere" style='max-height: 15vh; overflow-y: auto;'>
                        <b><?php echo $user_obj->getFirstAndLastName(); ?></b><br>
                        <span style='font-size: 85%;'>Location : <span id='placem'></span></span><br>
                        <span style='font-size: 85%; line-height: 2.5;'>Tagged Friends : <span id='placek' style='color: black;'></span></span>
                    </div>
                    <!-- <button class="btn btn-primary pull-right" id="ddrop" data-toggle="modal" data-target="#post_form">Mention Friends <span class="fa fa-caret-down"></span></button> -->
                    <textarea rows="10" style="height: auto;" name="body" id="post_text2" placeholder="Got something to say?"></textarea>
                    <div class="_row">
                        <!-- <input type="file" name="fileToUpload[]" id="fileToUpload" multiple capture>
                        <label for="fileToUpload" id="selector">
                            <h1>Add a photo</h1> <i class="fa fa-plus-square-o fa-lg"></i>
                        </label> -->
                        <input type="hidden" value="<?php echo $name; ?>" name="name" id="s_name">
                        <input type="hidden" value="" name="tags" id="_tags">
                        <center><input type="button" onclick='uploadFile()' id="post_button2" style='position: relative; width: 90%; margin: 0;' value="Post"></center>
                        <div class="progress" style="display: none;" id="bars">
                            <progress id="progressBar" value='0' max='100'></progress>
                        </div>
                        <h3 id="ssl"></h3>
                    </div>
                    <div id="image_preview" style='margin: 10 0'></div>
                    <input type="file" name="fileToUpload[]" id="fileToUpload" multiple capture accept="image/*">
                    <label for="fileToUpload" id="selector" style="display: block;">
                    <div class='markere'>
                        <span class='fa fa-camera-retro fa-lg' style='color: var(--spn);'></span> Photo
                    </div>
                    </label>
                    <input type="file" name="fileToUpload[]" id="fileToUpload2" multiple capture accept="video/*">
                    <label for="fileToUpload2" id="selector" style="display: block;">
                        <div class='markere'>
                            <span class='fa fa-video-camera fa-lg' style='color: #bdc3c7;'></span> Video
                        </div>
                    </label>
                    <div class='markere' onclick='localt()'><span class='fa fa-map-marker text-success fa-lg'></span> Location</div>
                    <div class='markere' data-toggle="modal" data-target="#tag_form"><span class='fa fa-tags text-primary fa-lg'></span> Tag Friends</div>
                    <div class='markere' data-toggle="modal" data-target="#post_form"><span class='fa fa-user-plus text-danger fa-lg'></span> Mention Friends</div>
                    <div class='markere'><span class='fa fa-smile-o fa-lg' style='color: orange;'></span> Feeling / Activity</div>
                </div>
                <div style="display: none;" id="locatione">
                    <div class="card-header fix" style="margin: 0 -10px; width: auto;">
						<span class="center-block">Enter your location</span>
					</div>
                    <input type="text" name="location" class="bio" id="bio_text" autocomplete="off" style="width: 100%;">
                    <input onclick="originate()" type='button' style='margin: 10 0 0; width: 100%; position: relative; background-color: red;' value='Done' id="bgd_btn">
                </div>
            </form>
            <?php
        }
    ?>

<script>
    function localt(){
        _("originals").style.display = "none";
        _("locatione").style.display = "block";
    }
    function originate(){
        _("originals").style.display = "block";
        _("locatione").style.display = "none";
        var loc = _("bio_text").value;
        _("placem").innerHTML = loc;
    }
    function showOthers(person){
        var body = _("post_text2").value;
        _("post_text2").value = body + "@"+person+" ";
        $('input[id="post_button2"]').attr('disabled',false);
        document.getElementById("post_button2").style.backgroundColor="var(--sbutton)";
    }
    function tagFriends(person){
        var body = _("_tags").value;
        var text = _("placek").innerHTML;
        var str = body.indexOf(person);
        // console.log(str);
        if(str != -1){
            body = body.replace(person+",", "");
            _("_tags").value = body;
            text = text.replace('<span style="background-color: #ccc; padding: 5; margin: 10 0;">'+person+'</span> ', "");
            _("placek").innerHTML = text;
        }
        else{
            _("_tags").value = body + person+",";            
            _("placek").innerHTML = text + "<span style='background-color: #ccc; padding: 5; margin: 10 0;'>"+person+"</span> ";
            _("placek").parentNode.style.lineHeight = "2.5";
        }
    }
    var arr = [];
    function uploadFile(){
        var total_file = arr.length;
        var body = _("post_text2").value;
        var loc = _("bio_text").value;
        var tags = _("_tags").value;
        _("image_preview").style.display="none";
        _("bars").style.display="block";
        var formdata = new FormData();
        var ajax = new XMLHttpRequest();
        ajax.upload.addEventListener("progress", progressHandler, false);
        ajax.addEventListener("load", completeHandler, false);
        ajax.addEventListener("error", errorHandler, false);
        ajax.addEventListener("abort", abortHandler, false);
        var name = _("s_name").value;
        console.log(total_file);
        if(total_file >= 1){
            for(var filee of arr){
                formdata.append("file[]", filee);
            }
            formdata.append("fileb", body);
            formdata.append("filel", loc);
            formdata.append("filet", tags);
            formdata.append("filen", name);
            ajax.open("POST", "uploadForm.php");
            ajax.send(formdata);
        }
        else{
            formdata.append("fileb", body);
            formdata.append("filel", loc);
            formdata.append("filet", tags);
            formdata.append("filen", name);
            ajax.open("POST", "uploadText.php");
            ajax.send(formdata);
        }
    }
    function editFile(){
        var tfile1= _("fileToUpload").files.length;
        var tfile2= _("fileToUpload2").files.length;
        var total_file = tfile1 + tfile2;
        var body = _("post_text2").value;
        var loc = _("bio_text").value;
        var tags = _("_tags").value;
        _("image_preview").style.display="none";
        _("bars").style.display="block";
        var formdata = new FormData();
        var ajax = new XMLHttpRequest();
        ajax.upload.addEventListener("progress", progressHandler, false);
        ajax.addEventListener("load", completeHandler, false);
        ajax.addEventListener("error", errorHandler, false);
        ajax.addEventListener("abort", abortHandler, false);
        var name = _("s_name").value;
        var id = _("s_id").value;
        if(total_file >= 1){                
            var files= _("fileToUpload").files;
            var files2= _("fileToUpload2").files;
            for(var file of files){            
                formdata.append("file[]", file);
            }
            for(var filee of files2){
                formdata.append("file[]", filee);
            }
            formdata.append("fileb", body);
            formdata.append("filel", loc);
            formdata.append("filet", tags);
            formdata.append("filen", name);
            formdata.append("fid", id);
            ajax.open("POST", "editForm.php");
            ajax.send(formdata);
        }
        else{
            formdata.append("fileb", body);
            formdata.append("filel", loc);
            formdata.append("filen", name);
            formdata.append("filet", tags);
            formdata.append("fid", id);
            ajax.open("POST", "editText.php");
            ajax.send(formdata);
        }
    }
    function progressHandler(event){
        var percent = (event.loaded / event.total) * 100;
        _("progressBar").value = Math.round(percent);
        _("ssl").innerHTML = Math.round(percent)+"% uploaded... please wait";
    }
    function completeHandler(event){
        _("ssl").innerHTML = event.target.responseText;
        _("progressBar").value = 100;
        window.location.href = "index.php";
    }
    function errorHandler(event){
        _("ssl").innerHTML = "upload failed";
    }
    function abortHandler(event){
        _("ssl").innerHTML = "Upload Aborted";
    }
    function remove(filern, hh){
        // console.log(filern);
        $(".rmv"+filern).css("display", "none");
        $(".rmve"+filern).css("display", "none");
        for(var filee of arr){
            if(filee.lastModified == hh){
                var ui = arr.indexOf(filee);
                arr.splice(ui, 1);
                // console.log(ui);
            }
        }
        // console.log(img);
    }
    $("#fileToUpload").change(function(event){
        $('#image_preview').css("height", "120px");
        $('#image_preview').css("alignItems", "center");
        var total_file=document.getElementById("fileToUpload").files.length;
        for(var i=0;i<total_file;i++)
        {
            let file = event.target.files[i];            
            if(file.type.match("image")){
                arr.push(file);
                var f_name = arr.indexOf(file);
                var e = file.lastModified;
                $('#image_preview').append("<img class='rmv"+f_name+"' style='width:80px; height:80px; margin: 5;' src='"+URL.createObjectURL(event.target.files[i])+"'><span class='rmve"+f_name+" rmv_btn' onclick='remove(\""+f_name+"\", \""+e+"\")'>&times;</span>");
            }
        }
    });
    $("#fileToUpload2").change(function(event){
        $('#image_preview').css("height", "120px");
        $('#image_preview').css("alignItems", "center");
        var total_file=document.getElementById("fileToUpload2").files.length;
        for(var i=0;i<total_file;i++)
        {
            let file = event.target.files[i];
            arr.push(file);
            var f_name = arr.indexOf(file);
            var e = file.lastModified;
            $('#image_preview').append("<video class='rmv"+f_name+"' style='width:80px; height:80px; margin: 5;' autoplay muted src='"+URL.createObjectURL(event.target.files[i])+"'></video><span class='rmve"+f_name+" rmv_btn' onclick='remove(\""+f_name+"\", \""+e+"\")'>&times;</span>");
        }
    });
    // var loader = function(e){
    //     let file = e.target.files;
    //     let output = document.getElementById("selector");
                
    //     if(file[0].type.match("image")){
    //         let reader = new FileReader();

    //         reader.addEventListener("load", function(e){
    //             let data = e.target.result;
    //             let image = document.createElement("img");
    //             image.src = data;

    //             output.innerHTML = "";
    //             output.insertBefore(image, null)
    //             output.classList.add("image");
    //         });

    //         reader.readAsDataURL(file[0]);
    //     }
    //     else{
    //         let show = "<span>Selected File : </span>"
    //         show = show + file[0].name;

    //         output.innerHTML = show;
    //         output.classList.add("active");

    //         if(output.classList.contains("image")){
    //             output.classList.remove("image");
    //         }
    //     }
    // };

    // let fileInput = document.getElementById("fileToUpload");
    // fileInput.addEventListener("change", loader);
</script>

<div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true" style="overflow: hidden; padding: 0 !important;">
    <div class="modal-dialog" class="modal_s" style="top: 30vh; width: 90%; margin: 30px auto;">
        <div class="modal-content" style="background-color: var(--bgc);">

        <div class="input_group" id="chat_row" style="color: var(--bgc); padding: 10px;">
            <input type="search" autocomplete='off' autofocus onkeyup='getFriends(this.value, "<?php echo $userLoggedIn; ?>")' id="search_box" placeholder='Search...' class='form-control inp'>
        </div>
        <div class="mentionees" style="max-height: 40vh; overflow-y: auto; margin-bottom: 20px;"></div>
        <div id="mentionees" style="max-height: 40vh; overflow-y: auto; margin-bottom: 20px; display: block;">
            <li onclick="showOthers('<?php echo $userLoggedIn; ?>')" style="list-style: none;">
                <a style='padding: 0; border-bottom: 1px solid #D3D3D3'>
                    <div class='resultDisplay' style='height: 60px;'>
                        <div class='liveSearchProfilePic'>
                            <img class='fa fa-flag fa-lg icons pull-left' src='<?php echo $user_obj->getProfilePic(); ?>'>
                        </div>
                        <div class='liveSearchText'>
                            <?php echo $user_obj->getFirstAndLastName(); ?>
                            <p><?php echo $userLoggedIn; ?></p>
                        </div>
                    </div>
                </a>
            </li>
            <?php
                $friends = $user_obj->getFriendArray();
                $friends = substr($friends, 1, -1);
                $friends = explode(',', $friends);
                sort($friends, SORT_STRING);
                foreach($friends as $friend){
                    $friend_obj = new User($con ,$friend);
                    $pic = $friend_obj->getProfilePic();
                    $name = $friend_obj->getFirstAndLastName();
                    ?>
                    <li onclick="showOthers('<?php echo $friend; ?>')" style="list-style: none;">
                        <a style='padding: 0; border-bottom: 1px solid #D3D3D3'>
                            <div class='resultDisplay' style='height: 60px;'>
                                <div class='liveSearchProfilePic'>
                                    <img class='fa fa-flag fa-lg icons pull-left' src='<?php echo $pic; ?>'>
                                </div>
                                <div class='liveSearchText'>
                                    <?php echo $name; ?>
                                    <p><?php echo $friend; ?></p>
                                </div>
                            </div>
                        </a>
                    </li>
                    <?php
                }
            ?>
        </div>
        <button type="button" style="width:100%;" class="btn btn-danger" data-dismiss="modal">Cancel</button>
    </div>
</div>

</div>

<div class="modal fade" id="tag_form" tabindex="-1" role="dialog" aria-labelledby="tagModalLabel" aria-hidden="true" style="overflow: hidden; padding: 0 !important;">
    <div class="modal-dialog" class="modal_s" style="top: 30vh; width: 90%; margin: 30px auto;">
        <div class="modal-content" style="background-color: var(--bgc);">

        <div class="input_group" style="color: var(--bgc); padding: 10px;">
            <input type="search" autocomplete='off' autofocus onkeyup='getFriendss(this.value, "<?php echo $userLoggedIn; ?>")' id="search_box2" placeholder='Search...' class='form-control inp'>
        </div>
        <div class="mentiones" style="max-height: 40vh; overflow-y: auto; margin-bottom: 20px;"></div>
        <div id="mentioneefs" style="max-height: 40vh; overflow-y: auto; margin-bottom: 20px;">
            <?php
                $friends = $user_obj->getFriendArray();
                $friends = substr($friends, 1, -1);
                $friends = explode(',', $friends);
                sort($friends, SORT_STRING);
                foreach($friends as $friend){
                    $friend_obj = new User($con ,$friend);
                    $pic = $friend_obj->getProfilePic();
                    $name = $friend_obj->getFirstAndLastName();
                    ?>
                    <li onclick="tagFriends('<?php echo $friend; ?>')" style="list-style: none;">
                        <a style='padding: 0; border-bottom: 1px solid #D3D3D3'>
                            <div class='resultDisplay' style='height: 60px;'>
                                <div class='liveSearchProfilePic'>
                                    <img class='fa fa-flag fa-lg icons pull-left' src='<?php echo $pic; ?>'>
                                </div>
                                <div class='liveSearchText'>
                                    <?php echo $name; ?>
                                    <p><?php echo $friend; ?></p>
                                </div>
                            </div>
                        </a>
                    </li>
                    <?php
                }
            ?>
        </div>
        <button type="button" style="width:100%;" class="btn btn-danger" data-dismiss="modal">Cancel</button>
    </div>
</div>