<?php
    $dbHost="192.168.0.108"; //on MySql
    $dbXeHost="192.168.0.108/XE"; 
    $dbUsername="test";
    $dbPassword="parole";
    
    $zanrsIsEmpty = false;
    $zanrsIsUnique = true;
    
    $con = mysqli_connect($dbHost, $dbUsername, $dbPassword);
    if(!$con){
        exit('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    }
    mysqli_set_charset($con, 'utf-8');
    mysqli_select_db($con, "gramatnica");
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if($_POST["zanrs"] == ""){
            $zanrsIsEmpty = true;
        }
        $zanrs = mysqli_real_escape_string($con, $_POST["zanrs"]);
        $checkZanrs = mysqli_query($con, "SELECT Nosaukums FROM Zanrs WHERE Nosaukums='" . $zanrs . 
                    "'");
        $checkZanrsNum =  mysqli_num_rows($checkZanrs);
        if($checkZanrsNum){
            $zanrsIsUnique = false;
        }
        
        if($zanrsIsUnique && !$zanrsIsEmpty){
            mysqli_query($con, "INSERT Zanrs(Nosaukums, ID_Nodala) VALUES('" . $zanrs . "', '" . 
                    $_POST['nodala'] . "')" );
            mysqli_free_result($checkZanrs);
            mysqli_close($con);
            header('Location: admin.php');
            exit;
        }
    }

?>

<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title>Pievienot žanru</title>
        <link rel="stylesheet" href="style.css" type="text/css" media="all" />
    </head>
    <body>
        <form action="newGenre.php" method="POST" enctype="multipart/form-data">
            <div class="gramataForm">
                Žanrs:<input type="text" name="zanrs"/><br/>
                Nodaļa:<select name="nodala">
                    <?php

                        $result = mysqli_query($con, "SELECT Nosaukums, ID_Nodala FROM Nodala");

                        while($row = mysqli_fetch_array($result)){
                            echo "<option value='" . $row['ID_Nodala'] . "'>" . $row['Nosaukums'] . "</option>";
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
