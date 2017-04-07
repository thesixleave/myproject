<?php
    require "head.php";
            $isAdmin = false;
    if($row){
          $username = $row['Username'];
          $permission = $row['Permission'];
          if($permission == "admin"){
              $isAdmin = true;    
          }else{
              header('Location: ./login.php');
              echo "</br><div class='alert alert-warning' role='alert'>本頁面為管理者使用, <a href='../index.php'>一般客戶請點此</a></div>";
          }
    }else{
        header('Location: ./login.php');
    }
    
    if(isset($_POST['addProduct']) && $isAdmin == true){
        $error = "</br><div class='alert alert-warning' role='alert'> 問題： ";
        $hasInput = true;
        
        if("" == trim($_POST['Productcode'])){
            $error = $error." 物品編號 ";
            $hasInput = false;
        }else{
            $select = $conn->prepare("SELECT Productcode FROM Products WHERE Productcode = :Productcode");
            $select->execute(array(':Productcode' => trim($_POST['Productcode'])));
            if($select->rowCount() > 0){
                $error = $error." 編號重複 ";
                $hasInput = false;
            }
        }
        if("" == trim($_POST['Productname'])){
            $error = $error." 名稱 ";
            $hasInput = false;
        }
        if("" == trim($_POST['Productname2'])){ //Price
            $error = $error." 價格 ";
            $hasInput = false;
        }
        if("" == trim($_POST['Product_Qty'])){
            $error = $error." 存量 ";
            $hasInput = false;
        }
        $error = $error." 必填項目</div>";
        
        $hasRemarks = false;
        if("" == trim($_POST['Product_Remarks'])){
            $hasRemarks = false;    
        }else{
            $hasRemarks = true;
        }
        
        $successInsert = false;
        
        if(!$hasInput){
            echo $error;
        }else{
            $Productcode = trim($_POST['Productcode']);
            $Productname = trim($_POST['Productname']);
            $Product_Qty = trim($_POST['Product_Qty']);
            $Productname2 = trim($_POST['Productname2']);
            
            $insert = $conn->prepare("INSERT INTO Products (
             Productcode,
             Productname,
             Product_Qty,
             Productname2) VALUES (
                :Productcode,
                :Productname,
                :Product_Qty,
                :Productname2)");
            $insert->execute(array(
                ':Productcode' => $Productcode,
                ':Productname' => $Productname,
                ':Product_Qty' => $Product_Qty,
                ':Productname2' =>  $Productname2));
            
            echo "</br><div class='alert alert-success' role='alert'>".$Productname." 新增成功</div>";
            $successInsert = true;
        }
        
        if($hasRemarks && $successInsert){
            $Product_Remarks = trim($_POST['Product_Remarks']);
            $update = $conn->prepare("UPDATE Products SET Product_Remarks = :Product_Remarks WHERE Productcode = :Productcode");
            $update->execute(array(':Product_Remarks' => $Product_Remarks, ':Productcode' => $Productcode));
        }
        
    }

?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">

    <title>1103137203 產品列表</title>

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
                    <li><a href="./Products.php" class="btn btn-secondary btn-sm"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> 產品列表</a></li>
                    <li class="active"><a class="btn btn-secondary btn-sm"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增產品</a></li>
                    <li><a href="./CHOrder.php" class="btn btn-secondary btn-sm"><span class="glyphicon glyphicon-asterisk" aria-hidden="true"></span> 管理訂單</a></li>
                    <li><a href='./newuser.php' class="btn btn-secondary btn-sm"><span class="glyphicon glyphicon-registration-mark" aria-hidden="true"></span> 新增管理者帳號</a></li>
                </ul>
                <ul class="nav navbar-nav pull-right">
                    <p class="navbar-text"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> <?php if($username != ""){echo $username;}else{echo "Username";} ?></p>
                    <li><a href="./signout.php"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> 登出</a></li>
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
    
    <div class="container col-xs-1"></div>
    <div class="container col-xs-10">
  <form method="post">
      <table class="table">
        <thead>
          <tr>
            <th class="col-sm-3">物品編號</th>
            <th class="col-sm-3">名稱</th>
            <th class="col-sm-1">價格</th>
            <th class="col-sm-1">存量</th>
            <th class="col-sm-3">備註</th>
            <th class="col-sm-1">新增</th>
          </tr>
        </thead>
        <tbody>
          <tr>
             <th><input type='text' name='Productcode' placeholder=''></th>
             <th><input type='text' name='Productname' placeholder=''></th>
             <th><input type='text' name='Productname2' placeholder=''></th>
             <th><input type='text' name='Product_Qty' placeholder=''></th>
             <th><input type='text' name='Product_Remarks' placeholder=''></th>
             <th>
                 <button type="submit" class="btn btn-success btn-xs" name="addProduct">
                     <span class="glyphicon glyphicon-plus" aria-hidden="true"> </span>
                     新增
                 </button>
            </th>
          </tr>

        </tbody>
      </table>
      
      <div class="col-lg-10"></div>
      <div class="col-lg-2"> </div>
  </form> 
    </div> <!-- /container -->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>