<?php
    require "head.php";
            $isAdmin = false;
    if($row){
          $username = $row['Username'];
          $permission = $row['Permission'];
          if($permission == "admin"){
              header('Location: ./Products.php');
          }else{
	      header('Location: ../Product_list.php');
              echo "</br><div class='alert alert-warning' role='alert'>本頁面為管理者登入用, <a href='../Product_list.php'>一般客戶請點此</a></div>";
          }
    }
    
if(isset($_POST['login'])){
    $email = $_POST['email'];
    $pass = $_POST['password'];
    
    $select = $conn->prepare("SELECT Passhash FROM Users WHERE Email = :Email;");
    $select->execute(array(':Email' => $email));
    $row = $select->fetch(PDO::FETCH_ASSOC);
    $hash = $row['Passhash'];
    $result = password_verify($pass, $hash);
    
    if(!$row){
        echo "
        <meta charset='UTF-8'>
        <div class='alert alert-warning' role='alert'>帳號或密碼錯誤</div>";
    }else{
        if ($result) {
                echo "<meta charset='UTF-8'>";
                $cookie = bin2hex(openssl_random_pseudo_bytes(20));
                setcookie("dbproject", $cookie, time()+2592000, "/", "fs.mis.kuas.edu.tw");
                $conn = "";
                $conn = new PDO("mysql:host=".$dbhost.";dbname=".$dbname,$dbuser,$dbpass);
                $update = $conn->prepare("UPDATE Users SET userCookie = :cookie WHERE Email LIKE :Email;");
                $resp = $update->execute(array(':cookie'=> $cookie, ':Email' => $email));
                echo "
                    <div class='alert alert-success' role='alert'>登入成功!</div></br>
                    <script language=javascript>window.location.reload(true); </script>";
        }else{
            echo "
            <meta charset='UTF-8'>
            <div class='alert alert-warning' role='alert'>帳號或密碼錯誤</div>";
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

    <title>1103137203 登入</title>

    <!-- Bootstrap core CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="../css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../css/signin.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="../js/ie-emulation-modes-warning.js"></script>

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
                    <li class="active"><a class="btn btn-secondary btn-sm"><span class="glyphicon glyphicon-flag" aria-hidden="true"></span> 管理者登入</a></li>
                    <li><a href='../index.php' class="btn btn-secondary btn-sm"><span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> 一般使用者登入</a></li>
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
        <h2 class="form-signin-heading">登入</h2>
        <label for="inputEmail" class="sr-only">Email 地址</label>
        <input type="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus name="email">
        <label for="inputPassword" class="sr-only">密碼</label>
        <input type="password" id="inputPassword" class="form-control" placeholder="Password" name="password" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="login">登入</button>
      </form>
    </div> <!-- /container -->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
