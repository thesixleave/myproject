<?php
    $dbhost = 'db.mis.kuas.edu.tw';
    $dbuser = 's1103137203';
    $dbpass = 'learnw1';
    $dbname = 's1103137203';
    try{
        $conn = new PDO("mysql:host=".$dbhost.";dbname=".$dbname,$dbuser,$dbpass);

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        $conn->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND,'SET NAMES UTF8');
    }catch (PDOException $e) {
        throw new PDOException("Error  : " .$e->getMessage());
    }
    $select = $conn->prepare("SELECT User_id FROM Users WHERE userCookie = :ucook ;");
    if (isset($_COOKIE['dbproject'])){
        $cook = $_COOKIE['dbproject'];
    }else {
        $cook = "";
    }
    $select->execute(array(':ucook' => $_COOKIE['dbproject']));
    $row = $select->fetch(PDO::FETCH_ASSOC);
    if($row){
                $userid = $row['User_id'];
                $select = $conn->prepare("UPDATE Users SET userCookie = NULL WHERE User_id = :userid ;");
                $select->execute(array(':userid' => $userid));
    }else{
        header('Location: ./index.php');
    }

    echo "<meta charset='UTF-8'>";
    if (isset($_COOKIE['dbproject'])) {
        unset($_COOKIE['dbproject']);
        setcookie('dbproject', null, -1, '/', 'dbproject-ouvek-kostiva.c9users.io');
        header('Location: ./login.php');
    }else{
        header('Location: ./login.php');
    }

    header('Location: ./login.php');

