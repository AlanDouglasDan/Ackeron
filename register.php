<?php
    require_once "config/config.php";
    require_once "includes/form_handlers/register_handler.php";
    require_once "includes/form_handlers/login_handler.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0 user-scalable=no">
    <title>Welcome to Ackeron!</title>
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <!-- <link rel="stylesheet" href="assets/css/bootstrap.css"> -->
    <link rel="stylesheet" href="assets/css/register_style.css">
    <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">
    <script src="assets/js/jquery-1.11.1.min.js"></script>
    <script src="assets/js/register.js"></script>
</head>
<body>

    <?php

    if(isset($_POST['register_button'])){
        echo '
        <script>

        $(document).ready(function(){
            $("#first").hide();
            $("#second").show();
        });

        </script>

        ';
    }

    ?>

    <script>
        function checkUser(user)
        {
        if (user.value == '')
        {
            $('#used').html('&nbsp;')
            return
        }

        $.post
        (
            'checkuser.php',
            { user : user.value },
            function(data)
            {
            $('#used').html(data)
            }
        )
        }
        function confirmUser(user){
            if (user.value == ''){
                $('#taken').html('&nbsp;')
                return
            }

            $.post
            (
                'confirmuser.php',
                { user : user.value },
                function(data)
                {
                $('#taken').html(data)
                }
            )
        }

        var state = false;
        function toggle(){
            if(state){
                document.getElementById("passwordHelpBlock").setAttribute("type", "password");
                document.getElementById("eye").style.color="#7a797e"
                state = false;
            }
            else{
                document.getElementById("passwordHelpBlock").setAttribute("type", "text");
                document.getElementById("eye").style.color="#5887ef";
                state = true;
            }
        }
        function toggle2(){
            if(state){
                document.getElementById("passwordHelpBlock2").setAttribute("type", "password");
                document.getElementById("eye2").style.color="#7a797e"
                state = false;
            }
            else{
                document.getElementById("passwordHelpBlock2").setAttribute("type", "text");
                document.getElementById("eye2").style.color="#5887ef";
                state = true;
            }
        }
        function toggle3(){
            if(state){
                document.getElementById("passwordHelpBlock3").setAttribute("type", "password");
                document.getElementById("eye3").style.color="#7a797e"
                state = false;
            }
            else{
                document.getElementById("passwordHelpBlock3").setAttribute("type", "text");
                document.getElementById("eye3").style.color="#5887ef";
                state = true;
            }
        }
    </script>

    <div class="wrapper">
        <div class="login_header">
            <h1>Ackeron</h1>
            <p>Login or signup below</p>
        </div>
        <div class="login_box">

            <div id="first">
                <form action="register.php" method="POST">
                    <div class="group" id="margin">
                        <label for="emailHelpBlock"><p>Email or Phone number</p></label>
                        <input type="text" name="log_email" id="emailHelpBlock" aria-describedby="helpBlock" placeholder="Email address or Phone number" value="<?php 
                        if(isset($_SESSION['log_email'])){
                            echo $_SESSION['log_email'];
                        }
                        ?>"required autocomplete="off" onBlur='confirmUser(this)'>
                        <div id='taken'>&nbsp;</div>
                    </div>
                    
                    <div class="group">
                        <label for="passwordHelpBlock"><p>Password</p></label>
                        <input type="password" id="passwordHelpBlock" aria-describedby="basic-addon" name="log_password" placeholder="Password" required>
                        <span id='basic-addon'>
                            <i class="fa fa-eye" id="eye" onclick="toggle()"></i>
                        </span>
                        <br>
                        <?php if(in_array("Invalid Login Details<br>", $error_array)) echo "Invalid Login Details<br>"; ?>
                    </div>

                    <input type="submit" name="login_button" value="Login">
                    
                    <br><br>

                    <div id="forgot" class="visible-xs">
                        <a href="#" class="text-muted">Forgot Password?</a><br>
                    </div>
                    
                    <a href="#" id="signup" class="signup">Need an account? Register here</a>
                </form>
                <div id="forgot" class="hidden-xs">
                    <a href="#" class="forgot">Forgot Password?</a><br>
                </div>
            </div>
            
            <div id="second">
                <form action="register.php" method="POST">
                    <div class="row con" id="margin">
                        <div class="group col-xs-6 extra">
                            <label for="fnameHelpBlock" class='half'><p>First name</p></label><br>
                            <input type="text" class='half' name="reg_fname" id="fnameHelpBlock" aria-describedby="helpBlock" placeholder="First Name" value="<?php 
                            if(isset($_SESSION['reg_fname'])){
                                echo $_SESSION['reg_fname'];
                            }
                            ?>"required autocomplete="off">
                            <br>
                            <?php if(in_array("Your first name must be between 2 and 25 characters<br>", $error_array)) echo "Your first name must be between 2 and 25 characters<br>"; ?>
                        </div>

                        <div class="group col-xs-6 eextra">
                            <label for="lnameHelpBlock" class='half'><p>Last name</p></label><br>
                            <input type="text" class='half' name="reg_lname" id="lnameHelpBlock" aria-describedby="helpBlock" placeholder="Last Name" value="<?php 
                            if(isset($_SESSION['reg_lname'])){
                                echo $_SESSION['reg_lname'];
                            }
                            ?>"required autocomplete="off">
                            <br>
                            <?php if(in_array("Your last name must be between 2 and 25 characters<br>", $error_array)) echo "Your last name must be between 2 and 25 characters<br>"; ?>
                        </div>
                    </div>
                    
                    <div class="group">
                        <label for="numberHelpBlock"><p>Phone Number</p></label>
                        <input type="text" name="reg_phone" id="numberHelpBlock" aria-describedby="helpBlock" placeholder="Phone number" value="<?php 
                        if(isset($_SESSION['reg_phone'])){
                            echo $_SESSION['reg_phone'];
                        }
                        ?>"required autocomplete="off">
                        <br>
                        <?php if(in_array("Your last name must be between 2 and 25 characters<br>", $error_array)) echo "Your last name must be between 2 and 25 characters<br>"; ?>
                    </div>

                    <div class="group">
                        <label for="emailHelpBlock2"><p>Email</p></label>
                        <input type="email" name="reg_email" id="emailHelpBlock2" aria-describedby="helpBlock" placeholder="Email" value="<?php 
                        if(isset($_SESSION['reg_email'])){
                            echo $_SESSION['reg_email'];
                        }
                        ?>" required autocomplete="off">
                        <br>
                    </div>

                    <div class="group">
                        <label for="emailHelpBlock3"><p>Confirm Email</p></label>
                        <input type="email" name="reg_email2" id="emailHelpBlock3" aria-describedby="helpBlock" placeholder="Confirm Email" value="<?php 
                        if(isset($_SESSION['reg_email2'])){
                            echo $_SESSION['reg_email2'];
                        }
                        ?>" required autocomplete="off" onBlur='checkUser(this)'>
                        <!-- <br> -->
                        <div id='used'>&nbsp;</div>
                        <?php if(in_array("Email already in use<br>", $error_array)) echo "Email already in use<br>"; 
                        else if(in_array("Invalid email format<br>", $error_array)) echo "Invalid email format<br>"; 
                        else if(in_array("Emails don't match<br>", $error_array)) echo "Emails don't match<br>"; ?>
                    </div>

                    <div class="row con">
                        <div class="form-group group col-xs-6 extra">
                            <label class="sr-only half">Gender</label>
                            <tr>
                                <section class='half'><td><p>Gender</p><input type="radio" name="gender" value="Male" <?php if (isset($_POST['gender']) && $_POST['gender'] == "Male"){
                                ?> checked <?php
                                } ?> required> Male &nbsp;
                                <input type="radio" name="gender" value="Female" <?php if (isset($_POST['gender']) && $_POST['gender'] == "Female"){
                                ?> checked <?php
                                } ?> required> Female
                                </td></section>
                            </tr>
                        </div>  

                        <!-- Birthday -->
                        <br><div class="form-group group col-xs-6 extra">      
                            <label class="sr-only half">Birthday</label>
                            <tr>
                                <section class='half'><td><p>Birthday</p>
                                <input type="date" name="dob" requred>
                                </td></section>
                            </tr>
                        </div><br>
                    </div>

                    <div class="group">
                        <label for="passwordHelpBlock"><p>Password</p></label>
                        <input type="password" id="passwordHelpBlock2" name="reg_password" placeholder="Password" required>
                        <span id='basic-addon'>
                            <i class="fa fa-eye" id="eye2" onclick="toggle2()"></i>
                        </span>
                        <br>
                    </div>

                    <div class="group">
                        <label for="passwordHelpBlock"><p>Confirm Password</p></label>
                        <input type="password" id="passwordHelpBlock3" name="reg_password2" placeholder="Confirm Password" required>
                        <span id='basic-addon'>
                            <i class="fa fa-eye" id="eye3" onclick="toggle3()"></i>
                        </span>
                        <br>
                        <?php if(in_array("Your passwords do not match<br>", $error_array)) echo "Your passwords do not match<br>"; 
                        else if(in_array("Your password can only contain english characters or numbers<br>", $error_array)) echo "Your password can only contain english characters or numbers<br>"; 
                        else if(in_array("Your password must be between 5 and 30 characters<br>", $error_array)) echo "Your password must be between 5 and 30 characters<br>"; ?>
                    </div>

                    <input type="submit" name="register_button" value="Register" required>
                    <br><br>

                    <?php if(in_array("<span style='color:#14C800;'>You're all set! Go ahead and login!</span><br>", $error_array)) echo "<span style='color:#14C800;'>You're all set! Go ahead and login!</span><br><br>"; ?>
                    <a href="#" id="signin" class="signin">Already have an account? Sign in here!</a>
                </form>
            </div>                
        </div>
        
    </div>

    <footer><h3>An Alan Douglas Production &copy;2020</h3></footer>
    
</body>
</html>