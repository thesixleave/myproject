<?php
    function customError($errno, $errstr) {
        if($errno == 2){
            echo "<div class='alert alert-danger' role='alert'> 資料庫不存在 <a href='./init/index.php'>請按此建立資料表</a> </div>";
    
        }        
    }
    
    set_error_handler("customError");

    $dbhost = '127.0.0.1';
    $dbuser = 'Signupuser';
    $dbpass = '';
    $dbname = 'c9';
        $conn = new PDO("mysql:host=".$dbhost.";dbname=".$dbname,$dbuser,$dbpass);

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        $conn->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND,'SET NAMES UTF8');
        
        $select = $conn->prepare("SELECT Username FROM Users WHERE userCookie = :ucook ;");
        $select->execute(array(':ucook' => $_COOKIE['dbproject']));
        $row = $select->fetch(PDO::FETCH_ASSOC);
        $username = "";
        if($row){
            $username = $row['Username'];
            header('Location: http://db4-ouvek-kostiva.c9users.io/Sales/index.php');
        }
?>

<!DOCTYPE html>
<!-- saved from url=(0038)https://kkbruce.tw/bs3/Examples/signin -->
<html lang="en">

<head>
    <title>使用者登入</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="./favicon.ico">
    <link href="./css/bootstrap.css" rel="stylesheet">
    <link href="./css/ie10-viewport-bug-workaround.css" rel="stylesheet">
    <link href="./css/starter-template.css" rel="stylesheet">
</head>

<body background="./pic2.jpg">

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
                    <li class="active"><a href="./Sales/index.php" class="btn btn-secondary btn-sm" disabled><span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> 使用者登入</a></li>
                    <!--<li><a href="./map.php"><span class="glyphicon glyphicon-move" aria-hidden="true"></span> 使用者地圖導覽</a></li>
                    <li><a href="./Introduction.php"><span class="glyphicon glyphicon-home" aria-hidden="true"></span> 系統介紹</a></li>-->
                    <li><a href="./registered.php"><span class="glyphicon glyphicon-registration-mark" aria-hidden="true"></span> 註冊帳號</a></li>
                </ul>
                <ul class="nav navbar-nav pull-right">

                    <p class="navbar-text"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> <?php if($username != ""){echo $username;}else{echo "Username";} ?></p>
                </ul>
            </div>
            <!--/.nav-collapse -->
            <div id="navbar" class=""></div>
        </div>
    </nav>

    <!--上選列-->
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <div class="container">

        <form action="login.php" method="POST" class="form-signin" role="form">
            <table align=center style="background-color=#FAFAFA">
                <tbody>
                    <tr>
                        <td>
                            <span style="color:orange;">
                            <h2 class="form-signin-heading">會員登入</h2>
                        </span>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label for="inputEmail" class="sr-only">請輸入E-mail</label>
                            <input type="email" id="inputEmail" class="form-control" placeholder="Email address" size="60%" required="" autofocus="" name="Email"> <!-- name="Email" 代表php 的 $_POST['Email']取值 -->
                            <br/>
                            <label for="inputPassword" class="sr-only">Password</label>
                            <input type="password" id="inputPassword" class="form-control" minlength="8" maxlength="20" placeholder="Password" size="60%" required="" autofocus="" name="Password">
                            </br>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <input class="btn btn-success" type="submit" value="送出" >
                            <!--onClick="javascript:chk(Password.value)"-->
                            <input class="btn btn-danger" type="reset" value="清除">
                            <!--div class="fb-login-button" data-max-rows="1" data-size="medium" data-show-faces="false" data-auto-logout-link="false" ></div-->
                            <!--data-auto-logout-link="false" :登入判別功能顯示 true為登出-->
                            <!--data-show-faces="false" false為登入圖示 true個人FB基本資訊-->
                        </td>
                    </tr>

                </tbody>
            </table>
        </form>
   <!--form method="GET" action="test.php"><input type="submit" value="test"></form-->
    </div>

<script>
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/zh_TW/sdk.js#xfbml=1&version=v2.6&appId=186294461770931";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>


<!--Script language="JavaScript">
var toalarm = false;
var ch;
var stralarm = new Array("<",">",".","!",";"); //禁止的字元
function chk(str) {
    for (var i=0;i<stralarm.length;i++){ //依序載入輸入的字元
        for (var j=0;j<str.length;j++){
            ch=str.substr(j,1);
            if (ch==stralarm[i]){ //如果包含禁止字元
                toalarm = true; //設置此變數為true
                }}}
    if (toalarm){
        alert("包含特殊字元,請修正!"); 
        }} 
</Script-->

    <script src="./js/ie10-viewport-bug-workaround.js"></script>
    <script>
  // This is called with the results from from FB.getLoginStatus().
  function statusChangeCallback(response) {
    console.log('statusChangeCallback');
    console.log(response);
    // The response object is returned with a status field that lets the
    // app know the current login status of the person.
    // Full docs on the response object can be found in the documentation
    // for FB.getLoginStatus().
    if (response.status === 'connected') {
      // Logged into your app and Facebook.
      testAPI();
    } else if (response.status === 'not_authorized') {
      // The person is logged into Facebook, but not your app.
      document.getElementById('status').innerHTML = 'Please log ' +
        'into this app.';
    } else {
      // The person is not logged into Facebook, so we're not sure if
      // they are logged into this app or not.
      document.getElementById('status').innerHTML = 'Please log ' +
        'into Facebook.';
    }
  }

  // This function is called when someone finishes with the Login
  // Button.  See the onlogin handler attached to it in the sample
  // code below.
  function checkLoginState() {
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
  }

  window.fbAsyncInit = function() {
  FB.init({
    appId      : '186294461770931',
    cookie     : true,  // enable cookies to allow the server to access 
                        // the session
    xfbml      : true,  // parse social plugins on this page
    version    : 'v2.6' // use version 2.6
  });

  // Now that we've initialized the JavaScript SDK, we call 
  // FB.getLoginStatus().  This function gets the state of the
  // person visiting this page and can return one of three states to
  // the callback you provide.  They can be:
  //
  // 1. Logged into your app ('connected')
  // 2. Logged into Facebook, but not your app ('not_authorized')
  // 3. Not logged into Facebook and can't tell if they are logged into
  //    your app or not.
  //
  // These three cases are handled in the callback function.

  FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });

  };

  // Load the SDK asynchronously
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));

  // Here we run a very simple test of the Graph API after login is
  // successful.  See statusChangeCallback() for when this call is made.
  function testAPI() {
    console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', function(response) {
      console.log('Successful login for: ' + response.name);
      document.getElementById('status').innerHTML =
        'Thanks for logging in, ' + response.name + '!';
    });
  }
</script>

</body>

</html>
<!--pixiv 註記id=51696492 作者:Hattori184著-->