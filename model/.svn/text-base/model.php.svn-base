<?php

class model{

public function setVars($vars){
    $tmp = get_object_vars($this);
    if($vars){
      foreach($tmp as $i=>$t){
        $attr[] = $i;
      }
      foreach($vars as $i=>$v){
        if(in_array($i,$attr)){
          $this->$i = $v;
        }
      }
    }
  }

public function getVars(){
  return get_object_vars($this);
}
}
?>
