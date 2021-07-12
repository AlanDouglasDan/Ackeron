<!-- <script src="assets/js/jquery-3.5.1.min.js"></script> -->
<?php
    if(isset($_COOKIE['user_login'])){
        $a = $_COOKIE['user_login'];
        $query = mysqli_query($con, "SELECT * FROM logins WHERE username='$a' AND logout='0000-00-00 00:00:00'");
        if(mysqli_num_rows($query)){
            $_SESSION['username'] = $a;
            header("Location: index.php");
            exit();
        }
    }

    if(isset($_POST['login_button'])){
        $email = sanitizeString($_POST['log_email']);
        $passwordd = sanitizeString($_POST['log_password']);

        $_SESSION['log_email'] = $email;

        $check_database_query = mysqli_query($con, "SELECT * FROM users WHERE email='$email' OR phone='$email'");
        // $check_database_query = "SELECT * FROM users WHERE email=? OR phone=?";
        // $stmt = mysqli_stmt_init($con);
        // if(!mysqli_stmt_prepare($stmt, $check_database_query)){
        //     echo "SQL ERROR";
        // }
        // else{
        //     mysqli_stmt_bind_param($stmt, "ss", $email, $email);
        //     mysqli_stmt_execute($stmt);
        // }
        $check_login_query = mysqli_num_rows($check_database_query);

        if($check_login_query){
            $row = mysqli_fetch_array($check_database_query);
            $username = $row['username'];
            $password = $row['password'];

            if(password_verify($passwordd, $password)){
                $user_closed_query = mysqli_query($con, "SELECT * FROM users WHERE (email='$email' OR phone='$email') AND user_closed='yes'");
                // $yes = "yes";
                // $user_closed_query = "SELECT * FROM users WHERE (email=? OR phone=?) AND user_closed=?";
                // $stmt = mysqli_stmt_init($con);
                // if(!mysqli_stmt_prepare($stmt, $user_closed_query)){
                //     echo "SQL ERROR";
                // }
                // else{
                //     mysqli_stmt_bind_param($stmt, "sss", $email, $email, $yes);
                //     mysqli_stmt_execute($stmt);
                // }
                if(mysqli_num_rows($user_closed_query) == 1){
                    $reopen_account = mysqli_query($con, "UPDATE users SET user_closed='no' WHERE (email='$email' OR phone='$email')");
                }

                $time = date("Y-m-d H:i:s");
                $clock_in_query = mysqli_query($con, "INSERT INTO logins VALUES('', '$username', '$time', '0')");
                
                setcookie("user_login", $username, time() + (30*24*60*60));
                
                $_SESSION['username'] = $username;
                header("Location: index.php");
                exit();
            }
            else{
                array_push($error_array, "Invalid Login Details<br>");
            }
        }
        else{
            array_push($error_array, "Invalid Login Details<br>");
        }
    }

?>