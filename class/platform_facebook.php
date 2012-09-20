<?php
include_once('platform_interface.php');

class platform_facebook implements platform_interface{
  function tokenToUid($token){
    $uid = knightlover::cache()->get('fb_token_'.$token);
      if(!$uid){
        $rs = knightlover::fb()->api('/me/',array('access_token' => $token));
        $uid = $rs['id'];
        if(!$uid){
          throw new Exception('failed to optain uid');
        }
        else{
          knightlover::cache()->set('fb_token_'.$token,$uid,_CACHE_TIME_LONG);
        }
      }
      return $uid;
  }

  function getFriendList($token){
    $rs = knightlover::fb()->api('/me/friends/',array('access_token' => $token));
    foreach($rs['data'] as &$r){
      $r['profilePic'] = 'http://graph.facebook.com/'.$r['id'].'/picture';
    }
    return $rs;
  }

  function getUserInfo($uid){
    $rs = knightlover::fb()->api('/'.$uid.'?locale=en_US');
    $rs['profilePic'] = 'http://graph.facebook.com/'.$uid.'/picture';
    return $rs;
  }
}

?>
