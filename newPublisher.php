<?php
    $dbHost="192.168.0.108"; //on MySql
    $dbXeHost="192.168.0.108/XE"; 
    $dbUsername="test";
    $dbPassword="parole";
    
    $nosaukumsIsEmpty = false;
    $izdevniecibaIsUnique = true;
    
    $con = mysqli_connect($dbHost, $dbUsername, $dbPassword);
    if(!$con){
        exit('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    }
    mysqli_set_charset($con, 'utf-8');
    mysqli_select_db($con, "gramatnica");
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if($_POST["nosaukums"] == "")
            $nosaukumsIsEmpty = true;
        
        $nosaukums = mysqli_real_escape_string($con, $_POST['nosaukums']);
        $kInfo = mysqli_real_escape_string($con, $_POST['kinfo']);
        $checkVar = mysqli_query($con, "SELECT Nosaukums FROM Izdevnieciba WHERE ID_kInfo='" .
                $kInfo . "'");
        $checkVarNum = mysqli_num_rows($checkVar);
        if($checkVarNum)
            $izdevniecibaIsUnique = false;
        
        if(!$nosaukumsIsEmpty && $izdevniecibaIsUnique){
            mysqli_query($con, "INSERT Izdevnieciba(Nosaukums, ID_kInfo) VALUES('" .
                    $nosaukums . "', '" . $kInfo . "')");
            mysqli_free_result($checkVar);
            mysqli_close($con);
            header('Location: admin.php');
            exit;
        }
    }
?>

<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title>Pievienot izdevniecību</title>
        <link rel="stylesheet" href="style.css" type="text/css" media="all" />
    </head>
    <body>
        <form action="newPublisher.php" method="POST" enctype="multipart/form-data">
            <div class="gramataForm">
                Nosaukums:<input type="text" name="nosaukums"/><br/>
                Kontaktinformacija:<select name="kinfo">
                    <?php
                        $resultKinf = mysqli_query($con, "SELECT ki.Telefonnumurs,"
                                . " ki.Epasts, ki.ID_kInfo, adr.Iela, adr.Indekss"
                                . " FROM Kontaktinformacija ki JOIN Adrese adr ON ki.ID_Adrese=adr.ID_Adrese");
                                
                        while($row = mysqli_fetch_array($resultKinf)){
                            echo "<option value='" . $row['ID_kInfo'] . "'>" 
                                    . $row['Telefonnumurs'] . " / " . $row['Epasts'] . " / " . $row['Iela'] .
                                    " / " . $row['Indekss'] .
                                    "</option>";
                        }
                        mysqli_free_result($resultKinf);
                        
                    ?>
                </select><br/>
                <a href="admin.php">Atpakaļ</a>
                <input type="submit" value="Reģistrēt"/>
            </div>
        </form>
    </body>
</html>
