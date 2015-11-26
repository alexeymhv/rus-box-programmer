<?php
    $dbHost="192.168.0.108"; //on MySql
    $dbXeHost="192.168.0.108/XE"; 
    $dbUsername="test";
    $dbPassword="parole";
    
    $valodaIsEmpty = false;
    $valodaIsUnique = true;
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if($_POST["valoda"] == ""){
            $valodaIsEmpty = true;
        }
    
    $con = mysqli_connect($dbHost, $dbUsername, $dbPassword);
    if(!$con){
        exit('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    }
    mysqli_set_charset($con, 'utf-8');
    
    mysqli_select_db($con, "gramatnica");
    $valoda = mysqli_real_escape_string($con, $_POST["valoda"]);
    $checkValoda = mysqli_query($con, "SELECT Nosaukums FROM Valoda WHERE Nosaukums='" . $valoda . 
                "'");
    $checkValodaNum =  mysqli_num_rows($checkValoda);
    if($checkValodaNum){
        $valodaIsUnique = false;
    }
    
    if($valodaIsUnique && !$valodaIsEmpty){
        mysqli_query($con, "INSERT Valoda(Nosaukums) VALUES('" . $valoda . "')");
        mysqli_free_result($checkValoda);
        mysqli_close($con);
        header('Location: admin.php');
        exit;
    }
    
    }
?>

<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title>Pievienot valodu</title>
        <link rel="stylesheet" href="style.css" type="text/css" media="all" />
    </head>
    <body>
        <form action="newLang.php" method="POST" enctype="multipart/form-data">
            <div class="gramataForm">
                Valoda:<input type="text" name="valoda"/><br/>
                <a href="admin.php">Atpakaļ</a>
                <input type="submit" value="Reģistrēt"/>
            </div>
        </form>
    </body>
</html>
