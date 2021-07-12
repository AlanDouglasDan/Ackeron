<?php
require_once "includes/header.php";
if(isset($_GET['name'])){
    $name = $_GET['name'];
    $query = mysqli_query($con, "UPDATE notifications SET opened='yes' WHERE user_to='$userLoggedIn' AND link='bday.php?name=$name'");
    // echo $name;
}

$user_obj = new User($con, $userLoggedIn);
$dobs = $time = $names = $keys = array();

$query = mysqli_query($con, "SELECT * FROM users WHERE user_closed='no'");
while($row = mysqli_fetch_array($query)){
    if($row['username'] == $name || $row['username'] == $userLoggedIn)
        continue;
    if($user_obj->isFriend($row['username'])){
        array_push($dobs, $row['username'].",".$row['dob']);
        // array_push($names, $row['username']);
    }
}

$date_time_now = date("Y-m-d");
$montsh = substr($date_time_now, 5, -3);
// echo $montsh;
$montsh = 04;

foreach($dobs as $key2 => $dob){
    // echo $dob."<br> ";
    $date = explode(",", $dob);
    $datef = $date[1];
    $person = $date[0];
    $month = substr($datef, 5, -3);
    // echo $month." ".$person."<br> ";
    array_push($time, $month.",".$person);
    // echo "<br>";
}

// sort($time, SORT_NUMERIC);
foreach($time as $key => $m){
    echo $m . "<br>";
    if($m >= $montsh){
        // echo $names[$key] ."<br>";
    }
}

?>