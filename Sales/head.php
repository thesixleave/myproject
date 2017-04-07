<?php 
    $dbhost = 'localhost';
    $dbuser = 'Signupuser';
    $dbpass = '';
    $dbname = 'c9';
    try{
        $conn = new PDO("mysql:host=".$dbhost.";dbname=".$dbname,$dbuser,$dbpass);

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        $conn->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND,'SET NAMES "utf8"');
        $select = $conn->prepare("SET CHARACTER_SET_CLIENT='utf8';SET CHARACTER_SET_RESULTS='utf8';SET NAMES 'utf8';");
        $select->execute();
    }catch (PDOException $e) {
        throw new PDOException("Error  : " .$e->getMessage());
    }
    $select = $conn->prepare("SELECT Username, User_id FROM Users WHERE userCookie = :ucook ;");
    if (isset($_COOKIE['dbproject'])){
        $cook = $_COOKIE['dbproject'];
    }else {
        $cook = "";
    }
    $select->execute(array(':ucook' => $cook));
    $row = $select->fetch(PDO::FETCH_ASSOC);
    $username = "";