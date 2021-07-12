<?php
    if(isset($_POST['update'])){
        $bio = sanitizeString($_POST['bio']);
        $_query = mysqli_query($con, "UPDATE users SET bio='$bio' WHERE username='$userLoggedIn'");
        header("Location: profile.php");
    }

    if(isset($_POST['update_details'])){
        $address = sanitizeString($_POST['address']);
        $city = sanitizeString($_POST['city']);
        $country = sanitizeString($_POST['country']);
        $phone = sanitizeString($_POST['tel']);
        $work = sanitizeString($_POST['work']);
        $relationship = sanitizeString($_POST['relationship']);
        
        $_query = mysqli_query($con, "UPDATE users SET address='$address', city='$city', country='$country', phone='$phone', work='$work', relationship='$relationship' WHERE username='$userLoggedIn'");

        // $link = '#profileTabs a[href="#about_div"]';
        // echo "<script> 
        //         $(function() {
        //             $('" . $link ."').tab('show');
        //         });
        //     </script>";

        // header("Location: profile.php?show_about=yes");
    }
?>