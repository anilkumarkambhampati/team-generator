<html>
    <head>
        <style>
            body{
                background-image: url("6th.jpeg");
                background-repeat: no-repeat;
                background-size:cover;
            }
            .container {
            height: auto;
            width: 250px;
            background-color: rgba(75, 74, 74, 0.751);
            border-radius: 5px;
            color: white;
            padding: 20px;
            margin-top: 20px;
        }
        </style>
    </head>
    <body>
        <h1 style="text-align: center;"><u>Teams Details</u></h1>
        <center>
            <div class="container">
        <?php 
            $con=new mysqli("localhost","root","","anil");
            session_start();
            $nemp=$_SESSION['nemp'];
            $nt=$_SESSION['nt'];
            $emp=[];
            $res=$con->query("select name,Sno from student");
            while($det=$res->fetch_assoc())
            {
                $emp[]=$det['name'];
                $sno[]=$det['Sno'];
            }

            $co=array_fill(0,$nemp,0);
            $m=1;
            if($nemp%$nt==0){
                for($i=0;$i<$nemp;$i++){
                    $x=rand(0,$nemp-1);
                    while($co[$x]!=0){
                        $x=rand(0,$nemp-1);
                    }
                    $co[$x]=1;
                    if ($i%($nemp/$nt)==0){ ?>
                        </div><br><br><div class="container">
                        <h1>Team <?php echo $m,"<br>"; ?></h1>
                        <?php echo $emp[$x],"<br>";
                        $m++;
                    }
                    else{
                        echo $emp[$x],"<br>";
                    }
                }
            }
            else{
                $y=$nemp-($nemp%$nt);
                for($i=0;$i<$y;$i++){
                    $x=rand(0,$y-1);
                    while($co[$x]!=0){
                        $x=rand(0,$y-1);
                    }
                    $co[$x]=1;
                    if ($i%($y/$nt)==0){ ?>
                        </div><br><br><div class="container">
                        <h1>Team <?php echo $m,"<br>"; ?></h1>
                        <?php echo $sno[$x],"\t",$emp[$x],"<br>";
                        $m++;
                    }
                    else{
                        echo $sno[$x],"\t",$emp[$x],"<br>";
                    }
                }
                $ta = array_fill(0, $nt, 0);
                $b = $nemp%$nt;
                for ($i = 0; $i < $b; $i++) {
                    do {
                        $x = rand(0, $b - 1);
                    } while ($co[$x+$y] != 0);
                    $co[$x+$y+1] = 1;

                    do {
                        $c = rand(0, $nt - 1);
                    } while ($ta[$c] != 0);
                    $ta[$c] = 1;
                    echo "</div><br><br>";
                    // Assuming $pl is an array of player names
                    echo "<div class='container'><h1>Team " . ($c + 1) . "</h1> \n " . $emp[$x+$y],$sno[$x+$y],"\t",  "\n";

                }
                session_start();
                $_SESSION['nteams']=$c;
                echo '<h1> $_SESSION["nteams"] </h1>';

            }
        ?>
        </div>
        </center>
    </body>
</html>