
<?php
    /** database connection credentials */
    $dbHost="192.168.0.108"; //on MySql
    $dbXeHost="192.168.0.108/XE"; 
    $dbUsername="test";
    $dbPassword="parole";
    
    /** other variables */
    $ISBNisUnique = true;
    $ISBNisEmpty = false;
    $nosaukumsIsEmpty = false;
    $aprakstsIsEmpty = false;
    $lppIsEmpty = false;
    $bildeIsEmpty = false;
    $gadsIsEmpty = false;
    $vakaTipsIsEmpty = false;
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($_POST["isbn"]=="" && $_POST["nosaukums"]=="" &&
                $_POST["apraksts"]=="" && $_POST["lpp"]=="" && 
                $_FILES["bilde"]["name"]=="" && $_POST["gads"]=="" &&
                $_POST["vakaTips"]=="") {
            $ISBNisEmpty = true;
            $nosaukumsIsEmpty = false;
            $aprakstsIsEmpty = true;
            $lppIsEmpty = true;
            $bildeIsEmpty = true;
            $gadsIsEmpty = true;
            $vakaTipsIsEmpty = true;
        }
        
        $con = mysqli_connect($dbHost, $dbUsername, $dbPassword);
        if (!$con) {
            exit('Connect Error (' . mysqli_connect_errno() . ') '
                . mysqli_connect_error());
        }
        mysqli_set_charset($con, 'utf-8');
        
        mysqli_select_db($con, "gramatnica");
        $isbn = mysqli_real_escape_string($con, $_POST["isbn"]);
        $isbn_db = mysqli_query($con, "SELECT ISBN FROM Gramata WHERE ISBN='" . $isbn . "'");
        $isbnNum = mysqli_num_rows($isbn_db);
        if ($isbnNum) {
            $ISBNisUnique = false;
        }
        
        if (!$ISBNisEmpty && $ISBNisUnique && !$nosaukumsIsEmpty
                && !$aprakstsIsEmpty && !$lppIsEmpty
                && !$bildeIsEmpty && !$gadsIsEmpty 
                && !$vakaTipsIsEmpty) {
            $isbn = mysqli_real_escape_string($con, $_POST['isbn']);
            $nosaukums = mysqli_real_escape_string($con, $_POST['nosaukums']);
            $apraksts = mysqli_real_escape_string($con, $_POST['apraksts']);
            $lpp = mysqli_real_escape_string($con, $_POST['lpp']);
            
            $imageData = mysqli_real_escape_string($con, file_get_contents($_FILES["bilde"]["tmp_name"]));
            $gads = mysqli_real_escape_string($con, $_POST['gads']);
            $vakaTips = mysqli_real_escape_string($con, $_POST['vakaTips']);
            mysqli_select_db($con, "gramatnica");
            mysqli_query($con, "INSERT Gramata (ISBN, Nosaukums, Apraksts, Lpp, "
                    . "Bilde, Gads, Vaka_tips) VALUES ('" . $isbn . "', '" .
                    $nosaukums . "', '" . $apraksts . "', '" .
                    $lpp . "', '" . $imageData . "', '" . $gads . "', '" .
                    $vakaTips . "')");
            mysqli_free_result($isbn_db);
            mysqli_close($con);
            header('Location: admin.php');
            exit;
        }
    } 
    
    
        
?>


<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title>Jauna grāmata</title>
        <link rel="stylesheet" href="style.css" type="text/css" media="all" />
    </head>
    <body>
      <form action="addNewGramata.php" method="POST" enctype="multipart/form-data">
            <div class="gramataForm">
                ISBN: <input type="text" name="isbn"/><br/>
                <?php
                    if($ISBNisEmpty){
                        echo("Ievadiet ISBN!");
                        echo("<br/>");
                    }
                ?>
                Autors:<select name="autorList">
                    <option></option>
                    <option></option>
                </select><br/>
                Nodala:<select name="nodalaList">
                    <option></option>
                    <option></option>
                </select><br/>
                Zanrs:<select name="zanrsList">
                    <option></option>
                    <option></option>
                </select><br/>
                Nosaukums: <input type="text" name="nosaukums"/><br/>
                Apraksts: <textarea name="apraksts" rows="10" cols="60" id="nosaukumsArea"></textarea><br/>
                Lpp: <input type="text" name="lpp" value="" /><br/>
                Bilde:<input type="file" name="bilde" value="" /><br/>
                Gads:<input type="text" name="gads" value="" /><br/>
                Vaka tips:<input type="text" name="vakaTips" value="" /><br/>
                <a href="admin.php">Atpakaļ</a>
                <input type="submit" value="Registret"/>
            </div>
      </form>
      
     </body>
</html>