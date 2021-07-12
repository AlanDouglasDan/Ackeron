<?php 
    require_once("includes/header.php");

    if(isset($_GET['name']))
        $name = $_GET['name'];
    else
        header("Location: $userLoggedIn");

    $opened_query = mysqli_query($con, "UPDATE notifications SET opened='yes' WHERE user_to='$userLoggedIn' AND link='level.php?name=$name'");

    $lin = $lot = $mins = array();
    $query = mysqli_query($con, "SELECT login,logout FROM logins WHERE username='$name'");
    if(mysqli_num_rows($query)){
        $date_time_now = date("Y-m-d H:i:s");
        while($row = mysqli_fetch_array($query)){
            $logins = $row['login'];
            array_push($lin, $logins);
            $logout = $row['logout'];
            if($logout=='0000-00-00 00:00:00')
                continue;
            array_push($lot, $logout);
        }
        for($l=0; $l<count($lot); $l++){
            $start_date = new DateTime($lin[$l]);			
            $end_date = new DateTime($lot[$l]); 
            $interval = $start_date->diff($end_date); //Difference between dates 
            array_push($mins, $interval->i);
        }
        // foreach($mins as $s)
        //     echo $s. "<br>";
        $start = 0;
        foreach($mins as $n)
            $start += $n;
        // echo $start . "<br>";
        if($start >= 2000000){
            $mk = "";
            $km = 500000;
            $level = "Ackerite";
        }
        else if($start >= 500000){
            $mk = 2000000;
            $km = 1000000;
            $level = "Ultimate Boss";
        }
        else if($start >= 100000){
            $mk = 500000;
            $km = 40000;
            $level = "Boss";
        }
        else if($start >= 40000){
            $mk = 100000;
            $km = 5000;
            $level = "Upcoming Boss";
        }
        else if($start >= 5000){
            $mk = 40000;
            $km = 1000;
            $level = "Professional";
        }
        else if($start >= 1000){
            $mk = 5000;
            $km = 400;
            $level = "Enthusiast";
        }
        else if($start >= 400){
            $mk = 1000;
            $km = 400;
            $level = "Up coming";
        }
        else if($start >= 120){
            $mk = 400;
            $km = 120;
            $level = "Amateur";        
        }
        else{
            $mk = 120;
            $km = 0;
            $level = "Beginner";
        }
        if($mk){
            $perc = round((($start-$km)/$mk) * 100, 1);
            // echo round($perc, 1) . "%";
        }

        switch ($level) {
            case 'Beginner':
                $sym = "<span class='fa fa-star nora' style='color:red'></span> <span class=''>Beginner</span>";
                $mys = "<span class='fa fa-star nora' style='color:purple'></span> <span class=''>Amateur</span>";
                break;
            case 'Amateur':
                $sym = "<span class='fa fa-star nora' style='color:purple'></span> <span class=''>Amateur</span>";	
                $mys = "<span class='fa fa-star nora' style='color:green'></span> <span class=''>Up coming</span>";
                break;
            case 'Up coming':
                $sym = "<span class='fa fa-star nora' style='color:green'></span> <span class=''>Up coming</span>";	
                $mys = "<span class='fa fa-star nora' style='color:blue'></span> <span class=''>Enthusiast</span>";						
                break;
            case 'Enthusiast':
                $sym = "<span class='fa fa-star nora' style='color:blue'></span> <span class=''>Enthusiast</span>";	
                $mys = "<span class='fa fa-star nora' style='color:orange'></span> <span class=''>Professional</span>";					
                break;
            case 'Professional':
                $sym = "<span class='fa fa-star nora' style='color:orange'></span> <span class=''>Professional</span>";		
                $mys = "<span class='fa fa-star nora' style='color:grey'></span> <span class=''>Upcoming Boss</span>";				
                break;
            case 'Ultimate Boss':
                $sym = "<span class='fa fa-star nora' style='color:orchid'></span> <span class=''>Ultimate Boss</span>";		
                $mys = "<span class='fa fa-star nora' style='color:gold'></span> <span class=''>Ackerite</span>";				
                break;
            case 'Upcoming Boss':
                $sym = "<span class='fa fa-star nora' style='color:grey'></span> <span class=''>Upcoming Boss</span>";	
                $mys = "<span class='fa fa-star nora' style='color:cyan'></span> <span class=''>Boss</span>";					
                break;
            case 'Boss':
                $sym = "<span class='fa fa-star nora' style='color:cyan'></span> <span class=''>Boss</span>";	
                $mys = "<span class='fa fa-star nora' style='color:orchid'></span> <span class=''>Ultimate Boss</span>";					
                break;
            case 'Ackerite':
                $sym = "<span class='fa fa-star nora' style='color:gold'></span> <span class=''>Ackerite</span>";						
                $mys = "";
                break;			
            default:
                # code...
                break;
        }
        ?>

        <div id="container">
            <div class="statt special_name"><?php echo $sym  ?><span class='white-text'> to</span>  <?php echo $mys; ?></div>
            <div class="card" id="piecharts">
                <div class="card-block">
                    <h2>Level analysis</h2>
                    <div class="round-chart" data-percent="<?php echo $perc; ?>">
                        <span><?php echo $perc; ?><small>% <br> to next level</small></span>
                    </div>               
                </div>
            </div>
            <div class="rowe">
                <progress class='progressbar-striped' min ="0" max="100" value="<?php echo $perc; ?>"></progress>
                <!-- <div class="progress">
                    <div class="progressbar-info progressbar-striped active" role='progressbar'>
                        <progress class='progressbar-striped' min ="0" max="100" value="<?php echo $perc; ?>"></progress>
                    </div>
                </div> -->
            </div>
            <center>
                <h4 id="view_levels" onclick='show()' style='cursor: pointer'>View Levels</h4>
            </center>
        </div>
        <div id="hidden">
            <div class="card-header fix">
                <span id="bring_back" class="fa fa-remove" onclick='hide()'></span>
                <span class="center-block">Levels</span>
            </div>
            <div class="column col-md-9 col-md-offset-3" style="padding:10px; border:none; box-shadow: none; line-height: 2;">
                <div class="special_name"><span class='fa fa-star nora' style='color:red'></span> Beginner</div>
                <div class="special_name"><span class='fa fa-star nora' style='color:purple'></span> Amateur</div>
                <div class="special_name"><span class='fa fa-star nora' style='color:green'></span> Up coming</div>
                <div class="special_name"><span class='fa fa-star nora' style='color:blue'></span> Enthusiast</div>
                <div class="special_name"><span class='fa fa-star nora' style='color:orange'></span> Professional</div>
                <div class="special_name"><span class='fa fa-star nora' style='color:grey'></span> Upcoming Boss</div>
                <div class="special_name"><span class='fa fa-star nora' style='color:cyan'></span> Boss</div>
                <div class="special_name"><span class='fa fa-star nora' style='color:orchid'></span> Ultimate Boss</div>
                <div class="special_name"><span class='fa fa-star nora' style='color:gold'></span> Ackerite</div>
            </div>
        </div>
    <?php
    }
    else 
        echo "<div class='column center-block unseen'>You can't view the levels of someone who isn't registered on the site</div>";
    ?>

<script>
    function show(){
        document.getElementById("hidden").style.display ="block";        
        document.getElementById("container").style.display ="none";        
    }
    function hide(){
        document.getElementById("hidden").style.display ="none";        
        document.getElementById("container").style.display ="block";        
    }
</script>

</div>