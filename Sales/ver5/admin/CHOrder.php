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
    
    if(isset($_POST['reject'])){//無法受理
        $SOList_id = $_POST['reject'];
        $getSOL = $conn->prepare("SELECT SO_List_Remarks FROM SalesOrder_List WHERE SOList_id = :SOList_id");
        $getSOL->execute(array(':SOList_id' => $SOList_id));
        $selSOL = $getSOL->fetch(PDO::FETCH_ASSOC);
        $SOListRemarks = $selSOL['SO_List_Remarks'];
        
        if(mb_substr($SOListRemarks,0,4,"UTF-8") == "訂單結束" || mb_substr($SOListRemarks,0,3,"UTF-8") == "已取消" || mb_substr($SOListRemarks,0,4,"UTF-8") == "無法受理"){
            echo "<div class='alert alert-warning' role='alert'>訂單項目已無法更改, 請洽系統管理員</div>";
        }else{
            $Date = date("Y-m-d H:i:s");
            $unable = $conn->prepare("UPDATE SalesOrder_List SET SO_List_Remarks = :SO_List_Remarks WHERE SOList_id = :SOList_id;");
            $unable->execute(array(
              ':SO_List_Remarks' => "無法受理 $Date",
              ':SOList_id' => $SOList_id));
            
            $Pid = $conn->prepare("SELECT SO_Product_id,SO_List_Quantity FROM SalesOrder_List WHERE SOList_id = :SOList_id");
            $Pid->execute(array(':SOList_id' => $SOList_id));
            $rowPid = $Pid->fetch(PDO::FETCH_ASSOC); 
            $Pro_id = $rowPid['SO_Product_id'];
            $Qty = $rowPid['SO_List_Quantity'];
            
            $Product = $conn->prepare("SELECT Product_Qty FROM Products WHERE Product_id = :Product_id");
            $Product->execute(array(':Product_id' => $Pro_id));
            $rowProd = $Product->fetch(PDO::FETCH_ASSOC);
            
            
            if($Qty > 0){
                $upQty = $conn->prepare("UPDATE Products SET
                  Product_Qty = :Product_Qty
                      WHERE Product_id = :Product_id");
                $upQty->execute(array(
                  ':Product_Qty' => $rowProd['Product_Qty']+$Qty,
                      ':Product_id' => $Pro_id));
            }
              
            
            
            echo "<div class='alert alert-warning' role='alert'>訂單項目 $SOList_id 已拒絕受理</div>";
        }
        
    }
    
    if(isset($_POST['delivered'])){ //已送貨
        $SOList_id = $_POST['delivered'];
        $getSOL = $conn->prepare("SELECT SO_List_Remarks FROM SalesOrder_List WHERE SOList_id = :SOList_id");
        $getSOL->execute(array(':SOList_id' => $SOList_id));
        $selSOL = $getSOL->fetch(PDO::FETCH_ASSOC);
        $SOListRemarks = $selSOL['SO_List_Remarks'];
        
        if($SOListRemarks == "下訂成功"){
            $Date = date("Y-m-d H:i:s");
            $unable = $conn->prepare("UPDATE SalesOrder_List SET SO_List_Remarks = :SO_List_Remarks WHERE SOList_id = :SOList_id;");
            $unable->execute(array(
              ':SO_List_Remarks' => "已送貨 $Date",
              ':SOList_id' => $SOList_id));
            echo "<div class='alert alert-warning' role='alert'>訂單項目 $SOList_id 狀態改為 已送貨</div>";
        }else{
            echo "<div class='alert alert-warning' role='alert'>訂單項目狀態必須為 下訂成功 才可改為已送貨</div>";
        }
        
    }
    
    if(isset($_POST['completed'])){
        $SOList_id = $_POST['completed'];
        $getSOL = $conn->prepare("SELECT SO_List_Remarks FROM SalesOrder_List WHERE SOList_id = :SOList_id");
        $getSOL->execute(array(':SOList_id' => $SOList_id));
        $selSOL = $getSOL->fetch(PDO::FETCH_ASSOC);
        $SOListRemarks = $selSOL['SO_List_Remarks'];
        
        if(mb_substr($SOListRemarks,0,3,"UTF-8") == "已送貨"){
            $Date = date("Y-m-d H:i:s");
            $unable = $conn->prepare("UPDATE SalesOrder_List SET SO_List_Remarks = :SO_List_Remarks WHERE SOList_id = :SOList_id;");
            $unable->execute(array(
              ':SO_List_Remarks' => "訂單結束 $Date",
              ':SOList_id' => $SOList_id));
            echo "<div class='alert alert-warning' role='alert'>訂單項目 $SOList_id 狀態改為 訂單結束</div>";
        }else{
            echo "<div class='alert alert-warning' role='alert'>訂單項目狀態必須為 已送貨 才可改為結束</div>";
        }
    }
    
    
    
    if(isset($_POST['delSO'])){
        $SO_id = $_POST['delSO'];
        
        $delSOL = $conn->prepare("DELETE FROM SalesOrder_List WHERE SO_List_SalesOrder_id = :SO_List_SalesOrder_id;");
        $delSOL->execute(array(':SO_List_SalesOrder_id' => $SO_id));
        
        $delSO = $conn->prepare("DELETE FROM SalesOrder WHERE SalesOrder_id = :SalesOrder_id;");
        $delSO->execute(array(':SalesOrder_id' => $SO_id));
    }
    
    
    
    
    
    $getSO = $conn->prepare("SELECT SO_User_id,SalesOrder_id,SO_Date FROM SalesOrder; ");
    $getSO->execute();
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
                    <li><a href="./newProducts.php" class="btn btn-secondary btn-sm"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增產品</a></li>
                    <li class="active"><a class="btn btn-secondary btn-sm"><span class="glyphicon glyphicon-asterisk" aria-hidden="true"></span> 管理訂單</a></li>
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
    
    
    <div class="container">
  <form method="post">
      <table class="table">
    <?php
    
        for($i = 1; $i <= $getSO->rowCount(); $i++){ //Sales Order
            $rowSO = $getSO->fetch(PDO::FETCH_ASSOC);
            
            $SO_Date = $rowSO['SO_Date'];
            $SO_id = $rowSO['SalesOrder_id'];
            $SO_User_id = $rowSO['SO_User_id'];
            
            $getUsername = $conn->prepare("SELECT Username,Email FROM Users WHERE User_id = :User_id;");
            $getUsername->execute(array(':User_id' => $SO_User_id));
            $gUserRow = $getUsername->fetch(PDO::FETCH_ASSOC);
            $SO_Username = $gUserRow['Username'];
            $SO_Email = $gUserRow['Email'];
            
            //echo headers of Sales Order
            echo "
                <div class='row'><strong><h4>
                        <div class='col-xs-4'>下訂日期 : $SO_Date</div>
                        <div class='col-xs-2'>訂單編號 : $SO_id</div>
                        <div class='col-xs-2'>客戶帳號 : $SO_Username</div>
                        <div class='col-xs-3'>信箱 : $SO_Email</div>
                        <div class='col-xs-1'>
                            <button type='submit' class='btn btn-danger btn-xs' name='delSO' value='$SO_id'>
                            <span class='glyphicon glyphicon-remove' aria-hidden='true'> </span>
                            刪除訂單資料 $SO_id
                            </button>
                        </div>
                </h4></strong></div>
                <div class='row'><hr></div>
                
            ";
            //get Sales Order List
            $getSOList = $conn->prepare("SELECT SO_List_Quantity,SO_Product_id,SO_List_UnitPrice,SOList_id,SO_List_Remarks FROM SalesOrder_List WHERE SO_List_SalesOrder_id = :SO_List_SalesOrder_id; ");
            $getSOList->execute(array(':SO_List_SalesOrder_id' => $SO_id));
                
            //Sales Order List    
            for($j = 1; $j <= $getSOList->rowCount(); $j++){
                $rowList = $getSOList->fetch(PDO::FETCH_ASSOC);
                
                $Quantity = $rowList['SO_List_Quantity']; //下訂數量
                $Product_id = $rowList['SO_Product_id']; //下定產品編號
                $UnitPrice = $rowList['SO_List_UnitPrice']; //下訂單價
                $SOList_id = $rowList['SOList_id']; //明細編號
                $SOListRemarks = $rowList['SO_List_Remarks']; //明細狀態
                //Product Name, Remarks
                $getProduct = $conn->prepare("SELECT Productcode,Product_Remarks,Productname,Product_Remarks FROM Products WHERE Product_id = :Product_id; ");
                $getProduct->execute(array(':Product_id' => $Product_id));
                $rowProduct = $getProduct->fetch(PDO::FETCH_ASSOC);
                $Productname = $rowProduct['Productname'];
                $Product_Remarks = $rowProduct['Product_Remarks'];
                $Productcode = $rowProduct['Productcode'];
                
                echo "
                    <div class='row'><h5>
                    <div class='col-xs-2'>$Productcode $Productname</div>
                    <div class='col-xs-2'>$Product_Remarks</div>
                    <div class='col-xs-1'>$Quantity 單位</div>
                    <div class='col-xs-1'>金額 $UnitPrice</div>
                    <div class='col-xs-3'>$SOListRemarks</div>
                    ";
                    
                    if(mb_substr($SOListRemarks,0,3,"UTF-8") == "已取消" || mb_substr($SOListRemarks,0,4,"UTF-8") == "訂單結束" || mb_substr($SOListRemarks,0,4,"UTF-8") == "無法受理"){
                        echo "
                            <div class='col-xs-1'>
                                
                            </div>
                        ";
                    }else{
                        echo "
                            <div class='col-xs-1'>
                                <button type='submit' class='btn btn-danger btn-xs' name='reject' value='$SOList_id'>
                                <span class='glyphicon glyphicon-remove' aria-hidden='true'> </span>
                                無法受理 $SOList_id
                                </button>
                            </div>
                        ";
                    }
                    if($SOListRemarks == "下訂成功"){
                        echo "
                            <div class='col-xs-1'>
                                <button type='submit' class='btn btn-primary btn-xs' name='delivered' value='$SOList_id'>
                                <span class='glyphicon glyphicon-send' aria-hidden='true'> </span>
                                已送貨 $SOList_id
                                </button>
                            </div>
                        ";
                    }else{
                        echo "
                            <div class='col-xs-1'>
                                
                            </div>
                        ";
                    }
                    if(mb_substr($SOListRemarks,0,3,"UTF-8") == "已送貨"){
                        echo "
                            <div class='col-xs-1'>
                                <button type='submit' class='btn btn-success btn-xs' name='completed' value='$SOList_id'>
                                <span class='glyphicon glyphicon-ok' aria-hidden='true'> </span>
                                訂單結束 $SOList_id
                                </button>
                            </div>
                        ";
                    }else{
                        echo "
                            <div class='col-xs-1'>
                                
                            </div>
                        ";
                    }
                echo
                    "
                    <p></p></h5>
                    </div>
                ";
            
            }
            echo "
            <hr style='width: 100%; height: 1px; background-color:black;' />
            </tbody>";
        }
        
    ?>    
      </table>
      
      
  </form> 
    </div> <!-- /container -->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
