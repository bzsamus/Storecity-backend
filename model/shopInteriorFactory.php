<?php

class shopInteriorFactory{
  public static function getInstance(){
    $obj = knightlover::objhandler()->getObject('shopInterior');
    return $obj;
  }

  public static function getNewInstance(){
    $obj = knightlover::objhandler()->getObject('shopInterior');
    $obj->items = array();
    $obj->shelfs = array();
    $obj->cashCounters = array();
    $obj->floors = array();
    $obj->trashes = array();
    return $obj;
  }
}

?>
