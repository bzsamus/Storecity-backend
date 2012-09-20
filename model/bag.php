<?php

class bag{

  function setVars($vars){
    $this->items = $vars;
  }

  function addItem(&$item){
    $key = $item->id;
    // separate street items
    if(substr((string)$item->globalItemId,0,1) == '5'){
      if(!array_key_exists($key,$this->streetItems)){
        $this->streetItems[$key] = $item;
        return true;
      }
      else{
        return false;
      }
    }
    // items minus street items
    elseif(!array_key_exists($key,$this->items)){
      $this->items[$key] = $item;
      return true;
    }
    else{
      return false;
    }
  }

  function removeItem(&$item){
    $key = $item->id;
    // separate street items
    if(substr((string)$item->globalItemId,0,1) == '5'){
      if(array_key_exists($key,$this->streetItems)){
        $tmp = $this->streetItems[$key];
        unset($this->streetItems[$key]);
        return knightlover::array_to_object($tmp);
      }
      else{
        return false;
      }
    }
    elseif(array_key_exists($key,$this->items)){
      $tmp = $this->items[$key];
      unset($this->items[$key]);
      return knightlover::array_to_object($tmp);
    }
    else{
      return false;
    }
  }

  function replaceItem($item){
    if(is_array($item)){
/*
      // hack for employeeId bigint problem
      if(array_key_exists('employeeId',$item)){
        $item['employeeId'] = sprintf("%.0f", $item['employeeId']);
      }
*/
    $item = knightlover::array_to_object($item);
    }
    $key = $item->id;
    // separate street items
    if(substr((string)$item->globalItemId,0,1) == '5'){
      if(array_key_exists($key,$this->streetItems)){
        $this->streetItems[$key] = $item;
        return true;
      }
      else{
        return false;
      }
    }
    elseif(array_key_exists($key,$this->items)){
      if(isset($this->items[$key]->timer)){
        if(isset($this->items[$key]->employeeId)){
        // calculate remain time
          $item->timer = max(0,$this->items[$key]->timer - (time() - $this->items[$key]->timestamp));
        }
        else{
          $item->timer = $this->items[$key]->timer;
        }
        $item->timestamp = time();
      }
      $this->items[$key] = $item;
      return true;
    }
    else{
      return false;
    }
  }

  function toArray(){
    $array1 = $this->items?$this->items:array();
    $array2 = $this->streetItems?$this->streetItems:array();
    $tmp = array_merge($array1,$array2);
        if($tmp){
      sort($tmp);
      knightlover::load_model('adapterClass');
      $classname = get_class($this);
      if($classname == 'ingameItem'){
        foreach($tmp as &$t){
          $t = new ownedItem($t);
          // calculate remaining time for cash counters
          if($t->timer > 0 && $t->employeeId > 0){
            
            //$t->timer = max(0,$t->timer - (time()-$t->timestamp)) * 1000;
            // update timestamp
            $t->timer = $t->timer * 1000;
            $this->items['id']['timer'] = $t->timer;
            $this->items['id']['timestamp'] = time();
          }
        }
      }
      else if($classname == 'inventory'){
        foreach($tmp as &$t){
          $t = new inventoryItems($t);
        }
      }
    }
    return $tmp;
  }

  function streetToArray(){
    $tmp = $this->streetItems;
    knightlover::load_model('adapterClass');
    if(is_array($tmp)){
        sort($tmp);
        foreach($tmp as &$t){
          $t = new ownedItem($t);
        }
    }
    if(is_array($this->items)){
      foreach($this->items as $i){
        if(substr($i['globalItemId'],0,1) == '6'){
          $tmp[] = new ownedItem($i);
        }
      }
    }
    return $tmp;
  }

 function ingameToArray(){
    $tmp = $this->items;
    if(is_array($tmp)){
        sort($tmp);
        knightlover::load_model('adapterClass');
        foreach($tmp as &$t){
          $t = new ownedItem($t);
        }
    }
    return $tmp;
  }
 
}
