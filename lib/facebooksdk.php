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
    
    if (isset($response['updated_time'])) {
      return $response['updated_time'];
    } else {
      return false;
    }
}

if (isset($_GET['linturl'])) {
  $result = url_linter($_GET['linturl'], $_GET['appid'], $_GET['appsecret']);
  echo $result;
}
?>