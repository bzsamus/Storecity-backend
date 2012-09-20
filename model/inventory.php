<?php
include_once('bag.php');

class inventory extends bag{

  var $items=array();

  function addItem(&$item){
    $key = $item->globalItemId;
    if(array_key_exists($key,$this->items)){
      if(is_array($this->items[$key])){
        $this->items[$key] = knightlover::array_to_object($this->items[$key]);
      }
      $this->items[$key]->number += 1;
    }
    else{
      include_once('inventoryItem.php');
      $this->items[$key] = new InventoryItem($key);
    }
    return true;
  }

  function removeItem(&$item){
    $key = (string)$item->globalItemId;
    if(array_key_exists($key,$this->items)){
      if(is_array($this->items[$key])){
        $this->items[$key] = knightlover::array_to_object($this->items[$key]);   
      }
      if($this->items[$key]->number - $item->number == 0){
        unset($this->items[$key]);
      }
      else if($this->items[$key]->number - $item->number < 0){
        return false;
      }
      else{
        $this->items[$key]->number -= $item->number;
      }
      if(isset($item->number)){
        unset($item->number);
      }
      return $item;
    }
    else{
      return false;
    }
  }
}

?>
