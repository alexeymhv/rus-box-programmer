<!DOCTYPE html>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>User Panel</title>
        <link rel="stylesheet" href="style.css" type="text/css" media="all" />
    </head>
    
    <body>
<!--        <input type = "submit" value = "Apskatit grāmatas" id = "showGramatasButton" />-->
        <form action="admin.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="filet" value="" />
            <button type="submit" name="btn-upload">upload</button>
        </form>  
        
    </body>
    
</html>


