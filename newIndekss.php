<?php
    $dbHost="192.168.0.108"; //on MySql
    $dbXeHost="192.168.0.108/XE"; 
    $dbUsername="test";
    $dbPassword="parole";
    
    $indekssIsEmpty = false;
    $indekssIsUnique = true;
    
    $con = mysqli_connect($dbHost, $dbUsername, $dbPassword);
    if(!$con){
        exit('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    }
    mysqli_set_charset($con, 'utf-8');
    mysqli_select_db($con, "gramatnica");
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if($_POST["indekss"] == "")
            $indekssIsEmpty = true;

        $indekss = mysqli_real_escape_string($con, $_POST["indekss"]);
        $pilseta = mysqli_real_escape_string($con, $_POST["pilseta"]);
        $checkVariables = mysqli_query($con, "SELECT Indekss FROM Indekss WHERE Indekss='" . $indekss . "'");
        $checkVarNum =  mysqli_num_rows($checkVariables);
        if($checkVarNum){
            $indekssIsUnique = false;
        }
        
        if($indekssIsUnique && !$indekssIsEmpty){
            mysqli_query($con, "INSERT Indekss(Indekss, Pilseta) VALUES('" . $indekss . "', '" . 
                    $pilseta . "')" );
            mysqli_free_result($checkVariables);
            mysqli_close($con);
            header('Location: admin.php');
            exit;
        }
    }
    
?>

<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title>Pievienot indeksu</title>
        <link rel="stylesheet" href="style.css" type="text/css" media="all" />
    </head>
    <body>
        <form action="newIndekss.php" method="POST" enctype="multipart/form-data">
            <div class="gramataForm">
                Indekss:<input type="text" name="indekss" value="" /><br/>
                Pilseta:<select name="pilseta">
                    <?php

                        $result = mysqli_query($con, "SELECT Pilseta FROM Valsts");

                        while($row = mysqli_fetch_array($result)){
                            echo "<option value='" . $row['Pilseta'] . "'>" . $row['Pilseta'] . "</option>";
                        }
                        mysqli_free_result($result);
                    ?>
                </select><br/>
                <a href="admin.php">Atpakaļ</a>
                <input type="submit" value="Reģistrēt"/>
            </div>
        </form>
    </body>
</html>
