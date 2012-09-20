<?php

class userInfoFactory{
  public static function getInstance($vars = null){
    $obj = knightlover::objhandler()->getObject('userInfo');
    if($vars){
      $obj->setVars($vars);
    }
    return $obj;
  }
}

?>
