<?php 

    if(isset($_COOKIE['user_login'])){
        $userLoggedIn = $_COOKIE['user_login'];
    }

    $lin = $lot = $mins = $idz = array();
    $query = mysqli_query($con, "SELECT login,logout FROM logins WHERE username='$userLoggedIn' AND logout!='0000-00-00 00:00:00'");
    $date_time_now = date("Y-m-d H:i:s");
    if(mysqli_num_rows($query)){
        while($row = mysqli_fetch_array($query)){
            $logins = $row['login'];
            array_push($lin, $logins);
            $logout = $row['logout'];
            array_push($lot, $logout);
        }
        for($l=0; $l<count($lot); $l++){
            $start_date = new DateTime($lin[$l]);			
            $end_date = new DateTime($lot[$l]); 
            $interval = $start_date->diff($end_date); //Difference between dates 
            array_push($mins, $interval->i);
        }
        $start = 0;
        foreach($mins as $n)
            $start += $n;
        if($start >= 2000000)
            $level = "Ackerite";
        else if($start >= 500000)
            $level = "Ultimate Boss";
        else if($start >= 100000)
            $level = "Boss";
        else if($start >= 40000)
            $level = "Upcoming Boss";
        else if($start >= 5000)
            $level = "Professional";
        else if($start >= 1000)
            $level = "Enthusiast";
        else if($start >= 400)
            $level = "Up coming";
        else if($start >= 120)
            $level = "Amateur";			
        else
            $level = "Beginner";
    }
    else{
        $level = "Beginner";
    }

    $check_query = mysqli_query($con, "SELECT * FROM levels WHERE username='$userLoggedIn'");
    $rown = mysqli_fetch_array($check_query);

    if(mysqli_num_rows($check_query) == 0){
        $level_in = mysqli_query($con, "INSERT INTO levels VALUES(NULL, '$userLoggedIn', '$level', '$date_time_now')");
    }
    else{
        $leel = $rown['level'];
        if($leel != $level){            
            $level_up = mysqli_query($con, "UPDATE levels SET level='$level' WHERE username='$userLoggedIn'");
            $leel_up = mysqli_query($con, "UPDATE levels SET date='$date_time_now' WHERE username='$userLoggedIn'");

            $notification = new Notification($con, $userLoggedIn);
            $notification->levelUpNotification($level);
        }
    }

    $asdd=$gn=$gna= array();
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

    $quer = mysqli_query($con, "SELECT id FROM messages WHERE user_to='$userLoggedIn'");
    $quer2 = mysqli_query($con, "UPDATE messages SET viewed='yes' WHERE user_to='$userLoggedIn' AND viewed='no'");
    foreach($gna as $group){
        $rg = mysqli_query($con, "SELECT id FROM group_chats WHERE group_info='$group'");
        $ffe = mysqli_fetch_array($rg);
        $gr = $ffe['id'];
        $gg = "ACK_GROUP..??.$gr";
        $quer3 = mysqli_query($con, "SELECT id FROM messages WHERE user_to='$gg' AND user_from!='$userLoggedIn'");
        while($dfd = mysqli_fetch_array($quer3)){
            $eyy = $dfd['id'];
            $query3 = mysqli_query($con, "SELECT * FROM message_views WHERE username='$userLoggedIn' AND message_id='$eyy'");
            if(mysqli_num_rows($query3) == 0)
                $query4 = mysqli_query($con, "INSERT INTO message_views VALUES(NULL, '$userLoggedIn', '$eyy', '0', '$date_time_now')");
        }
    }
    if(mysqli_num_rows($quer)){
        while($sds = mysqli_fetch_array($quer)){    
            $ey = $sds['id'];
            $query3 = mysqli_query($con, "SELECT * FROM message_views WHERE username='$userLoggedIn' AND message_id='$ey'");
            if(mysqli_num_rows($query3) == 0)
                $query4 = mysqli_query($con, "INSERT INTO message_views VALUES(NULL, '$userLoggedIn', '$ey', '0', '$date_time_now')");
        }
    }

    $user_obj = new User($con, $userLoggedIn);
    $friends = $user_obj->getFriendArray();
    $friends = substr($friends, 1, -1);
    $friendz = explode(',', $friends);
    foreach($friendz as $friend){
        if($friend){
            $bday_query = mysqli_query($con, "SELECT * FROM users WHERE username='$friend'");
            $bday_row = mysqli_fetch_array($bday_query);
            $bday = $bday_row['dob'];
            $date = date("Y-m-d");
            $dat = substr($date, 5);
            $bda = substr($bday, 5);
            if($bda == $dat){
                $notification = new Notification($con, $friend);
                $notification->bdayNotification();
            }
        }        
    }
?>
