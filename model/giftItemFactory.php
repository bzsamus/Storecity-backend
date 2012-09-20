<?php

class giftItemFactory{
  public static function getInstance(){
    $obj = knightlover::objhandler()->getObject('giftItem');
    return $obj;
  }
}

?>
