<?php

class OwnedItem{

  var $employeeId;
  var $data;
  var $positionX;
  var $positionY;
  var $globalItemId;
  var $roomIndex;
  var $id;

  function __construct($item){
    $tmp = get_object_vars($this);
    if($item){
      foreach($tmp as $i=>$t){
        $attr[] = $i;
      }
      foreach($item as $i=>$v){
        if(in_array($i,$attr)){
          $this->$i = $v;
        }
      }
    }
  }
}

?>
