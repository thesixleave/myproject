<?php

    require "head.php";
    
    if($row){
        $username = $row['Username'];
        $User_id = $row['User_id'];
    }else{
        header('Location: ./index.php');
    }
    
if(isset($_POST['delete'])){
    
    $SOList_id = $_POST['SOList_id'];
    for($i = 0; $i < count($SOList_id); $i++){
      
      $ListQty = $conn->prepare("SELECT SO_List_Remarks,SO_List_Quantity,SO_Product_id FROM SalesOrder_List WHERE SOList_id = :SOList_id;");
      $ListQty->execute(array(':SOList_id' => $SOList_id[$i]));
      $Listrow = $ListQty->fetch(PDO::FETCH_ASSOC);
      $SOListRemarks = $Listrow['SO_List_Remarks'];
      
      if("下訂成功" == $SOListRemarks){
      
        $Date = date("Y-m-d H:i:s");
        $cancel = $conn->prepare("UPDATE SalesOrder_List SET SO_List_Remarks = :SO_List_Remarks WHERE SOList_id = :SOList_id;");
        $cancel->execute(array(
          ':SO_List_Remarks' => "已取消 $Date",
          ':SOList_id' => $SOList_id[$i]));
        
        $Product = $conn->prepare("SELECT Product_Qty FROM Products WHERE Product_id = :Product_id");
        $Product->execute(array(':Product_id' => $Listrow['SO_Product_id']));
        $rowProd = $Product->fetch(PDO::FETCH_ASSOC);
          
        $upQty = $conn->prepare("UPDATE Products SET
          Product_Qty = :Product_Qty
              WHERE Product_id = :Product_id");
        $upQty->execute(array(
          ':Product_Qty' => $rowProd['Product_Qty']+$Listrow['SO_List_Quantity'],
              ':Product_id' => $Listrow['SO_Product_id']));
      }else{
        echo "<div class='alert alert-warning' role='alert'>訂單已送貨或已完成後無法更改, 請透過<a href='mailto:email@example.com'>Email</a>聯絡</div>";
      }  
    }
    
}//delete    
    
    $getSO = $conn->prepare("SELECT SalesOrder_id,SO_Date FROM SalesOrder WHERE SO_User_id = :SO_User_id; ");
    $getSO->execute(array(':SO_User_id' => $User_id));

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
                    <li><a href="./Product_list.php" class="btn btn-secondary btn-sm"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> 產品列表</a></li>
                    <li class="active"><a class="btn btn-secondary btn-sm"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> 我的訂單</a></li>
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
            <th class="col-sm-1">取消</th>
            <th class="col-sm-2">訂單日期</th>
            <th class="col-sm-3">商品名稱</th>
            <th class="col-sm-1">價格</th>
            <th class="col-sm-1">數量</th>
            <th class="col-sm-4">備註</th>
          </tr>
        </thead>
        <tbody>
          <?php
          
            for($j = 1; $j <= $getSO->rowCount(); $j++){
                $rowSO = $getSO->fetch(PDO::FETCH_ASSOC);
                $SO_Date = $rowSO['SO_Date'];
                $SO_id = $rowSO['SalesOrder_id'];
                $getSOList = $conn->prepare("SELECT SO_List_Quantity,SO_Product_id,SO_List_UnitPrice,SOList_id,SO_List_Remarks FROM SalesOrder_List WHERE SO_List_SalesOrder_id = :SO_List_SalesOrder_id; ");
                $getSOList->execute(array(':SO_List_SalesOrder_id' => $SO_id));
                
                for($i = 1; $i <= $getSOList->rowCount(); $i++){
                    $rowList = $getSOList->fetch(PDO::FETCH_ASSOC); //訂單明細
                    $Quantity = $rowList['SO_List_Quantity']; //下訂數量
                    $Product_id = $rowList['SO_Product_id']; //下定產品編號
                    $UnitPrice = $rowList['SO_List_UnitPrice']; //下訂單價
                    $SOList_id = $rowList['SOList_id']; //明細編號
                    $SOListRemarks = $rowList['SO_List_Remarks']; //明細狀態
                    
                    $getProduct = $conn->prepare("SELECT Productname,Product_Remarks FROM Products WHERE Product_id = :Product_id; ");
                    $getProduct->execute(array(':Product_id' => $Product_id));
                    $rowProduct = $getProduct->fetch(PDO::FETCH_ASSOC);
                    $Productname = $rowProduct['Productname'];
                    
                    echo "
                    <tr>
                    ";
                    if($SOListRemarks == "下訂成功"){
                      echo "<th><input type='checkbox' name='SOList_id[]' value='".$SOList_id."'></th>";
                    }else{
                      echo "<th> </th>";
                    }
                    
                    echo "    
                        <th>".$SO_Date."</th> <th>".$Productname."</th> <th>".$UnitPrice."</th> <th>".$Quantity."</th> <th>".$SOListRemarks."</th>
                        <input type='hidden' name='SOListRemarks' value='".$SOListRemarks."'>
                    </tr>
                    ";
                }
            }
            
          ?>

        </tbody>
      </table>
      
      <div class="col-lg-10"></div>
      <div class="col-lg-2">
        <button type="submit" class="btn btn-danger" name="delete">
          <span class="glyphicon glyphicon-trash" aria-hidden="true"> </span>
          取消
        </button>
      </div>
  </form> 
    </div> <!-- /container -->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="./js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
