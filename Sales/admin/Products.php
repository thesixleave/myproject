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

  if(isset($_POST['edit'])){
    $Product_id = $_POST['edit'];
    
    $getProduct = $conn->prepare("SELECT Product_Remarks FROM Products WHERE Product_id = :Product_id; ");
    $getProduct->execute(array(':Product_id' => $Product_id));
    $rowProduct = $getProduct->fetch(PDO::FETCH_ASSOC);
    
    $mustEntered = true;
    $error = "";
    
    if("" == trim($_POST['Productcode'])){
      $mustEntered = false;
      $error = $error." 物品編號 ";
    }else{
        $Productcode = trim($_POST['Productcode']);
    }
    
    if("" == trim($_POST['Productname'])){
      $mustEntered = false;
      $error = $error." 物品名稱 ";
    }else{
        $Productname = trim($_POST['Productname']);
    }
    
    if("" == trim($_POST['Productname2'])){
      $mustEntered = false;
      $error = $error." 物品單價 ";
    }else{
        $Productname2 = trim($_POST['Productname2']);
    }
    
    if("" == trim($_POST['Product_Qty'])){
        $Product_Qty = 0;
    }else{
        $Product_Qty = trim($_POST['Product_Qty']);
    }
    $Product_Remarks = $_POST['Product_Remarks'];
    
    $error = $error." 為必填項目";
    
    if(substr($rowProduct['Product_Remarks'],0,7) == "REMOVED"){
        $mustEntered = false;
       $error = "$Product_id 已刪除, 無法修改";
    }
    
    if(!$mustEntered){
      echo "<div class='alert alert-warning' role='alert'> $error </div>";
    }else{
      $getSOL = $conn->prepare("UPDATE Products SET 
          Productcode = :Productcode,
          Productname = :Productname,
          Productname2 = :Productname2,
          Product_Qty = :Product_Qty,
          Product_Remarks = :Product_Remarks
          WHERE Product_id = :Product_id;");
      $getSOL->execute(array(
          ':Product_id' => $Product_id,
          ':Productcode' => $Productcode,
          ':Productname' => $Productname,
          ':Productname2' => $Productname2,
          ':Product_Qty' => $Product_Qty,
          ':Product_Remarks' => $Product_Remarks
          ));
    }
  }
    
  if(isset($_POST['delete'])){
    $Product_id = $_POST['delete'];
    
    $getProduct = $conn->prepare("SELECT Product_Remarks FROM Products WHERE Product_id = :Product_id; ");
    $getProduct->execute(array(':Product_id' => $Product_id));
    $rowProduct = $getProduct->fetch(PDO::FETCH_ASSOC);
    
    $mustEntered = true;
    $error = "";
    
    if(substr($rowProduct['Product_Remarks'],0,7) == "REMOVED"){
      $mustEntered = false;
       $error = "$Product_id 已刪除, 無法修改";
    }
    
    if(!$mustEntered){
      echo "<div class='alert alert-warning' role='alert'> $error </div>";
    }else{
      $Date = date('m/d/Y h:i:s a', time());
      $getSOL = $conn->prepare("UPDATE Products SET 
          Productcode = :Productcode,
          Productname = :Productname,
          Productname2 = :Productname2,
          Product_Qty = :Product_Qty,
          Product_Remarks = :Product_Remarks
          WHERE Product_id = :Product_id;");
      $getSOL->execute(array(
          ':Product_id' => $Product_id,
          ':Productcode' => "",
          ':Productname' => "產品不存在",
          ':Productname2' => "",
          ':Product_Qty' => 0,
          ':Product_Remarks' => "REMOVED $Date"
          ));
    }
  }
    

$select = $conn->prepare("SELECT Product_id,Productcode,Productname,Productname2,ProductImage,Product_Qty,Product_Remarks FROM Products; ");
$select->execute();
?>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">

    <title>  產品列表</title>

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
                    <li class="active"><a class="btn btn-secondary btn-sm"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> 產品列表</a></li>
                    <li><a href="./newProducts.php" class="btn btn-secondary btn-sm"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增產品</a></li>
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
  
      <table class="table">
        <thead>
          <tr>
            <th class="col-xs-1">修改</th>
            <th class="col-xs-2">物品編號</th>
            <th class="col-xs-2">名稱</th>
            <th class="col-xs-1">價格</th>
            <th class="col-xs-1">存量</th>
            <th class="col-xs-3">備註</th>
            <th class="col-xs-1"></th>
            <th class="col-xs-1">刪除</th>
          </tr>
        </thead>
        <tbody>
          <?php
            for($i = 1; $i <= $select->rowCount(); $i++){
                $row = $select->fetch();
                echo "
                    <tr>
                      <form method='post'>";
                
                if(substr($row->Product_Remarks,0,11) == "ItemDeleted"){
                    //echo "<th>已刪除</th>";
                }else{
                    echo "
                        <th>
                            <button type='submit' class='btn btn-primary btn-xs' name='edit' value='".$row->Product_id."'>
                            <span class='glyphicon glyphicon-pencil' aria-hidden='true'> </span>
                            修改 ".$row->Product_id."
                            </button>
                        </th>
                        <th><input type='text' name='Productcode' value='".$row->Productcode."'></th>
                        <th><input type='text' name='Productname' value='".$row->Productname."'></th>
                        <th><input type='text' name='Productname2' value='".$row->Productname2."'></th>
                        <th><input type='text' name='Product_Qty' value='".$row->Product_Qty."'></th>
                        <th><input type='text' name='Product_Remarks' value='".$row->Product_Remarks."'></th>
                        <th></th>
                    
                    ";
                }

                if(substr($row->Product_Remarks,0,11) == "ItemDeleted"){
                    //echo "<th>已刪除</th>";
                }else{
                    echo "
                        <th>
                            <button type='submit' class='btn btn-danger btn-xs' name='delete' value='".$row->Product_id."'>
                            <span class='glyphicon glyphicon-trash' aria-hidden='true'> </span>
                            刪除 ".$row->Product_id."
                            </button>
                        </th>
                    ";
                }
                echo "
                     </form>     
                    </tr>
                ";
            }
          ?>

        </tbody>
      </table>
      
 
    </div> <!-- /container -->
  <div class="container col-xs-1"></div>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
