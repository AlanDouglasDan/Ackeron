<?php 
  require_once 'config/config.php';

  if (isset($_POST['user']))
  {
    $user   = sanitizeString($_POST['user']);

    if(strpos($user,"@") !== false)
      $type = "email adress";
    else
      $type = "phone number";

    $result = mysqli_query($con, "SELECT * FROM users WHERE (email='$user' OR phone='$user')");

    if (mysqli_num_rows($result))
      echo  "<span class='available'>&nbsp;&#x2714;" .
            "The $type '$user' is recognized by the network</span>";
    else
      echo "<span class='taken'>&nbsp;&#x2718; " .
           "The $type '$user' is not registered on this network</span>";
  }
?>