<?php

class skinDataFactory{
  public static function getInstance($vars = null){
    $obj = knightlover::objhandler()->getObject('skinData');
    if($vars){
      $obj->setVars($vars);
    }
    return $obj;
  }
}

?>
