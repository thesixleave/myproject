<?php
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
    $select->execute(array(':ucook' => $_COOKIE['dbproject']));                               //確定已登入所分配的Cookie['dbproject']是否存在於資料庫
    $row = $select->fetch(PDO::FETCH_ASSOC);
    if($row){   //如果有回傳資料代表Cookie存在(已登入)
                $username = $row['Username']; //將使用者名稱放入$username變數(登出旁邊)
                $User_id = $row['User_id'];   //將使用者id放入$User_id變數
    }else{
        header('Location: http://db4-ouvek-kostiva.c9users.io'); //如沒有回傳資料代表未登入, 回到登入頁面
    }

/*************************************************供應商********************************************************/
$has_vendor = false; //是否有輸入供應商
if(!"" == trim($_POST['PO_Vendor_id'])){ //確定PO_Vendor_id是否有輸入
    $has_vendor = true;
    $Vendorcode = $_POST['PO_Vendor_id'];
}else{
    $vendor_check = $vendor_check."供應商編號 \t"; //檢查是否有輸入的Vendor訊息
    $Vendorcode = NULL;
}

if(!"" == trim($_POST['Vendorname'])){
    $Vendorname = $_POST['Vendorname'];
    $has_vendor = true;
}else{
    $vendor_check = $vendor_check."供應商名稱 \t";
    $Vendorname = NULL;
}

if(!"" == trim($_POST['VendorEmail'])){
    $VendorEmail = $_POST['VendorEmail'];
    $has_vendor = true;
}else{
    $vendor_check = $vendor_check."供應商Email \t";
    $VendorTel = NULL;
}
/*************************************************訂單標頭********************************************************/
if(!"" == trim($_POST['PO_Date'])){ //是否有輸入日期
    $PO_Date = date("Y-m-d H:i:s", strtotime($_POST['PO_Date'])); //將日期從字串->變整數->變MySQL日期格式
}else{
    $PO_Date = date("Y-m-d H:i:s"); //未輸入日期自動填入現在伺服器時間
}

if(!"" == trim($_POST['PO_ValidDate'])){ //送貨截止日
    $PO_ValidDate = date("Y-m-d H:i:s", strtotime($_POST['PO_ValidDate']));
}else{
    $PO_ValidDate = NULL; //空值
}

if(!"" == trim($_POST['PO_Address'])){ //送貨地址
    $PO_Address = $_POST['PO_Address'];
}else{
    $PO_Address = NULL;
}

if(!"" == trim($_POST['PO_Amount'])){ //總金額
    $PO_Amount = $_POST['PO_Amount'];
}else{
    $PO_Amount = NULL;
}

if(!"" == trim($_POST['PO_Code'])){ //是否有輸入 PO_Code
    $PO_Code = $_POST['PO_Code'];
}else {
    $PO_Code = NULL;
}
/*************************************************是否有商品********************************************************/
$has_product = false;
if(count($_POST["Productname"]) > 0 || count($_POST["Productcode"]) > 0){
    $Productname = $_POST["Productname"];
    $Productcode = $_POST["Productcode"];
    $has_product = true;
}
$Qty = $_POST["Qty"];
$UnitPrice = $_POST["UnitPrice"];
$Discount = $_POST["Discount"];
$Tax = $_POST["Tax"];
$Remarks = $_POST["Remarks"];
/*************************************************回到上一頁********************************************************/
$goback = "
    3秒<a onclick='goBack()'>回到上一頁</button>

    <script>
    var speed = 3000;
    setTimeout('history.back()', speed);
    function goBack() {
        window.history.back();
    }
    </script>";
/*************************************************按下新增********************************************************/
if(isset($_POST['add'])){
    if($has_vendor){
        $Vendor_id = checkVendor($Vendorcode,$Vendorname,$VendorEmail,$VendorTel,$conn);
        if(is_numeric($Vendor_id)){
            if(!is_null($PO_Code)){
                if(checkPOCode($PO_Code,$conn)){ //PO_Code存在
                    echo "<div class='alert alert-danger' role='alert'> 訂單編號 $PO_Code 已使用 </div>$goback";
                }else{
                    $chkHeader = checkHeader($PO_Code,$PO_Date,$PO_ValidDate,$PO_Address,$PO_Amount,$conn);
                    if($chkHeader == "OK"){
                        if($has_product){
                            $list = getList($Productcode,$Productname,$Qty,$UnitPrice,$Discount,$Tax,$Remarks,$conn,$_POST['listrows']);
                            if(is_null($list)){
                                echo "<div class='alert alert-danger' role='alert'> 物品不存在 或 數量為0 </div>$goback";
                            }else{
                                $return = insert($list,$Vendor_id,$PO_Code,$PO_Date,$PO_ValidDate,$PO_Address,$conn,$User_id);
                                if($return['success']){
                                    echo "<div class='alert alert-success' role='alert'> 新增訂單成功".
    " </div>回到<a href='http://db4-ouvek-kostiva.c9users.io/Purchase/Purchase_Order_Add.php?view=".$return['PO_id']."'>進貨訂單</a>";
                                }else{
                                    echo "<div class='alert alert-danger' role='alert'>".$return['message']." </div>";//."$goback";
                                }
                            }
                        }else{
                            echo "<div class='alert alert-danger' role='alert'> 請至少填一項物品 </div>$goback";
                        }
                    }else{
                        echo "<div class='alert alert-danger' role='alert'> $chkHeader 為必填項目 </div>$goback";
                    }
                }
            }else{
                echo "<div class='alert alert-danger' role='alert'> $PO_Code 請輸入訂單編號 </div>$goback";
            }
        }else{
            echo "<div class='alert alert-danger' role='alert'> $Vendor_id 找不到供應商資料 </div>$goback";
        }
    }else{
        echo "<div class='alert alert-danger' role='alert'> 請至少在 $vendor_check 其中一格中輸入一項供應商資料 </div>$goback";
    }
}
/**********************************************按下Edit******************************************************/
if(isset($_POST['edit'])){
    $PurchaseOrder_id = $_POST['POid'];
   
    if($has_vendor){
        $Vendor_id = checkVendor($Vendorcode,$Vendorname,$VendorEmail,$VendorTel,$conn);
        if(is_numeric($Vendor_id)){
            if(!is_null($PO_Code)){
                if($PO_Code == NULL){
                    echo "<div class='alert alert-danger' role='alert'> 必須輸入訂單編號 </div>$goback";
                }else {
                    $chkHeader = checkHeader($PO_Code,$PO_Date,$PO_ValidDate,$PO_Address,$PO_Amount,$conn);
                    if($chkHeader == "OK"){
                        if($has_product){
                            $list = getList($Productcode,$Productname,$Qty,$UnitPrice,$Discount,$Tax,$Remarks,$conn,$_POST['listrows']);
                            if(is_null($list)){
                                echo "<div class='alert alert-danger' role='alert'>"." 物品不存在 或 數量為0 </div>$goback";
                            }else{
                                $return = update($list,$Vendor_id,$PO_Code,$PO_Date,$PO_ValidDate,$PO_Address,$conn,$User_id,$PurchaseOrder_id);
                                if($return['success']){
                                    echo "<div class='alert alert-success' role='alert'> 更改訂單成功"." 
            </div>回到<a href='http://db4-ouvek-kostiva.c9users.io/Purchase/Purchase_Order_Add.php?view=".$return['PO_id']."'>進貨訂單</a>";
                                }else{
                                    echo "<div class='alert alert-danger' role='alert'>".$return['message']." </div>";//."$goback";
                                }
                            }
                        }else{
                            echo "<div class='alert alert-danger' role='alert'> 請至少填一項物品 </div>$goback";
                        }
                    }else{
                        echo "<div class='alert alert-danger' role='alert'> $chkHeader 為必填項目 </div>$goback";
                    }
                }
            }else {
                echo "<div class='alert alert-danger' role='alert'> $PO_Code 請輸入訂單編號 </div>$goback";
            }
        }else {
            echo "<div class='alert alert-danger' role='alert'> $Vendor_id 找不到供應商資料 </div>$goback";
        }
    }else{
        echo "<div class='alert alert-danger' role='alert'> 請至少在 $vendor_check 其中一格中輸入一項供應商資料 </div>$goback";
    }
}

/**********************************************檢查供應商資料******************************************************/
function checkVendor($Vendorcode,$Vendorname,$VendorEmail,$VendorTel,$conn)
{
    $array = array(
        ':Vendorcode' => $Vendorcode,
        ':Vendorname' => $Vendorname,
        ':VendorEmail' => $VendorEmail,
        ':VendorTel' => $VendorTel);
    $array = array_filter($array,function ($value) {return null !== $value;});
    $query = 'SELECT * FROM Vendor WHERE';
    $values = array();
    
    foreach ($array as $name => $value) {
    $query .= ' '.substr($name,1).' = '.$name.' AND'; // the :$name part is the placeholder, e.g. :zip
    $values[''.$name] = $value; // save the placeholder
    }
    
    $query = substr($query, 0, -3).';';
    $select = $conn->prepare($query);
    $select->execute($values);
    $row = $select->fetch(PDO::FETCH_OBJ);
    
    return $row->Vendor_id;
    
}
/**********************************************檢查訂單編號******************************************************/
function checkPOCode($PO_Code,$conn)
{
    $query = 'SELECT * FROM PurchaseOrder WHERE PO_Code = :PO_Code';
    $select = $conn->prepare($query);
    $select->execute(array(':PO_Code' => $PO_Code));
    if($select->rowCount() > 0){
        return true;
    }else {
        return false;
    }
}

/**********************************************檢查訂單標頭******************************************************/
function checkHeader($PO_Code,$PO_Date,$PO_ValidDate,$PO_Address,$PO_Amount,$conn){
        $notPresent = "";
        $hasNeeded = true;
    if(is_null($PO_Code)){
        $notPresent = $notPresent." 訂單編號 ";
        $hasNeeded = false;
    }
    if(is_null($PO_Date)){
        $notPresent = $notPresent." 訂單日期 ";
        $hasNeeded = false;
    }
    if($hasNeeded){
        return "OK";
    }else{
        return $notPresent;
    }
}
/**********************************************取得物品列表******************************************************/
function getList($Productcode,$Productname,$Qty,$UnitPrice,$Discount,$Tax,$Remarks,$conn,$listrows)
{
    $list = array('Product_id'=>NULL,
        'Qty'=>NULL,
        'UnitPrice'=>NULL,
        'Discount'=>NULL,
        'Tax'=>NULL,
        'Remarks'=>NULL);
    
    for($i = 0; $i < count($Productcode); $i++){

        /*************Product_id*************/
        if(empty($Productname[$i]) && empty($Productcode[$i])){
            continue;
        }elseif(!empty($Productname[$i])){
            $query = 'SELECT Product_id FROM Products WHERE Productname = :Productname';
            $select = $conn->prepare($query);
            $select->execute(array(':Productname' => trim($Productname[$i])));
            $r = $select->fetch(PDO::FETCH_ASSOC);
            if($select->rowCount() > 0){
                $Product_id = $r['Product_id'];
            }
            $list['Product_id'][] = $Product_id;
            $list['Qty'][] = trim($Qty[$i]);
            $list['UnitPrice'][] = trim($UnitPrice[$i]);
            $list['Discount'][] = trim($Discount[$i]);
            $list['Tax'][] = trim($Tax[$i]);
            $list['Remarks'][] = trim($Remarks[$i]);
        }elseif(!empty($Productcode[$i])){
            $query = 'SELECT Product_id FROM Products WHERE Productcode = :Productcode';
            $select = $conn->prepare($query);
            $select->execute(array(':Productcode' => trim($Productcode[$i])));
            $r = $select->fetch(PDO::FETCH_ASSOC);
            if($select->rowCount() > 0){
                $Product_id = $r['Product_id'];
            }
            $list['Product_id'][] = $Product_id;
            $list['Qty'][] = trim($Qty[$i]);
            $list['UnitPrice'][] = trim($UnitPrice[$i]);
            $list['Discount'][] = trim($Discount[$i]);
            $list['Tax'][] = trim($Tax[$i]);
            $list['Remarks'][] = trim($Remarks[$i]);
        }
        /*************Qty UnitPrice*************/
        if(trim($Qty[$i]) == ""){
            $Qty[$i] = 0;
            return NULL;
        }
        if(trim($UnitPrice[$i]) == ""){
            $UnitPrice[$i] = 0;
        }
        /*************Append List*************/
        
    }
    
    if(is_null($list['Product_id'])){
        return NULL;
    }else{
        return $list;
    }
}
/***************************Transaction***************************/
function insert($list,$Vendor_id,$PO_Code,$PO_Date,$PO_ValidDate,$PO_Address,$conn,$PO_User_id)
{
    $commit = true;
    $return = array(
        'message' => 'Add',
        'success' => false,
        'PO_id' => NULL);
    
    $q1 = "INSERT INTO PurchaseOrder (
        PO_Date,PO_ValidDate,PO_Address,PO_Vendor_id,PO_User_id,PO_Code
        ) VALUES (
        :PO_Date,:PO_ValidDate,:PO_Address,:PO_Vendor_id,:PO_User_id,:PO_Code)";
        
    $q2 = "INSERT INTO PurchaseOrder_List (PO_List_Quantity,PO_List_Discount,PO_List_UnitPrice,
           PO_List_Tax,PO_List_Remarks,PO_List_Product_id,PO_List_PurchaseOrder_id
        ) VALUES (
           :PO_List_Quantity,:PO_List_Discount,:PO_List_UnitPrice,
           :PO_List_Tax,:PO_List_Remarks,:PO_List_Product_id,:PO_List_PurchaseOrder_id)";
        
    try{
        $conn->beginTransaction();
        $stmt1 = $conn->prepare($q1);
        if($stmt1){
            $stmt1->execute(array(
                ":PO_Date" => $PO_Date,":PO_ValidDate"=>$PO_ValidDate,":PO_Address"=>$PO_Address,
                ":PO_Vendor_id"=>$Vendor_id,":PO_User_id"=>$PO_User_id,":PO_Code"=>$PO_Code));
            $PO_List_PurchaseOrder_id = $conn->lastInsertId();
            if($stmt1->rowCount() > 0){
                for($i = 0; $i < count($list); $i++){
                    
                    $PO_List_Product_id = $list['Product_id'][$i];
                    $PO_List_Quantity = $list['Qty'][$i];
                    $PO_List_UnitPrice = $list['UnitPrice'][$i];
                    $PO_List_Discount = $list['Discount'][$i];
                    $PO_List_Tax = $list['Tax'][$i];
                    $PO_List_Remarks = $list['Remarks'][$i];
                    
                    if(is_null($PO_List_Quantity)){
                        $return['message'] = " 未新增 數量 為零者:$i,".$PO_List_Product_id[$i]." | ";
                        continue;
                    }
                    if(is_null($PO_List_UnitPrice)){
                        $PO_List_UnitPrice = 0;
                        $return['message'] = " 單價為零:$i,".$PO_List_Product_id[$i]." | ";
                    }
                    
                    $stmt2 = $conn->prepare($q2);
                    $stmt2->execute(array(
                        ":PO_List_Quantity"=>$PO_List_Quantity,":PO_List_Discount"=>$PO_List_Discount,":PO_List_UnitPrice"=>$PO_List_UnitPrice,
                        ":PO_List_Tax"=>$PO_List_Tax,
                        ":PO_List_Remarks"=>$PO_List_Remarks,
                        ":PO_List_Product_id"=>$PO_List_Product_id,":PO_List_PurchaseOrder_id"=>$PO_List_PurchaseOrder_id));
                    if($stmt2->rowCount() == 0){
                        $commit = false;
                    }
                }
            }else{
                $commit = false;
            }
        }
    }catch(PDOException $e){
        $return['message'] = "Error Message:  " . $e->getMessage();
        $commit = false;
    }
    
    if(!$commit){
		    $conn->rollback();
	    } else {
		    $conn->commit();
		    $return['success'] = true;
		    $return['PO_id'] = $PO_List_PurchaseOrder_id;
	    }
	    
	return $return;
}
/**********************************************更新******************************************************/
function update($list,$Vendor_id,$PO_Code,$PO_Date,$PO_ValidDate,$PO_Address,$conn,$User_id,$PurchaseOrder_id){
    $POList_id = $_POST['listUpdate'];
    
    $commit = true;
    $return = array(
        'message' => 'Update',
        'success' => false,
        'PO_id' => NULL);
        
    $q1 = "UPDATE PurchaseOrder SET
        PO_Date = :PO_Date, PO_ValidDate = :PO_ValidDate, PO_Address = :PO_Address, 
        PO_Vendor_id = :PO_Vendor_id, PO_User_id = :PO_User_id, PO_Code = :PO_Code
        WHERE PurchaseOrder_id = :PurchaseOrder_id";
        
    $q2 = "UPDATE PurchaseOrder_List SET PO_List_Quantity = :PO_List_Quantity, PO_List_Discount = :PO_List_Discount,
        PO_List_UnitPrice = :PO_List_UnitPrice, PO_List_Tax = :PO_List_Tax, PO_List_Remarks = :PO_List_Remarks,
        PO_List_Product_id = :PO_List_Product_id
        WHERE POList_id = :POList_id";
    
    try{
        
        $conn->beginTransaction();
        $stmt1 = $conn->prepare($q1);
        if($stmt1){
            $stmt1->execute(array(
                ":PO_Date" => $PO_Date,":PO_ValidDate"=>$PO_ValidDate,":PO_Address"=>$PO_Address,
                ":PO_Vendor_id"=>$Vendor_id,":PO_User_id"=>$User_id,":PO_Code"=>$PO_Code,":PurchaseOrder_id"=>$PurchaseOrder_id ));
            if($stmt1->rowCount() >= 0){
                
                $stmt3 = $conn->prepare("SELECT POList_id FROM PurchaseOrder_List WHERE PO_List_PurchaseOrder_id = :PO_List_PurchaseOrder_id");
                $stmt3->execute(array(':PO_List_PurchaseOrder_id' => $PurchaseOrder_id));
                $listnum = $stmt3->rowCount();
                
                for($i = 0; $i < $listnum; $i++){
                    $r = $stmt3->fetch(PDO::FETCH_ASSOC);
                    $POL_id = $r['POList_id'];
                   
                    $PO_List_Product_id = $list['Product_id'][$i];
                    $PO_List_Quantity = $list['Qty'][$i];
                    $PO_List_UnitPrice = $list['UnitPrice'][$i];
                    $PO_List_Discount = $list['Discount'][$i];
                    $PO_List_Tax = $list['Tax'][$i];
                    $PO_List_Remarks = $list['Remarks'][$i];
                    if(is_null($PO_List_Quantity)){
                        $return['message'] = " 減列 數量 為零者:$i,".$POL_id." | ";
                        continue;
                    }
                    if(is_null($PO_List_UnitPrice)){
                        $PO_List_UnitPrice = 0;
                        $return['message'] = " 單價為零:$i,".$POL_id." | ";
                    }
                    
                        $stmt2 = $conn->prepare($q2);
                        $stmt2->execute(array(
                            ":PO_List_Quantity"=>$PO_List_Quantity,
                            ":PO_List_Discount"=>$PO_List_Discount,":PO_List_UnitPrice"=>$PO_List_UnitPrice,
                            ":PO_List_Tax"=>$PO_List_Tax,
                            ":PO_List_Remarks"=>$PO_List_Remarks,
                            ":PO_List_Product_id"=>$PO_List_Product_id,":POList_id"=>$POL_id));
                }
            }
        }
    }catch(PDOException $e){
        $return['message'] = "Error Message:  " . $e->getMessage();
        $commit = false;
    }
    
    if(!$commit){
		    $conn->rollback();
	    } else {
		    $conn->commit();
		    $return['success'] = true;
		    $return['PO_id'] = $PurchaseOrder_id;
	    }
	    
	return $return;
}
/**********************************************按下Delete******************************************************/
if(isset($_POST['delete'])){
    $PurchaseOrder_id = $_POST['delete'];
    $delete = $conn->prepare("DELETE FROM PurchaseOrder_List WHERE 	PO_List_PurchaseOrder_id = :PO_List_PurchaseOrder_id");
    $delete->execute(array(':PO_List_PurchaseOrder_id' => $PurchaseOrder_id));
    $totalRow = $delete->rowCount();
    $delete = $conn->prepare("DELETE FROM PurchaseOrder WHERE PurchaseOrder_id = :PurchaseOrder_id");
    $delete->execute(array(':PurchaseOrder_id' => $PurchaseOrder_id));
    $totalRow = $totalRow + $delete->rowCount();
    if($delete->rowCount() > 0){
        echo "<div class='alert alert-success' role='alert'> 刪除訂單成功 </div>
        回到<a href='http://db4-ouvek-kostiva.c9users.io/Purchase/Purchase_Order_Search.php'>進貨訂單</a>";

    }
}


?>
<meta charset="utf-8">
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