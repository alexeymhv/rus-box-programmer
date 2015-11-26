<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Admin Panel</title>
        <link rel="stylesheet" href="style.css" type="text/css" media="all" />
    </head>
    
    <body>
        <p>Grāmatas</p>
        <ul>
            <li><a href="addNewGramata.php">Pievienot grāmatu</a></li>
            <li><a href="gramataList.php">Gramatu saraksts</a></li>
            <li><a href="newAuthor.php">Pievienot autoru</a></li>
            <li><a href="newLang.php">Pievienot valodu</a></li>
            <li><a href="newDepartment.php">Pievienot nodaļu</a></li>
            <li><a href="newGenre.php">Pievienot žanru</a></li>
            <li><a href="newPublisher.php">Pievienot izdevniecību</a></li>
            <li><a href="newContact.php">Pievienot kontaktinformaciju</a></li>
            <li><a href="newAdress.php">Pievienot adrese</a></li>
            <li><a href="newIndekss.php">Pievienot indekse</a></li>
            <li><a href="newCountry.php">Pievienot valsti</a></li>
        </ul>
        <?php
        
        
        
//        $con = mysqli_connect("192.168.0.109:3306", "test", "parole");
//        if(!$con){
//            exit('Connect Error(' . mysqli_connect_errno() . ') '
//                .mysqli_connect_error());              
//        }
//        
//        mysqli_select_db($con, "gramatnica");
//        $user = mysqli_real_escape_string($con, htmlentities($_GET["userAdmin"]));
//        
//        $wisher = mysqli_query($con, "SELECT Vards, Uzvards, Login, Parole FROM Lietotajs WHERE ID_Lietotajs = '" 
//                . $user . "' ");
//        if(mysqli_num_rows($wisher) < 1){
//            exit("The person " . htmlentities($_GET["user"]) . " is not found");     
//        }
//        
//        $row = mysqli_fetch_row($wisher);
//        $wisherID = $row[0];
//        mysqli_free_result($wisher);

        ?>
<!--        <table border="black">
            <tr>
                <th>Vards</th>
                <th>Uzvards</th>
                <th>Login</th>
                <th>Parole</th>
                <?php
//                $wisher = mysqli_query($con, "SELECT Vards, Uzvards, Login, Parole FROM Lietotajs WHERE ID_Lietotajs = '" 
//                . $user . "' ");
//                while ($row = mysqli_fetch_array($wisher)) {
//                    echo "<tr><td>" . htmlspecialchars($row["Vards"], ENT_QUOTES, "UTF-8") . "</td>";
//                    echo "<td>" . htmlspecialchars($row["Uzvards"], ENT_QUOTES, "UTF-8") . "</td>";
//                    echo "<td>" . htmlentities($row["Login"]) . "</td>";
//                    echo "<td>" . htmlentities($row["Parole"]) . "</td></tr>\n";
//                }
//                mysqli_free_result($wisher);
//                mysqli_close($con);
                ?>
            </tr>
            
        </table>-->
    </body>
    
</html>