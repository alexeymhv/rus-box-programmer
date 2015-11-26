<?php
    session_start();
    $dbHost="192.168.0.108"; //on MySql
    $dbXeHost="192.168.0.108/XE"; 
    $dbUsername="test";
    $dbPassword="parole";
    
    $vardsIsEmpty = false;
    $uzvardsIsEmpty = false;
    $autorsIsUnique = true;
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if($_POST["vards"] == ""){
            $vardsIsEmpty = true;
        }
        if($_POST["uzvards"] == ""){
            $uzvardsIsEmpty = true;
        }
        
        $con = mysqli_connect($dbHost, $dbUsername, $dbPassword);
        if(!$con){
            exit('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
        }
        mysqli_set_charset($con, 'utf-8');

        mysqli_select_db($con, "gramatnica");
        $vards = mysqli_real_escape_string($con, $_POST["vards"]);
        $uzvards = mysqli_real_escape_string($con, $_POST["uzvards"]);
        $checkAutors = mysqli_query($con, "SELECT Vards, Uzvards FROM Autors WHERE Vards='" . $vards . 
                "' AND Uzvards='" . $uzvards . "'");
        $checkAutorsNum =  mysqli_num_rows($checkAutors);
        if($checkAutorsNum){
            $autorsIsUnique = false;
        }

        if(!$vardsIsEmpty && !$uzvardsIsEmpty && $autorsIsUnique){
            mysqli_query($con, "INSERT Autors(Vards, Uzvards) VALUES ('" . $vards . "', '" . $uzvards . "')");
            mysqli_free_result($checkAutors);
            mysqli_close($con);
            header('Location: admin.php');
            exit;
        }  
    }
    
?>

<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title>Pievienot autoru</title>
        <link rel="stylesheet" href="style.css" type="text/css" media="all" />
    </head>
    <body>
        <form action="newAuthor.php" method="POST" enctype="multipart/form-data">
            <div class="gramataForm">
                Vārds:<input type="text" name="vards" value="" /><br/>
                Uzvārds:<input type="text" name="uzvards" value="" /><br/>
                <a href="admin.php">Atpakaļ</a>
                <input type="submit" value="Reģistrēt"/>
            </div>
        </form>
    </body>
</html>

