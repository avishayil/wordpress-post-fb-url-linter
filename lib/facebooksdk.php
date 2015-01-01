<?php
require_once 'facebook-php-sdk/src/facebook.php';

function url_linter($url,$appid,$appsecret) {

	$facebook = new Facebook(array(
       'appId'  => $appid,
       'secret' => $appsecret,
       'cookie' => true,
	));

    $response = $facebook->api('/','POST',array(
        'id'=>$url,
        'scrape'=>'true'
    ));

    return $response['updated_time'];
}
?>