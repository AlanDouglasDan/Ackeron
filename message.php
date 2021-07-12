<?php 
    require_once 'config/config.php';
    require_once 'includes/classes/Message.php';
    require_once 'includes/classes/User.php';
    $userLoggedIn = $_SESSION['username'];
    // require_once 'includes/header.php';

    $message_obj = new Message($con, $userLoggedIn);

    // if(isset($_GET['id'])){
    //     $id = $_GET['id'];
    // }
    // else{
    //     header("Location: messages.php");
    // }
    $id = $_POST['msg_id'];

    $f_query = mysqli_query($con, "SELECT * FROM messages WHERE id='$id'");
    if(mysqli_num_rows($f_query)){
        $f_row = mysqli_fetch_array($f_query);
        if($f_row['user_from'] != $userLoggedIn)
            header("Location: messages.php");
    }

    $quer = mysqli_query($con, "SELECT user_to, date FROM messages WHERE id='$id'");
    $na = mysqli_fetch_array($quer);
    $name = $na['user_to'];
    if(strpos($name, "ACK_GROUP..??.") !== false){
        $ie = substr($name, 14);
        $f = mysqli_query($con, "SELECT group_info FROM group_chats WHERE id='$ie'");
        $ei = mysqli_fetch_array($f);
        $nam = $ei['group_info'];
        $nam = substr($nam, 14);
    }
    else
        $nam = $name;
    $date_posted = $na['date'];

    $qu = mysqli_query($con, "SELECT * FROM backgrounds WHERE username='$userLoggedIn'");
    if(mysqli_num_rows($qu)){
        $ss = mysqli_fetch_array($qu);
        $image = $ss['image'];
    }
    else
        $image = "";

    $que = mysqli_query($con, "SELECT date_viewed FROM message_views WHERE message_id='$id'");
    if(mysqli_num_rows($que)){
        $ro = mysqli_fetch_array($que);
        $date = $ro['date_viewed'];
        $sec3 = substr($date, 17);
        $min3 = substr($date, 14, -3);
        $hour3 = substr($date, 11, -6);
        $day3 = substr($date, 8, -9);
        $month3 = substr($date, 5, -12);
        $year3 = substr($date, 0, -15);
        $nice3 = mktime($hour3, $min3, $sec3, $month3, $day3, $year3);
        $forme = date("g:i A", $nice3);
        $form = date("Y-m-d", $nice3);
        if($date == "0000-00-00 00:00:00"){
            $form = "";
            $forme = "<span class='fa fa-ellipsis-o'>o o o</span>";
        }
    }
    else{
        $form = "";
        $forme = "<span class='fa fa-ellipsis-o'>o o o</span>";
    }

    $query2 = mysqli_query($con, "SELECT date_delivered FROM message_views WHERE message_id='$id'");
    if(mysqli_num_rows($query2)){
        $eeo = mysqli_fetch_array($query2);
        $login = $eeo['date_delivered'];
        $sec = substr($date_posted, 17);
        $min = substr($date_posted, 14, -3);
        $hour = substr($date_posted, 11, -6);
        $day = substr($date_posted, 8, -9);
        $month = substr($date_posted, 5, -12);
        $year = substr($date_posted, 0, -15);
        $nice = mktime($hour, $min, $sec, $month, $day, $year);

        $sec2 = substr($login, 17);
        $min2 = substr($login, 14, -3);
        $hour2 = substr($login, 11, -6);
        $day2 = substr($login, 8, -9);
        $month2 = substr($login, 5, -12);
        $year2 = substr($login, 0, -15);
        $nice2 = mktime($hour2, $min2, $sec2, $month2, $day2, $year2);
        $formedd = date("g:i A", $nice2);
        $formed = date("Y-m-d", $nice2);
        // echo $formed. "<br>";
        // echo $formedd. "<br>";
        if(($nice - $nice2) < 0){
            $time_message = $formedd;
            $tmg = $formed;
        }
        else{
            $time_message = "<span class='fa fa-ellipsis-o'>o o o</span>";
            $tmg = "";
        }
    }
    else{
        $time_message = "<span class='fa fa-ellipsis-o'>o o o</span>";
        $tmg = "";
    }
    
?>

<style>
    .msg-container{
        background-image: url(<?php echo $image; ?>);
        background-size: cover;
    }
    b{
        color: black !important;
    }
    #bring{
        font-size: 130%;
        color: red;
        position: absolute;
        right: 10;
        top: 10;
    }
    .seen{
        color: var(--gname);
    }
</style>


<div class="col-md-offset-4 col-md-8">
    <div class="msg-container" style="overflow-y: auto;">  
        <div class="msg-header">
            <span class='fa fa-remove' id='bring' onclick='bring_back("<?php echo $name; ?>", "<?php echo $userLoggedIn; ?>")'></span>
            <!-- <div class="name">
                <?php echo $nam; ?>
            </div> -->
            <div class="special_name" style='margin: 10 auto 0 auto;'>
                Message Info
            </div>
        </div>
        <?php echo $message_obj->getSingleMessage($id, "1", "hide");
            if(strpos($name, "ACK_GROUP..??.") === false){
                ?>
                <div class="mg-info">
                    <span class='fa fa-check' style="color: #42a5f5;"></span> <span class='ddi'>Read</span> <span class="time" style='font-size: 100%'><?php echo $form; ?>&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo $forme; ?></b></span> <hr class="widz">
                    <span class='warning_msg fa fa-check'></span> <span class='ddi'>Delivered</span> <span class="time" style='font-size: 100%'><?php echo $tmg; ?>&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo $time_message; ?></b></span> <hr class="widz">
                </div>
                <?php
            }
            else{
                $query3 = mysqli_query($con, "SELECT * FROM message_views WHERE message_id='$id'");
                $names = $viewed = $delivered = array();
                if(mysqli_num_rows($query3)){
                    while($row = mysqli_fetch_array($query3)){
                        array_push($names, $row['username']);
                        array_push($viewed, $row['date_viewed']);
                        array_push($delivered, $row['date_delivered']);
                    }
                    echo "<div class='card-header'><span class='seen fa fa-check'></span> Viewed By</div>";
                    for($i=0; $i<count($names); $i++){
                        if($names[$i] == $userLoggedIn)
                            continue;
                        if($viewed[$i] != "0000-00-00 00:00:00"){
                            $sec3 = substr($viewed[$i], 17);
                            $min3 = substr($viewed[$i], 14, -3);
                            $hour3 = substr($viewed[$i], 11, -6);
                            $day3 = substr($viewed[$i], 8, -9);
                            $month3 = substr($viewed[$i], 5, -12);
                            $year3 = substr($viewed[$i], 0, -15);
                            $nice3 = mktime($hour3, $min3, $sec3, $month3, $day3, $year3);
                            $forme = date("g:i A", $nice3);
                            $form = date("Y-m-d", $nice3);
                            ?>
                            <div class="mg-info">                    
                                <span class="nam">@<?php echo $names[$i]; ?></span><span class="time" style='font-size: 100%'><?php echo $form; ?>&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo $forme; ?></b></span> <hr class="widz">
                            </div>
                        <?php
                        }
                    }
                    echo "<div class='card-header'><span class='warning_msg fa fa-check'></span> Delivered to</div>";
                    for($i=0; $i<count($names); $i++){
                        if($names[$i] == $userLoggedIn)
                            continue;
                        if(!$names[$i])
                            continue;
                        if($viewed[$i] == "0000-00-00 00:00:00"){
                            $sec3 = substr($delivered[$i], 17);
                            $min3 = substr($delivered[$i], 14, -3);
                            $hour3 = substr($delivered[$i], 11, -6);
                            $day3 = substr($delivered[$i], 8, -9);
                            $month3 = substr($delivered[$i], 5, -12);
                            $year3 = substr($delivered[$i], 0, -15);
                            $nice3 = mktime($hour3, $min3, $sec3, $month3, $day3, $year3);
                            $forme = date("g:i A", $nice3);
                            $form = date("Y-m-d", $nice3);
                            ?>
                            <div class="mg-info">
                                <span class="nam">@<?php echo $names[$i]; ?></span><span class="time" style='font-size: 100%'><?php echo $form; ?>&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo $forme; ?></b></span> <hr class="widz">
                            </div>
                            <?php                    
                        }
                    }
                }
            }
        ?>
    </div>
</div>