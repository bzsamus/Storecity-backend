<?php

class giftFactory{
  public static function getInstance($id){
    $obj = knightlover::objhandler()->getObject('gift');
    $obj->__construct($id);
    return $obj;
  }
}

?>
