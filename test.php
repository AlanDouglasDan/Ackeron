<?php
    require_once 'includes/header.php';
?>

<!-- <p class='popover'><button id="popover-button" class="btn btn-primary arrow" data-toggle='popover' data-content='This is a popover example'>Popopver Button</button></p>

<p><a data-placement='bottom' data-toggle='popover' data-content='This is the content of my popover which can be longer than a tooltip' id="popover-link">This is a popover</a>STUGG FTUFF SUTFF</p>
<p><a data-placement='right' data-toggle='popover' data-content='This is the content of my popover which can be longer than a tooltip' id="popover-link">This is a popover</a>STUGG FTUFF SUTFF</p>
<p><a data-placement='left' data-toggle='popover' data-content='This is the content of my popover which can be longer than a tooltip' id="popover-link">This is a popover</a>STUGG FTUFF SUTFF</p>
<p><a data-placement='top' data-toggle='popover' data-content='This is the content of my popover which can be longer than a tooltip' id="popover-link">This is a popover</a>STUGG FTUFF SUTFF</p> -->

<!-- <div class="dropup">
    <button class="btn btn-primary dropdown-toggle" data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Dropdown Button</button>
    <div class="dropdown-menu">
        <li class='dropdown-item'>1</li>
        <li class='dropdown-item'>2</li>
        <li class='dropdown-item'>3</li>
        <div class="dropdown-divider"></div>
        <li class='dropdown-item'>4</li>
    </div>
</div> -->

<!-- <div id="carousel-example-generic" class="carousel slide" data-ride='carousel'>
    <ol class="carousel-indicators">
        <li class="active" data-target='#carousel-example-generic' data-slide-to='0'></li>
        <li data-target='#carousel-example-generic' data-slide-to='1'></li>
        <li data-target='#carousel-example-generic' data-slide-to='2'></li>
    </ol>
    <div class="carousel-inner" role='list-box'>
        <div class="carousel-inner active item">
            <img src="a.jpg" alt="">
        </div>
        <div class="carousel-inner item">
            <img src="a.jpg" alt="">
        </div>
        <div class="carousel-inner item">
            <video style='height: 750;' controls autoplay loop src="ab.mp4" alt="">
        </div>
    </div>
    <a href="#carousel-example-generic" role='button' data-slide='prev' class="left carousel-control">
        <span class="icon-prev" aria-hidden='true'></span>
        <span class="sr-only">Previous</span>
    </a>
    <a href="#carousel-example-generic" role='button' data-slide='next' class="right carousel-control">
        <span class="icon-next" aria-hidden='true'></span>
        <span class="sr-only">Next</span>
    </a>
</div> -->
<!-- <style>
    video{
        width: 100%;
        height: 100%;
    }
</style>

<div class="container-fluid myphoto-section bg-myphoto-light">
    <div class="container">
        <div class="row">
            <h3>Gallery</h3>
            <div id="gallery-carousel" class="carousel slide" data-ride='carousel' data-interval=''>
                <ol class="carousel-indicators">
                    <li class="active" data-target='#gallery-carousel' data-slide-to='0'></li>
                    <li data-target='#gallery-carousel' data-slide-to='1'></li>
                    <li data-target='#gallery-carousel' data-slide-to='2'></li>
                </ol>
                <div class="carousel-inner" role='listbox'>
                    <div class="carousel-inner item active img_container2" style='height: 750px'>
                        <img src="a.jpg" alt="">
                        <div class="carousel-caption">
                            Brazil
                        </div>
                    </div>
                    <div class="carousel-inner item img_container2" style='height: 750px'>
                        <video src="ab.MOV" autoplay controls></video>
                        <div class="carousel-caption">
                            Datsum 260za aaaa aaaaaaaaa aaaaaaaa a aaaaaaaaaaa a aaaaaaaaaaaaa a aaaaaaaaaa a aaaaaaaaaaaa a aaaaaaaaaaaaa a aa a a aaaaaaaaaaaaaaaa aaaaaaaaaaaaa aa a a a aa
                        </div>
                    </div>
                    <div class="carousel-inner item img_container2" style='height: 750px'>
                        <div id='status' style='background-color: orange;'>
							<div class='status_body_text'>hello datkkasdjfadsjfasdfj adsijf iasd fjisadohfa dsdfhjasdifnaksd fnoasdifh iasdfja sidhf uiasdfhuiads fuisdfhusda fhasduifhs aduf</div>							
						</div>
                    </div>
                </div>
                <a href="#gallery-carousel" role='button' data-slide='prev' class="left carousel-control">
                    <span class="icon-prev" aria-hidden='true'></span>
                </a>
                <a href="#gallery-carousel" role='button' data-slide='next' class="right carousel-control">
                    <span class="icon-next" aria-hidden='true'></span>
                </a>
                
            </div>
        </div>
    </div>
</div> -->

<!-- <center>
<div class="input-grousp">
<textarea name="" id="input" class="form-control" onkeypress="auto_grow(this);" cols="30" rows="1" style="width: 90%; margin-top: 2em; border-radius: 20px;"></textarea>
</div>
</center>-->

<script>
    // function _(el){
    //     return document.getElementById(el);
    // }
    // function uploadFile(){
    //     var total_file= _("file1").files.length;
    //     if(total_file >= 1){
    //         var files= _("file1").files;
    //         var formdata = new FormData();
    //         var ajax = new XMLHttpRequest();
    //         ajax.upload.addEventListener("progress", progressHandler, false);
    //         ajax.addEventListener("load", completeHandler, false);
    //         ajax.addEventListener("error", errorHandler, false);
    //         ajax.addEventListener("abort", abortHandler, false);
    //         for(const file of files){
    //             formdata.append("file[]", file);
    //         }
    //         ajax.open("POST", "uploadForm.php");
    //         ajax.send(formdata);
    //         // console.log(formdata);
    //     }             
    // }
    // function progressHandler(event){
    //     _("loaded_n_total").innerHTML = "uploaded "+event.loaded+" bytes of "+event.total;
    //     var percent = (event.loaded / event.total) * 100;
    //     _("progressBar").value = Math.round(percent);
    //     _("ssl").innerHTML = Math.round(percent)+"% uploaded... please wait";
    // }
    // function completeHandler(event){
    //     _("ssl").innerHTML = event.target.responseText;
    //     _("progressBar").value = 100;
    //     window.location.href = "index.php";
    // }
    // function errorHandler(event){
    //     _("ssl").innerHTML = "upload failed";
    // }
    // function abortHandler(event){
    //     _("ssl").innerHTML = "Upload Aborted";
    // }
    // $(document).ready(function(){
    //     $('#uploadImage').submit(function(event){
    //         if($('#uploadFile').val()){                
    //             event.preventDefault;
    //             $('#loader-icon').show();
    //             $('#targetLayer').hide();
    //             $(this).ajaxSubmit({
    //                 target: '#targetLayer',
    //                 beforeSubmit: function(){
    //                     $('.progress-bar').width('0%');
    //                 },
    //                 uploadProgress: function(event, position, total, percentageComplete){
    //                     $('.progress-bar').animate({
    //                         width: percentageComplete + '%'
    //                     }, {
    //                         duration: 1000
    //                     });
    //                 },
    //                 success: function(){
    //                     $('#loader-icon').hide();
    //                     $('#targetLayer').show();
    //                 },
    //                 resetForm: true
    //             });
    //         }
    //         return false;
    //     });
    // });
</script> 

<?php
    // if(strpos("Can't wait", "'t")){
    //     echo "yes";
    // }
    // else
    //     echo "no";
?>

<!-- <div class="shofwcase">
    <div>
        <video src="assets/images/posts/5fb3c1f6d966169e07fe1-ea75-47ca-ba39-c772de4c4255.mp4" alt=""></video>
    </div>
    <div>
        <video src="assets/images/posts/5fb3c1f6d9ac684C3C25C-D818-47E6-8403-22CCFC35E884.mp4" alt=""></video>
    </div>
    <img src="a.jpg" alt="">
    <div class="text-wrapper">
        +3
    </div>
</div> -->

<!-- <div class="carousel-container-2">
    <div class="carousel-slide-2">
        <video data-toggle="modal" data-target="#med_modal83" onclick="image(83, 0)" src="assets/images/posts/5fb3c1f6d966169e07fe1-ea75-47ca-ba39-c772de4c4255.mp4"></video>
        <video data-toggle="modal" data-target="#med_modal83" onclick="image(83, 1)" src="assets/images/posts/5fb3c1f6d9ac684C3C25C-D818-47E6-8403-22CCFC35E884.mp4"></video>
    </div>
</div> -->

<!-- <div class="carousel-container-2">
    <div class="carousel-slide-6">
        <img class="img1" data-toggle="modal" data-target="#med_modal55" onclick="image(55, 0)" src="assets/images/posts/5f7f452225e1fIMG_0006.jpg">
        <video class="img2" data-toggle="modal" data-target="#med_modal55" onclick="image(55, 1)" src="assets/images/posts/5f7f45222623cQUKI0712.mp4"></video>
        <img class="img4" src="a.jpg" alt="">
        <img class="img6" src="a.jpg" alt="">
        <video class="img3" data-toggle="modal" data-target="#med_modal55" onclick="image(55, 2)" src="assets/images/posts/5f7f4522265e7RPReplay_Final1595894340.mp4"></video>
        <video class="img5" data-toggle="modal" data-target="#med_modal83" onclick="image(83, 1)" src="assets/images/posts/5fb3c1f6d9ac684C3C25C-D818-47E6-8403-22CCFC35E884.mp4"></video>
    </div>
</div> -->

<style>
    /* .showcase{
        display: grid;
        grid-gap: 5px;
        grid-template-columns: 1fr 1fr 1fr;
        height: 60vh;
    }
    .img1{
        grid-area: img1;
    }
    .img3{
        grid-area: img3;
    }
    .img2{
        grid-area: img2;
    }
    img, video{
        /* height: 100%; */
        width: 100%;        
    }
    .text-wrapper{
        width: 100%;
        
    } */
</style>

<script>
    // navigator.geolocation.getCurrentPosition(granted);

    // function granted(position){
    //     var lat = position.coords.latitude;
    //     var lon = position.coords.longitude;

    //     console.log(lat);
    //     console.log(lon);
    // }

    // function denied(error){
    //     console.log(error);
    // }
    // var ti = [1, 2, 3, 4, 5];
    // var num = ti.indexOf(2);
    // console.log(ti);    
    // ti.splice(num, 1);
    // console.log(ti);
</script>

<!-- <input type="button" class="deep_blue" data-toggle='modal' data-target='#post_form' value="Post Something">

<div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true" style="overflow: hidden;">
  <div class="modal-dialog" style="position: static; margin: 0;">
    <div class="modal-content" style="height: 100%;">
        <img src="a.jpg" alt="">
        <button type="button" style="width:100%;" class="btn btn-default" data-dismiss="modal">Close</button>
  </div>
</div> -->
<!-- <div class="bootbox modal fade bootbox-confirm" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
    <div class="modal-backdrop fade" style="height: 1076px;"></div>
    <div class="modal-dialog">
        <div class="modal-content">
        <ul class='dropdown-menu' style="height: 310px; overflow: auto; top: 40vh;">
            <div class="input_group" id="chat_row" style="color: var(--bgc); padding: 10px;">
                <input type="search" autocomplete='off' autofocus onkeyup='getFriends(this.value, "<?php echo $userLoggedIn; ?>")' id="search_box" placeholder='Search...' class='form-control inp'>
            </div>
            <div class="mentionees"></div>
            <div id="mentionees">
            <li onclick="showOthers('<?php echo $userLoggedIn; ?>')">
                <a style='padding: 0; border-bottom: 1px solid #D3D3D3'>
                    <div class='resultDisplay'>
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
            </div>
        </ul>
        <br><br>
        </div>
    </div>
</div> -->
<!-- <label class='labell' for='c_box11'><div class='user_tb'>
    <img src='a.jpg' style='border-radius: 50%; margin-right: 5px;'>
    Alan Douglas
    </div>
</label>
<input type='checkbox' name='to_add[]' value='alan_douglas' class='tb_ch_box' id='c_box11'> -->
<?php   
    $tmps = "a1.jpg";
    $save_to = "a.jpg";
    // list($w, $h) = getimagesize($save_to);
    // $src = imagecreatefromjpeg($save_to);
    // $max = 100;
    // $tw  = $w;
    // $th  = $h;

    // if ($w > $h && $max < $w)
    // {
    // $th = $max / $w * $h;
    // $tw = $max;
    // }
    // elseif ($h > $w && $max < $h)
    // {
    // $tw = $max / $h * $w;
    // $th = $max;
    // }
    // elseif ($max < $w)
    // {
    // $tw = $th = $max;
    // }

    // $tmp = imagecreatetruecolor($tw, $th);
    // imagecopyresampled($tmp, $src, 0, 0, 0, 0, $tw, $th, $w, $h);
    // imageconvolution($tmp, array(array(-1, -1, -1),
    // array(-1, 16, -1), array(-1, -1, -1)), 8, 0);
    // imagejpeg($tmp, $tmps);
    // imagedestroy($tmp);
    // imagedestroy($src);
?>
<style>
    /* .bg_img{
        background-position: center center;
        background-size: cover;
        filter: blur(40px);
        height: 80vh;
        display: flex;
        align-items: center;
        padding: 5em;
    }
    #bh{
        background-image: url("b.jpg");
    }
    span{
        color: white;
        font-size: 30;
        font-weight: bold;
        text-align: center;
        line-height: 1.2;
    } */
</style>
<script>
    // var image = new Image();
    // var bo = document.querySelector('.bg_img');
    // image.src = "<?php echo $save_to; ?>";
    // image.addEventListener("load", function(){
    //     bo.style.backgroundImage = "<?php echo $save_to; ?>";
    // });
    function fh(){
        console.log("hello world");
    }
    // var adm = document.getElementById("bh");
    // adm.onload = function(){
    //     console.log("hello world");
    // }
</script>
<!-- <div class='bg_img' id='bh' onclick="fh()">
    <span>This is why Sex is a global phenomenon that many may enjoy This is why Sex is a global phenomenon that many may enjoy This is why Sex is a global phenomenon that many may enjoy This is why Sex is a global phenomenon that many may enjoy This is why Sex is a global phenomenon that many may enjoy This is why Sex is a global phenomenon that many may enjoy This is why Sex is a global phenomenon that many may enjoy</span>
</div> -->
<!-- <h1>Ajax Progress Bar</h1>
<form id="upload_form" enctype="multipart/form-data" method="post">
    <label>Choose File:</label>
    <input type="file" id="file1" style="display: block;" multiple><br>
    <input type="button" value="Upload" onclick='uploadFile()'><br>
    <div class="progress">
        <progress id="progressBar" value='0' max='100'></progress>
    </div>
    <h3 id="ssl"></h3>
    <p id="loaded_n_total"></p>
</form>
<div id="uploadStatus"></div>  -->
<!-- <div class="container">
    <br>
    <h3 align="center">Ajax File upload Progressbar</h3>
    <br>
    <div class="panel panel-default">
        <div class="panel-heading"><b>Ajax file upload progressbar using ajax</b></div>
        <div class="panel-body">
            <form action="uploadForm.php" method="POST" id="uploadImage" enctype="multipart/form-data">
                <div class="form-group">
                    <label>File Upload</label>
                    <input type="file" name="uploadFile" id="uploadFile" accept=".jpg, .png, .mp4," style="display: block;" multiple>
                </div>
                <div class="form-group">
                    <input type="submit" value="Upload" id="uploadSubmit" class="btn btn-info">    
                </div>
                <div class="progress">
                    <div class="progress-bar" role="progress-bar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div id="targetLayer" style="display: none;"></div>
                <div id="loader-icon" style="display: none;"><img id='loading' src='assets/images/icons/loading.gif'></div>
            </form>
        </div>
    </div>
</div> -->

<style>
    /* .progress-bar{
        height: 35px;
        width: 250px;
        border: 2px solid darkblue;
    } */
</style>
<!-- <button class='btn btn-primary' id='stope' onclick='start()'>Start</button>
<button class='btn btn-primary' id='stop'>Stop</button>
<div class="audio" id="audio"></div> -->
<!-- <script>
    // var device = navigator.mediaDevices.getUserMedia({audio: true});
    // var items = [];
    // device.then( stream => {
    //     var recorder = new MediaRecorder(stream);
    //     recorder.ondataavailable = e=>{
    //         items.push(e.data);
    //         if (recorder.state == 'inactive'){
    //             var blob = new Blob(items, {type: 'audio/webm'});
    //             var audio = document.getElementById('audio');
    //             var mainaudio = document.createElement('audio');
    //             mainaudio.setAttribute('controls', 'controls');
    //             mainaudio.setAttribute('loop', 'loop');
    //             audio.appendChild(mainaudio);
    //             mainaudio.innerHTML = '<source src="'+URL.createObjectURL(blob)+'"type="video/webm"/>';
    //         }
    //     }
    //     recorder.start(100);
    //     setTimeout(()=>{
    //         recorder.stop();
    //     }, 10000);
    // })
    
//     function start(){
//         var allow = 1;
//         var device = navigator.mediaDevices.getUserMedia({audio: true});
//         var items = [];
//         var stopBtn = document.getElementById('stop');
//         device.then( stream => {
//             var recorder = new MediaRecorder(stream);
//             recorder.state = 'active';
//             recorder.ondataavailable = e=>{
//                 items.push(e.data);
//                 if (recorder.state == 'inactive'){
//                     var blob = new Blob(items, {type: 'audio/webm'});
//                     var audio = document.getElementById('audio');
//                     var mainaudio = document.createElement('audio');
//                     mainaudio.setAttribute('controls', 'controls');
//                     mainaudio.setAttribute('loop', 'loop');
//                     audio.appendChild(mainaudio);
//                     mainaudio.innerHTML = '<source src="'+URL.createObjectURL(blob)+'"type="video/webm"/>';
//                 }
//             }
//             recorder.start(100);
//             stopBtn.addEventListener("click", ()=>{
//                 recorder.stop();
//             })
//             // setTimeout(() => {
//             //     recorder.stop();
//             // }, 20000);
//         });
//     }
</script> -->

<!-- <div class="holder">
    <div data-role="controls">
        <button>Record</button>
    </div>
    <div data-role="recordings"></div>
</div> -->

<!-- <div class="card">
    <div class="card-block">
        <div class="row">
            <div class="col-sm-3" id="content-spy">
                <ul class="nav nav-pills nav-stacked">
                    <li class="active" role='presentation'>
                        <a href="#lorem">The Lorem</a>
                    </li>
                    <li role='presentation'>
                        <a href="#eros">The eros</a>
                    </li>
                    <li role='presentation'>
                        <a href="#vestibulum">The Vestibulum</a>
                    </li>
                </ul>
            </div>
            <div class="col-sm-9" id="content" data-spy='scroll' data-target='#content-spy'>
                <div id="lorem">
                    <h2>The Lorem</h2>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempora esse consequatur mollitia iusto, modi perferendis similique asperiores! Alias, rerum earum placeat accusantium consectetur repellat voluptatibus, cumque culpa quia eligendi nihil dolor quisquam. Aliquid tempora architecto debitis numquam vero expedita? Quos doloremque voluptatibus tempore est earum labore perferendis vel. Accusantium necessitatibus blanditiis in expedita ut aliquam rerum molestiae nihil neque dicta quia corrupti quibusdam quo quisquam, quae repellendus tenetur ex! Nulla fugiat id sit nisi ducimus. Sapiente velit in minus excepturi tenetur praesentium illum ipsam perspiciatis omnis, non cumque deleniti commodi voluptates ut beatae soluta possimus facere aspernatur sunt quasi provident tempore reprehenderit vero iusto. Nisi aliquam ipsam quibusdam repudiandae quam quaerat omnis, deserunt assumenda dolorem eaque veritatis ex recusandae illum itaque nesciunt sed ipsa hic doloremque aspernatur facilis consequuntur quae praesentium dolores! Architecto sapiente minima inventore debitis illum adipisci accusantium, eum provident magni iste sequi vel dolorem dolorum. Doloremque quo labore laboriosam at ducimus nesciunt facilis fugiat assumenda in cum delectus consequuntur laborum nulla suscipit voluptates odio, sapiente iure natus pariatur quasi repellendus ipsam eligendi obcaecati. Magnam, incidunt ea laboriosam quas, exercitationem necessitatibus ipsa ipsum architecto cum nisi eum, repudiandae illum odit animi minima eveniet tempore magni. Voluptatibus temporibus amet praesentium iure commodi quam ratione laboriosam vitae exercitationem. Error, ducimus beatae dolorum eveniet natus alias nesciunt, dolor minima sit eaque enim sed molestiae veniam distinctio unde sunt esse maxime at id suscipit deleniti reiciendis ratione facere iure. Suscipit doloribus dolorem quam exercitationem veritatis ducimus illo similique debitis distinctio, itaque dicta error autem facere, praesentium ea molestias, quibusdam inventore et laboriosam quisquam facilis quaerat excepturi eos sequi. Ab nobis nisi dolore saepe consequuntur non quaerat debitis error hic! Delectus tempore accusamus autem dignissimos sapiente nisi debitis provident voluptate incidunt vitae totam, quo expedita dolorum molestiae dolores? Est, minus doloribus delectus ut corporis totam placeat qui fuga ipsam, ad nisi inventore sequi cupiditate officia reiciendis. Nulla libero modi, impedit architecto deserunt dolorum similique, dolore reprehenderit earum sequi vel error nemo reiciendis itaque corrupti voluptatem, cum odio neque assumenda autem aliquam quasi amet iusto inventore! Eligendi doloremque iusto sed quidem in laudantium possimus nisi dolores, nostrum provident, ducimus rerum facere eum. Quaerat expedita eum a tempora assumenda aperiam debitis tempore earum laborum. Beatae, cupiditate veniam ratione pariatur vero soluta officiis reiciendis reprehenderit consequatur, aliquid odit dolorum obcaecati optio nam unde labore quasi assumenda eaque iusto quidem in. Perspiciatis asperiores illo magnam vitae eos accusantium illum ratione culpa vel esse fugiat cupiditate, ea impedit cum, omnis ullam, sint tempora voluptate inventore excepturi. Asperiores repellendus quibusdam natus magnam! Animi aut hic nesciunt libero quibusdam corporis! Dolor ullam quaerat mollitia modi expedita quae aliquid. Eaque itaque nisi ad facere corporis minima sapiente consectetur maiores! Quam, magni esse laboriosam doloremque natus a nesciunt quisquam illum cum similique sunt optio, alias vel assumenda deserunt facere quibusdam quis? Cumque quaerat quos, doloremque perferendis enim repellendus totam? Provident laboriosam iusto nisi! Consectetur a voluptas autem eaque provident odio facilis amet! Sint magnam accusamus laudantium optio expedita dolores praesentium dolor neque.</p>
                </div>
                <div id="eros">
                    <h2>The Eros</h2>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempora esse consequatur mollitia iusto, modi perferendis similique asperiores! Alias, rerum earum placeat accusantium consectetur repellat voluptatibus, cumque culpa quia eligendi nihil dolor quisquam. Aliquid tempora architecto debitis numquam vero expedita? Quos doloremque voluptatibus tempore est earum labore perferendis vel. Accusantium necessitatibus blanditiis in expedita ut aliquam rerum molestiae nihil neque dicta quia corrupti quibusdam quo quisquam, quae repellendus tenetur ex! Nulla fugiat id sit nisi ducimus. Sapiente velit in minus excepturi tenetur praesentium illum ipsam perspiciatis omnis, non cumque deleniti commodi voluptates ut beatae soluta possimus facere aspernatur sunt quasi provident tempore reprehenderit vero iusto. Nisi aliquam ipsam quibusdam repudiandae quam quaerat omnis, deserunt assumenda dolorem eaque veritatis ex recusandae illum itaque nesciunt sed ipsa hic doloremque aspernatur facilis consequuntur quae praesentium dolores! Architecto sapiente minima inventore debitis illum adipisci accusantium, eum provident magni iste sequi vel dolorem dolorum. Doloremque quo labore laboriosam at ducimus nesciunt facilis fugiat assumenda in cum delectus consequuntur laborum nulla suscipit voluptates odio, sapiente iure natus pariatur quasi repellendus ipsam eligendi obcaecati. Magnam, incidunt ea laboriosam quas, exercitationem necessitatibus ipsa ipsum architecto cum nisi eum, repudiandae illum odit animi minima eveniet tempore magni. Voluptatibus temporibus amet praesentium iure commodi quam ratione laboriosam vitae exercitationem. Error, ducimus beatae dolorum eveniet natus alias nesciunt, dolor minima sit eaque enim sed molestiae veniam distinctio unde sunt esse maxime at id suscipit deleniti reiciendis ratione facere iure. Suscipit doloribus dolorem quam exercitationem veritatis ducimus illo similique debitis distinctio, itaque dicta error autem facere, praesentium ea molestias, quibusdam inventore et laboriosam quisquam facilis quaerat excepturi eos sequi. Ab nobis nisi dolore saepe consequuntur non quaerat debitis error hic! Delectus tempore accusamus autem dignissimos sapiente nisi debitis provident voluptate incidunt vitae totam, quo expedita dolorum molestiae dolores? Est, minus doloribus delectus ut corporis totam placeat qui fuga ipsam, ad nisi inventore sequi cupiditate officia reiciendis. Nulla libero modi, impedit architecto deserunt dolorum similique, dolore reprehenderit earum sequi vel error nemo reiciendis itaque corrupti voluptatem, cum odio neque assumenda autem aliquam quasi amet iusto inventore! Eligendi doloremque iusto sed quidem in laudantium possimus nisi dolores, nostrum provident, ducimus rerum facere eum. Quaerat expedita eum a tempora assumenda aperiam debitis tempore earum laborum. Beatae, cupiditate veniam ratione pariatur vero soluta officiis reiciendis reprehenderit consequatur, aliquid odit dolorum obcaecati optio nam unde labore quasi assumenda eaque iusto quidem in. Perspiciatis asperiores illo magnam vitae eos accusantium illum ratione culpa vel esse fugiat cupiditate, ea impedit cum, omnis ullam, sint tempora voluptate inventore excepturi. Asperiores repellendus quibusdam natus magnam! Animi aut hic nesciunt libero quibusdam corporis! Dolor ullam quaerat mollitia modi expedita quae aliquid. Eaque itaque nisi ad facere corporis minima sapiente consectetur maiores! Quam, magni esse laboriosam doloremque natus a nesciunt quisquam illum cum similique sunt optio, alias vel assumenda deserunt facere quibusdam quis? Cumque quaerat quos, doloremque perferendis enim repellendus totam? Provident laboriosam iusto nisi! Consectetur a voluptas autem eaque provident odio facilis amet! Sint magnam accusamus laudantium optio expedita dolores praesentium dolor neque.</p>
                </div>
                <div id="vestibulum">
                    <h2>The Vestibulum</h2>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempora esse consequatur mollitia iusto, modi perferendis similique asperiores! Alias, rerum earum placeat accusantium consectetur repellat voluptatibus, cumque culpa quia eligendi nihil dolor quisquam. Aliquid tempora architecto debitis numquam vero expedita? Quos doloremque voluptatibus tempore est earum labore perferendis vel. Accusantium necessitatibus blanditiis in expedita ut aliquam rerum molestiae nihil neque dicta quia corrupti quibusdam quo quisquam, quae repellendus tenetur ex! Nulla fugiat id sit nisi ducimus. Sapiente velit in minus excepturi tenetur praesentium illum ipsam perspiciatis omnis, non cumque deleniti commodi voluptates ut beatae soluta possimus facere aspernatur sunt quasi provident tempore reprehenderit vero iusto. Nisi aliquam ipsam quibusdam repudiandae quam quaerat omnis, deserunt assumenda dolorem eaque veritatis ex recusandae illum itaque nesciunt sed ipsa hic doloremque aspernatur facilis consequuntur quae praesentium dolores! Architecto sapiente minima inventore debitis illum adipisci accusantium, eum provident magni iste sequi vel dolorem dolorum. Doloremque quo labore laboriosam at ducimus nesciunt facilis fugiat assumenda in cum delectus consequuntur laborum nulla suscipit voluptates odio, sapiente iure natus pariatur quasi repellendus ipsam eligendi obcaecati. Magnam, incidunt ea laboriosam quas, exercitationem necessitatibus ipsa ipsum architecto cum nisi eum, repudiandae illum odit animi minima eveniet tempore magni. Voluptatibus temporibus amet praesentium iure commodi quam ratione laboriosam vitae exercitationem. Error, ducimus beatae dolorum eveniet natus alias nesciunt, dolor minima sit eaque enim sed molestiae veniam distinctio unde sunt esse maxime at id suscipit deleniti reiciendis ratione facere iure. Suscipit doloribus dolorem quam exercitationem veritatis ducimus illo similique debitis distinctio, itaque dicta error autem facere, praesentium ea molestias, quibusdam inventore et laboriosam quisquam facilis quaerat excepturi eos sequi. Ab nobis nisi dolore saepe consequuntur non quaerat debitis error hic! Delectus tempore accusamus autem dignissimos sapiente nisi debitis provident voluptate incidunt vitae totam, quo expedita dolorum molestiae dolores? Est, minus doloribus delectus ut corporis totam placeat qui fuga ipsam, ad nisi inventore sequi cupiditate officia reiciendis. Nulla libero modi, impedit architecto deserunt dolorum similique, dolore reprehenderit earum sequi vel error nemo reiciendis itaque corrupti voluptatem, cum odio neque assumenda autem aliquam quasi amet iusto inventore! Eligendi doloremque iusto sed quidem in laudantium possimus nisi dolores, nostrum provident, ducimus rerum facere eum. Quaerat expedita eum a tempora assumenda aperiam debitis tempore earum laborum. Beatae, cupiditate veniam ratione pariatur vero soluta officiis reiciendis reprehenderit consequatur, aliquid odit dolorum obcaecati optio nam unde labore quasi assumenda eaque iusto quidem in. Perspiciatis asperiores illo magnam vitae eos accusantium illum ratione culpa vel esse fugiat cupiditate, ea impedit cum, omnis ullam, sint tempora voluptate inventore excepturi. Asperiores repellendus quibusdam natus magnam! Animi aut hic nesciunt libero quibusdam corporis! Dolor ullam quaerat mollitia modi expedita quae aliquid. Eaque itaque nisi ad facere corporis minima sapiente consectetur maiores! Quam, magni esse laboriosam doloremque natus a nesciunt quisquam illum cum similique sunt optio, alias vel assumenda deserunt facere quibusdam quis? Cumque quaerat quos, doloremque perferendis enim repellendus totam? Provident laboriosam iusto nisi! Consectetur a voluptas autem eaque provident odio facilis amet! Sint magnam accusamus laudantium optio expedita dolores praesentium dolor neque.</p>
                </div>
            </div>
        </div>
    </div>
</div> -->
<!-- <video controls autoplay src="ab.MOV"></video> -->
<br>
<div class="caruu">
    <div class="yui">
        <span class="img1">
            <img src="a.jpg" alt="">
        </span>
        <span class="img2">
            <img src="a.jpg" alt="">
        </span>
    </div>
</div>

<style>
    .caruu{
        width: 90%;
        height: 60%;
        margin: auto;
    }
    .yui{
        /* display: grid; */
        grid-template-areas: 
            'img1 img2';
        grid-gap: 10px;
        height: -webkit-fill-available;
    }
    .img1{
        grid-area: img1;
        height: 60%;
        width: 50%;
    }
    .img2{
        grid-area: img2;
        height: 60%;
        width: 50%;
    }
    img, video{
        width: 100%;
        height: 100%;
    }
</style>