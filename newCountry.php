<?php
    $dbHost="192.168.0.108"; //on MySql
    $dbXeHost="192.168.0.108/XE"; 
    $dbUsername="test";
    $dbPassword="parole";
    
    $pilsetaIsEmpty = false;
    $pilsetaIsUnique = true;
    $valstsIsEmpty = false;
    
    $con = mysqli_connect($dbHost, $dbUsername, $dbPassword);
    if(!$con){
        exit('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    }
    mysqli_set_charset($con, 'utf-8');
    mysqli_select_db($con, "gramatnica");
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if($_POST["pilseta"] == "")
            $pilsetaIsEmpty = true;
        if($_POST["valsts"] == "")
            $valstsIsEmpty = true;

        $pilseta = mysqli_real_escape_string($con, $_POST["pilseta"]);
        $valsts = mysqli_real_escape_string($con, $_POST["valsts"]);
        $checkVariables = mysqli_query($con, "SELECT Pilseta, Valsts FROM Valsts WHERE Pilseta='" . $pilseta . 
                    "' AND Valsts='" . $valsts . "'");
        $checkVarNum =  mysqli_num_rows($checkVariables);
        if($checkVarNum){
            $pilsetaIsUnique = false;
        }
        
        if($pilsetaIsUnique && !$pilsetaIsEmpty && !$valstsIsEmpty){
            mysqli_query($con, "INSERT Valsts(Pilseta, Valsts) VALUES('" . $pilseta . "', '" . 
                    $valsts . "')" );
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
        <title>Pievienot valsts</title>
        <link rel="stylesheet" href="style.css" type="text/css" media="all" />
    </head>
    <body>
        <form action="newCountry.php" method="POST" enctype="multipart/form-data">
            <div class="gramataForm">
                Pilseta:<input type="text" name="pilseta" value="" /><br/>
                Valsts:<input type="text" name="valsts" value="" /><br/>
                <a href="admin.php">Atpakaļ</a>
                <input type="submit" value="Reģistrēt"/>
            </div>
        </form>
    </body>
</html>
