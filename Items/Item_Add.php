<?php
    $dbhost = '127.0.0.1';
    $dbuser = 'Signupuser';
    $dbpass = '';
    $dbname = 'c9';
    try{
        $conn = new PDO("mysql:host=".$dbhost.";dbname=".$dbname,$dbuser,$dbpass);

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        $conn->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND,'SET NAMES UTF8');
    }catch (PDOException $e) {
        throw new PDOException("Error  : " .$e->getMessage());
    }
    $select = $conn->prepare("SELECT Username FROM Users WHERE userCookie = :ucook ;");
    $select->execute(array(':ucook' => $_COOKIE['dbproject']));
    $row = $select->fetch(PDO::FETCH_ASSOC);
    if($row){
                $username = $row['Username'];
    }else{
        header('Location: http://db4-ouvek-kostiva.c9users.io');
    }

if(isset($_GET['view'])){

    $Searchsel = $conn->prepare("SELECT * FROM Products WHERE Productcode = :Productcode;");
    $Searchsel->execute(array(':Productcode' => $_GET['view']));//執行SQL
    $SearchExist = $Searchsel->rowCount(); //取得數量

    if($SearchExist > 0){
      $search = $Searchsel->fetch(PDO::FETCH_ASSOC);
    }

}

if(isset($_POST['save'])){

    $has_needed = true;

    if(!"" == trim($_POST['Productcode'])){
        $Productcode = $_POST['Productcode'];
    }else{
        $has_needed = false;
        echo "</br></br><div class='alert alert-danger' role='alert'> 請輸入物品編號 </div>";
    }

    if(!"" == trim($_POST['Product_Barcode'])){
        $Product_Barcode = $_POST['Product_Barcode'];
    }else{$Product_Barcode = NULL;}
    
    if(!"" == trim($_POST['Product_Qty'])){
        $Product_Qty = $_POST['Product_Qty'];
    }else{$Product_Qty = NULL;}

    if(!"" == trim($_POST['Productname'])){
        $Productname = $_POST['Productname'];
    }else{
        $has_needed = false;
        echo "</br></br><div class='alert alert-danger' role='alert'> 請輸入物品名稱 </div>";
    }

    if(!"" == trim($_POST['Productname2'])){
        $Productname2 = $_POST['Productname2'];
    }else{$Productname2 = NULL;}

    if(!"" == trim($_POST['Unit'])){
        $Unit = $_POST['Unit'];
    }else{$Unit = NULL;}

    if(!"" == trim($_POST['Product_Remarks'])){
        $Product_Remarks = $_POST['Product_Remarks'];
    }else{$Product_Remarks = NULL;}

    if($has_needed){ //檢查是否有完成必填

        $Pcodesel = $conn->prepare("SELECT COUNT(Product_id) FROM Products WHERE Productcode = :Productcode; ");
        $Pcodesel->execute(array(':Productcode' => $Productcode));//執行SQL
        $PcodeExist = $Pcodesel->rowCount(); //取得數量
        $PcodeExist = $Pcodesel->fetchColumn();

        if($PcodeExist > 0){ //Productcode已存在 (更新)
            $update = $conn->prepare("UPDATE Products SET
                Productname = :Productname,
                Product_Barcode = :Product_Barcode,
                Productname2 = :Productname2,
                Product_Remarks = :Product_Remarks,
                Unit = :Unit,
                Product_Qty = :Product_Qty
                WHERE
                Productcode = :Productcode;");
            $update->execute(array(
                ':Productname' => $Productname,
                ':Product_Barcode' => $Product_Barcode,
                ':Productname2' => $Productname2,
                ':Product_Remarks' => $Product_Remarks,
                ':Unit' => $Unit,
                ':Product_Qty' => $Product_Qty,
                ':Productcode' => $Productcode
              ));
            if($update->rowCount() > 0){
                echo "<div class='alert alert-success' role='alert'> 成功更新 $Productcode 資料 </div>";
            }else{
                echo "<div class='alert alert-warning' role='alert'> 更新有問題 </div>";
            }
        }else{ //Productcode不存在 (新增)
            $insert = $conn->prepare("INSERT INTO Products (
                Productname,
                Product_Barcode,
                Productname2,
                Product_Remarks,
                Unit,
                Productcode) VALUES (
                    :Productname,
                    :Product_Barcode,
                    :Productname2,
                    :Product_Remarks,
                    :Unit,
                    :Productcode);");
            $insert->execute(array(
                ':Productname' => $Productname,
                ':Product_Barcode' => $Product_Barcode,
                ':Productname2' => $Productname2,
                ':Product_Remarks' => $Product_Remarks,
                ':Unit' => $Unit,
                ':Productcode' => $Productcode));
            if($insert->rowCount() > 0){
                echo "<div class='alert alert-success' role='alert'> 成功新增 $Productcode 資料 </div>";
            }else{
                echo "<div class='alert alert-warning' role='alert'> 新增有問題 </div>";
            }
        }

    }

}elseif (isset($_POST['delete'])) {
    if(!"" == trim($_POST['Productcode'])){
        $Productcode = $_POST['Productcode'];
    }else{
        echo "</br></br><div class='alert alert-danger' role='alert'> 請輸入物品編號 </div>";
    }

    $Pcodesel = $conn->prepare("SELECT COUNT(Product_id) FROM Products WHERE Productcode = :Productcode; ");
    $Pcodesel->execute(array(':Productcode' => $Productcode));//執行SQL
    $PcodeExist = $Pcodesel->rowCount(); //取得數量

    if($PcodeExist > 0){
        $update = $conn->prepare("UPDATE Products SET
                Productname = :Productname,
                Product_Barcode = :Product_Barcode,
                Productname2 = :Productname2,
                Product_Remarks = :Product_Remarks,
                Unit = :Unit,
                Product_Qty = :Product_Qty
                WHERE
                Productcode = :Productcode;");
            $update->execute(array(
                ':Productname' => "ItemDeleted",
                ':Product_Barcode' => "ItemDeleted",
                ':Productname2' => "ItemDeleted",
                ':Product_Remarks' => "ItemDeleted",
                ':Unit' => "ItemDeleted",
                ':Product_Qty' => 0,
                ':Productcode' => $Productcode
              ));
            if($update->rowCount() > 0){
                echo "<div class='alert alert-success' role='alert'> 成功刪除 $Productcode 資料 </div>";
            }else{
                echo "<div class='alert alert-warning' role='alert'> 刪除有問題 </div>";
            }
    }else{
        echo "</br></br><div class='alert alert-danger' role='alert'> 你想刪除東西跟本不存在啊！ </div>";
    }

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

    <title>物品-新增</title>
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
          <li><a href="../Purchase/Purchase_Order_Add.php"><span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> 進貨</a></li>

          <li class="active"><a href="./Item_Add.php"><span class="glyphicon glyphicon-indent-left" aria-hidden="true"></span> 品項</a></li>
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
        <li role="presentation" class="active"><a>物品</a></li>
        <li role="presentation"><a href="../Sales/admin/CHOrder.php">已售</a></li>
        <div class="col-lg-3">
          <a href="./Item_Search.php" class="btn btn-primary btn-sm">
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
<form method="post" >
      <div class="row">
        <div class="col-lg-3">
          <div class="input-group">
            <span class="input-group-addon">物品編號*</span>
            <input type="text" class="form-control" placeholder="Productcode" 
            name="Productcode" value="<?php if($SearchExist > 0){echo $search['Productcode']; } ?>">
          </div><!-- /input-group -->
        </div><!-- /.col-lg-6 -->
        <div class="col-lg-3">
          <div class="input-group">
            <span class="input-group-addon">物品條碼</span>
            <input type="text" class="form-control" placeholder="Product_Barcode" 
            name="Product_Barcode" value="<?php if($SearchExist > 0){echo $search['Product_Barcode']; } ?>">
          </div><!-- /input-group -->
        </div><!-- /.col-lg-6 -->
        <div class="col-lg-3">
          <div class="input-group">
            <span class="input-group-addon">物品名稱*</span>
            <input type="text" class="form-control" placeholder="Productname" 
            name="Productname" value="<?php if($SearchExist > 0){echo $search['Productname']; } ?>">
          </div><!-- /input-group -->
        </div><!-- /.col-lg-6 -->
        <div class="col-lg-3">
          <div class="input-group">
            <span class="input-group-addon">現時售價</span>
            <input type="text" class="form-control" placeholder="Productname2" 
            name="Productname2" value="<?php if($SearchExist > 0){echo $search['Productname2']; } ?>">
          </div><!-- /input-group -->
        </div><!-- /.col-lg-6 -->
      </div><!-- /.row -->

      <div class="row">
        <div class="col-lg-2">
          <div class="input-group">
            <span class="input-group-addon">單位</span>
            <input type="text" class="form-control" placeholder="Unit" 
            name="Unit" value="<?php if($SearchExist > 0){echo $search['Unit']; } ?>">
          </div><!-- /input-group -->
        </div><!-- /.col-lg-6 -->
        <div class="col-lg-7">
          <div class="input-group">
            <span class="input-group-addon">備註</span>
            <input type="text" class="form-control" placeholder="Product_Remarks" 
            name="Product_Remarks" value="<?php if($SearchExist > 0){echo $search['Product_Remarks']; } ?>">
          </div><!-- /input-group-->
        </div><!-- /.col-lg-6 -->
        <div class="col-lg-3">
          <div class="input-group">
            <span class="input-group-addon">數量</span>
            <input type="text" class="form-control" placeholder="Product_Qty" 
            name="Product_Qty" value="<?php if($SearchExist > 0){echo $search['Product_Qty']; } ?>">
          </div><!-- /input-group -->
        </div><!-- /.col-lg-6 -->
      </div><!-- /.row -->

            <h3> </h3>

      <div class="col-lg-8"></div>
      <div class="col-lg-2">
        <button type="submit" class="btn btn-default" name="save">
          <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
          儲存物品資料
        </button>
      </div><!-- /.col-lg-6 -->
      <div class="col-lg-2">
        <button type="submit" class="btn btn-danger" name="delete">
          <span class="glyphicon glyphicon-trash" aria-hidden="true"> </span>
          刪除
        </button>
      </div><!-- /.col-lg-6 -->
</form>
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
