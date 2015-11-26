<?php
    $dbHost="192.168.0.108"; //on MySql
    $dbXeHost="192.168.0.108/XE"; 
    $dbUsername="test";
    $dbPassword="parole";
    
    $ielaIsEmpty = false;
    $adreseIsUnique = true;
    
    $con = mysqli_connect($dbHost, $dbUsername, $dbPassword);
    if(!$con){
        exit('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    }
    mysqli_set_charset($con, 'utf-8');
    mysqli_select_db($con, "gramatnica");
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if($_POST["iela"] == "")
            $iela = true;

        $iela = mysqli_real_escape_string($con, $_POST["iela"]);
        $indekss = mysqli_real_escape_string($con, $_POST["indekss"]);
        $checkVariables = mysqli_query($con, "SELECT Iela, Indekss FROM Adrese WHERE Indekss='" . $indekss . "' AND "
                . "Iela='" . $iela . "'");
        $checkVarNum =  mysqli_num_rows($checkVariables);
        if($checkVarNum){
            $adreseIsUnique = false;
        }
        
        if($adreseIsUnique && !$ielaIsEmpty){
            mysqli_query($con, "INSERT Adrese(Iela, Indekss) VALUES('" . $iela . "', '" . 
                    $indekss . "')" );
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
        <title>Pievienot adrese</title>
        <link rel="stylesheet" href="style.css" type="text/css" media="all" />
    </head>
    <body>
        <form action="newAdress.php" method="POST" enctype="multipart/form-data">
            <div class="gramataForm">
                Iela:<input type="text" name="iela" value="" /><br/>
                Indekss:<select name="indekss">
                    <?php

                        $result = mysqli_query($con, "SELECT Indekss FROM Indekss");

                        while($row = mysqli_fetch_array($result)){
                            echo "<option value='" . $row['Indekss'] . "'>" . $row['Indekss'] . "</option>";
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

