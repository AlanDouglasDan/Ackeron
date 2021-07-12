<?php
// require_once "includes/header.php";
require_once 'config/config.php';
require_once 'includes/classes/Message.php';
require_once 'includes/classes/User.php';
$userLoggedIn = $_SESSION['username'];

// if(isset($_GET['id'])){
$id = $_POST['msg_id'];
// }

$message_obj = new Message($con, $userLoggedIn);
$queryf = mysqli_query($con, "SELECT user_to, user_from FROM messages WHERE id='$id'");
$rowf = mysqli_fetch_array($queryf);
$name1 = $rowf['user_to'];
$name2 = $rowf['user_from'];
if($name1 == $userLoggedIn)
    $name = $name2;
else
    $name = $name1;

echo "div class='msg-container' id='msg_container' style='overflow-y: auto;'>";
if(isset($_POST['forward'])){
    $do = $_POST['id'];
    $query = mysqli_query($con, "SELECT body FROM messages WHERE id='$do'");
    if(mysqli_num_rows($query)){
        $row = mysqli_fetch_array($query);
        $body = $row['body'];    
        $body = mysqli_real_escape_string($con, $body);
        $body_array = preg_split("/\s+/", $body);
        foreach($body_array as $key => $value) {
            if(strpos($value, "reply.php.??.id=") !== false) {
                $value = "";
                $body_array[$key] = $value;
            }
            if(strpos($value, "status.php.??.id=") !== false) {
                $value = "";
                $body_array[$key] = $value;
            }
        }
        $body = implode(" ", $body_array);
        $date = date("Y-m-d H:i:s");
        $peeps = $_POST['to_add'];
        $date = date("Y-m-d H:i:s");
        if($peeps){
            foreach($peeps as $key => $pes){
                if($key == 0)
                    $nek = $pes;
                $queery = mysqli_query($con, "INSERT INTO messages VALUES('', '$pes', '$userLoggedIn', '$body', '$date', 'no', 'no', 'no')");
            }
            header("Location: messages.php?u=" .$nek);
        }
    }
    // else{
    //     header("Location: about.php");
    // }
    // echo $body;
}

?>
<div class="card-header fix">
    <span class="fa fa-remove" style='float:left;' onclick='bring_back("<?php echo $name; ?>", "<?php echo $userLoggedIn; ?>")'></span>
    <span class="center-block">Forward to</span>
</div>
<form method="POST">
<!-- <div class='resultDisplay' style='height: 40px; display: flex; align-items: center;' onclick='checkAll()'>
    <div class='liveSearchText' style='width: 95%'>
        Select All
    </div>
    <div class='pull-right hrm' id='fow' style='margin: 0;'></div>
</div> -->
<?php
echo $message_obj->getSingleMessage($id, 0, "hide"); 
$usersReturned = mysqli_query($con, "SELECT username FROM users");
$counter=0;
$users = array();
while($row = mysqli_fetch_array($usersReturned)) {
    array_push($users, $row['username']);
}

$asdd=$gn=$gna=$idz= array();
$g_query = mysqli_query($con, "SELECT group_info, users FROM group_chats");
if(mysqli_num_rows($g_query)){
    while($rr = mysqli_fetch_array($g_query)){        
        array_push($asdd, $rr['users']);
        array_push($gn, $rr['group_info']);
    }
    for($i=0; $i<count($gn); $i++){
        $pep = explode(",", $asdd[$i]);
        foreach($pep as $pess)
            if($pess == $userLoggedIn)
                array_push($gna, $gn[$i]);
    }
}
foreach($gna as $group){
    // $dj = substr($group, 14);
    array_push($users, $group);
}
        
sort($users, SORT_STRING);
$pass_on = array();
foreach($users as $usee){
    // echo $usee;
    $counter++;
    if(strpos($usee, "ACK_GROUP..??.") === false){
        $user = new User($con, $userLoggedIn);
        $user_found_obj = new User($con, $usee);
        $pp = $usee;
    
        if($usee == $userLoggedIn)
            continue;
    
        if($user->isFriend($usee)) {
            echo "<div class='resultDisplay' style='height: 60px;' onclick='check(\"".$usee."\")'>
                    <div class='liveSearchProfilePic'>
                        <img src='" . $user_found_obj->getProfilePic() ."'>
                    </div>

                    <span class='liveSearchText'>
                    " . $user_found_obj->getFirstAndLastName() . "
                    </span>
                    <div class='pull-right hrm' id='fow$usee'></div>
                </div>
                ";
            array_push($pass_on, $usee);
            // echo "<label class='labell' for='c_box$counter'><div class='user_tb'>
            //         <img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 50%; margin-right: 5px;'>
            //         " . $user_found_obj->getFirstAndLastName() . "
            //         </div>
            //     </label>
            //     <input type='checkbox' name='to_add[]' value='$pp' class='tb_ch_box' id='c_box$counter'>
            //     ";
        }
    }
    else{
        $pa = mysqli_query($con, "SELECT id, group_pic FROM group_chats WHERE group_info='$usee'");
        if(mysqli_num_rows($pa)){
            $rows = mysqli_fetch_array($pa);
            $idr = $rows['id'];
            $send_to = "ACK_GROUP..??.$idr";
            $image = $rows['group_pic'];
            if(!$image)
                $image = "assets/images/profile_pics/defaults/male.png";
        }
        $nam = substr($usee, 14);
        echo "<div class='resultDisplay' style='height: 60px;' onclick='check(\"".$idr."\")'>
                <div class='liveSearchProfilePic'>
                    <img src='" . $image ."'>
                </div>

                <span class='liveSearchText'>
                " . $nam . "
                </span>
                <div class='pull-right hrm' id='fow$idr'></div>
            </div>
            ";
        array_push($pass_on, $idr);
        // echo "<label class='labell' for='c_box$counter'><div class='user_tb'>
        //         <img src='" . $image . "' style='border-radius: 50%; margin-right: 5px;'>
        //         " . $name . "
        //         </div>
        //     </label>
        //     <input type='checkbox' name='to_add[]' value='$send_to' class='tb_ch_box' id='c_box$counter'>
        //     ";
    }
}
?>
<!-- <input type="hidden" name="id" value=<?php echo $id; ?>> -->
<input type='hidden' value='' id='tofow'>
<!-- <input onclick='sound.play()' disabled="disabled" value='Send' type="submit" id="post_button2" name='forward' style='position: relative; margin: 10 0; float: right;'> -->
<input type='button' style='position: relative; margin: 10; float: right;' class='btn' disabled onclick='forwardMsgs()' id='post_button2' value='Send'>

</div>
</form>
</div>
</div>

<script>    
    function check(person){
        $("#fow"+person).toggleClass("checkedd");
        $("#fow"+person).toggleClass("fa");
        $("#fow"+person).toggleClass("fa-check");
        let body = _("tofow").value
        var str = body.indexOf(person);
        if(str != -1){
            body = body.replace(person+",", "");
            _("tofow").value = body;
            var bodd = _("tofow").value
        }
        else{
            _("tofow").value = body + person+",";
            var bodd = _("tofow").value
        }
        if(bodd){							
            $('input[id="post_button2"]').attr('disabled',false);
            $('input[id="post_button2"]').css('background-color','var(--sbutton)');
        }
        else{
            $('input[id="post_button2"]').attr('disabled',true);
            $('input[id="post_button2"]').css('background-color','var(--shadow2)');
        }
    }

    function forwardMsgs(){
        sound.play()
        var userLoggedIn = '<?php echo $userLoggedIn; ?>';
        var id = '<?php echo $id; ?>';
        var toSend = _("tofow").value;
        var toSen = toSend.length - 1;
        var res = toSend.substring(0, toSen);
        res = res.split(",");
        for(var check of res){
            $("#fow"+check).removeClass("checkedd");
            $("#fow"+check).removeClass("fa");
            $("#fow"+check).removeClass("fa-check");
        }					
        var formdata = new FormData();
        var ajax = new XMLHttpRequest();
        ajax.addEventListener("load", completeHandler, false);
        formdata.append('usernames', toSend);
        formdata.append('userLoggedIn', userLoggedIn);
        formdata.append('id', id);
        if(toSend){
            ajax.open('POST', 'includes/form_handlers/uploadForwardMessagee.php');
            ajax.send(formdata);
        }
        _("tofow").value = '';
        $('input[id="forwardBtn<?php echo $id; ?>"]').attr('disabled',true);
        $('input[id="forwardBtn<?php echo $id; ?>"]').css('background-color','var(--shadow2)');        
    }
    
    function completeHandler(event){
        bring_back("<?php echo $name; ?>", "<?php echo $userLoggedIn; ?>");
    }
    
    var sound = new Audio();
    sound.src = "button_click.mp3";
</script>