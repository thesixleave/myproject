<?php
    require "head.php";
    
    if($row){
        $username = $row['Username'];
    }else{
        header('Location: ./index.php');
    }
    
if(isset($_POST['addOrder'])){
  $rows = $_POST['rows'];
  //按下下訂
  $Product_id = $_POST['Product_id'];
  $orderQty = $_POST['orderQty'];
  $Date = date("Y-m-d H:i:s");
  $User_id = $row['User_id'];
  
  $getCid = $conn->prepare("SELECT Customer_id FROM Customer WHERE CustomerUser_id = :CustomerUser_id");
  $getCid->execute(array(':CustomerUser_id' => $User_id));
  $rowCid = $getCid->fetch(PDO::FETCH_ASSOC);
  
  $select = $conn->prepare("INSERT INTO SalesOrder (
     SO_Date,
     SO_Customer_id,
     SO_User_id,
     SO_Code) VALUES (
        :SO_Date,
        :SO_Customer_id,
        :SO_User_id,
        :SO_Code)");
  $select->execute(array(
      ':SO_Date' => $Date,
      ':SO_Customer_id' => $rowCid['Customer_id'],
      ':SO_User_id' => $User_id,
      ':SO_Code' => $Date));
      
  $getSO = $conn->prepare("SELECT SalesOrder_id FROM SalesOrder WHERE SO_Code = :SO_Code ");
  $getSO->execute(array(':SO_Code' => $Date));
  $rowSO = $getSO->fetch(PDO::FETCH_ASSOC);
  $SO_id = $rowSO['SalesOrder_id'];
  $i = 0;
  foreach($rows as $r){
    if("" == trim($orderQty[$r]) || trim($orderQty[$r]) <= 0){
      $orderQty[$r] = 0;
      continue;
    }
    
    $getPrice = $conn->prepare("SELECT Productname2,Product_Qty FROM Products WHERE Product_id = :Product_id ");
    $getPrice->execute(array(':Product_id' => $Product_id[$i]));
    $rowPrice = $getPrice->fetch(PDO::FETCH_ASSOC);
    $UnitPrice = $rowPrice['Productname2'];
    
    $resultQty = $rowPrice['Product_Qty']-$orderQty[$r];
    
    if($resultQty >= 0){
        $update = $conn->prepare("UPDATE Products SET
        Product_Qty = :Product_Qty
            WHERE Product_id = :Product_id");
    
        $update->execute(array(
        ':Product_Qty' => $resultQty,
            ':Product_id' => $Product_id[$i]));
    
        echo "<div class='alert alert-success' role='alert'>新增內容".$Product_id[$i]."成功</div><br>";
    }elseif($rowPrice['Product_Qty'] > 0){
        if($rowPrice['Product_Qty'] > 0){
            $resultQty = $rowPrice['Product_Qty'];
            $update = $conn->prepare("UPDATE Products SET
            Product_Qty = :Product_Qty
                WHERE Product_id = :Product_id");
        
            $update->execute(array(
            ':Product_Qty' => $resultQty,
                ':Product_id' => $Product_id[$i]));
        }else{
            continue;
        }
        echo "<div class='alert alert-warning' role='alert'>因為庫存數量不足 系統僅為您處理庫存現有存貨 如需更多商品 請直恰店家</div>";
    }else {
        echo "<div class='alert alert-warning' role='alert'>無庫存 如需更多商品 請直恰店家</div>";
        continue;
    }
    
    $insert = $conn->prepare("INSERT INTO SalesOrder_List (
     SO_List_SalesOrder_id,
     SO_List_Quantity,
     SO_List_UnitPrice,
     SO_Product_id,
     SO_List_Remarks) VALUES (
        :SO_List_SalesOrder_id,
        :SO_List_Quantity,
        :SO_List_UnitPrice,
        :SO_Product_id,
        :SO_List_Remarks)");
    $insert->execute(array(
        ':SO_List_SalesOrder_id' => $SO_id,
        ':SO_List_Quantity' => $orderQty[$r],
        ':SO_List_UnitPrice' => $UnitPrice,
        ':SO_Product_id' => $Product_id[$i],
        ':SO_List_Remarks' => "下訂成功"));
        
        
    $i++;
  }
  
}

//商品列表
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

    <title>1103137203 產品列表</title>

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
                    <li class="active"><a class="btn btn-secondary btn-sm"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> 產品列表</a></li>
                    <li><a href="./Order.php" class="btn btn-secondary btn-sm"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> 我的訂單</a></li>
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
    
    
    <div class="container">
  <form method="post">
      <table class="table">
        <thead>
          <tr>
            <th class="col-sm-1">購買</th>
            <th class="col-sm-3">物品編號</th>
            <th class="col-sm-2">名稱</th>
            <th class="col-sm-1">價格</th>
            <th class="col-sm-1">存量</th>
            <th class="col-sm-3">備註</th>
            <th class="col-sm-1">購買數量</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $rows = 0;
            for($i = 1; $i <= $select->rowCount(); $i++){
                $row = $select->fetch();
                if(substr($row->Product_Remarks,0,7) != "REMOVED"){
                    echo "
                        <tr>
                            <th><input type='checkbox' name='Product_id[]' value='".$row->Product_id."'><input type='hidden' name='rows[]' value='$rows'></th>
                            <th>".$row->Productcode."</th>
                            <th>".$row->Productname."</th>
                            <th>".$row->Productname2."</th>
                            <th>".$row->Product_Qty."</th>
                            <th>".$row->Product_Remarks."</th>
                            <th><input type='text' name='orderQty[]' value='0'></th>
                        </tr>
                    ";
                    $rows++;
                }
            }
          ?>

        </tbody>
      </table>
      
      <div class="col-lg-10"></div>
      <div class="col-lg-2">
        <button type="submit" class="btn btn-primary" name="addOrder">
          <span class="glyphicon glyphicon-ok" aria-hidden="true"> </span>
          下訂
        </button>
      </div>
  </form> 
    </div> <!-- /container -->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="./js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
