<?php
    require_once "includes/header.php";

    if(isset($_GET['name']))
        $name = $_GET['name'];
    else
        $name = "";

    if(isset($_GET['id']))
        $id = $_GET['id'];
    else
        $id = "";

    if(isset($_POST['post_message'])){
        $message_obj = new Message($con, $userLoggedIn);
        $body = $_POST['message_body'];
        $post_id = $_POST['id'];
        $date = date("Y-m-d H:i:s");
        $body = mysqli_real_escape_string($con, "status.php.??.id=$post_id $body");
        $message_obj->sendMessage($name, $body, $date);
    }
?>

    <div class="main_column column" id="status_column">
        <?php 
            $post = new Post($con, $userLoggedIn);
            $post->getStatus($name, $id);
        ?>
    </div>
</div>