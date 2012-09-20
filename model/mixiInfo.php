<?php
include_once('model.php');

class mixiInfo extends model{
  var $username;
  var $profilePic;
  var $gender;

  function getData($uid){
    $platform = knightlover::conf()->platform;
    $user = knightlover::cache()->get($platform.'_'.$uid);
    $this->setVars($user);
  }
}

?>
