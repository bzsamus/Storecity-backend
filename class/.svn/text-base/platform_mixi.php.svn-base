<?php
include_once('platform_interface.php');

class platform_mixi implements platform_interface{

  private $rest_url = 'http://api.mixi-platform.com/os/0.8/';
  private $CONSUMER_KEY = '05b91e8fab667ebb8c3c';
  private $CONSUMER_SECRET = 'b52c82e6febfc1474a0a6b2b9365b150d0448cf8';

  function tokenToUid($token){
	$uid = knightlover::cache()->get($token);
	return $uid;
  }

  function getFriendList($token){
    $uid = $this->tokenToUid($token);
    $rs = $this->restRequest($uid,'people/'.$uid.'/@all');
    $users = array();
    if(is_array($rs->entry)){
    foreach($rs->entry as $r){
      $tmp['id'] = str_replace('mixi.jp:','',$r->id);
      $tmp['name'] = $r->displayName;
      $tmp['profilePic'] = $r->thumbnailUrl;
      $users[] = $tmp;
    }
    }
    return array('data' => $users);
  }

  function getUserInfo($uid){
    $rs = $this->restRequest($uid,'people/'.$uid.'/@self');
    $user['id'] = $rs->entry->id;
    $user['name'] = $rs->entry->displayName;
    $user['profilePic'] = $rs->entry->thumbnailUrl;
    return $user;
  }

  function restRequest($uid,$feed){
    knightlover::load_library('oauth');
    $consumer = new OAuthConsumer($this->CONSUMER_KEY, $this->CONSUMER_SECRET, NULL);
    $base_feed = $this->rest_url.$feed;
    $params = array('xoauth_requestor_id' => $uid);
    $request = OAuthRequest::from_consumer_and_token($consumer, NULL, 'GET', $base_feed, $params);
    // Sign the constructed OAuth request using HMAC-SHA1
    $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, NULL);

    // Make signed OAuth request to the Contacts API server
    $url = $base_feed . '?' . $this->implode_assoc('=', '&', $params);
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_ENCODING, 'gzip');

    $auth_header = $request->to_header();
    if ($auth_header) {
      curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));
    }

    $response = curl_exec($curl);
    if (!$response) {
      $response = curl_error($curl);
    }
    curl_close($curl);
    return json_decode($response);
  }

  function implode_assoc($inner_glue, $outer_glue, $array) {
    $output = array();
    foreach($array as $key => $item) {
        $output[] = $key . $inner_glue . urlencode($item);
    }
    return implode($outer_glue, $output);
  }
}

?>
