<?php
        session_start();
        $dbHost="192.168.0.108"; //on MySql
        $dbXeHost="192.168.0.108/XE"; 
        $dbUsername="test";
        $dbPassword="parole";
        
        $con = mysqli_connect($dbHost, $dbUsername, $dbPassword);
        if(!$con){
            exit('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
        }
        mysqli_set_charset($con, 'utf-8');
        mysqli_select_db($con, "gramatnica");
?>

<html>
    <head>
         <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title>User Panel</title>
        <link rel="stylesheet" href="style.css" type="text/css" media="all" />
    </head>
    
    <body>
<!--        <input type = "submit" value = "Apskatit grāmatas" id = "showGramatasButton" />-->
        <div class="userPanelTitle">
            <h2 style="font-size: 60px;">Laipni lūdzam Alex Grāmatnīcā!</h2>
        </div>
        <div class="upMenuList">
            <ul>
                <li><a href="user.php?">Sākums</a></li>
                <li><a href="user.php?page=1">Grāmatas</a></li>
                <li><a href="user.php?page=2">Mani pasutijumi</a></li>
                <li><a href="user.php?page=3">Administratoru panele</a></li>
            </ul>
            <hr size="3">
        </div>
        <?php
            if(!isset($_GET['page'])){
                if(!isset($_SESSION['shopping_cart'])){
                    $_SESSION['shopping_cart'] = array();    
                }
                //Empty cart
                if(isset($_GET['empty_cart'])){
                    $_SESSION['shopping_cart'] = array();
                    header('Location: user.php?page=2');
                }
                
                echo "<p style=\"text-align: center; font-size: 30px; font-weight: bold\">Top 3 grāmatas</p>";
                $result = mysqli_query($con, "SELECT * FROM top3");
                echo "<table  style=\"text-align:center; width:100%; margin-top:10px; \">";
                echo "<tr>";
                while($row = mysqli_fetch_array($result)){
                    echo "<td><img width=320 height=480 src='data:image/jpeg;base64," . base64_encode( $row["Bilde"] ) . "'/></td>";
                }
                echo "</tr>";

                $result = mysqli_query($con, "SELECT * FROM top3");
                echo "<tr>";
                while($row = mysqli_fetch_array($result)){
                    echo "<td>Nousakums: " . $row["Nosaukums"] . "</td>";
                }

                $result = mysqli_query($con, "SELECT * FROM top3");
                echo "</tr>";
                echo "<tr>";
                while($row = mysqli_fetch_array($result)){
                    echo "<td>Autors: " . $row["Vards"] .  " " . $row["Uzvards"] . "</td>";
                }

                $result = mysqli_query($con, "SELECT * FROM top3");
                echo "</tr>";
                echo "<tr>";
                while($row = mysqli_fetch_array($result)){
                    echo "<td>Reitings: " . $row["Reitings"] . "</td>";
                }
                echo "</tr>";
                echo "</table>";
            }
            else{
                
                $result = mysqli_query($con, "SELECT DISTINCT Nodala FROM Zanrs");
                $idx = 10;
                while($row = mysqli_fetch_array($result)){
                    $nodalaArr[$idx-9] = array(
                        'name' => '' . $row["Nodala"] . '',
                    );
                    $idx++;
                }
                
                
                if($_GET['page'] == 1){
                        echo "<div class=\"nodalaList\">";
                        echo "<ul>";
                        foreach($nodalaArr as $id => $nodala){
                            $id = $id + 9;
                            echo "<li><a href=\"user.php?page=" . $id . "\">" . $nodala['name'] . "</a></li>";
                        }
                        echo "</ul>";
                        echo "</div>";
                }
                
                
                if($_GET['page'] == 2){
                    if(!empty($_SESSION['shopping_cart'])){
                        echo "<table style=\"width:100%;\">";
                        echo "<tr>
                        <th style=\"text-align:center; padding:5px; font-size:35px;\"><h3></h3></th>
                        <th style=\"text-align:center; padding:5px; font-size:35px;\"><h3>Iepirkumu groza</h3></th>
                        <th style=\"text-align:center; padding:5px; font-size:35px;\"><h3></h3></th>
                        </tr>
                        <tr>";
                        echo "<td style=\"width:25%;\">";
                        echo "<a href=\"user.php?empty_cart=1\" style=\"font-size:25px; margin-left:10px;\">Iztukšot grozu</a><br/><br/>";
                    }
                    else{
                        echo "<h3 style=\"text-align:center; font-size:35px;\">Iepirkumu groza</h3>";
                    }
                    
                    if(empty($_SESSION['shopping_cart'])){
                        echo "<p style=\"font-size:25px;\">Grozs ir tukšs.</p><br/>";
                    }
                    else{
                        echo "<form action=\"user.php?page=2\" method=\"POST\" enctype=\"multipart/form-data\">";
                        $summa = 0;
                        echo"</td>";
                        echo "<td style=\"width:50%;\">";
                        echo "<div class=\"pasutijumuApraksts\">";
                        echo "<table>
                                <tr>
                                      <th></th>
                                      <th>Nosaukums</th>
                                      <th>Cena</th>
                                      <th>Skaits</th>
                                      <th>Summa</th>
                                </tr>";
                        
                        foreach($_SESSION['shopping_cart'] as $id => $product){
                            $gramataQuery = mysqli_query($con, "SELECT * FROM Gramata WHERE ISBN = '". $product['gr_isbn'] ."'");
                            while($gramataRow = mysqli_fetch_array($gramataQuery))   {
                                echo"
                                     <tr>
                                        <td>
                                            <img width=160 height=240 src='data:image/jpeg;base64,".base64_encode( $gramataRow["Bilde"] )."'/>
                                        </td>
                                        <td style=\"vertical-align:middle\">";
                                            echo "<span>". $gramataRow['Nosaukums'] ."</span>
                                        </td>
                                        <td style=\"vertical-align:middle\">";
                                            echo "<span>". $gramataRow['Cena'] ."&euro;</span>
                                        </td>
                                        <td style=\"vertical-align:middle\">";
                                            echo "<span>". $product['quantity'] ."</span>
                                        </td>
                                        <td style=\"vertical-align:middle\">";
                                            $summa += $gramataRow['Cena']*$product['quantity'];
                                            echo "<span>". $gramataRow['Cena']*$product['quantity'] ."&euro;</span>
                                        </td>
                                        </tr>";
                            }
                        }
                        echo "</table>";
                        echo "</div>";
                        echo"</td>";
                        echo"<td style=\"width:25%;\">";
                        echo "<p style=\"font-size:25px; font-weight:bold;\">Pasūtījuma noformēšana</p><br/>";
                        echo "<p style=\"font-size:25px;\">Lietotājvārds</p>
                              <input type=\"text\" name=\"login\" value=\"\" style=\"border: solid 1px; height:28px; font-size:20px;\"/>
                              <p style=\"font-size:25px;\">Parole</p>
                              <input type=\"password\" name=\"parole\" value=\"\" style=\"border: solid 1px; height:28px;font-size:20px;\"/>
                              <p style=\"font-size:25px;\">Vārds</p>
                              <input type=\"text\" name=\"vards\" value=\"\" style=\"border: solid 1px; height:28px; font-size:20px;\"/>
                              <p style=\"font-size:25px;\">Uzvārds</p>
                              <input type=\"text\" name=\"uzvards\" value=\"\" style=\"border: solid 1px; height:28px; font-size:20px;\"/>
                              <p style=\"font-size:25px;\">Epasts</p>
                              <input type=\"text\" name=\"epasts\" value=\"\" style=\"border: solid 1px; height:28px; font-size:20px;\"/>
                              <p style=\"font-size:25px;\">Telefonnumurs</p>
                              <input type=\"text\" name=\"telefons\" value=\"\" style=\"border: solid 1px; height:28px; font-size:20px;\"/>
                              <p style=\"font-size:25px;\">Iela</p>
                              <input type=\"text\" name=\"iela\" value=\"\" style=\"border: solid 1px; height:28px; font-size:20px;\"/>
                              <p style=\"font-size:25px;\">Pilsēta</p>
                              <input type=\"text\" name=\"pilseta\" value=\"\" style=\"border: solid 1px; height:28px; font-size:20px;\"/>
                              <p style=\"font-size:25px;\">Indekss</p>
                              <input type=\"text\" name=\"indekss\" value=\"\" style=\"border: solid 1px; height:28px; font-size:20px;\"/>
                              <p style=\"font-size:25px;\">Valsts</p>
                              <input type=\"text\" name=\"valsts\" value=\"\" style=\"border: solid 1px; height:28px; font-size:20px;\"/>
                              ";
                        echo "<p style=\"font-size:25px; font-weight:bold;\">Kopa: " . $summa . "&euro;</p>";
                        echo "<br/><input type=\"submit\" name=\"registret\" value=\"Pasūtīt\" style=\"font-size:20px;width:246px;height:30px;\"/>";
                        echo"</td>";
                        
                    
                    echo "</tr>";
                        echo "</table>";
                    echo "</form>";
                        
                    if($_SERVER["REQUEST_METHOD"] == "POST"){
                        $lietotajvardsEmpty = false;
                        $paroleEmpty = false;
                        $vardsEmpty = false;
                        $uzvardsEmpty = false;
                        $epastsEmpty = false;
                        $telefonnumursEmpty = false;
                        $ielaEmpty = false;
                        $pilsetaEmpty = false;
                        $indekssEmpty = false;
                        $valstsEmpty = false;
                        
                        if($_POST["login"] == "")
                            $lietotajvardsEmpty = true;
                        if($_POST["parole"] == "")
                            $paroleEmpty = true;
                        if($_POST["vards"] == "")
                            $vardsEmpty = true;
                        if($_POST["uzvards"] == "")
                            $uzvardsEmpty = true;
                        if($_POST["epasts"] == "")
                            $epastsEmpty = true;
                        if($_POST["telefons"] == "")
                            $telefonnumursEmpty = true;
                        if($_POST["iela"] == "")
                            $ielaEmpty = true;
                        if($_POST["pilseta"] == "")
                            $pilsetaEmpty = true;
                        if($_POST["indekss"] == "")
                            $indekssEmpty = true;
                        if($_POST["valsts"] == "")
                            $valstsEmpty = true;
                        
                        $lietotajvards = $_POST["login"];
                        $parole = $_POST["parole"];
                        $vards = $_POST["vards"];
                        $uzvards = $_POST["uzvards"];
                        $epasts = $_POST["epasts"];
                        $telefons = $_POST["telefons"];
                        $iela = $_POST["iela"];
                        $pilseta = $_POST["pilseta"];
                        $indekss = $_POST["indekss"];
                        $valsts = $_POST["valsts"];
                        
                        if(!$lietotajvardsEmpty && !$paroleEmpty && !$vardsEmpty && !$uzvardsEmpty 
                                && !$epastsEmpty && !$telefonnumursEmpty && !$ielaEmpty && !$pilsetaEmpty &&
                                !$indekssEmpty && !$valstsEmpty){
                            
                            $checkVar = mysqli_query($con, "SELECT Pilseta FROM Valsts WHERE Pilseta='". $pilseta ."'");
                            $checkVarNum = mysqli_num_rows($checkVar);
                            if($checkVarNum == 0){
                                $query = mysqli_query($con, "INSERT INTO Valsts(Pilseta, Valsts) VALUES('"
                                        . $pilseta . "', '". $valsts ."')");
                            }
                            
                            mysqli_free_result($checkVar);
                            $checkVar = mysqli_query($con, "SELECT Indekss FROM Indekss WHERE Indekss='". $indekss ."'");
                            $checkVarNum = mysqli_num_rows($checkVar);
                            if($checkVarNum == 0){
                                $query = mysqli_query($con, "INSERT INTO Indekss(Indekss, Pilseta) VALUES('"
                                            . $indekss ."', '". $pilseta ."')");
                            }
                            
                            mysqli_free_result($checkVar);
                            $checkVar = mysqli_query($con, "SELECT Iela FROM Adrese WHERE Iela='".
                                    $iela ."' AND Indekss='". $indekss ."'");
                            $checkVarNum = mysqli_num_rows($checkVar);
                            if($checkVarNum == 0){
                                $query = mysqli_query($con, "INSERT INTO Adrese(Iela, Indekss) VALUES('"
                                                . $iela ."', '". $indekss . "')");
                            }
                            
                            $checkVar = mysqli_query($con, 
                                    "SELECT Telefonnumurs, Epasts FROM Kontaktinformacija WHERE Telefonnumurs='".
                                    $telefons ."' AND Epasts='". $epasts ."'");
                            $checkVarNum = mysqli_num_rows($checkVar);
                            if($checkVarNum == 0){
                                $query = mysqli_query($con, "SELECT ID_Adrese FROM Adrese WHERE Iela='".
                                        $iela ."' AND Indekss='". $indekss ."'");
                                $row = mysqli_fetch_array($query);
                                $id_adrese = $row["ID_Adrese"];
                                mysqli_free_result($query);

                                $query = mysqli_query($con, 
                                        "INSERT INTO Kontaktinformacija(ID_Adrese, Telefonnumurs, Epasts) VALUES('"
                                        . $id_adrese ."', '". $telefons ."', '". $epasts ."')");
                            }
                            
                            $checkVar = mysqli_query($con, "SELECT * FROM Lietotajs WHERE Login = '". $lietotajvards ."'");
                            $checkVarNum = mysqli_num_rows($checkVar);
                            if($checkVarNum == 0){
                                $query = mysqli_query($con, "SELECT ID_kInfo FROM Kontaktinformacija WHERE "
                                        . "Telefonnumurs='". $telefons ."' AND Epasts='". $epasts ."'");
                                $row = mysqli_fetch_array($query);
                                $id_kInfo = $row["ID_kInfo"];
                                mysqli_free_result($query);

                                $query = mysqli_query($con, 
                                        "INSERT INTO Lietotajs(Vards, Uzvards, ID_Kontaktinformacija,"
                                        . " Login, Parole) VALUES('". $vards ."', '". $uzvards ."', '".
                                        $id_kInfo ."', '". $lietotajvards ."', '". $parole ."')");
                            }
                            
                            $today = getdate();                               
                            $query = mysqli_query($con, "SELECT ID_Lietotajs FROM Lietotajs WHERE "
                                    . "Login='". $lietotajvards ."'");
                            $row = mysqli_fetch_array($query);
                            $id_lietotajs = $row["ID_Lietotajs"];
                            $datums = $today["year"] ."-". $today["mon"] ."-". $today["mday"];

                            $query = mysqli_query($con, 
                                    "INSERT INTO Pasutijums(ID_Lietotajs, Datums) VALUES('".
                                    $id_lietotajs ."', '". $datums ."')");
                            
                            $query = mysqli_query($con, "SELECT ID_Pasutijums FROM Pasutijums WHERE "
                                    . "ID_Lietotajs='". $id_lietotajs ."' AND Datums='". $datums ."'");
                            $row = mysqli_fetch_array($query);
                            $id_pasutijums = $row["ID_Pasutijums"];
                            
                            foreach($_SESSION['shopping_cart'] as $id => $product){
                                $query = mysqli_query($con, "INSERT INTO Pasutijums_Prece(ID_Pasutijums, "
                                    . "ISBN, Daudzums) VALUES('". $id_pasutijums ."', '".
                                    $product["gr_isbn"] ."', '". $product["quantity"] ."')");
                            }
                            
                            echo "<script>window.location.assign(\"user.php?empty_cart=1\")</script>";
                        }
                        else{
                            echo "<h3 style=\"color:red; text-align:center; margin-top:10px; \">Jāaizpilda visus laukus!</h3>";
                        }

                        }
                    }
                }
                
                if($_GET['page'] == 3){
                    echo "<div class=\"adminPanelList\">";
                    echo "<ul>";
                        echo "
                            <li><a href=\"user.php?page=3&admin_menu=1\">Pasūtījumi</a></li>
                            <li><a href=\"user.php?page=4&admin_menu=2\">Datu pārvaldīšana</a></li>
                                
                            ";
                    echo "</ul>";
                    echo "</div>";
                }
                
                if($_GET['page'] == 3 && isset($_GET['admin_menu']) == 1){
                    $query = mysqli_query($con, "SELECT * FROM pasutijumu_info ORDER BY pasutijumu_info.Datums ASC");
                    
                    echo "<div class=\"admpasutijumuApraksts\">
                            <table>
                                <tr>
                                    <th>
                                        Datums
                                    </th>
                                    <th>
                                        ISBN
                                    </th>
                                    <th>
                                        Nosaukums
                                    </th>
                                    <th>
                                        Lietotajs
                                    </th>
                                    <th>
                                        E-pasts
                                    </th>
                                    <th>
                                        Adrese
                                    </th>
                                    <th>
                                        Cena
                                    </th>
                                    <th>
                                        Daudzums
                                    </th>
                                    <th>
                                        Summa
                                    </th>
                                </tr>";
                    while($row = mysqli_fetch_array($query)){
                        echo "  <tr><td>". $row["Datums"] ."</td>";
                        echo "      <td>". $row["ISBN"] ."</td>";
                        echo "      <td>". $row["Nosaukums"] ."</td>";
                        echo "      <td>". $row["login"] ."</td>";
                        echo "      <td>". $row["Epasts"] ."</td>";
                        echo "      <td>". $row["Iela"] .", " . $row["Indekss"] .", ". $row["Pilseta"] .", ". $row["Valsts"] ."</td>";
                        echo "      <td>". $row["Cena"] ."&euro;" ."</td>";
                        echo "      <td>". $row["Daudzums"] ."</td>";
                        echo "      <td>". $row["Summa"] ."&euro;" ."</td>";
                        echo "  </tr>";
                    }
                    echo "</table>";
                }
                
                if($_GET['page'] == 4 && isset($_GET['admin_menu']) == 2){
                    echo "<div class=\"adminPanelList\">";
                    echo "<ul>";
                        echo "
                            <li><a href=\"user.php?page=5&admin_menu=2&data_reg=1&gramatas=1\">Gramatas</a></li>
                            <li><a href=\"user.php?page=4&admin_menu=2&data_reg=2\">Visas izdevniecības</a></li>
                            <li><a href=\"user.php?page=4&admin_menu=2&data_reg=3\">Visi lietotāji</a></li>
                            ";
                    echo "</ul>";
                    echo "</div>";
                }
                if($_GET['page'] == 5 && isset($_GET['admin_menu']) == 2 && isset($_GET['data_reg']) == 1 && isset($_GET['gramatas']) == 1){
                    echo "<div class=\"adminPanelList\">";
                    echo "<ul>";
                        echo "
                            <li><a href=\"user.php?page=5&admin_menu=2&data_reg=1&gramatas=1&gr_menu=1\">Pievienot grāmatu</a></li>
                            <li><a href=\"user.php?page=5&admin_menu=2&data_reg=1&gramatas=1&gr_menu=2\">Visas grāmatas</a></li>
                            ";
                    echo "</ul>";
                    echo "</div>";
                }
                
                $data_reg = @$_GET["data_reg"];
                if($data_reg == 2){
                    echo "
                        <div class=\"pasutijumuApraksts\">
                            <table>
                                <tr>
                                    <th>Nosaukums</th>
                                    <th>Telefonnumurs</th>
                                    <th>E-pasts</th>
                                    <th>Adrese</th>
                                </tr>
                                ";
                    $result = mysqli_query($con, "SELECT * FROM izdevnieciba_info_complete");
                    while($row = mysqli_fetch_array($result)){
                        echo "
                            <tr>
                                <td>". $row["Nosaukums"] ."</td>
                                <td>". $row["Telefonnumurs"] ."</td>
                                <td>". $row["Epasts"] ."</td>
                                <td>". $row["Iela"] .", ". $row["Indekss"] .", ". $row["Pilseta"] .", ". $row["Valsts"] ."</td>
                            </tr>
                            ";
                    }
                           echo "</table>
                        </div>
                         "; 
                }
                
                if($data_reg == 3){
                    echo "
                        <div class=\"pasutijumuApraksts\">
                            <table>
                                <tr>
                                    <th>Vards</th>
                                    <th>Uzvards</th>
                                    <th>Login</th>
                                    <th>Parole</th>
                                    <th>Telefonnumurs</th>
                                    <th>E-pasts</th>
                                    <th>Adrese</th>
                                </tr>
                                ";
                    $result = mysqli_query($con, "SELECT * FROM lietotaji_info_complete");
                    while($row = mysqli_fetch_array($result)){
                                                echo "
                            <tr>
                                <td>". $row["Vards"] ."</td>
                                <td>". $row["Uzvards"] ."</td>
                                <td>". $row["Login"] ."</td>
                                <td>". $row["Parole"] ."</td>
                                <td>". $row["Telefonnumurs"] ."</td>
                                <td>". $row["Epasts"] ."</td>
                                <td>". $row["Iela"] .", ". $row["Indekss"] .", ". $row["Pilseta"] .", ". $row["Valsts"] ."</td>
                            </tr>
                            ";
                    }
                        echo "</table>
                        </div>
                         "; 
                }
                
                $gr_menu = @$_GET["gr_menu"];
                if($gr_menu == 1){
                    echo "
                         <div class=\"pasutijumuApraksts\">
                            <form action=\"user.php?page=5&admin_menu=2&data_reg=1&gramatas=1&gr_menu=1\" method=\"POST\" enctype=\"multipart/form-data\">
                                <div class=\"gramataForm\">
                                    <table>
                                        <tr>
                                            <td><p>ISBN</p></td>
                                            <td><input type=\"text\" name=\"isbn\"/></td>
                                        </tr>
                                        <tr>
                                            <td><p>Nosaukums</p></td>
                                            <td><input type=\"text\" name=\"nosaukums\"/></td>
                                        </tr>
                                        <tr>
                                            <td><p>Apraksts</p></td>
                                            <td><textarea name=\"apraksts\" rows=\"10\" cols=\"60\" id=\"aprakstsArea\"></textarea></td>
                                        </tr>
                                        <tr>
                                            <td><p>Autors</p></td>
                                            <td>
                                                <select name=\"autorList\" style=\"width:450px\">";
                                                 $result = mysqli_query($con, "SELECT * FROM Autors");
                                                 while($row = mysqli_fetch_array($result)){
                                                     echo "<option value='". $row["ID_Autors"] ."'>". $row["Vards"] ." ". $row["Uzvards"] ."</option>";
                                                 }
                                                 mysqli_free_result($result);
                                           echo "</select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p>Izdevniecība</p></td>
                                            <td>
                                                <select name=\"izdevniecibaList\" style=\"width:450px\">";
                                                  $result = mysqli_query($con, "SELECT ID_Izdevnieciba, Nosaukums FROM Izdevnieciba");
                                                  while($row = mysqli_fetch_array($result)){
                                                     echo "<option value='". $row["ID_Izdevnieciba"] ."'>". $row["Nosaukums"] ."</option>";
                                                  }
                                                  mysqli_free_result($result);
                                            echo "</select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p>Nodala - Zanrs</p></td>
                                            <td>
                                                <select name=\"nodalaList\" style=\"width:450px\">";
                                                $result = mysqli_query($con, "SELECT Zanrs, Nodala FROM Zanrs ORDER BY Nodala ASC");
                                                while($row = mysqli_fetch_array($result)){
                                                    echo "<option value='". $row["Zanrs"] ."'>". $row["Nodala"] ."-". $row["Zanrs"] ."</option>";
                                                }
                                          echo "</select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p>Valoda</p></td>
                                            <td><input type=\"text\" name=\"valoda\"/></td>
                                        </tr>
                                        <tr>
                                            <td><p>Lpp</p></td>
                                            <td><input type=\"text\" name=\"lpp\"/></td>
                                        </tr>
                                        <tr>
                                            <td><p>Bilde</p></td>
                                            <td><input type=\"file\" name=\"bilde\" value=\"\" /></td>
                                        </tr>
                                        <tr>
                                            <td><p>Gads</p></td>
                                            <td><input type=\"text\" name=\"gads\"/></td>
                                        </tr>
                                        <tr>
                                            <td><p>Vaka tips</p></td>
                                            <td><input type=\"text\" name=\"vaka_tips\"/></td>
                                        </tr>
                                        <tr>
                                            <td><p>Cena</p></td>
                                            <td><input type=\"text\" name=\"cena\"/></td>
                                        </tr>
                                        <tr>
                                            <td><p>Daudzums</p></td>
                                            <td><input type=\"text\" name=\"daudzums\"/></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td><input type=\"submit\" value=\"Saglabāt\"/></td>
                                        </tr>
                                    </table>
                                </div>
                            </form>
                         </div>";
                                          
                        $ISBNisUnique = true;
                        $lppIsInt = true;
                        $cenaIsInt = true;
                        $idAutorsIsInt = true;
                        $daudzumsIsInt = true;
                        
                        
                        if ($_SERVER["REQUEST_METHOD"] == "POST"){
                            $isbn = mysqli_real_escape_string($con, $_POST["isbn"]);
                            $isbn_db = mysqli_query($con, "SELECT ISBN FROM Gramata WHERE ISBN='" . $isbn . "'");
                            $isbnNum = mysqli_num_rows($isbn_db);
                            if ($isbnNum) {
                                $ISBNisUnique = false;
                            }
                            $lpp = mysqli_real_escape_string($con, $_POST['lpp']);
                            $id_autors = mysqli_real_escape_string($con, $_POST["autorList"]);
                            $cena = mysqli_real_escape_string($con, $_POST["cena"]);
                            $daudzums = mysqli_real_escape_string($con, $_POST["daudzums"]);

                            if(!is_numeric($lpp))
                                $lppIsInt = false;
                            if(!is_numeric($id_autors))
                                $idAutorsIsInt = false;
                            if(!is_numeric($cena))
                                $cenaIsInt = false;
                            if(!is_numeric($daudzums))
                                $daudzumsIsInt = false;
                            
                            if ($lppIsInt && $idAutorsIsInt && $cenaIsInt && 
                                    $daudzumsIsInt && $ISBNisUnique && !$_POST["isbn"]=="" && !$_POST["nosaukums"]=="" &&
                                !$_POST["apraksts"]=="" && !$_POST["lpp"]=="" && 
                                !$_FILES["bilde"]["name"]=="" && !$_POST["gads"]=="" &&
                                !$_POST["vaka_tips"]=="" && !$_POST["cena"]=="" && !$_POST["daudzums"] == ""){
                                    $isbn = mysqli_real_escape_string($con, $_POST['isbn']);
                                    $nosaukums = mysqli_real_escape_string($con, $_POST['nosaukums']);
                                    $apraksts = mysqli_real_escape_string($con, $_POST['apraksts']);
                                    $id_autors = mysqli_real_escape_string($con, $_POST["autorList"]);
                                    $id_izdevnieciba = mysqli_real_escape_string($con, $_POST["izdevniecibaList"]);
                                    $zanrs_nodala = mysqli_real_escape_string($con, $_POST["nodalaList"]);
                                    $valoda = mysqli_real_escape_string($con,$_POST["valoda"]);
                                    $lpp = mysqli_real_escape_string($con, $_POST['lpp']);
                                    $imageData = mysqli_real_escape_string($con, file_get_contents($_FILES["bilde"]["tmp_name"]));
                                    $gads = mysqli_real_escape_string($con, $_POST["gads"]);
                                    $vaka_tips = mysqli_real_escape_string($con, $_POST["vaka_tips"]);
                                    $cena = mysqli_real_escape_string($con, $_POST["cena"]);
                                    $daudzums = mysqli_real_escape_string($con, $_POST["daudzums"]);
                                    
                                    mysqli_select_db($con, "gramatnica");
                                    mysqli_query($con, "CALL add_gramata('".
                                            $isbn ."', '". $nosaukums ."', '". $apraksts ."', '". $id_autors ."', '". 
                                            $id_izdevnieciba ."', '". $zanrs_nodala ."', '". $valoda ."', '". $lpp ."', '". 
                                            $imageData ."', '". 
                                            $gads ."', '". $vaka_tips ."', '". $cena ."', '". $daudzums ."')");
                                    
                            }
                            
                        }
                    
                    
                }
                $gr_dati = @$_GET["gr_dati"];
                if($gr_menu == 2 && !$gr_dati){
                    $result = mysqli_query($con, "SELECT * FROM Gramata ORDER BY ISBN");
                    echo "
                        <form action=\"user.php?page=5&admin_menu=2&data_reg=1&gramatas=1&gr_menu=2\" method=\"POST\" enctype=\"multipart/form-data\">
                        <div class=\"pasutijumuApraksts\">
                            <table>
                                <tr>
                                    <th><h3>ISBN</h3>
                                    <th><h3>Nosaukums</h3>
                                    <th><h3>Cena</h3></th>
                                    <th><h3>Daudzums</h3></th>
                                    <th></th>
                                    <th></th>
                                </tr>";
                    while($row = mysqli_fetch_array($result)){
                        echo"
                                <tr>
                                    <td>".
                                        $row["ISBN"]
                                  ."</td>
                                    <td>".
                                        $row["Nosaukums"]
                                    ."</td>
                                    <td>".
                                        $row["Cena"]
                                    ."</td>
                                     <td>".
                                        $row["Skaits"]
                                     ."</td>
                                       <td><a href=\"user.php?page=5&admin_menu=2&data_reg=1&gramatas=1&gr_menu=2&gr_dati=". 
                                       $row["ISBN"] ."\">Dāti</a></td>
                                       <td>
                                       <input type=\"submit\" name=\"clicked[". $row["ISBN"] ."]\" value=\"Dzēst\" style=\"background: red; font-size:10px; font-weight:bold;\"/></td>        
                                </tr>";
                    }
                    echo " </table>
                        </div>
                        </form>
                         ";
                    if (isset($_POST["clicked"])){
                        mysqli_query($con, "CALL remove_gramata('". key($_POST["clicked"]) ."')");
                        mysqli_close($con);
                        echo "<script>window.location.assign(\"user.php?page=5&admin_menu=2&data_reg=1&gramatas=1&gr_menu=2\")</script>";
                        
                    }

                }
                
                
                if($gr_dati){
                    $dati = mysqli_query($con, "SELECT * FROM gramata_info_complete WHERE ISBN = '". $gr_dati ."'");
                    $datiRes = mysqli_fetch_array($dati);
                    echo "
                        <div class=\"pasutijumuApraksts\">
                            <form action=\"user.php?page=5&admin_menu=2&data_reg=1&gramatas=1&gr_menu=2&gr_dati=". $gr_dati
                            ."\" method=\"POST\" enctype=\"multipart/form-data\">
                                <div class=\"gramataForm\">
                                    <table>
                                        <tr>
                                            <td><p>ISBN</p></td>
                                            <td><input type=\"text\" name=\"isbn\" value=\"". $gr_dati ."\" readonly/></td>
                                        </tr>
                                        <tr>
                                            <td><p>Nosaukums</p></td>
                                            <td><input type=\"text\" name=\"nosaukums\" value=\"". $datiRes["Nosaukums"] ."\"/></td>
                                        </tr>
                                        <tr>
                                            <td><p>Apraksts</p></td>
                                            <td><textarea name=\"apraksts\" rows=\"10\" cols=\"60\" id=\"aprakstsArea\" text=\"\">". $datiRes["Apraksts"] ."</textarea></td>
                                        </tr>
                                        <tr>
                                            <td><p>Autors</p></td>
                                            <td>
                                                <select name=\"autorList\" style=\"width:450px\">";
                                                 $result = mysqli_query($con, "SELECT * FROM Autors");
                                                 while($row = mysqli_fetch_array($result)){
                                                     if($row["ID_Autors"] == $datiRes["ID_Autors"])
                                                         echo "<option value='". $row["ID_Autors"] ."' selected=\"selected\">". $row["Vards"] ." ". $row["Uzvards"] ."</option>";
                                                     else
                                                        echo "<option value='". $row["ID_Autors"] ."'>". $row["Vards"] ." ". $row["Uzvards"] ."</option>";
                                                 }
                                                 mysqli_free_result($result);
                                           echo "</select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p>Izdevniecība</p></td>
                                            <td>
                                                <select name=\"izdevniecibaList\" style=\"width:450px\">";
                                                  $result = mysqli_query($con, "SELECT ID_Izdevnieciba, Nosaukums FROM Izdevnieciba");
                                                  while($row = mysqli_fetch_array($result)){
                                                     if($row["ID_Izdevnieciba"] == $datiRes["ID_Izdevnieciba"])
                                                        echo "<option value='". $row["ID_Izdevnieciba"] ."' selected=\"selected\">". $row["Nosaukums"] ."</option>";
                                                     else
                                                         echo "<option value='". $row["ID_Izdevnieciba"] ."'>". $row["Nosaukums"] ."</option>";
                                                  }
                                                  mysqli_free_result($result);
                                            echo "</select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p>Nodala - Zanrs</p></td>
                                            <td>
                                                <select name=\"nodalaList\" style=\"width:450px\">";
                                                $result = mysqli_query($con, "SELECT Zanrs, Nodala FROM Zanrs ORDER BY Nodala ASC");
                                                while($row = mysqli_fetch_array($result)){
                                                    if($row["Zanrs"] == $datiRes["Zanrs"])
                                                        echo "<option value='". $row["Zanrs"] ."' selected = \"selected\">". $row["Nodala"] ."-". $row["Zanrs"] ."</option>";
                                                    else
                                                        echo "<option value='". $row["Zanrs"] ."'>". $row["Nodala"] ."-". $row["Zanrs"] ."</option>";
                                                }
                                          echo "</select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p>Valoda</p></td>
                                            <td><input type=\"text\" name=\"valoda\" value=\"". $datiRes["Valoda"] ."\"/></td>
                                        </tr>
                                        <tr>
                                            <td><p>Lpp</p></td>
                                            <td><input type=\"text\" name=\"lpp\" value=\"". $datiRes["Lpp"] ."\"/></td>
                                        </tr>
                                        <tr>
                                            <td><p>Bilde</p></td>
                                            <td>
                                                <img width=160 height=240 src='data:image/jpeg;base64,".base64_encode( $datiRes["Bilde"] )."'/>
                                                <input type=\"file\" name=\"bilde\" value=\"\"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p>Gads</p></td>
                                            <td><input type=\"text\" name=\"gads\" value=\"". $datiRes["Gads"] ."\"/></td>
                                        </tr>
                                        <tr>
                                            <td><p>Vaka tips</p></td>
                                            <td><input type=\"text\" name=\"vaka_tips\" value=\"". $datiRes["Vaka_tips"] ."\"/></td>
                                        </tr>
                                        <tr>
                                            <td><p>Cena</p></td>
                                            <td><input type=\"text\" name=\"cena\" value=\"". $datiRes["Cena"] ."\"/></td>
                                        </tr>
                                        <tr>
                                            <td><p>Daudzums</p></td>
                                            <td><input type=\"text\" name=\"daudzums\" value=\"". $datiRes["Skaits"] ."\"/></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td><input type=\"submit\" value=\"Saglabāt izmaiņas\"/></td>
                                        </tr>
                                    </table>
                                </div>
                            </form>
                        </div>
                         ";
                    $lppIsInt = true;
                    $cenaIsInt = true;
                    $idAutorsIsInt = true;
                    $daudzumsIsInt = true;
                        
                        
                    if ($_SERVER["REQUEST_METHOD"] == "POST"){
                        $lpp = mysqli_real_escape_string($con, $_POST['lpp']);
                        $id_autors = mysqli_real_escape_string($con, $_POST["autorList"]);
                        $cena = mysqli_real_escape_string($con, $_POST["cena"]);
                        $daudzums = mysqli_real_escape_string($con, $_POST["daudzums"]);

                        if(!is_numeric($lpp))
                            $lppIsInt = false;
                        if(!is_numeric($id_autors))
                            $idAutorsIsInt = false;
                        if(!is_numeric($cena))
                            $cenaIsInt = false;
                        if(!is_numeric($daudzums))
                            $daudzumsIsInt = false;

                        if ($lppIsInt && $idAutorsIsInt && $cenaIsInt && 
                                $daudzumsIsInt && !$_POST["nosaukums"]=="" &&
                            !$_POST["apraksts"]=="" && !$_POST["lpp"]=="" && 
                            !$_POST["gads"]=="" &&
                            !$_POST["vaka_tips"]=="" && !$_POST["cena"]=="" && !strlen($_POST["daudzums"]) == 0){
                                $isbn = mysqli_real_escape_string($con, @$_GET["gr_dati"]);
                                $nosaukums = mysqli_real_escape_string($con, $_POST['nosaukums']);
                                $apraksts = mysqli_real_escape_string($con, $_POST['apraksts']);
                                $id_autors = mysqli_real_escape_string($con, $_POST["autorList"]);
                                $id_izdevnieciba = mysqli_real_escape_string($con, $_POST["izdevniecibaList"]);
                                $zanrs_nodala = mysqli_real_escape_string($con, $_POST["nodalaList"]);
                                $valoda = mysqli_real_escape_string($con,$_POST["valoda"]);
                                $lpp = mysqli_real_escape_string($con, $_POST['lpp']);
                                if($_FILES["bilde"]["name"]=="")
                                    $imageData = mysqli_real_escape_string($con, $datiRes["Bilde"]);
                                else
                                    $imageData = mysqli_real_escape_string($con, file_get_contents($_FILES["bilde"]["tmp_name"]));
                                $gads = mysqli_real_escape_string($con, $_POST["gads"]);
                                $vaka_tips = mysqli_real_escape_string($con, $_POST["vaka_tips"]);
                                $cena = mysqli_real_escape_string($con, $_POST["cena"]);
                                $daudzums = mysqli_real_escape_string($con, $_POST["daudzums"]);

                                mysqli_select_db($con, "gramatnica");
                                mysqli_query($con, "CALL update_gramata('".
                                        $isbn ."', '". $nosaukums ."', '". $apraksts ."', '". $id_autors ."', '". 
                                        $id_izdevnieciba ."', '". $zanrs_nodala ."', '". $valoda ."', '". $lpp ."', '". 
                                        $imageData ."', '". 
                                        $gads ."', '". $vaka_tips ."', '". $cena ."', '". $daudzums ."')");
                                 echo "<script>window.location.assign(\"user.php?page=5&admin_menu=2&data_reg=1&gramatas=1&gr_menu=2\")</script>";
                        }
                        

                    }
                }
                
                
                $page_id = $_GET['page'];
                if($page_id >= 10){
                    
                    $zanrsArr = array();
                    $gramatasArr = array();
                    
                    $result = mysqli_query($con, "SELECT Zanrs FROM Zanrs WHERE Nodala = '"
                            . $nodalaArr[$page_id-9]['name'] . "'");
                    $idx = 1;
                    while($row = mysqli_fetch_array($result)){
                        $zanrsArr[$idx] = array(
                            'name' => '' . $row["Zanrs"] . '',
                        );
                        $idx++;
                    }
                    echo "<div class=\"nodalaList\">";
                    echo "<ul>";
                    foreach($zanrsArr as $id => $zanrs){
                        echo "<li><a href=\"user.php?page=" . $page_id . "&zanrs=" . $id .
                                "\">" . $zanrs['name'] . "</a></li>";
                    }
                    echo "</ul>";
                    echo "</div>";
                    
                    if(isset($_GET['zanrs']) && isset($_GET['gramata'])){
                        if(isset($_POST['add_to_cart'])){
                            $isbn = $_POST['gr_isbn'];
                            $counter = 1;
                            $idx = 0;
                            if(!empty($_SESSION['shopping_cart'])){
                                foreach($_SESSION['shopping_cart'] as $id => $product){
                                    if($product['gr_isbn'] == $isbn){
                                        $counter+=$product['quantity'];
                                        break;
                                    }
                                    $idx++;
                                }
                            }
                            if($counter == 1){
                                $count = count($_SESSION['shopping_cart']);
                                $_SESSION['shopping_cart'][$count]['gr_isbn'] = $_POST['gr_isbn'];
                                $_SESSION['shopping_cart'][$count]['quantity'] = $counter; 
                            }
                            else{
                                $count = $idx;
                                $_SESSION['shopping_cart'][$count]['gr_isbn'] = $_POST['gr_isbn'];
                                $_SESSION['shopping_cart'][$count]['quantity'] = $counter; 
                            }
                        }
                    }
                    
                    if(isset($_GET['zanrs']) && !isset($_GET['gramata'])){
                        
                        if(isset($_POST['add_to_cart'])){
                            $isbn = $_POST['gr_isbn'];
                            $counter = 1;
                            $idx = 0;
                            if(!empty($_SESSION['shopping_cart'])){
                                foreach($_SESSION['shopping_cart'] as $id => $product){
                                    if($product['gr_isbn'] == $isbn){
                                        $counter+=$product['quantity'];
                                        break;
                                    }
                                    $idx++;
                                }
                            }
                            if($counter == 1){
                                $count = count($_SESSION['shopping_cart']);
                                $_SESSION['shopping_cart'][$count]['gr_isbn'] = $_POST['gr_isbn'];
                                $_SESSION['shopping_cart'][$count]['quantity'] = $counter; 
                            }
                            else{
                                $count = $idx;
                                $_SESSION['shopping_cart'][$count]['gr_isbn'] = $_POST['gr_isbn'];
                                $_SESSION['shopping_cart'][$count]['quantity'] = $counter; 
                            }
                        }
                        
                        $zanrs_id = $_GET['zanrs'];
                        $result = mysqli_query($con, "SELECT * FROM Gramata WHERE Zanrs = '" .
                                $zanrsArr[$zanrs_id]['name'] . "'");
                        if(mysqli_num_rows($result)){
                            echo "<div class=\"gramatuList\">";
                            echo "<table>";
                            $count = 0;
                            $idx = 1;
                            while($row = mysqli_fetch_array($result)){
                                $gramatasArr[$idx] = array(
                                    'ISBN' => '' . $row['ISBN'] . '',
                                );
                                $publisher = mysqli_query($con, "SELECT izd.Nosaukums"
                                        . " FROM Izdevnieciba izd JOIN Izdevnieciba_Gramata ig ON "
                                        . "ig.ID_Izdevnieciba = izd.ID_Izdevnieciba JOIN Gramata g ON g.ISBN = ig.ISBN"
                                        . " WHERE g.ISBN = '" . $row['ISBN'] . "'"
                                        );
                                $pubRow = mysqli_fetch_array($publisher);
                                $pubName = $pubRow['Nosaukums'];
                                
                                $ratingQuery = mysqli_query($con, "SELECT rating(" . $row['ISBN'] . ") as Reitings FROM Gramata");
                                $rateRow = mysqli_fetch_array($ratingQuery);
                                $rating = $rateRow['Reitings'];
                                
                                if($count % 2 == 0)
                                    echo "<tr>";
                                
                                echo "<td><img width=160 height=240 src='data:image/jpeg;base64,".base64_encode( $row["Bilde"] )."'/></td>";
                                echo "<td>";
                                echo "<table>";
                                    echo "<tr>";
                                        echo "<td>";
                                            echo "<h1><a href=\"user.php?page="
                                        . $_GET['page'] . "&zanrs=". $_GET['zanrs'] . "&gramata=" .
                                                    $idx . "\"><h1>". $row['Nosaukums'] ."</a></h1>";
                                        echo "</td>";
                                    echo "</tr>";
                                    echo "<tr>";
                                        echo "<td>";
                                            echo "<p>". $pubName ."</p>";
                                        echo "</td>";
                                    echo "</tr>";
                                    echo "<tr>";
                                        echo "<td>";
                                            echo "<p>Reitings -> " . $rating . "</p>";
                                        echo "</td>";
                                    echo "</tr>";
                                    echo "<tr>";
                                        echo "<td>";
                                            echo "<p style=\""
                                            . "color:FF8400; font-weight:bold;"
                                            . "\">". $row['Cena'] ."&euro; </p>";
                                        echo "</td>";
                                    echo "</tr>";
                                    echo "<tr>";
                                        echo "<td>";
                                            $checkSkaits = mysqli_query($con, "SELECT Skaits FROM Gramata WHERE ISBN='". $row["ISBN"] ."'");
                                            $daudzums = mysqli_fetch_array($checkSkaits);
                                            $gr_available = true;
                                            
                                            if($daudzums["Skaits"] == 0)
                                                $gr_available = false;
                                            else
                                                foreach($_SESSION['shopping_cart'] as $id => $product){
                                                    if($product["gr_isbn"] == $row["ISBN"]){
                                                        $starpiba = $daudzums["Skaits"] - $product["quantity"];
                                                        if($starpiba <= 0)
                                                            $gr_available = false;
                                                        break;
                                                    }
                                                }
                                            
                                            if($gr_available){
                                                echo "<form action=\"user.php?page=".$_GET['page']."&zanrs=".$_GET['zanrs']."\" method=\"post\">
                                                        <input type=\"hidden\" name=\"gr_isbn\" value=\"".$row['ISBN']."\"/>
                                                        <input type=\"submit\" name=\"add_to_cart\" value=\"Ielikt grozā\"/>
                                                  </form>";
                                            }
                                            else
                                            {
                                                echo "<h3>Nav noliktavā</h3>";
                                            }
                                        echo "</td>";
                                    echo "</tr>";
                                echo "</table>";
                                echo "</td>";
                                $count++;
                                
                                if($count %2 == 0)
                                    echo "</tr>";
                                $idx++;
                            }
                            echo "</table>";
                            echo "</div>";
                        }
                        else{
                            echo "<h3 style=\"text-align:center;"
                                           . "font-size:30px;\">Grāmatu nav!</h3>";
                        }
                                
                    }
                    else if(isset($_GET['gramata'])){
                        $zanrs_id = $_GET['zanrs'];
                        $result = mysqli_query($con, "SELECT * FROM Gramata WHERE Zanrs = '" .
                                $zanrsArr[$zanrs_id]['name'] . "'");
                        $count = 0;
                        $idx = 1;
                        
                        while($row = mysqli_fetch_array($result)){
                            if($idx == $_GET['gramata']){
                                $gramatasArr[$idx] = array(
                                    'ISBN' => '' . $row['ISBN'] . '',
                                    'Nosaukums' => '' . $row['Nosaukums'] . '',
                                    'Apraksts' => '' . $row['Apraksts'] . '',
                                );
                                break;
                            }
                            $idx++;
                        }
                        
                        $publisher = mysqli_query($con, "SELECT izd.Nosaukums"
                                . " FROM Izdevnieciba izd JOIN Izdevnieciba_Gramata ig ON "
                                . "ig.ID_Izdevnieciba = izd.ID_Izdevnieciba JOIN Gramata g ON g.ISBN = ig.ISBN"
                                . " WHERE g.ISBN = '" . $row['ISBN'] . "'"
                                );
                        $pubRow = mysqli_fetch_array($publisher);
                        $pubName = $pubRow['Nosaukums'];
                        
                        $autorsResult = mysqli_query($con, "SELECT Vards, Uzvards FROM Autors WHERE ID_Autors = '" . 
                                $row['ID_Autors'] . "'");
                        while($autorsRow = mysqli_fetch_array($autorsResult)){
                            $vards = $autorsRow['Vards'];
                            $uzvards = $autorsRow['Uzvards'];
                        }
                        
                        echo "<div class=\"gramatuApraksts\">";
                            echo "<table>";
                                echo "<tr>";
                                    echo "<td><img width=320 height=480 src='data:image/jpeg;base64,".base64_encode( $row["Bilde"] )."'/></td>";
                                    echo "<td>";
                                        echo "<table>";
                                            echo "<tr>";
                                                echo "<h1 style=\"font-size:35px;\">" . $row['Nosaukums'] . "</h1>";
                                            echo "</tr>";
                                            echo "<tr>";
                                                echo "<p style=\"font-size:15px;\">Autors: " . $vards . " " . $uzvards . "</p>";
                                                echo "<hr style=\"size: 5px; margin-top:10px; margin-bottom:20px;\">";
                                            echo "</tr>";
                                            echo "<tr>";
                                                echo "<p style=\""
                                            . "color:FF8400; font-weight:bold; font-size:35px;"
                                            . "\">". $row['Cena'] . "&euro;</p>";
                                                echo "<form action=\"user.php?page=".$_GET['page']."&zanrs=".$_GET['zanrs']."&gramata=".$_GET['gramata']."\" method=\"post\">
                                                        <input type=\"hidden\" name=\"gr_isbn\" value=\"".$row['ISBN']."\"/>
                                                        <input type=\"submit\" name=\"add_to_cart\" value=\"Ielikt grozā\"/>
                                                      </form>";
                                                echo "<hr style=\"size: 5px; margin-top:10px; margin-bottom:20px;\">";
                                            echo "</tr>";
                                            echo "<tr>";
                                                echo "<p style=\"font-size:17px; font-weight:bold;\">Apraksts</p>";
                                                echo "<p style=\"font-\">" . $row['Apraksts'] . "</p>";
                                            echo "</tr>";
                                            echo "<tr>";
                                                echo "<p style=\"font-size:17px; font-weight:bold; margin-top: 10px;\">Informācija par produktu</p>";
                                            echo "</tr>";
                                            echo "<tr>";
                                               echo "<span style=\"color:706A6A;\">Valoda</span>";
                                               echo "<span style=\"margin-left:150px;\">". $row['Valoda'] ."<br></span>";
                                            echo "<tr>";
                                               echo "<span style=\"color:706A6A;\">Izdevniecība</span>";
                                               echo "<span style=\"margin-left:114px;\">". $pubName ."<br></span>";
                                            echo "</tr>";
                                            echo "<tr>";
                                               echo "<span style=\"color:706A6A;\">ISBN</span>";
                                               echo "<span style=\"margin-left:160px;\">". $row['ISBN'] ."<br></span>";
                                            echo "</tr>";
                                            echo "<tr>";
                                               echo "<span style=\"color:706A6A;\">Iesējuma veids</span>";
                                               echo "<span style=\"margin-left:100px;\">". $row['Vaka_tips'] ."<br></span>";
                                            echo "</tr>";
                                            echo "<tr>";
                                               echo "<span style=\"color:706A6A;\">Izdošanas gads</span>";
                                               echo "<span style=\"margin-left:100px;\">". $row['Gads'] ."<br></span>";
                                            echo "</tr>";
                                            echo "<tr>";
                                               echo "<span style=\"color:706A6A;\">Lappušu skaits</span>";
                                               echo "<span style=\"margin-left:101px;\">". $row['Lpp'] ."<br></span>";
                                            echo "</tr>";                                     
                                        echo "</table>";
                                    echo "</td>";
                                echo "</tr>";

                            echo "</table>";
                        echo "</div>";
                    }
                    
                }
                
                
                mysqli_close($con);
            }
            

        ?>
    </body>
    
</html>


