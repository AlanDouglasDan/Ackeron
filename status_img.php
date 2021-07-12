<?php
    require_once "includes/header.php";

?>

<div class="main_column column" id="status_column">
    <form action="index.php" method="post" enctype="multipart/form-data" style='height: 100%'>
        <input type="file" name="fileToUpload" id="fileToUpload">
        <label for="fileToUpload" id="img_selector">
            <h1>Choose a photo</h1> <i class="fa fa-plus-square-o fa-lg"></i>
        </label>
    
        <div id="status_form" style="display:none;">
            <textarea rows='5' name='status_body' id='status_textarea' placeholder='Add a Caption...'></textarea>
            <input type='submit' name='post_status_img' id='message_submit'>
            <label onclick='sound.play()' id='new_btn3' for='message_submit'><span class='fa fa-paper-plane'></span>
        </div>
    </form>
</div>

<script>
    var loader = function(e){
        let file = e.target.files;
        let output = document.getElementById("img_selector");
        
        if(file[0].type.match("image")){
            let reader = new FileReader();

            reader.addEventListener("load", function(e){
                let data = e.target.result;
                let image = document.createElement("img");
                image.src = data;

                output.innerHTML = "";
                output.insertBefore(image, null)
                output.classList.add("image");
            });

            reader.readAsDataURL(file[0]);
        }
        else if(file[0].type.match("video")){
            let reader = new FileReader();

            reader.addEventListener("load", function(e){
                let data = e.target.result;
                let image = document.createElement("video");
                image.controls = "controls";
                image.src = data;
                image.autoplay = "autoplay";

                output.innerHTML = "";
                output.insertBefore(image, null)
                output.classList.add("image");
            });

            reader.readAsDataURL(file[0]);
        }
        else{
            let show = "<span>Selected File : </span>"
            show = show + file[0].name;

            output.innerHTML = show;
            output.classList.add("active");

            if(output.classList.contains("image")){
                output.classList.remove("image");
            }
        }

        let form = document.getElementById("status_form");
        form.style.display = "block";

        output.style.paddingTop = "0px";

        let body = document.getElementById("status_column");
        body.style.backgroundColor = "black";
    };

    let fileInput = document.getElementById("fileToUpload");
    fileInput.addEventListener("change", loader);

    var sound = new Audio();
    sound.src = "button_click.mp3";
</script>