<?php
$app_id = 186294461770931;
$app_secret = bcb18be050d02f2043a5b46a6e7cf262;
			
$exchange_long_lived_token_url = "https://graph.facebook.com/oauth/access_token?client_id=$app_id&client_secret=$app_secret&grant_type=fb_exchange_token&fb_exchange_token=$access_token";


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $exchange_long_lived_token_url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch);


header("Location:https://www.facebook.com/dialog/oauth? client_id=186294461770931 &redirect_uri=http://db4-ouvek-kostiva.c9users.io/Sales/Product_list.php &scope=email&response_type=token");


?>