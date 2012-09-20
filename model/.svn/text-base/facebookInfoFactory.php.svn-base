<?php

class facebookInfoFactory{
  public static function getInstance($uid){
    $platform = knightlover::conf()->platform.'Info';
    $obj = knightlover::objhandler()->getObject($platform);
    $obj->getData($uid);
    return $obj;
  }
}

?>
