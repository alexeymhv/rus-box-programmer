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
                <li><a href="user.php?">Sakums</a></li>
                <li><a href="user.php?page=1">Gramātas</a></li>
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
                        
                    }
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
                        $epasts = $_POST["uzvards"];
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
                                
                                mysqli_free_result($checkVar);
                                $checkVar = mysqli_query($con, "SELECT Indekss FROM Indekss WHERE Indekss='". $indekss ."'");
                                $checkVarNum = mysqli_num_rows($checkVar);
                                
                                if($checkVarNum == 0){
                                    $query = mysqli_query($con, "INSERT INTO Indekss(Indekss, Pilseta) VALUES('"
                                            . $indekss ."', '". $pilseta ."')");
                                    
                                    mysqli_free_result($checkVar);
                                    $checkVar = mysqli_query($con, "SELECT Iela FROM Adrese WHERE Iela='".
                                            $iela ."' AND Indekss='". $indekss ."'");
                                    $checkVarNum = mysqli_free_result($checkVar);
                                    
                                    if($checkVarNum == 0){
                                        $query = mysqli_query($con, "INSERT INTO Adrese(Iela, Indekss) VALUES('"
                                                . $iela ."', '". $indekss . "')");
                                        
                                        //mysqli_free_result($checkVar);
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
                                            }
                                            
                                        }
                                        
                                    }
                                    
                                }
                                
                            }
                            
                            
                            
                            $checkVar = mysqli_query($con, "SELECT * FROM Lietotajs WHERE Login = '". $lietotajvards ."'");
                            $checkVarNum = mysqli_num_rows($checkVar);
                            if($checkVarNum == 0){
                                $query = mysqli_query($con, "INSERT INTO Lietotajs(Login, Parole) VALUES('"
                                        . $lietotajvards ."', '" . $parole ."')");
                            }
                            
                        }
                        else{
                            echo "Jāaizpilda visus laukus.";
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
                    
                    echo "<div class=\"pasutijumuApraksts\">
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
                            <li><a href=\"user.php?page=6&admin_menu=2&data_reg=2&izdevniecibas=1\">Izdevniecības</a></li>
                            ";
                    echo "</ul>";
                    echo "</div>";
                }
                if($_GET['page'] == 5 && isset($_GET['admin_menu']) == 2 && isset($_GET['data_reg']) == 1 && isset($_GET['gramatas']) == 1){
                    echo "<div class=\"adminPanelList\">";
                    echo "<ul>";
                        echo "
                            <li><a href=\"user.php?page=5&admin_menu=2&data_reg=1&gramatas=1&gr_menu=1\">Pievienot grāmatu</a></li>
                            <li><a href=\"user.php?page=5&admin_menu=2&data_reg=1&gramatas=1&gr_menu=2\">Rēdiģet grāmatas datus</a></li>
                            <li><a href=\"user.php?page=5&admin_menu=2&data_reg=1&gramatas=1&gr_menu=3\">Dzēst grāmatu</a></li>
                            ";
                    echo "</ul>";
                    echo "</div>";
                }
                
                if($_GET['page'] == 6 && isset($_GET['admin_menu']) == 2 && isset($_GET['data_reg']) == 2 && isset($_GET['izdevniecibas']) == 1){
                    echo "<div class=\"adminPanelList\">";
                    echo "<ul>";
                        echo "
                            <li><a href=\"user.php?page=6&admin_menu=2&data_reg=2&izdevniecibas=1&izd_menu=1\">Pievienot izdevniecību</a></li>
                            <li><a href=\"user.php?page=6&admin_menu=2&data_reg=2&izdevniecibas=1&izd_menu=2\">Rēdiģet izdevniecības datus</a></li>
                            <li><a href=\"user.php?page=6&admin_menu=2&data_reg=2&izdevniecibas=1&izd_menu=3\">Dzēst izdevniecību</a></li>
                            ";
                    echo "</ul>";
                    echo "</div>";
                }
                
                if(isset($_GET['gr_menu']) == 1){
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
                                                <select name=\"autorList\" style=\"width:450px\">
                                                    <option></option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p>Izdevniecība</p></td>
                                            <td>
                                                <select name=\"izdevniecibaList\" style=\"width:450px\">
                                                    <option></option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p>Nodala</p></td>
                                            <td>
                                                <select name=\"nodalaList\" style=\"width:450px\">
                                                    <option></option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p>Zanrs</p></td>
                                            <td>
                                                <select name=\"zanrsList\" style=\"width:450px\">
                                                    <option></option>
                                                </select>
                                            </td>
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


                                    </table>
                                </div>
                            </form>
                         </div>";
                    
                    
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
                                            echo "<form action=\"user.php?page=".$_GET['page']."&zanrs=".$_GET['zanrs']."\" method=\"post\">
                                                        <input type=\"hidden\" name=\"gr_isbn\" value=\"".$row['ISBN']."\"/>
                                                        <input type=\"submit\" name=\"add_to_cart\" value=\"Ielikt grozā\"/>
                                                  </form>";
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
                
                
                mysqli_free_result($result);
                mysqli_close($con);
            }
            
            
//            if(isset($_GET['page'])){
//                $page_id = $_GET['page'];
//                if($page_id >= 10){
//                    echo $nodalaArr[$page_id-9]['name'];
//                }
////                $result = mysqli_query($con, "SELECT DISTINCT Zanrs FROM Zanrs WHERE Nodala = '" . $nodalaArr[$page_id-9]['name'] . "'");
////                while($row = mysqli_fetch_array($result)){
////                    echo $row['Zanrs'];
////                }
////                mysqli_free_result($result);
////                mysqli_close($con);
//            }

        ?>
    </body>
    
</html>


