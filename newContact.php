<?php
    $dbHost="192.168.0.108"; //on MySql
    $dbXeHost="192.168.0.108/XE"; 
    $dbUsername="test";
    $dbPassword="parole";
    
    $telefonsIsEmpty = false;
    $epastsIsEmpty = false;
    $adreseIsUnique = true;
    
    $con = mysqli_connect($dbHost, $dbUsername, $dbPassword);
    if(!$con){
        exit('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    }
    mysqli_set_charset($con, 'utf-8');
    mysqli_select_db($con, "gramatnica");
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if($_POST["telefons"] == "")
            $telefonsIsEmpty = true;
        if($_POST["epasts"] == "")
            $epastsIsEmpty = true;
        $adrese = $_POST["adrese"];
        $adrese_explode = explode('|', $adrese);
        $indekss = mysqli_real_escape_string($con, $adrese_explode[0]);
        $iela = mysqli_real_escape_string($con, $adrese_explode[1]);
        $telefons = mysqli_real_escape_string($con, $_POST["telefons"]);
        $epasts = mysqli_real_escape_string($con, $_POST["epasts"]);
        $checkVariables = mysqli_query($con, "SELECT adr.Iela, adr.Indekss, k.Telefonnumurs, k.Epasts FROM Kontaktinformacija k "
                . "JOIN Adrese adr ON adr.ID_Adrese=k.ID_Adrese WHERE adr.Iela = '" . $iela . "' AND adr.Indekss = '" . $indekss .
                "' AND k.Telefonnumurs = '" . $telefons . "' AND k.Epasts = '" . $epasts . "'");
        $checkVarNum = mysqli_num_rows($checkVariables);
        if($checkVarNum){
            $adreseIsUnique = false;
        }
        if($adreseIsUnique && !$telefonsIsEmpty && !$epastsIsEmpty){
            $id_adrese_db = mysqli_query($con, "SELECT DISTINCT ID_Adrese FROM Adrese WHERE Iela='" . $iela . 
                                        "' AND Indekss='" . $indekss . "'");
            $id_adrese = mysqli_fetch_row($id_adrese_db);
            
            mysqli_query($con, "INSERT Kontaktinformacija(ID_Adrese, Telefonnumurs, Epasts) VALUES('" . $id_adrese[0] . "', '" . 
                    $telefons . "', '" . $epasts ."')" );
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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <title>Pievienot kontaktinformāciju</title>
        <link rel="stylesheet" href="style.css" type="text/css" media="all" />
    </head>
    <body>
        <form action="newContact.php" method="POST" enctype="multipart/form-data">
            <div class="gramataForm">
                Adrese:<select name="adrese">
                    <?php
                        $resultAdrese = mysqli_query($con, "SELECT Indekss, Iela FROM Adrese ORDER BY Indekss");

                        while($row = mysqli_fetch_array($resultAdrese)){
                            echo "<option value='" . $row['Indekss'] . "|" . $row['Iela'] . "'>" 
                                    . $row['Indekss'] . " " . $row['Iela'] . "</option>";
                        }
                        mysqli_free_result($resultAdrese);
                        
                    ?>
                </select><br/>
                Telefonnumurs:<input type="text" name="telefons" value="" /><br/>
                E-pasts:<input type="text" name="epasts" value="" /><br/>
                <a href="admin.php">Atpakaļ</a>
                <input type="submit" value="Reģistrēt"/>
            </div>
        </form>
    </body>
</html>
