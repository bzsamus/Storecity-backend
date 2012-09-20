<?php
class UserFactory{
  public static function getInstance($id){
    $obj = knightlover::objhandler()->getObject('user');
    $obj->__construct($id);
    return $obj;
  }
}
?>
