<?php

class inboxFactory{
  public static function getInstance($id){
    $obj = knightlover::objhandler()->getObject('mailbox');
    $obj->__construct($id);
    return $obj;
  }
}

?>
