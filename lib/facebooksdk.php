<?php
require_once 'facebook-php-sdk/src/facebook.php';

function url_linter($url,$appid,$appsecret) {

  $facebook = new Facebook(array(
       'appId'  => $appid,
       'secret' => $appsecret,
       'cookie' => true,
  ));

  do {
    $response = $facebook->api('/','POST',array(
        'id'=>$url,
        'scrape'=>'true'
    ));
    if (isset($response['updated_time'])) {
      return $response['updated_time'];
      break;
    } else {
      sleep(15);
    }
  } while (!isset($response['updated_time']) );
}
?>