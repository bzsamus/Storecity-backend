<?php

class inventoryFactory{
  public static function getInstance($vars = null){
    $obj = knightlover::objhandler()->getObject('inventory');
    if($vars){
      $obj->setVars($vars);
    }
    return $obj;
  }
}

?>
