<?php
    $dbHost="192.168.0.108"; //on MySql
    $dbXeHost="192.168.0.108/XE"; 
    $dbUsername="test";
    $dbPassword="parole";
    
    $nodalaIsEmpty = false;
    $nodalaIsUnique = true;
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if($_POST["nodala"] == ""){
            $nodalaIsEmpty = true;
        }
    
    $con = mysqli_connect($dbHost, $dbUsername, $dbPassword);
    if(!$con){
        exit('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    }
    mysqli_set_charset($con, 'utf-8');
    
    mysqli_select_db($con, "gramatnica");
    $nodala = mysqli_real_escape_string($con, $_POST["nodala"]);
    $checkNodala = mysqli_query($con, "SELECT Nosaukums FROM Nodala WHERE Nosaukums='" . $nodala . 
                "'");
    $checkNodalaNum =  mysqli_num_rows($checkNodala);
    if($checkNodalaNum){
        $nodalaIsUnique = false;
    }
    
    if($nodalaIsUnique && !$nodalaIsEmpty){
        mysqli_query($con, "INSERT Nodala(Nosaukums) VALUES('" . $nodala . "')");
        mysqli_free_result($checkNodala);
        mysqli_close($con);
        header('Location: admin.php');
        exit;
    }
    
    }
?>

<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title>Pievienot nodaļu</title>
        <link rel="stylesheet" href="style.css" type="text/css" media="all" />
    </head>
    <body>
        <form action="newDepartment.php" method="POST" enctype="multipart/form-data">
            <div class="gramataForm">
                Nodaļa:<input type="text" name="nodala"/><br/>
                <a href="admin.php">Atpakaļ</a>
                <input type="submit" value="Reģistrēt"/>
            </div>
        </form>
    </body>
</html>
