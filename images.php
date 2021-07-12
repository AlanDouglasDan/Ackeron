<?php
    // require_once "includes/header.php";
    require_once "config/config.php";

    // if(isset($_GET['post_id'])) {
    //     $id = $_GET['post_id'];
    $id = $_POST['post_id'];
    $number = $_POST['number'];
    
?>
    
    <div class="center-block" style="height: 90%;">
        
    <style>
        video{
            width: 100%;
            height: 100%;
        }        
    </style>
        <!-- <h4 class="special_name center-block">Images</h4> -->
<?php
    $imagesQuery = mysqli_query($con, "SELECT body,image FROM posts WHERE id = '$id' AND deleted='no'");
    if(mysqli_num_rows($imagesQuery)){
        $row = mysqli_fetch_array($imagesQuery);
        $body = $row['body'];
        $list = $row['image'];
        if(!$list)
            header("Location: index.php");
        $list = substr($list, 0, -1);
        $list = explode(",", $list);
        if(count($list) > 1){
            ?>
                <div id="carousel-example-generic<?php echo $id; ?>" class="carousel slide img_container borded" data-ride='carousel' data-interval='300000'>
                    <ol class="carousel-indicators">
                        <?php
                            for($i=0; $i<count($list); $i++){
                                if($i == $number)
                                    $ru = "class='active'";
                                else
                                    $ru = "";
                                ?>
                                <li <?php echo $ru; ?> data-target='#carousel-example-generic<?php echo $id; ?>' data-slide-to=<?php echo $i; ?>></li>
                                <?php
                            }
                        ?>
                    </ol>
                    <div class="img_container2 carousel-inner" role='list-box'>
            <?php
            for($i=0; $i<count($list); $i++) {
                if($i == $number){
                    $rud = "carousel-inner item img_container2 active";
                    $bg_image = $list[$i];
                }
                else
                    $rud = "carousel-inner item img_container2";
                echo "<div class='$rud'>";
                if(strpos($list[$i], ".mp4"))
                    echo "<video controls src='$list[$i]'></video></div>";
                else
                    echo "<img src='$list[$i]'></div>";
            }
            ?>
                    </div>
                    <a href="#carousel-example-generic<?php echo $id; ?>" onclick='pause()' role='button' data-slide='prev' class="left carousel-control">
                        <span class="icon-prev" aria-hidden='true'></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a href="#carousel-example-generic<?php echo $id; ?>" onclick='pause()' role='button' data-slide='next' class="right carousel-control">
                        <span class="icon-next" aria-hidden='true'></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
                <!-- <div style="background-image: url('<?php echo $bg_image; ?>'); width: 100%; height: 100%; margin-top: -100%;"></div> -->
        <?php
        }
        else{?>
            <div class="carousel-container img-slider borded" style="width: 100%;">
                <?php
                    if(strpos($list[0], ".mp4") || strpos($list[0], ".MOV"))
                        echo "<video src='$list[0]' autoplay loop controls></video>";
                    else
                        echo "<img src='$list[0]' alt=''>";
                ?>                
                <!-- <div class="carousel-caption"><?php echo $body; ?></div> -->
            </div>
            <?php
        }        
    }
    else
        echo "You can't view the images of a non-existent post";
    // }
    // else
    //     echo "<div class='main_column column' id='main_column'>You can't view the images of a non-existent post</div>";

?>
    </div>