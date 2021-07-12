<?php
    require_once "includes/header.php";

    $date = date("Y-m-d H:i:s");
    $sec = substr($date, 17);
    $min = substr($date, 14, -3);
    $hour = substr($date, 11, -6);
    $day = substr($date, 8, -9);
    $month = substr($date, 5, -12);
    $year = substr($date, 0, -15);
    $nice = mktime($hour, $min, $sec, $month, $day, $year);
    $formed = date("l", $nice);
    // $formed = "Wednesday";
    switch ($formed) {
        case 'Monday':
            $bg = "#555";
            break;
        case 'Tuesday':
            $bg = "#00b0fc";
            break;
        case 'Wednesday':
            $bg = "black";
            break;
        case 'Thursday':
            $bg = "#2ecc71";
            break;
        case 'Friday':
            $bg = "brown";
            break;
        case 'Saturday':
            $bg = "orchid";
            break;
        case 'Sunday':
            $bg = "orange";
            break;
        default:
            $bg = "";
            break;
    }
?>

<style>
    #new_btn2{            
        color: <?php echo $bg; ?>;
        background-color: <?php echo $bg; ?>;
    }
</style>

    <div class="main_column column" id="status_column">
        <form action="index.php" method="post">
            <textarea name="status_text" id="status_text" cols="30" rows="10" style="background-color:<?php echo $bg; ?>" placeholder="Type a status"></textarea>
            <input type="hidden" name="bg" value="<?php echo $bg; ?>">
            <input type='submit' name='post_status_text' id='message_submit'>
            <label onclick='sound.play()' id='new_btn2' for='message_submit'><span class='fa fa-paper-plane'></span></label>
        </form>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('input[id="new_btn2"]').attr('disabled',true);
        $('textarea[id="status_text"]').on('keyup',function(){
            if($(this).val()){
                $('input[id="new_btn2"]').attr('disabled',false);
                document.getElementById("new_btn2").style.backgroundColor="blue";
                document.getElementById("new_btn2").style.color="white";
            }
            else{
                $('input[id="new_btn2"]').attr('disabled',true);
                document.getElementById("new_btn2").style.backgroundColor="<?php echo $bg; ?>";
                document.getElementById("new_btn2").style.color="<?php echo $bg; ?>";
            }
        });
    });

    var sound = new Audio();
    sound.src = "button_click.mp3";


</script>

<!-- monday #555
tuesday blue
wednesday red
thursday green
friday lightbrown
saturday orange
sunday yellow -->
