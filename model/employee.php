<?php
include_once('model.php');

class employee extends model{
  var $id;
  var $task;
  /*jack do not like these
  var $clothes;
  var $notify;*/
  var $lifetime;
  
  function __construct($arr){
    $this->setVars($arr);
  }
}

?>
