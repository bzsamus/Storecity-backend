<?php
include_once('model.php');

class facebookInfo extends model{
  var $username;
  var $profilePic;
  var $gender;

  function getData($uid){
    //get facebook info from cache
    $info = knightlover::cache()->get("fbinfo_$uid");
    // cache not exist, get it from facebook
    if(!$info){
/* deprecated old method
      $query = "SELECT name,pic_square,sex FROM user WHERE uid='$uid'";
      $rs = knightlover::fb()->api(array(
        'method' => 'fql.query',
        'query' => $query,
      ));
*/
      $rs = knightlover::fb()->api('/'.$uid.'?locale=en_US');
      if($rs){
        $this->username = $rs['name'];
        $this->profilePic = 'http://graph.facebook.com/'.$uid.'/picture';
        $this->gender = $rs['gender'];
        // cache da info
        knightlover::cache()->set("fbinfo_$uid",get_object_vars($this),_CACHE_TIME_LONG);
      }
    }
    else{
      $this->setVars($info);
    }
  }
}

?>
