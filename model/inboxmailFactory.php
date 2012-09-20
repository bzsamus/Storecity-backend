<?php

class inboxmailFactory{
  public static function getInstance(){
    $obj = knightlover::objhandler()->getObject('inboxmail');
    return $obj;
  }
}

?>
