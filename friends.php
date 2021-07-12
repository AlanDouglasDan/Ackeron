<?php

include("includes/header.php");

if(isset($_GET['name'])) {
    $nam = $_GET['name'];
    $name = ucfirst($nam);
}
else {
	$name = $nam = $userLoggedIn;
}

if(isset($_GET['id']))
    $id = $_GET['id'];
else
    $id = "";
?>

<div class="main_column column" id="main_column">
    <?php 
        if($name == "")
            echo "You can't search for friends that don't have a name.";
        else {
            $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username = '$name' AND user_closed='no'");

            if(mysqli_num_rows($usersReturnedQuery) == 0)
                echo "This account has been blocked or doesn't exist";
            else{
                while($row = mysqli_fetch_array($usersReturnedQuery)){
                    $user_obj = new User($con, $userLoggedIn);
                    $view_obj = new User($con, $row['username']);
                    $f_name = $row['first_name'];
                    $friends = $view_obj->getFriendArray();
                    $friends = substr($friends, 1, -1);
                    $friendz = explode(',', $friends);
                    sort($friendz, SORT_STRING);
                    $total = count($friendz);
                    if(strlen($friends) <= 2)
                        $total = 0;
                    echo "<h1 class='center-block special_name'> " .$f_name ."'s friends(" .$total .")</h1>";
                    ?>
                        <!-- <div class="input-group">
                            <input type="search" id="search_box" onkeyup='getUserz(this.value, "<?php echo $nam; ?>", "<?php echo $userLoggedIn; ?>")' name='q' autocomplete='off' placeholder="Search..." class="form-control center-block search_bar">
                            <div class="input-group-addon">
                                <span class="input-group-text"><img src="assets/images/icons/magnifying_glass.png" alt="" style="height:100%;"></span>
                            </div>
                        </div> -->
                        <div class="input_group" id="chat_row">
                            <input type="search" autocomplete='off' onkeyup='getUserz(this.value, "<?php echo $nam; ?>", "<?php echo $userLoggedIn; ?>")' id="search_box" placeholder='Search...' class='form-control inp'>
                        </div>
                        <br>
                        <div class="searches"></div>
                        <div id='all_friends'>
                    <?php
                    foreach ($friendz as $friend) {
                        if($friend){
                            if($userLoggedIn == $friend)
                                $mutual_friends = "";
                            else
                                $mutual_friends = $user_obj->getMutualFriends($friend) . " mutual friends";
                            if($id)
                                $link = "messages.php?u=$friend&forward=$id";
                            else
                                $link = $friend;
                            $query2 = mysqli_query($con, "SELECT * FROM users WHERE username = '$friend' AND user_closed='no'");
                            while($row2 = mysqli_fetch_array($query2)){                            
                                echo "<a href='$link'>
                                        <div class='search_result'd>
                                            <div class='result_profile_pic'>
                                                <img src='". $row2['profile_pic'] ."' style='height: 100px;'>
                                            </div>                                        
                                                <p id='grey' style='margin: 0 0 5; font-size: 1.2em;'> " ."@". $row2['username'] ."</p>                                        
                                                " . $mutual_friends ."<br>
                                        </div>
                                    </a>
                                    <hr id='search_hr'>";
                            }
                        }
                        else
                            echo $name . " doesn't have any friends yet";
                    }
                }
            }
        }
    ?>
</div>
</div>

<script>
    $(document).ready(function(){
        $('input[id="search_box"]').on('keyup',function(){
            if($(this).val()){
                document.getElementById("all_friends").style.display="none";
            }
            else{
                document.getElementById("all_friends").style.display="block";
            }
        });
    });
</script>