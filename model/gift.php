<?php

class gift{

  var $userId;

  public function __construct($id){
    $this->userId = $id;
  }

  public function __get($var){
    if(!isset($this->$var)){
      if($var == 'dbdata'){
        $collection = knightlover::db()->getCollection(get_class($this));
        $filter = array('userId' => $this->userId);
        $obj = $collection->findOne($filter);
        $this->$var = &$obj;
      }
      else{
        eval('$this->_load'.$var.'();');
      }
    }
    return $this->$var;
  }

  function _loaditems(){
    $this->items = $this->dbdata['items'];
    if(!isset($this->items)){
      $this->items = array();
    }
  }

  public function queue($giftItem){
    if(is_array($this->items)){
      array_push($this->items,$giftItem);
      $this->inject();
    }
  }

  public function getAll(){
    $items = $this->items;
    $this->items = array();
    $this->inject();
    return $items;
  }

  function genhash(){

  }

  function createPrize($giftItem,$num,$duration){

  }

  function claimPrize(&$user,$hash){

  }

  public function inject(){
    if(isset($this->dbdata))
      unset($this->dbdata);
    $objs = get_object_vars($this);
    foreach($objs as $i=>$u){
      if($u === NULL){
        unset($objs[$i]);
      }
    }
    $filter = array('userId' => $this->userId);
    $update = array(
      '$set'  =>  $objs
    );
    $options = array(
      'multiple' => false,
      'upsert'   => true
    );
    $rs = knightlover::db()->getCollection(get_class($this))->update($filter,$update,$options)?true:false;
    return $rs;
  }
}

?>
