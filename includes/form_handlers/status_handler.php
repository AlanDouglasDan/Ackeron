<?php 
    $date_time_now = date("Y-m-d H:i:s");
    $query = mysqli_query($con, "SELECT id,date_added FROM statuses WHERE deleted='no'");
    while($stats = mysqli_fetch_array($query)){
        $date_added = $stats['date_added'];
        $id = $stats['id'];
        $start_date = new DateTime($date_added); //Time of post			
        $end_date = new DateTime($date_time_now); //Current time
        $interval = $start_date->diff($end_date); //Difference between dates 
        if($interval->d >= 1) {
            $delete_query2 = mysqli_query($con, "DELETE FROM views WHERE status_id='$id'");
            $delete_query = mysqli_query($con, "UPDATE statuses SET deleted='yes' WHERE id='$id'");
        }
    }	
?>