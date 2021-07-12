<?php
    require_once "config/config.php";

    $id = $_POST['msg_id'];
    $number = $_POST['number'];
    
?>
    
    <div class="center-block" style="height: 90%;">
    <!-- media.php.??.assets/images/messages/60018e9d6f3f2vlc-record-2021-01-15-11h15m36s-VictoriousS03E07ToriandJadesPlayDate(toxicwap.com).mp4-.mp4, victorious -->
    <!-- media.php.??.assets/images/messages/6001b519605f2IMG_5656.JPG,assets/images/messages/6001b51960970IMG_5663.JPG,assets/images/messages/6001b51960c7dIMG_5668.JPG,assets/images/messages/6001b51960fc5IMG_5676.JPG,assets/images/messages/6001b5196136cIMG_5460.JPG,  -->
    <style>
        video{
            width: 100%;
            height: 100%;
        }        
    </style>
<?php
    $imagesQuery = mysqli_query($con, "SELECT body FROM messages WHERE id='$id'");
    if(mysqli_num_rows($imagesQuery)){
        $row = mysqli_fetch_array($imagesQuery);
        $list = $row['body'];
        $list = substr($list, 13, -1);
        $list = explode(",", $list);
        if(count($list) > 2){
            ?>
                <div id="carousel-example-generic<?php echo $id; ?>" class="carousel slide img_container borded" data-ride='carousel' data-interval='300000'>
                    <ol class="carousel-indicators">
                        <?php
                            for($i=0; $i<(count($list)-1); $i++){
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
            for($i=0; $i<(count($list)-1); $i++) {
                if($i == $number)
                    $rud = "carousel-inner item img_container2 active";
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
        <?php
        }
        else{?>
            <div class="carousel-inner">
                <div class="item img_container2 active">
                    <?php
                        if(strpos($list[0], ".mp4") || strpos($list[0], ".MOV"))
                            echo "<video src='$list[0]' autoplay loop controls></video>";
                        else
                            echo "<img src='$list[0]' alt=''>";
                    ?>                
                </div>
            </div>
            <?php
        }        
    }
    else
        echo "You can't view the images of a non-existent post";
?>
    </div>