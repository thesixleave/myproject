<?php
    require "head.php";
    
    if($row){
        $username = $row['Username'];
        header('Location: ./Product_list.php');
    }
    
if(isset($_POST['signup'])){
    
    $Username = trim($_POST['username']);
    $Email = trim($_POST['email']);
    $pass_1 = trim($_POST['pass1']);
    $pass_2 = trim($_POST['pass2']);
    $Permission = "Customer";
    
    $select = $conn->prepare("SELECT COUNT(Username) FROM Users WHERE Username = :Username; "); //確認是否有重複帳號
    $select->execute(array(':Username' => $Username));
    $result = $select->fetchColumn();
    $select = $conn->prepare("SELECT COUNT(Email) FROM Users WHERE Email = :Email; "); //確認是否有重複Email
    $select->execute(array(':Email' => $Email));
    $result = $result + $select->fetchColumn();
    if($result > 0){
        echo "
            <meta charset='UTF-8'>
            <div class='alert alert-warning' role='alert'>帳號或密碼已註冊</div>";
    
    }else{
        if($pass_1 == $pass_2 ){ //兩次輸入的密碼相同
            echo "<meta charset='UTF-8'>";
            $Passhash = password_hash($pass_1, PASSWORD_BCRYPT); //密碼+隨機數字 進行 雜湊函數 運算
    
            $select = $conn->prepare("INSERT INTO Users (User_id, Username, Email, Passhash, Salt, Permission) VALUES (:User_id, :Username, :Email, :Passhash, :Salt, :Permission)"); //INSERT 資料進入資料庫
            $select->execute(array(
                ':User_id' => NULL,
                ':Username' => $Username,
                ':Email' => $Email,
                ':Passhash' => $Passhash,
                ':Salt' => "Deprecated",
                ':Permission' => $Permission
                ));
                
            $select = $conn->prepare("SELECT User_id FROM Users WHERE Username = :Username");
            $select->execute(array(':Username' => $Username));
            $row = $select->fetch(PDO::FETCH_ASSOC);
                
            $select = $conn->prepare("INSERT INTO Customer (
                Customer_id,
                Customercode,
                Customername,
                CustomerEmail,
                CustomerUser_id) VALUES (
                    :Customer_id,
                    :Customercode,
                    :Customername,
                    :CustomerEmail,
                    :CustomerUser_id)");
            $select->execute(array(
                ':Customer_id' => NULL,
                ':Customercode' => $row['User_id'],
                ':Customername' => $Username,
                ':CustomerEmail' => $Email,
                ':CustomerUser_id' => $row['User_id']));
            
            echo "<div class='alert alert-success' role='alert'>註冊成功</div><br>";
        }else{
            echo "<meta charset='UTF-8'>
            <div class='alert alert-warning' role='alert'>請輸入相同密碼兩次</div>";
        }
    
    }
    
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">

    <title>1103137203 申請帳號</title>

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="./css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="./css/signin.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="./js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
    <!-- 功能列 -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div id="navbar" class="collapse navbar-collapse navbar-inverse">
                <ul class="nav navbar-nav">
                    <li><a class="btn btn-secondary btn-sm" href="./index.php"><span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> 使用者登入</a></li>
                    <!--<li><a href="./map.php"><span class="glyphicon glyphicon-move" aria-hidden="true"></span> 使用者地圖導覽</a></li>
                    <li><a href="./Introduction.php"><span class="glyphicon glyphicon-home" aria-hidden="true"></span> 系統介紹</a></li>-->
                    <li class="active"><a class="btn btn-secondary btn-sm"><span class="glyphicon glyphicon-registration-mark" aria-hidden="true"></span> 註冊帳號</a></li>
                </ul>
                <ul class="nav navbar-nav pull-right">

                    <p class="navbar-text"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> <?php if($username != ""){echo $username;}else{echo "Username";} ?></p>
                </ul>
            </div>
            <!--/.nav-collapse -->
            <div id="navbar" class=""></div>
        </div>
    </nav>
    </br>
    </br>
    </br>
    <!-- 功能列 -->
    
    
    <div class="container">
      <form class="form-signin" method="post">
        <h2 class="form-signin-heading">申請帳號</h2>
        <label for="inputEmail" class="sr-only">帳號名稱</label>
        <input type="text" id="inputUsername" class="form-control" placeholder="Username" required autofocus name="username">
        <label for="inputEmail" class="sr-only">Email 地址</label>
        <input type="email" id="inputEmail" class="form-control" placeholder="Email address" required name="email">
        <label for="inputPassword" class="sr-only">密碼</label>
        <input type="password" id="inputPassword" class="form-control" placeholder="Password" name="pass1" required>
        <label for="inputPassword" class="sr-only">確認密碼</label>
        <input type="password" id="inputPassword" class="form-control" placeholder="Password Again" name="pass2" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="signup">註冊</button>
      </form>
    </div> <!-- /container -->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="./js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
