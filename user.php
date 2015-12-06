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
            </ul>
            <hr size="3">
        </div>
        <?php
            if(!isset($_GET['page'])){
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
                    
                    if(isset($_GET['zanrs']) && ! isset($_GET['gramata'])){
                        
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
                                            echo "<button type=\"submit\" style=\""
                                        . ""
                                                    . "\">Ielikt grozā</button>";
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
//                        $row = mysqli_fetch_array($result);
//                        $arrayOfRows = array();
//                        while(mysqli_fetch_array($result)){
//                            $arrayOfRows = $row;
//                        }
//                        $gramatasArr[1] = array(
//                                'ISBN' => '' . $arrayOfRows[$_GET['gramata']]['ISBN'] . '',
//                         );
//                        echo print_r($gramatasArr);
                        
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
                                                echo "<button type=\"submit\" style=\" height:40px; margin-top:10px;"
                                                    . ""
                                                    . "\"><p style=\"font-size:20px;\">Ielikt grozā</p></button>";
                                                echo "<hr style=\"size: 5px; margin-top:10px; margin-bottom:20px;\">";
                                            echo "</tr>";
                                            echo "<tr>";
                                                echo "<p style=\"font-size:17px; font-weight:bold;\">Apraksts</p>";
                                                echo "<p style=\"font-\">" . $row['Apraksts'] . "</p>";
                                            echo "</tr>";
                                            echo "<tr>";
                                                echo "<p style=\"font-size:17px; font-weight:bold; margin-top: 10px;\">Informācija par produktu</p>";
                                            echo "</tr>";
                                            echo "<table>"
                                            
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


