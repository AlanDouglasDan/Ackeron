<?php // Example 27-6: checkuser.php
  require_once 'config/config.php';

  if (isset($_POST['user']))
  {
    $user   = sanitizeString($_POST['user']);
    $result = mysqli_query($con, "SELECT * FROM users WHERE email='$user'");

    if (mysqli_num_rows($result))
      echo  "<span class='taken'>&nbsp;&#x2718; " .
            "The email address '$user' is already in use</span>";
    else
      echo "<span class='available'>&nbsp;&#x2714; " .
           "The email address '$user' is available</span>";
  }
?>