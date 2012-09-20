<?php

class shopExteriorFactory{
  public static function getInstance(){
    $obj = knightlover::objhandler()->getObject('shopExterior');
    return $obj;
  }
}

?>
