<?php
$listTotalRows = 10;
$listrows = 0;
$Status = "Add";
if(isset($_GET['view'])){
  $Status = "Edit";
}


    $dbhost = '127.0.0.1';  //資料庫位址 127.0.0.1 = localhost
    $dbuser = 'Signupuser'; //資料庫帳號
    $dbpass = '';           //資料庫密碼
    $dbname = 'c9';         //資料庫名稱
    try{
        $conn = new PDO("mysql:host=".$dbhost.";dbname=".$dbname.";charset=utf8mb4;",$dbuser,$dbpass); //建立資料庫連線物件

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);              //設定當資料庫連線出錯回訊息
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);       //設定用fetch時用PDO::FETCH_ASSOC
        $conn->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND,'SET NAMES UTF8');        //採萬國碼
    }catch (PDOException $e) {                                                     //接收錯誤訊息
        throw new PDOException("Error  : " .$e->getMessage());
    }
    $select = $conn->prepare("SELECT Username, User_id FROM Users WHERE userCookie = :ucook ;");    //Prepared Statement 避免出現 SQL Injection
    $select->execute(array(':ucook' => $_COOKIE['dbproject']));                                     //確定已登入所分配的Cookie['dbproject']是否存在於資料庫
    $row = $select->fetch(PDO::FETCH_ASSOC);
    if($row){   //如果有回傳資料代表Cookie存在(已登入)
                $username = $row['Username']; //將使用者名稱放入$username變數(登出旁邊)
                $User_id = $row['User_id'];   //將使用者id放入$User_id變數
    }else{
        header('Location: http://db4-ouvek-kostiva.c9users.io'); //如沒有回傳資料代表未登入, 回到登入頁面
    }
    
    if(isset($_GET['view'])){
      $update = $conn->prepare("SELECT * FROM PurchaseOrder WHERE PurchaseOrder_id = :PurchaseOrder_id");
      $update->execute(array(':PurchaseOrder_id' => $_GET['view']));
      $updaterow = $update->fetch(PDO::FETCH_ASSOC);
      
      $Vendor_id = $updaterow['PO_Vendor_id'];
      $vendor = $conn->prepare("SELECT * FROM Vendor WHERE Vendor_id = :Vendor_id");
      $vendor->execute(array(':Vendor_id' => $Vendor_id));
      $updatevendor = $vendor->fetch(PDO::FETCH_ASSOC);
      
      $getlist = $conn->prepare("SELECT * FROM PurchaseOrder_List WHERE PO_List_PurchaseOrder_id = :PO_List_PurchaseOrder_id");
      $getlist->execute(array(':PO_List_PurchaseOrder_id' => $_GET['view']));
      $listrows = $getlist->rowCount();
    }

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../favicon.ico">

    <title>進貨訂單-新增</title>
    <!-- Bootstrap core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="../css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../css/starter-template.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]-->
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <!--[endif]-->
  </head>

  <body>
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
            <li><a href="../Sales/index.php"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> 銷貨</a></li>
            <li class="active"><a href="./Purchase_Order_Add.php"><span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> 進貨</a></li>

            <li><a href="../Items/Item_Add.php"><span class="glyphicon glyphicon-indent-left" aria-hidden="true"></span> 品項</a></li>
            <li><a href="../Partners/Customer_Add.php"><span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span> 商業夥伴</a></li>
          </ul>
          <ul class="nav navbar-nav pull-right">
            <li class="navbar-left"><a href="../User.php"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> 個人設定</a></li>
            <p class="navbar-text"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> <?php echo $username ?></p>
            <li><a href="../signout.php"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> 登出</a></li>
          </ul>
        </div><!--/.nav-collapse -->
        <div id="navbar" class=""></div>
      </div>
    </nav>
    <nav class="navbar">
      <ul class="nav nav-tabs">
   
        <li role="presentation" class="active"><a>訂單</a></li>

        <div class="col-lg-3">
          <a href="./Purchase_Order_Search.php" class="btn btn-primary btn-sm">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
            查詢
          </a>
          <a class="btn btn-secondary btn-sm" disabled>
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            新增
          </a>
        </div>
      </ul>
    </nav>

    <div class="container">
<form class="form-horizontal" role="form" method="post" action="PO.php">
      <div class="row">
        <div class="col-lg-3">
          <div class="input-group">
            <span class="input-group-addon">供應商編號*</span>
            <input type="text" class="form-control" placeholder="PO_Vendor_id" name="PO_Vendor_id" value="<?php echo $updatevendor['Vendorcode']; ?>">
          </div><!-- /input-group -->
        </div><!-- /.col-lg-6 -->
        <div class="col-lg-3">
          <div class="input-group">
            <span class="input-group-addon">供應商名稱*</span>
            <input type="text" class="form-control" placeholder="Vendorname" name="Vendorname" value="<?php echo $updatevendor['Vendorname']; ?>">
          </div><!-- /input-group -->
        </div><!-- /.col-lg-6 -->
        <div class="col-lg-3">
          <div class="input-group">
            <span class="input-group-addon">供應商Email</span>
            <input type="text" class="form-control" placeholder="VendorTel" name="VendorEmail" value="<?php echo $updatevendor['VendorEmail']; ?>">
          </div><!-- /input-group -->
        </div><!-- /.col-lg-6 -->
      </div><!-- /.row -->

      <div class="row">
        <div class="col-lg-3">
          <div class="input-group">
            <span class="input-group-addon">訂單編號*</span>
            <input type="text" class="form-control" placeholder="PO_Code" 
            name="PO_Code" <?php if(!"" == trim($_GET['view'])){echo "value='".$updaterow['PO_Code']."' ";} ?>>
          </div><!-- /input-group -->
        </div><!-- /.col-lg-6 -->
        <div class="col-lg-3">
          <div class="input-group">
            <span class="input-group-addon">下訂日期*</span>
            <input type="text" class="form-control" placeholder="PO_Date" 
            name="PO_Date" value="<?php if(!"" == trim($_GET['view'])){ echo $updaterow['PO_Date']; }else{ echo date("Y-m-d H:i:s");} ?>">
          </div><!-- /input-group -->
        </div><!-- /.col-lg-6 -->
        <div class="col-lg-3">
          <div class="input-group">
            <span class="input-group-addon">送貨截止日</span>
            <input type="text" class="form-control" placeholder="PO_ValidDate" name="PO_ValidDate" value="<?php echo $updaterow['PO_ValidDate']; ?>">
          </div><!-- /input-group -->
        </div><!-- /.col-lg-6 -->
        <div class="col-lg-3">
          <div class="input-group">
            <span class="input-group-addon">送貨地址</span>
            <input type="text" class="form-control" placeholder="PO_Address" name="PO_Address" value="<?php $updaterow['PO_Address']; ?>">
          </div><!-- /input-group -->
        </div><!-- /.col-lg-6 -->
      </div><!-- /.row -->

      <div class="input-group input-group-sm"></div>
      <table class="table">
        <thead>
          <tr>
            <th>列</th>
            <th>商品編號</th>
            <th>名稱</th>
            <th>數量</th>
            <th>單價</th>
            <th>折扣 %</th>
            <th>稅率 %</th>
            <th>備註</th>
          </tr>
        </thead>
        <tbody>
          <?php
          echo "<input type='hidden' name='rows' value='$listTotalRows'>";
          
          /*  ~~~~~~~~~~~~~~~~~~~~~~~~~~~顯示清單輸入框~~~~~~~~~~~~~~~~~~~~~~~~~~~  */

          if(isset($_GET['view'])){
            echo "<input type='hidden' name='POid' value='".$_GET['view']."'>";
            
            for($i = 1; $i <= $listrows; $i++){
                $updatelist = $getlist->fetch(PDO::FETCH_ASSOC);
                $getProduct = $conn->prepare("SELECT Productcode,Productname FROM Products WHERE Product_id = :Product_id");
                $getProduct->execute(array(':Product_id' => $updatelist['PO_List_Product_id']));
                $updateProduct = $getProduct->fetch(PDO::FETCH_ASSOC);

                echo "
                    <tr>
                      <td>$i</td>
           <input type='hidden' name='listUpdate[]' value='".$updatelist['PO_List_Product_id']."'></td>
         <td><input type='text' class='form-control' placeholder='Productcode[]' name='Productcode[]' value='".$updateProduct['Productcode']."'></td>
      <td><input type='text' class='form-control' placeholder='Productname[]' name='Productname[]' value='".$updateProduct['Productname']."'></td>
       <td><input type='number' min='0' max='9999999999999' class='form-control' placeholder='Qty[]' name='Qty[]' value='".$updatelist['PO_List_Quantity']."'></td>
       <td><input type='text' class='form-control' placeholder='UnitPrice[]' name='UnitPrice[]' value='".$updatelist['PO_List_UnitPrice']."'></td>                      <td><input type='text' class='form-control' placeholder='Discount[]' name='Discount[]' value='".$updatelist['PO_List_Discount']."'></td>
         <td><input type='text' class='form-control' placeholder='Tax[]' name='Tax[]' value='".$updatelist['PO_List_Tax']."'></td>
      <td><input type='text' class='form-control' placeholder='Remarks[]' name='Remarks[]' value='".$updatelist['PO_List_Remarks']."'></td>
                    </tr>
                ";
            }
          }
            for($i = $listrows+1; $i <= $listTotalRows; $i++){
                echo "
                    <tr>
                      <td>$i</td>
                      <td><input type='text' class='form-control' placeholder='Productcode[]' name='Productcode[]'></td>
                      <td><input type='text' class='form-control' placeholder='Productname[]' name='Productname[]'></td>
                      <td><input type='number' min='0' max='9999999999999' class='form-control' placeholder='Qty[]' name='Qty[]'></td>
                      <td><input type='text' class='form-control' placeholder='UnitPrice[]' name='UnitPrice[]'></td>
                      <td><input type='text' class='form-control' placeholder='Discount[]' name='Discount[]'></td>
                      <td><input type='text' class='form-control' placeholder='Tax[]' name='Tax[]'></td>
                      <td><input type='text' class='form-control' placeholder='Remarks[]' name='Remarks[]'></td>
                    </tr>
                ";
            }

          /*  ~~~~~~~~~~~~~~~~~~~~~~~~~~~顯示清單輸入框 輸入~~~~~~~~~~~~~~~~~~~~~~~~~~~  */
          ?>


<!-- Haven't changed below for javascript end  -->

        </tbody>
      </table>

    <!--  <div class="col-lg-3">
        <div class="input-group">
          <span class="input-group-addon">總金額 $</span>
          <input type="text" class="form-control" placeholder="PO_Amount" name="PO_Amount">
        </div>--><!-- /input-group -->
    <!--  </div>--><!-- /.col-lg-6 -->

      <div class="col-lg-6"> </div>
      <div class="col-lg-2">
        <button type="submit" class="btn btn-danger" name="delete" value="<?php echo $_GET['view']; ?>">
          <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
          刪除本訂單
        </button>
      </div><!-- /.col-lg-6 -->
      <div class="col-lg-2">
      </div><!-- /.col-lg-6 -->
      <div class="col-lg-2">
        <?php
        if($Status == "Add"){
          echo "<button type='submit' class='btn btn-default' name='add'>
                  <span class='glyphicon glyphicon-ok' aria-hidden='true'></span>
                  新增訂單
                </button>";
        }elseif($Status == "Edit"){
          echo "<button type='submit' class='btn btn-default' name='edit'>
                  <span class='glyphicon glyphicon-ok' aria-hidden='true'></span>
                  變更訂單
                </button>";
        }
        ?>
      </div><!-- /.col-lg-6 -->
</form>
      <h3>.</h3>

    </div><!-- /.container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="../js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../js/ie10-viewport-bug-workaround.js"></script>

    <script type="text/javascript" src="../js/count.js"></script>
  </body>
</html>
