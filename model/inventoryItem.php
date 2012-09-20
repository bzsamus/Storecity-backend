<?php
  class InventoryItem{
    var $globalItemId;
    var $number;

    function __construct($id){
      $this->globalItemId = $id;
      $this->number = 1;
    }
  }
?>
